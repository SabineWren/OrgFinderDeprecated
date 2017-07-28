<?php

namespace thalos_api;

require_once('rsi/RSIForumUpdater.php');
require_once('rsi/RSIAccountUpdater.php');
require_once('rsi/RSIOrgUpdater.php');

new Scheduler();

class Scheduler
{
    protected $_SETTINGS;
    
    public function __construct()
    {
        $this->GetSettings();
        
        if($this->_SETTINGS['enabled'])
        {
            $RSIOrgUpdater = new RSIOrgUpdater($this->_SETTINGS);
            echo $RSIOrgUpdater->nl . $RSIOrgUpdater->nl;

            $RSIAccountUpdater = new RSIAccountUpdater($this->_SETTINGS);
            echo $RSIAccountUpdater->nl . $RSIAccountUpdater->nl;
        }
    }
    
    protected function GetSettings()
    {
        require(__DIR__.'/../../settings.php');
        
        $name = Controller::parse_classname(get_class($this));
        $this->_SETTINGS = $_SETTINGS['cache'][$name['classname']];
    }
}

