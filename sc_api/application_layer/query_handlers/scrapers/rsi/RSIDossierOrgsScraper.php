<?php

namespace thalos_api;

require_once(__DIR__.'/../Scraper.php');

class RSIDossierOrgsScraper extends Scraper
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
        $result = $this->GetPage('http://robertsspaceindustries.com/citizens/' . urlencode($target_id) . '/organizations');
        
        $orgs = null;

        // If we have page data
        if($result)
        {
            // Set up our patterns
            $pattern_keys = array
            (
                1=>new Item('sid','<a href="/orgs/(.*)">',array(new Postprocess('strtolower'))),
                2=>new Item('visibility','<div class="box-content org main visibility-(\w)">',array(new Postprocess(array($this, 'ProcessVisibility')))),
                3=>new Item('type','.',array(new Postprocess(array($this, 'ProcessType'),array('main')))),
                4=>new Item('stars','<div class="ranking.*">(.*)</div>',array(new Postprocess(array($this, 'ResolveStars')))),
                5=>new Item('rank','Organization rank</span>\s*<strong class="value.*">(.*)<'),
            );
            
            // Run the search
            $info = $this->ScrapePage($result, $pattern_keys, '<div class="profile-content orgs-content');
            
            if($info[0]['visibility'] != 'visible'
                && $info[0]['sid'] != null)
            {
                $info[0]['stars'] = null;
                $info[0]['rank'] = null;
                $info[0]['sid'] = null;
            }
            
            if($info[0]['sid'] != null)
                $orgs[] = $info[0];
            
            
            // Set up our patterns
            $pattern_keys = array
            (
                1=>new Item('sid','<a href="/orgs/(.*)">',array(new Postprocess('strtolower'))),
                2=>new Item('visibility','visibility-(\w)',array(new Postprocess(array($this, 'ProcessVisibility')))),
                3=>new Item('type','.',array(new Postprocess(array($this, 'ProcessType'),array('affiliate')))),
                4=>new Item('stars','<div class="ranking.*">(.*)</div>',array(new Postprocess(array($this, 'ResolveStars')))),
                5=>new Item('rank','Organization rank</span>\s*<strong class="value .*">(.*)<'),
            );
            
            // Run the search
            $data = $this->ScrapePage($result, $pattern_keys, '<div class="box-content org affiliation');
            
            if($data != null)
            {
                foreach($data as $key=>$item)
                {
                    if($data[$key]['visibility'] != 'visible'
                        && $data[$key]['sid'] != null)
                    {
                        $data[$key]['stars'] = null;
                        $data[$key]['rank'] = null;
                    }
                }
                
                $orgs = array_merge($orgs, $data);
            }
        }
        
        if($orgs != null)
        {
            $info = array();
            $info['organizations'] = $orgs;
            $info['handle'] = $target_id;

            // Update our data array
            $this->Output->SetData($info);
        
            if(!$this->Output->request_stats->resolved_query->expedite)
            {
                if($info['organizations'] != null)
                {
                    // For each organization we found
                    foreach($info['organizations'] as $index=>$org)
                    {
                        if(isset($org['sid']))   
                        {
                            $input = new Output(array());
                            $input->request_stats->resolved_query->target_id = $org['sid'];

                            $Handler = new RSIOrgScraper($input, $this->query_chain_entry);
                            $Handler->PerformQuery();
                        }
                    }
                }
            }

            if($info['organizations'] != null
                && count($info['organizations']) > 0)
            {
                // Update the cache
                $Cacher = new RSIAccountCacher($this->Output, $this->query_chain_entry);
                $Cacher->UpdateCache(strtolower($target_id), $this);
            }
        }
    }
    
    protected function ValidateLocalSettings()
    { }
    
    public static function ProcessType($insert)
    {
        return $insert;
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
    
    public function ResolveStars($input)
    {
        $pattern = '|active|Us';
        
        preg_match_all($pattern, $input, $matches);
        
        return "".count($matches[0]);
    }
}