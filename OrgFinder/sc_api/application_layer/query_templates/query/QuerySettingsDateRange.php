<?php

namespace thalos_api;

require_once(__DIR__.'/../Template.php');

class QuerySettingsDateRange extends Template
{
    public $start_date;
    public $end_date;
    
    public function __construct($input_array)
    {
        parent::__construct($input_array);
        
        // If no start date provided
        if(!isset($input_array['start_date'])
            || !is_numeric($input_array['start_date']))
        {
            // Set it to a default
            $this->__set('start_date', 0);
        }
        
        // If no end date provided
        if(!isset($input_array['end_date'])
            || !is_numeric($input_array['end_date']))
        {
            // Set it to a default
            $this->__set('end_date', time());
        }
    }
    
    public function __set($name, $value)
    {
        // Check for items that require validation
        switch($name)
        {
            case 'start_date':
                $value = $this->ValidateStartDate($value);
                break;
            case 'end_date':
                $value = $this->ValidateEndDate($value);
                break;
            default:
                break;
        }
        
        parent::__set($name, $value);
    }
    
    private function ValidateStartDate($value)
    {
        // $value >= 0
        $value = max(0, $value);
        
        // $value <= current time
        $value = min(time(), $value);
        
        return $value;
    }
    
    private function ValidateEndDate($value)
    {
        // $value >= the start date
        $value = max($this->start_date, $value);
        
        // $value <= current time
        $value = min(time(), $value);
        
        return $value;
    }
}