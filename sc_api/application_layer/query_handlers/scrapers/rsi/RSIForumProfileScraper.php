<?php

namespace thalos_api;

require_once(__DIR__.'/../Scraper.php');

class RSIForumProfileScraper extends Scraper
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
        $result = $this->GetPage('http://forums.robertsspaceindustries.com/profile/' . urlencode($target_id));
        
        // If we have page data
        if($result)
        {
            $info['handle'] = strtolower($target_id);
            
            // Set up our patterns
            $pattern_keys = array
            (
                1=>new Item('status','<dl class="About">.*<dd class="Value"><span class="Tag Tag-Banned">(Banne.)', array(new Postprocess(array($this, 'ResolveStatus')))),
                2=>new Item('discussion_count','Discussions.*<span.*class="Aside">.*<span class="Count">(.*)</span>.*Comments'),
                3=>new Item('post_count','Comments.*<span.*class="Count">(.*)</span>'),
                4=>new Item('last_forum_visit','<dd class="LastActive"><time title=".*" datetime="(.*)"',array(new Postprocess('strtotime'))),
                5=>new Item('forum_roles','<dd class="Roles">(.*)</dd>',array(new Postprocess('explode', array(', ','$?')))),
            );
            
            // Run the search
            $data = $this->ScrapePage($result, $pattern_keys, '<div id="Content">');
            $info = @array_merge($info, $data[0]);
        }
        
        if($info['last_forum_visit'] != null)
        {
            // Update our data array
            $this->Output->SetData($info);
            
            // Update the cache
            $Cacher = new RSIAccountCacher($this->Output, $this->query_chain_entry);
            $Cacher->UpdateCache($target_id, $this);
        }
        
        Cacher::UpdateScrapeAttempt($target_id, TableDirectory::RSIAccountsTable);
    }
    
    protected function ValidateLocalSettings()
    { }
    
    public static function ResolveStatus($status)
    {
        // Element is only present on the page if the value is "banned"
        if($status != null)
        {
            return 'banned';
        }
        
        return 'active';
    }
}