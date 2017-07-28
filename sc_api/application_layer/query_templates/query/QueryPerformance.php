<?php

namespace thalos_api;

require_once(__DIR__.'/../Template.php');
require_once('QueryChain.php');

class QueryPerformance extends Template
{
    public $network_io_time;
    public $processing_time;
    public $total_time;
    public $handler_chain;
    
    public function __construct()
    {
        $this->network_io_time = 0;
        $this->processing_time = 0;
        $this->total_time = 0;
        $this->handler_chain = null;
    }
    
    public function __set($name, $value)
    {
        if(gettype($value) == 'string')
        {
            $value = strtolower($value);
        }
        
        parent::__set($name, $value);
    }
}