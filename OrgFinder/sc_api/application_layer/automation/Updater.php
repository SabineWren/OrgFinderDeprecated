<?php

namespace thalos_api;

use PDO;
use COM;

require_once(__DIR__.'/../Controller.php');
require_once(__DIR__.'/../../database_layer/DBInterface.php');
require_once(__DIR__.'/../query_handlers/scrapers/LoginClient.php');

abstract class Updater
{
    public $_SETTINGS;
    public $_GLOBAL_SETTINGS;
    
    public $DB;
    public $API;
    
    public $nl;
    public $tab;
    
    public $start_time;
    
    public $queue;
    
    public function __construct($settings)
    {
        $this->start_time = microtime(true);
        
        $this->_GLOBAL_SETTINGS = $settings;
        $this->GetSettings();
        
        if($this->_SETTINGS['enabled'])
        {
            set_time_limit($this->_SETTINGS['max_execution_time'] + 10);

            $this->GetFormatting();

            $this->API = new Controller();
            $this->DB = new DBInterface();

            if($this->DB->Connect())
            {
                $this->GetQueue();

                list($entries_attempted, $entries_resolved, $entries_failed)
                        = $this->UpdateQueue();

                echo "ATTEMPTED $this->tab $entries_attempted ENTRIES $this->nl";
                echo "RESOLVED $this->tab $entries_resolved ENTRIES $this->nl";
                echo "FAILED $this->tab $entries_failed ENTRIES $this->nl";
            }

            echo "TIME TAKEN: ".(microtime(true) - $this->start_time)." SECONDS$this->nl";
        }
    }
    
    protected function GetSettings()
    {
        require(__DIR__.'/../../settings.php');
        
        $name = Controller::parse_classname(get_class($this));
        $this->_SETTINGS = $_SETTINGS['cache'][$name['classname']];
    }
    
    public function GetFormatting()
    {
        if (PHP_SAPI == 'cli') 
        {
            $this->nl = "\n";
            $this->tab = "\t";
        } 
        else 
        {
            $this->nl = '<br>';
            $this->tab = '&nbsp;&nbsp;&nbsp;&nbsp;';
        }
    }
    
    protected abstract function GetQueue();
    
    public function UpdateQueue()
    {
        $entries_attempted = 0;
        $entries_resolved = 0;
        $entries_failed = 0;
        
        if(count($this->queue) > 0)
        {
            foreach($this->queue as $target=>$last_scrape_date)
            {
                $this->DoWait();
                
                if(!$this->CheckElapsedTime())
                    break;
                
                $data = $this->RunQuery($target);

                if($data['data'] != null)
                {
                    echo 'Success'."$this->nl";
                    $entries_resolved++;
                }
                else
                {
                    echo 'Failure'."$this->nl";
                    $entries_failed++;
                }
                
                $entries_attempted++;
                
                unset($data);
                unset($query);
                
                sleep($this->_SETTINGS['delay_time']);
            }
        }

        return array($entries_attempted, $entries_resolved, $entries_failed);
    }
    
    protected abstract function RunQuery($target);
    
    protected function DoWait()
    {
        $load = $this->GetServerLoad();

        while($load > $this->_GLOBAL_SETTINGS['max_load']
            && $this->CheckElapsedTime())
        {
            echo "CPU LOAD: $load $this->tab SLEEPING FOR: ".$this->_SETTINGS['sleep_time']."...$this->nl";
            
            sleep($this->_SETTINGS['sleep_time']);
            
            $load = $this->GetServerLoad();
        }
    }
    
    protected function CheckElapsedTime()
    {
        if(microtime(true) - $this->start_time >= 
            $this->_SETTINGS['max_execution_time'])
        {
            return false;
        }
        
        return true;
    }
    
    protected function GetServerLoad()
    {
        if($this->IsWindows())
        {
            if(class_exists('COM'))
            {
                $wmi=new COM('WinMgmts:\\\\.');
                $cpus=$wmi->InstancesOf('Win32_Processor');
                
                $load=0;
                $cpu_count=0;
                
                if(version_compare('4.50.0', PHP_VERSION) == 1)
                {
                    while($cpu = $cpus->Next())
                    {
                        $load += $cpu->LoadPercentage;
                        $cpu_count++;
                    }
                }
                else
                {
                    foreach($cpus as $cpu)
                    {
                        $load += $cpu->LoadPercentage;
                        $cpu_count++;
                    }
                }
                
                return $load;
            }
            
            return false;
        }
        else
        {
            $load = sys_getloadavg();
            
            return $load[0];
        }
    }
    
    protected function IsWindows()
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') 
            return true;
        
        return false;
    }
}