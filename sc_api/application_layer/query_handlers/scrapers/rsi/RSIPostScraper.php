<?php

namespace thalos_api;

require_once(__DIR__.'/../Scraper.php');

class RSIPostScraper extends Scraper
{
    public function PerformQuery()
    {
        $base_url = 'http://forums.robertsspaceindustries.com/discussion/'.$this->Output->request_stats->resolved_query->target_id.
                '/a/p';
        
        $calced_end = $this->GetMaxPages($base_url.'1');
        $end_page = min($this->Output->request_stats->resolved_query->pagination->end_page, $calced_end);
        
        $info = $this->AgnosticPostScrape($base_url, $end_page);

        $this->Output->SetData($info);
            
        if($this->Output->data != null)
        {
            foreach($this->Output->data as $entry)
            {
                $input = new Output($this->Output->GetAll());
                $input->data = $entry;
                $input->request_stats->resolved_query->target_id = $entry['handle'];
                if(isset($entry['handle']))   
                {
                    $Cacher = new RSIAccountCacher($input, $this->query_chain_entry);
                    $Cacher->UpdateCache($entry['handle'], $this);
                }
            }
        }
    }
    
    protected function ValidateLocalSettings()
    { }
    
    public function AgnosticPostScrape($base_url, $end_page)
    {
        $current_page = $this->Output->request_stats->resolved_query->pagination->start_page;
        
        $info = array();
        
        while($current_page <= $end_page)
        {
            $result = $this->GetPage($base_url . $current_page);
            
            if($result == false)
            {
                break;
            }
            
            $block_start = '<div class="Comment';
            
            $pattern_keys = array
            (
                1=>new Item('citizen_number','<a href="/profile/(.*)/(.*)"'),
                2=>new Item('handle',null,array(new Postprocess('strtolower'))),
                3=>new Item('avatar','<img src="(.*)".*class="ProfilePhoto'),
                4=>new Item('title_image','<div class="AuthorIcon"><a><img src="(.*)" title="(.*)"'),
                5=>new Item('title'),
                6=>new Item('post_count','Posts: (.*)<'),
                7=>new Item('moniker','class="FullName.*>(.*)</a>'),
                8=>new Item('post_time','Posted:.*datetime="(.*)"',array(new Postprocess('strtotime'))),
                9=>new Item('last_edit_time','Edited:.*<time.*datetime="(.*)">',array(new Postprocess('strtotime'))),
                10=>new Item('post_text','<div class=".*article.*">(.*)</div>',array(new Postprocess('trim'))),
                11=>new Item('post_id','<a href=".*/discussion.*/(\d*)/.*".*Permalink</a>'),
                12=>new Item('permalink','<a href=".*discussion/(.*)".*>Permalink</a>',array(new Postprocess(array($this, 'StrConcat'),array('https://forums.robertsspaceindustries.com/discussion/','$?')))),
                13=>new Item('signature','<div class=".*Signature.*UserSignature.*">(.*)<\!',array(new Postprocess('trim'))),
            );

            $data = $this->ScrapePage($result, $pattern_keys, $block_start);
            
            if($data == false
                || $data == null)
            {
                break;
            }

            $info = array_merge($info, $data);
            
            $current_page++;
        }
        
        return $info;
    }
    
    protected function GetMaxPages($url)
    {
        $result = $this->GetPage($url);
        
        $block_start = '/discussion/'.$this->Output->request_stats->resolved_query->target_id;
            
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