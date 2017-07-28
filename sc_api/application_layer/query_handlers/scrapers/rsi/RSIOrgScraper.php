<?php

namespace thalos_api;

require_once(__DIR__.'/../Scraper.php');

class RSIOrgScraper extends Scraper
{
    public function PerformQuery()
    {
   if($GLOBALS['TRACE'])echo "RSIOrgScraper.php PerformsQuery()\n";
        // Get a shorter reference to our target
        $target_id = $this->Output->request_stats->resolved_query->target_id;
        
        // Return failure if the target isn't set
        if(!isset($target_id)
            || $target_id == null)
        {
            return false;
        }
        
        // Get the target page
        $result = $this->GetPage('http://robertsspaceindustries.com/orgs/' . urlencode($target_id));
        
        // If we have page data
        if($result)
        {
            // Set up our patterns
            $pattern_keys = array
            (
                1=>new Item('title','<h1>(.*)\s?/\s?<span class="symbol">'),
                2=>new Item('member_count','<span class="count">(.*) members?</span>'),
                3=>new Item('banner','<div class="banner"><img src="(.*)"',array(new Postprocess(array($this, 'StrConcat'),array('http://robertsspaceindustries.com','$?')))),
                4=>new Item('logo','<div class="logo [\s\w\d]*">\s*<img src="(.*)"',array(new Postprocess(array($this, 'StrConcat'),array('http://robertsspaceindustries.com','$?')))),
                5=>new Item('archetype','<li class="model">(.*)<'),
                6=>new Item('commitment','<li class="commitment">(.*)<'),
                7=>new Item('roleplay','<li class="roleplay">(.*)<',array(new Postprocess(array($this, 'ResolveRoleplay')))),
                8=>new Item('primary_image','<li class="primary tooltip-wrap.*">.*<img src="(.*)".*<div class="content">(.*)<',
                        array(new Postprocess(array($this, 'StrConcat'),array('http://robertsspaceindustries.com','$?')))),
                9=>new Item('primary_focus'),
                10=>new Item('secondary_image','<li class="secondary tooltip-wrap.*">.*<img src="(.*)".*<div class="content">(.*)<',
                        array(new Postprocess(array($this, 'StrConcat'),array('http://robertsspaceindustries.com','$?')))),
                11=>new Item('secondary_focus'),
                12=>new Item('headline','<div class="body markitup-text">(.*)</div>',array(new Postprocess('trim'))),
                13=>new Item('history','<h2.*>History</h2>.*<div class="markitup-text">(.*)</div>',array(new Postprocess('trim'))),
                14=>new Item('manifesto','<h2.*>Manifesto</h2>.*<div class="markitup-text">(.*)</div>',array(new Postprocess('trim'))),
                15=>new Item('charter','<h2.*>Charter</h2>.*<div class="markitup-text">(.*)</div>',array(new Postprocess('trim'))),
                16=>new Item('recruiting','<span class="holobtn-top.*">(Join us now!)</span>',array(new Postprocess(array($this, 'ResolveRecruiting')))),
                17=>new Item('cover_image','<div class=".*cover">.*<img src="(.*)"',array(new Postprocess(array($this, 'StrConcat'),array('http://robertsspaceindustries.com','$?')))),
                18=>new Item('cover_video','<iframe.*src="//(.*)"'),
            );
            
            $data = $this->ScrapePage($result, $pattern_keys, '<div id="contentbody"');
            // Run the search
            $info = $data[0];
        }
        
        if($info['title'] != null)
        {
            $info['sid'] = strtolower($target_id);
            
            // Update our data array
            @$this->Output->SetData($info);
        }
    }
    
    protected function ValidateLocalSettings()
    { }
    
    public static function ResolveRoleplay($input)
    {
        // Element is only present on the page if the value is "Yes"
        if($input != null)
        {
            return 'Yes';
        }
        
        return 'No';
    }
    
    public static function ResolveRecruiting($input)
    {
        // Element is only present on the page if the value is "Yes"
        if($input != null)
        {
            return 'Yes';
        }
        
        return 'No';
    }
}
