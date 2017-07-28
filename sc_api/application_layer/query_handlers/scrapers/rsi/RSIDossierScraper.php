<?php

namespace thalos_api;

require_once(__DIR__.'/../Scraper.php');

class RSIDossierScraper extends Scraper
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
        $result = $this->GetPage('http://robertsspaceindustries.com/citizens/' . urlencode($target_id));

        // If we have page data
        if($result)
        {
            // Set up our patterns
            $pattern_keys = array
            (
                1=>new Item('citizen_number','UEE Citizen Record.*<strong class="value">#(\d*)</strong>'),
                2=>new Item('avatar','<div class="thumb">.*<img src="(.*)" />',array(new Postprocess(array($this, 'StrConcat'),array('http://robertsspaceindustries.com','$?')))),
                3=>new Item('moniker','<div class="info">.*<p class="entry">.*<strong class="value">(.*)</strong>'),
                4=>new Item('handle','Handle name</span>\s*<strong class="value">(.*)</strong>',array(new Postprocess('strtolower'))),
                5=>new Item('enlisted','Enlisted</span>\s*<strong class="value">(.*)<',array(new Postprocess('strtotime'))),
                6=>new Item('title_image','<span class="icon">\s*<img src="(.*)".*<span class="value">(.*)</span>',array(new Postprocess(array($this, 'StrConcat'),array('http://robertsspaceindustries.com','$?')))),
                7=>new Item('title'),
                8=>new Item('bio','Bio</span>\s*<div class="value">(.*)</div>',array(new Postprocess('trim'))),
                9=>new Item('website_link','Website</span>\s*<a .* href="(.*)" .*>(.*)</a>'),
                10=>new Item('website_title'),
                11=>new Item('country','Location</span>\s*<strong class="value">([\w\s]*)\,?\s*?([\w\s]*)</',array(new Postprocess('trim'))),
                12=>new Item('region',null,array(new Postprocess('trim'))),
                13=>new Item('fluency','Fluency</span>\s*<strong class="value">(.*)</strong>',array(new Postprocess(array($this, 'ProcessLanguages')))),
            );
            
            // Run the search
            $info = $this->ScrapePage($result, $pattern_keys, '<div id="profile"');
            $info = $info[0];
        }
        
        // Update our data array
        $this->Output->SetData($info);
        
        if($info['citizen_number'] != null)
        {
            // Update the cache
            $Cacher = new RSIAccountCacher($this->Output, $this->query_chain_entry);
            $Cacher->UpdateCache(strtolower($target_id), $this);
        }
        
        Cacher::UpdateScrapeAttempt(strtolower($target_id), TableDirectory::RSIAccountsTable);
    }
    
    protected function ValidateLocalSettings()
    { }
    
    public static function ProcessLanguages($block)
    {
        $tok = strtok($block, ",");
        
        while ($tok !== false) 
        {
            $results[] = trim($tok);
            $tok = strtok(",");
        }
        
        return $results;
    }
}