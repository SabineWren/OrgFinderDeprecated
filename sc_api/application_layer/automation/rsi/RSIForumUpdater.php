<?php

namespace thalos_api;

use PDO;

require_once('RSIUpdater.php');

class RSIForumUpdater extends RSIUpdater
{
    protected function GetQueue()
    {
        return null;
    }
    
    protected function RunQuery($target)
    {
        return null;
    }
}