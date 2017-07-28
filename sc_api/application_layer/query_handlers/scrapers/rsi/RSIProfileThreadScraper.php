<?php

namespace thalos_api;

require_once(__DIR__.'/../Scraper.php');

class RSIProfileThreadScraper extends Scraper
{
    public function PerformQuery()
    {
        $user_id = $this->GetUserID();
        
        if($user_id == -1)
            return null;
        
        $base_url = 'http://forums.robertsspaceindustries.com/profile/discussions/'
                .$user_id.'/'
                .$this->Output->request_stats->resolved_query->target_id.
                '/p';
        
        $end_page = $this->Output->request_stats->resolved_query->pagination->end_page;
        
        $ThreadScraper = new RSIThreadScraper($this->Output);
        $info = $ThreadScraper->AgnosticThreadScrape($base_url, $end_page);

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