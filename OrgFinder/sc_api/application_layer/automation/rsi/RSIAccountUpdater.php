<?php

namespace thalos_api;

use PDO;

require_once('RSIUpdater.php');

class RSIAccountUpdater extends RSIUpdater
{
    protected function GetQueue()
    {
        $query = $this->DB->db->prepare(
                'SELECT 
                    id,
                    last_scrape_date
                FROM 
                    accounts_rsi
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
        echo 'UPDATING ACCT: '.$target."...$this->tab";
        
        $query=array(
            'system'=>QuerySystems::Accounts,
            'action'=>AccountActions::Full_Profile,
            'api_source'=>APISources::Live,
            'data_source'=>AccountSources::RSI,
            'target_id'=>$target,
            );
        
        $data = $this->API->Query($query);
        
        return $data;
    }
}