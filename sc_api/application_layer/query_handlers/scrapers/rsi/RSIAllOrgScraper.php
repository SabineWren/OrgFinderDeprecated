<?php

namespace thalos_api;

require_once(__DIR__.'/../Scraper.php');

class RSIAllOrgScraper extends Scraper
{
    public function PerformQuery()
    {
        $base_url = 'http://robertsspaceindustries.com/community/orgs/listing?'.
                'sort='.$this->Output->request_stats->resolved_query->pagination->sort_method.
                '_'.$this->Output->request_stats->resolved_query->pagination->sort_direction.
                '&pagesize='.$this->Output->request_stats->resolved_query->pagination->items_per_page.
                '&page=';
        
        $current_page = $this->Output->request_stats->resolved_query->pagination->start_page;
        $end_page = $this->Output->request_stats->resolved_query->pagination->end_page;
        
        $info = array();
        
        if($GLOBALS['TRACE'])echo "getting pages...\n";
        while($current_page <= $end_page)
        {
            $result = $this->GetPage($base_url . $current_page);
            
            if($result == false)
            {
                break;
            }

            $pattern_keys = array
            (
                1=>new Item('logo','<span class="thumb">.*<img src="(.*)"',array(new Postprocess(array($this, 'StrConcat'),array('http://robertsspaceindustries.com','$?')))),
                2=>new Item('title','<span class="identity">.*<h3 class="trans-03s name">(.*)</h3>',array(new Postprocess('trim'))),
                3=>new Item('sid','<span class="symbol">(.*)</span>',array(new Postprocess('strtolower'))),
                4=>new Item('archetype','<span class="label">Archetype:.*</span><span class="value.*">(.*)</span>'),
                5=>new Item('lang','<span class="label">Lang:.*</span><span class="value.*">(.*)</span>'),
                6=>new Item('commitment','<span class="label">Commitment:.*</span><span class="value.*">(.*)</span>'),
                7=>new Item('recruiting','<span class="label">Recruiting:.*</span><span class="value.*">(.*)</span>'),
                8=>new Item('roleplay','<span class="label">Role play:.*</span><span class="value.*">(.*)</span>'),
                9=>new Item('member_count','<span class="label">Members:.*</span><span class="value.*">(.*)</span>'),
            );

            $data = $this->ScrapePage($result, $pattern_keys, '<div class="org-cell');
            
            if($data == false)
            {
                break;
            }

            $info = array_merge($info, $data);
            
            $current_page++;
        }
        if($GLOBALS['TRACE'])echo "got all pages\n";

        $this->Output->SetData($info);
        
        if($GLOBALS['TRACE'])echo "done setting data\n";
        
        if($this->Output->data != null)
        {
            foreach($this->Output->data as $entry)
            {
                $input = new Output(array());
                $input->data = $entry;
                $input->request_stats->resolved_query->target_id = $entry['sid'];
            }
        }
    }
    
    protected function ValidateLocalSettings()
    { 
    //var_dump($this->Output->request_stats->resolved_query->pagination);
        // Cap the page size or else CIG rolls it back to 32
        $this->Output->request_stats->resolved_query->pagination->page_item_limit
            = min($this->Output->request_stats->resolved_query->pagination->items_per_page, 255);
        
        // Validate the sort method
        switch($this->Output->request_stats->resolved_query->pagination->sort_method)
        {
            case 'created':
            case 'size':
            case 'name':
            case 'active':
                break;
            default:
                $this->Output->request_stats->resolved_query->pagination->sort_method = 
                    'name';
                break;
        }
        
        if($this->Output->request_stats->resolved_query->pagination->sort_direction == 'descending')
        {
            $this->Output->request_stats->resolved_query->pagination->sort_direction = 'desc';
        }
        
        if($this->Output->request_stats->resolved_query->pagination->sort_direction == 'ascending')
        {
            $this->Output->request_stats->resolved_query->pagination->sort_direction = 'asc';
        }
        
        // Validate the sort direction
        switch($this->Output->request_stats->resolved_query->pagination->sort_direction)
        {
            case 'desc':
            case 'asc':
                break;
            default:
                $this->Output->request_stats->resolved_query->pagination->sort_direction = 
                    'asc';
                break;
        }
    }
}
