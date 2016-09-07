<?php

namespace thalos_api;

require_once(__DIR__.'/../Scraper.php');

class WikiaOrgScraper extends Scraper
{
    public function PerformQuery()
    {
        $this->output['request_stats']['resolved_query']['system'] = 'organizations';
        $this->output['request_stats']['resolved_query']['action'] = 'single_organization';
        $this->output['request_stats']['resolved_query']['data_source'] = 'rsi';
        $this->output['request_stats']['resolved_query']['api_source'] = 'live';
        
        $result = $this->GetPage('http://starcitizen.wikia.com/wiki/Star_Citizen_Squadrons_%26_Guilds');
        
        if($result)
        {
            $pattern_keys = array
            (
                1=>new Item('website','<a rel="nofollow" class="external text.*" href="(.*)">(.*)</a>'),
                2=>new Item('name'),
                3=>new Item('motto','<span style="font-size:90%"><i>[<|.|>]*?(.*)</'),
                4=>new Item('logo','<img.*src="(.*)"'),
                5=>new Item('member_count','>Population.*<.*<td>[\s\~]*?([\d]*?).*</td>'),
                6=>new Item('voip','>VOIP.*<td>(.*)(\\n)*<.*</table>'),
             );
            
            $info = $this->ScrapePage_Merged_All($result, $pattern_keys);
        }

        $this->output['data'] = $info;
        
        //if(isset($this->output['data']['citizen_number']))
            //$this->set_cache_dossier();
    }
    
    protected function ValidateLocalSettings()
    { }
    
    public static function ResolveRoleplay($input)
    {
        if($input != null)
        {
            return 'Yes';
        }
        
        return 'No';
    }
    
    public static function ResolveRecruiting($input)
    {
        if($input != null)
        {
            return 'Yes';
        }
        
        return 'No';
    }
}