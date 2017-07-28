<?php

namespace thalos_api;
use PDO;

require_once(__DIR__.'/../../database_layer/DBInterface.php');

class APIStatistics
{
    private $DB;
    
    public $unique_org_count;
    public $total_org_count;
    public $unique_acct_count;
    public $total_acct_count;
    public $total_unique_records;
    public $account_refresh_time;
    public $org_refresh_time;
    public $total_refresh_time;
    public $current_refresh_cycle;
    public $total_users;
    public $total_hits;
    
    public function __construct()
    {
        $this->DB = new DBInterface();
        $this->DB->Connect();

        $this->unique_org_count = $this->GetSingleCount('SELECT COUNT(DISTINCT id) FROM organizations_rsi');
        $this->total_org_count = $this->GetSingleCount('SELECT COUNT(sid) FROM organizations_rsi_info') + $this->unique_org_count;

        $this->unique_acct_count = $this->GetSingleCount('SELECT COUNT(DISTINCT id) FROM accounts_rsi');
        $this->total_acct_count = $this->GetSingleCount('SELECT COUNT(handle) FROM accounts_rsi_info') + $this->unique_acct_count;

        $this->total_unique_records = $this->unique_acct_count + $this->unique_org_count;

        $account_refresh_range = $this->GetRefreshRange('accounts_rsi');
        //$this->account_refresh_time = $this->CalculateAvgRefreshTime($account_refresh_range);

        $org_refresh_range = $this->GetRefreshRange('organizations_rsi');
        //$this->org_refresh_time = $this->CalculateAvgRefreshTime($org_refresh_range);

//        $this->total_refresh_time = $this->CalculateAvgRefreshTime(array(
//            max($org_refresh_range[0], $account_refresh_range[0]),
//            min($org_refresh_range[1], $account_refresh_range[1]),
//            $org_refresh_range[2] + $account_refresh_range[2]));
        
        $this->current_refresh_cycle = 
            max($org_refresh_range[0], $account_refresh_range[0]) -
            min($org_refresh_range[1], $account_refresh_range[1]);

        $query = $this->DB->db->prepare('SELECT COUNT(ip), SUM(times_seen) FROM api_users');
        $query->execute();
        list($this->total_users, $this->total_hits) = $query->fetch(PDO::FETCH_NUM);
    }
    
    public function GetSingleCount($query_string)
    {
        $query = $this->DB->db->prepare($query_string);
        $query->execute();
        $unique_org_count = $query->fetch();
        return $unique_org_count[0];
    }

    public function GetRefreshRange($table)
    {
        $margin = time() - (60*60*24*90);
        
        $query = $this->DB->db->prepare('
            SELECT 
                MAX(last_scrape_date), 
                MIN(last_scrape_date) 
            FROM 
                '.$table.' 
            WHERE 
                last_scrape_date > "0"
                AND last_scrape_success >= "'.$margin.'"');
        
        $query->execute();
        $result = $query->fetch();
        
        $result[0] = intval($result[0]);
        $result[1] = intval($result[1]);
        
        if($result[0] == null)
            $result[0] = time();
        
        if($result[1] == null)
            $result[1] = time();
        
        return $result;
    }

    public function CalculateAvgRefreshTime($range)
    {
        // Avg seconds per entry = 1 / ((max - min) / (count - 1))
        return (1 / (($range[0] - $range[1]) / ($range[2] - 1)));
    }
}