<?php

namespace thalos_api;

class Template
{
    public function __construct($input_array)
    {
        // Assign our member variables with
        // the input data
        $this->SetAll($input_array);
    }
    
    public function __set($name, $value)
    {
        // Make sure we don't create any new variables
        if(property_exists($this, $name))
        {
            $this->$name = $value;
        }
    }
    
    public function __get($name)
    {
    		if($GLOBALS['TRACE'])echo"accessing this->$name\n";
        return $this->$name;
    }
    
    public function SetAll($input_array)
    {
        if($input_array != null
            && gettype($input_array) == 'array')
        {
            // For each value in our supplied data
            foreach($input_array as $key=>$value)
            {
                // Attempt to assign the matching variable
                $this->__set($key, $value);
            }
        }
    }
    
    public function GetAll()
    {
        // Return an array of all our member variables
        return get_object_vars($this);
    }
}
