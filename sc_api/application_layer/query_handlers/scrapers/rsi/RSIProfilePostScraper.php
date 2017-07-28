<?php

namespace thalos_api;

require_once(__DIR__.'/../Scraper.php');

class RSIProfilePostScraper extends Scraper
{
    public function PerformQuery()
    {
        $user_id = $this->GetUserID();
        
        if($user_id == -1)
            return null;
        
        $base_url = 'http://forums.robertsspaceindustries.com/profile/comments/'
                .$user_id.'/'
                .$this->Output->request_stats->resolved_query->target_id.
                '/p';
        
        $current_page = $this->Output->request_stats->resolved_query->pagination->start_page;
        $end_page = $this->Output->request_stats->resolved_query->pagination->end_page;
        
        $info = array();
        
        while($current_page <= $end_page)
        {
            $result = $this->GetPage($base_url . $current_page);
            
            if($result == false)
            {
                break;
            }
            
            $block_start = '<li id="Comment';
            
            $pattern_keys = array
            (
                1=>new Item('post_id','(\d+)"'),
                2=>new Item('post_text','<div class="Message">(.*)</div>'),
                3=>new Item('thread_title','in <b><a href="/discussion/comment/\d+/#Comment_\d+">(.*)</a>'),
                4=>new Item('citizen_number','by <a href="/profile/(\d+)/(.*)"'),
                5=>new Item('handle',null,array(new Postprocess('strtolower'))),
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

        $this->Output->SetData($info);
    }
    
    protected function ValidateLocalSettings()
    { }
    
    protected function GetUserID()
    {
        $id = -1;
        
        $base_url = 'http://forums.robertsspaceindustries.com/profile/discussions/'.$this->Output->request_stats->resolved_query->target_id;
        
        $result = $this->GetPage($base_url, true);
            
        if($result == false)
        {
            return $id;
        }

        $block_start = '<span class="CrumbLabel">';

        $pattern_keys = array
        (
            1=>new Item('id','<a href="/profile/(\d+)/'),
        );

        $data = $this->ScrapePage($result, $pattern_keys, $block_start);

        if($data == false
            || $data == null)
        {
            return $id;
        }
        
        $id = $data['id'];
        
        return $id;
    }
}