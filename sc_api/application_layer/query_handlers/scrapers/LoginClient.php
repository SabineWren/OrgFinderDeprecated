<?php

namespace thalos_api;

class LoginClient
{
    private $settings;
    private $cookie_file_path;
    
    public $cookies;
    
    public function __construct()
    {
        // Get our login credentials
        require(__DIR__.'/../../../settings.php');
        $this->settings = $_SETTINGS;
        
        $this->Reset();
    }
    
    private function Reset()
    {
        $this->cookies = array();
        $this->cookie_file_path = __DIR__."/cookies.txt";
    }
    
    public function GetPage($url, $custom_headers = array())
    {
		try 
		{
			$ch = curl_init();

			if (FALSE === $ch)
			{
				throw new \Exception('Curl failed to initialize.');
			}
			
			$agent = "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:29.0) Gecko/20100101 Firefox/29.0";

			$headers = $custom_headers;
			$headers[] = "Accept: */*";
			$headers[] = "Connection: Keep-Alive";
			$headers[] = "Accept-Encoding: gzip, identity";

			// basic curl options for all request
			curl_setopt($ch, CURLOPT_HTTPHEADER,  $headers);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);    
			curl_setopt($ch, CURLOPT_USERAGENT, $agent);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($ch, CURLOPT_ENCODING, '');
			curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookie_file_path);
			curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookie_file_path);
			
			curl_setopt($ch, CURLOPT_URL, $url);

			$content = curl_exec($ch);

			if (FALSE === $content)
			{
				throw new \Exception(curl_error($ch), curl_errno($ch));
			}
		
			// Close request.
			curl_close($ch);
			
			return $content;
		} 
		catch(Exception $e) 
		{

			trigger_error(sprintf(
				'Curl failed with error #%d: %s',
				$e->getCode(), $e->getMessage()),
				E_USER_ERROR);

		}
		
		return false;
    }
    
    public function LoginRSI()
    {
        // Clear out temp cookies
        $this->Reset();
        
        // options
        $HANDLE           = $this->settings['clients']['rsi']['username'];
        $PASSWORD         = $this->settings['clients']['rsi']['password'];
        $LOGINURL         = "https://robertsspaceindustries.com/connect";
        $agent            = "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:29.0) Gecko/20100101 Firefox/29.0";


        // begin script
        $ch = curl_init();

        // extra headers
        $headers[] = "Accept: */*";
        $headers[] = "Connection: Keep-Alive";
		$headers[] = "Accept-Encoding: gzip, identity";
        $headers[] = "Referer: https://robertsspaceindustries.com/";

        // basic curl options for all requests
        curl_setopt($ch, CURLOPT_HTTPHEADER,  $headers);
        curl_setopt($ch, CURLOPT_HEADER,  0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);    
        curl_setopt($ch, CURLOPT_USERAGENT, $agent);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_ENCODING, '');
        curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookie_file_path);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookie_file_path);
        
        // set first URL
        curl_setopt($ch, CURLOPT_URL, $LOGINURL);

        // execute session to get cookies and required form inputs
        $content = curl_exec($ch);
        
        $string = file_get_contents($this->cookie_file_path);
        
        // get cookies
        $cookies = array();
        preg_match_all('|\.robertsspaceindustries\.com(\s+\S+){4}\s+(?<name>\S+)\s+(?<value>\S+)\s*|', $string, $cookies, PREG_SET_ORDER);
        
        $headers = array();
        $headers[] = "Accept: */*";
        $headers[] = "Connection: Keep-Alive";
        $headers[] = "X-Requested-With: XMLHttpRequest";
        $headers[] = "Connection: Keep-Alive";
		$headers[] = "Accept-Encoding: gzip, identity";
        $headers[] = "Referer: ".$LOGINURL;
        foreach($cookies as $cookie)
        {
            if($cookie['name'] == 'Rsi-Token')
            {
                $headers[] = "X-Rsi-Token: ".$cookie['value'];
            }
            
            $this->cookies[$cookie['name']] = $cookie['value'];
        }
        
        $fields['username'] = $HANDLE;
        $fields['password'] = $PASSWORD;
        $fields['remember'] = 1;

        // set postfields using what we extracted from the form
        $POSTFIELDS = http_build_query($fields);

        // change URL to login URL
        $LOGINURL   = "https://robertsspaceindustries.com/api/account/signin";
        curl_setopt($ch, CURLOPT_URL, $LOGINURL);

        // set post options
        curl_setopt($ch, CURLOPT_HTTPHEADER,  $headers);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $POSTFIELDS);
		curl_setopt($ch, CURLOPT_ENCODING, '');

        // perform login
        $result = curl_exec($ch);
        
        return $result;
    }


    private function GetFormFields($data)
    {
        if (preg_match('/(<form class="signin-form".*?<\/form>)/is', $data, $matches)) {
            $inputs = $this->GetInputs($matches[1]);

            return $inputs;
        } else {
            die('didnt find login form');
        }
    }

    private function GetInputs($form)
    {
        $inputs = array();

        $elements = preg_match_all('/(<input[^>]+>)/is', $form, $matches);

        if ($elements > 0) {
            for($i = 0; $i < $elements; $i++) {
                $el = preg_replace('/\s{2,}/', ' ', $matches[1][$i]);

                if (preg_match('/name=(?:["\'])?([^"\'\s]*)/i', $el, $name)) {
                    $name  = $name[1];
                    $value = '';

                    if (preg_match('/value=(?:["\'])?([^"\'\s]*)/i', $el, $value)) {
                        $value = $value[1];
                    }

                    $inputs[$name] = $value;
                }
            }
        }

        return $inputs;
    }
}