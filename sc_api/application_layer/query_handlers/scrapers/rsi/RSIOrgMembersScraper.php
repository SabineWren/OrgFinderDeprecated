<?php

namespace thalos_api;

require_once(__DIR__.'/../Scraper.php');

class RSIOrgMembersScraper extends Scraper
{
    public function PerformQuery()
    {
        // Get a shorter reference to our target
        $target_id = $this->Output->request_stats->resolved_query->target_id;
        
        // Return failure if the target isn't set
        if(!isset($target_id)
            || $target_id == null)
        {
            return false;
        }
        
        // Get the target page
        $base_url = 'http://robertsspaceindustries.com/orgs/'.$target_id.'/members?page=';
        
        $current_page = $this->Output->request_stats->resolved_query->pagination->start_page;
        $end_page = $this->Output->request_stats->resolved_query->pagination->end_page;
        
        $info = array();
        
        while($current_page <= $end_page)
        {
            $result = $this->GetPage($base_url . $current_page);
        
            // If we don't have page data
            if($result == false)
            {
                // Exit the loop
                break;
            }
            
            // Set up our patterns
            $pattern_keys = array
            (
                1=>new Item('visibility','visibility-(\w)',array(new Postprocess(array($this, 'ProcessVisibility')))),
                2=>new Item('handle','nick .*">(.*)<',array(new Postprocess('strtolower'))),
                3=>new Item('moniker','name .*">(.*)<',array(new Postprocess('strtolower'))),
                4=>new Item('type','roles">\s*<span class="title">(.*)</span>(.*)</span>', array(new Postprocess(array($this, 'ProcessType')))),
                5=>new Item('roles',null,array(new Postprocess(array($this, 'ResolveRoles')))),
                6=>new Item('stars','class="stars" style="width: (.*)%;"></span>',array(new Postprocess(array($this, 'ResolveStars')))),
                7=>new Item('rank','<span class="rank">\s*(.*)\s*</span>'),
            );
            
            // Run the search
            $data = $this->ScrapePage($result, $pattern_keys, '<li class="member-item');
            
            if($data == false
                || $data == null)
            {
                break;
            }
            
            foreach($data as $key=>$item)
            {
                if($data[$key]['visibility'] != 'visible')
                {
                    $data[$key]['roles'] = null;
                    $data[$key]['stars'] = null;
                    $data[$key]['rank'] = null;
                    $data[$key]['type'] = null;
                    $data[$key]['handle'] = null;
                }
            }

            $info = array_merge($info, $data);
            
            $current_page++;
        }

        // If we got valid data
        if(count($info) > 0)
        {
            // Update our data array
            $this->Output->SetData($info);

            $temp_output = $this->Output->GetAll();
            $temp_output = new Output($temp_output);
            unset($temp_output->data);
            
            if(!$this->Output->request_stats->resolved_query->expedite)
            {
                $Scraper = new RSIOrgScraper($temp_output, $this->query_chain_entry);
                $Scraper->PerformQuery();
            }
        }
    }
    
    protected function ValidateLocalSettings()
    { }
    
    public function ResolveRoles($input)
    {
        $roles = array();
        
        if(preg_match_all('|.*<li class="role.*>\-*?\s*?(.*)</li>|Us', $input, $matches, PREG_SET_ORDER))
        {
            foreach($matches as $match)
            {
                $roles[] = $match[1];
            }
        }
        
        return $roles;
    }
    
    public function ResolveStars($input)
    {
        return round(($input/100)*5);
    }
    
    public static function ProcessType($insert)
    {
        if($insert == 'Affiliate')
        {
            return 'affiliate';
        }
         return 'main';
    }
    
    public static function ProcessVisibility($block)
    {
        switch($block)
        {
            case 'V':
                return 'visible';
            case 'R':
                return 'redacted';
            case 'H':
                return 'hidden';
        }
        
        return null;
    }
}
