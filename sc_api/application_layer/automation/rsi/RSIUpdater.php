<?php

namespace thalos_api;

require_once(__DIR__.'/../Updater.php');

abstract class RSIUpdater extends Updater
{
    public function __construct($settings)
    {
        parent::__construct($settings);
        
        $LoginClient = new LoginClient();
        $LoginClient->LoginRSI();
    }
}