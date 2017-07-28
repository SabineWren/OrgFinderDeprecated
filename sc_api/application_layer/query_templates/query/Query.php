<?php

namespace thalos_api;

require_once(__DIR__.'/../Template.php');
require_once('QuerySettings.php');
require_once('QueryPerformance.php');

class Query extends Template
{
    public $request_ip;
    public $timestamp;
    public $items_returned;
    public $query_status;
    public $input_query;
    public $resolved_query;
    public $performance;
    
    public function __construct($input_array)
    {
        // Try to get the user's IP
        $this->request_ip = @$_SERVER['REMOTE_ADDR'];
        
        // Get the current time
        $this->timestamp = microtime(true);
        $this->items_returned = 0;
        $this->query_status = 'unknown';
        
        $this->performance = new QueryPerformance();
        
        // Initialize new QuerySettings objects with the input data
        $this->input_query = new QuerySettings($input_array);
        $this->resolved_query = new QuerySettings($input_array);
        
        parent::__construct($input_array);
    }
}