<?php

namespace thalos_api;

require_once(__DIR__.'/../Scraper.php');

class RSIForumScraper extends Scraper
{
    public function PerformQuery()
    {
        $base_url = 'https://forums.robertsspaceindustries.com/';
        
        $result = $this->GetPage($base_url);
            
        if($result == false)
        {
            return;
        }

        $block_start = '<td class="CategoryName depth2';

        $pattern_keys = array
        (
            1=>new Item('forum_id','<a href="//forums.robertsspaceindustries.com:443/categories/(\S*)">(.*)</a>'),
            2=>new Item('forum_title'),
            3=>new Item('forum_description','<div class="CategoryDescription">\s*?(.*)\s*?</div>'),
            4=>new Item('forum_discussion_count','<span title="([\d,]*) discussions',array(new Postprocess('str_replace', array(',','','$?')))),
            5=>new Item('forum_post_count', '<span title="([\d,]*) comments',array(new Postprocess('str_replace', array(',','','$?')))),
            6=>new Item('forum_url','<a href="//(forums.robertsspaceindustries.com:443/categories/\S*)">',array(
                        new Postprocess(array($this, 'StrConcat'),array('http://','$?')),
                        new Postprocess('str_replace', array(':443','','$?')))),
            7=>new Item('forum_rss','<a href="/categories/(.*.rss)"',array(new Postprocess(array($this, 'StrConcat'),array('http://forums.robertsspaceindustries.com/categories/','$?')))),
        );

        $data = $this->ScrapePage($result, $pattern_keys, $block_start);

        $this->Output->SetData($data);
    }
    
    protected function ValidateLocalSettings()
    { }
    
    protected function GetMaxPages($url)
    {
        $result = $this->GetPage($url);
        
        $block_start = '/categories/'.$this->Output->request_stats->resolved_query->target_id;
            
        $pattern_keys = array
        (
            1=>new Item('pages','/p\d*">(\d*)</a>'),
        );

        $data = $this->ScrapePage($result, $pattern_keys, $block_start);
        
        $max = 1;
        if($data != null)
        {
            foreach($data as $item)
            {
                if($item['pages'] > $max)
                    $max = $item['pages'];
            }
        }
        
        return $max;
    }
}