<?php

namespace thalos_api;

class Item
{
    private $name;
    private $pattern;
    private $postprocesses;
    
    public function __construct($name, $pattern = null, $postprocesses = null)
    {
        $this->name = $name;
        $this->pattern = $pattern;
        $this->postprocesses = $postprocesses;
    }
    
    public function __set($name, $value)
    {
        switch($name)
        {
            case 'name':
                if(gettype($value) == 'string')
                {
                    $this->name = $value;
                }
                else
                {
                    $this->name = null;
                }
                break;
                
            case 'pattern':
                if(gettype($value) == 'string')
                {
                    $this->pattern = $value;
                }
                else
                {
                    $this->pattern = null;
                }
                break;
                
            case 'postprocesses':
                if(gettype($value) == 'array')
                {
                    $new_value = array();
                    foreach($value as $item)
                    {
                        if(gettype($item) == 'PatternMatchPostprocess')
                        {
                            $new_value[] = $item;
                        }
                    }
                    
                    $this->postprocesses = $new_value;
                }
                else
                {
                    $this->postprocesses = null;
                }
                break;
        }
    }
    
    public function __get($name)
    {
        return $this->$name;
    }
}