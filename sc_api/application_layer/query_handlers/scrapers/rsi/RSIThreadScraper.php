<?php

namespace thalos_api;

require_once(__DIR__.'/../Scraper.php');

class RSIThreadScraper extends Scraper
{
    public function PerformQuery()
    {
        $base_url = 'http://forums.robertsspaceindustries.com/categories/'.$this->Output->request_stats->resolved_query->target_id.
                '/p';
        
        $calced_end = $this->GetMaxPages($base_url.'1');
        $end_page = min($this->Output->request_stats->resolved_query->pagination->end_page, $calced_end);
        
        $info = $this->AgnosticThreadScrape($base_url, $end_page);

        $this->Output->SetData($info);
    }
    
    protected function ValidateLocalSettings()
    { }
    
    public function AgnosticThreadScrape($base_url, $end_page)
    {
        $current_page = $this->Output->request_stats->resolved_query->pagination->start_page;
        
        $info = array();
        
        while($current_page <= $end_page)
        {
            $result = $this->GetPage($base_url . $current_page, true);
            
            if($result == false)
            {
                break;
            }
            
            $block_start = '<li id="Discussion';
            
            $pattern_keys = array
            (
                1=>new Item('thread_id','_(\d*)"'),
                2=>new Item('thread_title','<a .* class="Title">(.*)</a>'),
                3=>new Item('thread_replies','<span class="CommentCount.*">(\d*) comments'),
                4=>new Item('author_id','<div class="started-by.*">\s*<a href="/profile/(\d*)/(.*)".*><img src="(.*)"'),
                5=>new Item('author_handle'),
                6=>new Item('author_avatar'),
                7=>new Item('thread_start_time','<p class="started-date"><time .* datetime="(.*)">',array(new Postprocess('strtotime'))),
                8=>new Item('thread_views','<div class="views">\s*<p>(\d*)<'),
                9=>new Item('newest_poster_id','<div class="most-recent.*">\s*<a href="/profile/(\d*)/(.*)".*><img src="(.*)".*<time .* datetime="(.*)">'),
                10=>new Item('newest_poster_handle'),
                11=>new Item('newest_poster_avatar'),
                12=>new Item('newest_reply_time',null,array(new Postprocess('strtotime'))),
            );

            $data = $this->ScrapePage($result, $pattern_keys, $block_start);
            
            if($data == false
                || $data == null)
            {
                break;
            }
            
            foreach($data as $key=>$entry)
            {
                $data[$key]['original_poster'] = [
                    'citizen_number' => $data[$key]['author_id'],
                    'handle' => $data[$key]['author_handle'],
                    'avatar' => $data[$key]['author_avatar'],
                    'post_time' => $data[$key]['thread_start_time'],
                    ];

                unset($data[$key]['author_id']);
                unset($data[$key]['author_handle']);
                unset($data[$key]['author_avatar']);
                unset($data[$key]['thread_start_time']);

                $data[$key]['recent_poster'] = [
                    'citizen_number' => $data[$key]['newest_poster_id'],
                    'handle' => $data[$key]['newest_poster_handle'],
                    'avatar' => $data[$key]['newest_poster_avatar'],
                    'post_time' => $data[$key]['newest_reply_time'],
                    ];

                unset($data[$key]['newest_poster_id']);
                unset($data[$key]['newest_poster_handle']);
                unset($data[$key]['newest_poster_avatar']);
                unset($data[$key]['newest_reply_time']);
            }

            $info = array_merge($info, $data);
            
            $current_page++;
        }
        
        return $info;
    }
    
    protected function GetMaxPages($url)
    {
        $result = $this->GetPage($url, true);
        
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