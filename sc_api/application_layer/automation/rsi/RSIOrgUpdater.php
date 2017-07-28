<?php

namespace thalos_api;

use PDO;

require_once('RSIUpdater.php');

class RSIOrgUpdater extends RSIUpdater
{
    protected function GetQueue()
    {
        $this->GetNewestOrgs();
        
        $query = $this->DB->db->prepare(
            'SELECT 
                id,
                last_scrape_date
            FROM 
                organizations_rsi
            WHERE 
                1
            ORDER BY 
                last_scrape_date ASC 
            LIMIT 400');

        $this->queue = array();
        if($query->execute())
        {
            $result = $query->fetchAll(PDO::FETCH_ASSOC);

            foreach($result as $row)
            {
                $this->queue[$row['id']] = $row['last_scrape_date'];
            }
        }
    }
    
    protected function RunQuery($target)
    {
        echo 'UPDATING ORG: '.$target."...$this->tab";
        
        $query=array(
            'system'=>QuerySystems::Organizations,
            'action'=>OrganizationActions::Single_Organization,
            'api_source'=>APISources::Live,
            'data_source'=>OrganizationSources::RSI,
            'target_id'=>$target,
            );
        
        $data = $this->API->Query($query);
        
        if($data['data'] != null)
            $this->DoMembers($target);
        
        return $data;
    }
    
    protected function GetNewestOrgs()
    {
        echo 'ADDING NEW ORGS...'.$this->nl;
        
        $curr_page = 1;
        $items_found = 0;
        
        $query=array(
            'system'=>QuerySystems::Organizations,
            'action'=>OrganizationActions::All_Organizations,
            'api_source'=>APISources::Cache,
            'data_source'=>OrganizationSources::RSI,
            'sort_method'=>'created',
            'sort_direction'=>'desc',
            );

        $existing_data  = $this->API->Query($query);
        
        $existing_orgs = array();
        if($existing_data['data'] != null)
        {
            foreach($existing_data['data'] as $item)
            {
                $existing_orgs[] = $item['sid'];
            }
        }
        
        while($this->CheckElapsedTime())
        {
            $query=array(
                'system'=>QuerySystems::Organizations,
                'action'=>OrganizationActions::All_Organizations,
                'api_source'=>APISources::Live,
                'data_source'=>OrganizationSources::RSI,
                'items_per_page'=>255,
                'start_page'=>$curr_page,
                'end_page'=>$curr_page,
                'sort_method'=>'created',
                'sort_direction'=>'desc',
                );
            
            $data = $this->API->Query($query);
            
            if(isset($data['data']))
            {
                foreach($data['data'] as $item)
                {
                    if(in_array($item['sid'], $existing_orgs))
                    {
                        echo $this->tab.'ADDED '.$items_found.' NEW ORGS.'.$this->nl;
                        return;
                    }
                    
                    $items_found++;
                }

                $curr_page++;
            }
            else
            {
                break;
            }
            
            unset($data);
        }
        
        echo $this->tab.'ADDED '.$items_found.' NEW ORGS.'.$this->nl;
    }
    
    protected function DoMembers($target)
    {   
        $query=array(
            'system'=>QuerySystems::Organizations,
            'action'=>OrganizationActions::Organization_Members,
            'api_source'=>APISources::Live,
            'data_source'=>OrganizationSources::RSI,
            'target_id'=>$target,
            'start_page'=>1,
            'end_page'=>9999,
            );
        
        $data = $this->API->Query($query);

        echo 'UPDATING '.$data['request_stats']->items_returned.' MEMBERS'."$this->tab";
                
        unset($data);
        unset($query);
    }
}