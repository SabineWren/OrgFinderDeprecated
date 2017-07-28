<?php

namespace thalos_api;

require_once(__DIR__.'/../Template.php');

class QuerySettingsPagination extends Template
{
    public $start_page;
    public $end_page;
    public $items_per_page;
    public $sort_method;
    public $sort_direction;
    
    public function __construct($input_array)
    {
        parent::__construct($input_array);
        
        // If no start page provided
        if(!isset($input_array['start_page'])
            || !is_numeric($input_array['start_page']))
        {
            // Set it to a default
            $this->__set('start_page', 1);
        }
        
        // If no end page provided
        if(!isset($input_array['end_page'])
            || !is_numeric($input_array['end_page']))
        {
            // Set it to a default
            $this->__set('end_page', 1);
        }
        
        // If no page limit provided
        if(!isset($input_array['items_per_page'])
            || !is_numeric($input_array['items_per_page']))
        {
            // Set it to a default
            $this->__set('items_per_page', 1);
        }
        
        // If no page limit provided
        if(!isset($input_array['sort_method'])
            || !is_string($input_array['sort_method']))
        {
            // Set it to a default
            $this->__set('sort_method', 'alphabetic');
        }
        
        // If no page limit provided
        if(!isset($input_array['sort_direction'])
            || !is_string($input_array['sort_direction']))
        {
            // Set it to a default
            $this->__set('sort_direction', 'ascending');
        }
    }
    
    public function __set($name, $value)
    {
        if(gettype($value) == 'string')
        {
            $value = strtolower($value);
        }
        
        // Check for items that require validation
        switch($name)
        {
            case 'start_page':
                $$value = $this->ValidateStartPage($value);
                break;
            case 'end_page':
                $$value = $this->ValidateEndPage($value);
                break;
            case 'items_per_page':
                $$value = $this->ValidateLimit($value);
                break;
            default:
                break;
        }
        
        parent::__set($name, $value);
    }
    
    private function ValidateStartPage($value)
    {
        // $value >= 1
        $value = max(1, $value);
        
        return $value;
    }
    
    private function ValidateEndPage($value)
    {
        // $value >= start page
        $value = max($this->start_page, $value);
        
        return $value;
    }
    
    private function ValidateLimit($value)
    {
        // $value >= 1
        $value = max(1, $value);
        
        return $value;
    }
}