<?php

namespace thalos_api;

require_once(__DIR__.'/../Template.php');
require_once('QuerySettingsPagination.php');
require_once('QuerySettingsDateRange.php');

class QuerySettings extends Template
{
    public $system;
    public $action;
    public $data_source;
    public $target_id;
    public $api_source;
    public $pagination;
    public $date_range;
    public $expedite = false;
    
    public function __construct($input_array)
    {
        // Initialize a Pagination object with the input data
        $this->pagination = new QuerySettingsPagination($input_array);
        
        // Initialize a Date object with the input data 
        $this->date_range = new QuerySettingsDateRange($input_array);
        
        parent::__construct($input_array);
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