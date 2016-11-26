<?php

namespace thalos_api;

require_once('Template.php');
require_once('query/Query.php');

class Output extends Template
{
    public $data;
    public $request_stats;
    
    public function __construct($input_array)
    {
        // Initialize a new Query template
        $this->request_stats = new Query($input_array);
        
        parent::__construct($input_array);
    }
    
    public function SetData($input_data)
    {
        // If our input data is valid
        if($input_data != null
            && gettype($input_data) == 'array')
        {
            // If our current data is set
            if($this->data == null)
            {
                // Simply assign the new, filtered data
                $this->data = array_filter($input_data);
                
                return true;
            }
            else
            {
                // Filter our current data and the new data to remove any null values
                // so that the latter array doesn't wipe out the former.
                // Then combine the arrays
                $this->data = array_filter($this->data) + array_filter($input_data);
                
                return true;
            }
        }
        
        return false;
    }
}