<?php

namespace thalos_api;

class Postprocess
{
    private $callback;
    private $arguments;
    
    public function __construct($callback, $arguments = null)
    {
        $this->callback = $callback;
        $this->__set('arguments',$arguments);
    }
    
    public function __set($name, $value)
    {
        switch($name)
        {
            case 'callback':
                if(gettype($value) == 'string'
                    || gettype($value) == 'array')
                {
                    $this->callback = $value;
                }
                else
                {
                    $this->callback = null;
                }
                break;
                
            case 'arguments':
                if(gettype($value) == 'array')
                {
                    $this->arguments = $value;
                }
                else
                {
                    $this->arguments = array('$?');
                }
                break;
        }
    }
    
    public function __get($name)
    {
        return $this->$name;
    }
}