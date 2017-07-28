<?php

$_SETTINGS = array(
    
    // Database connection settings for API cache
    'database' => array(
        'host' => 'localhost',
        'database' => '',
        'username' => '',
        'password' => '',
        ),
    
    'cache' => array(
        // Global updater settings
        'Scheduler' => array(
            'enabled' => true,          // Set to false to disable all updaters
            'max_load' => 50,           // Max CPU load of server before triggering sleep_time
        ),
        
        // Updater names here must be identical to the class name of
        // the updater it controls
        'RSIAccountUpdater' => array(
            'enabled' => true,          // Set to false to disable this updater
            'max_execution_time' => 30, // Max seconds this updater can run (approximately)
            'sleep_time' => 5,          // Seconds to sleep while waiting for server load to lower
            'delay_time' => 2,          // Seconds to sleep between processing each entry in the queue
        ),
        
        'RSIOrgUpdater' => array(
            'enabled' => true,          // Set to false to disable this updater
            'max_execution_time' => 30, // Max seconds this updater can run (approximately)
            'sleep_time' => 5,          // Seconds to sleep while waiting for server load to lower
            'delay_time' => 2,          // Seconds to sleep between processing each entry in the queue
        ),
        
        'RSIForumUpdater' => array(
            'enabled' => true,          // Set to false to disable this updater
            'max_execution_time' => 30, // Max seconds this updater can run (approximately)
            'sleep_time' => 5,          // Seconds to sleep while waiting for server load to lower
            'delay_time' => 2,          // Seconds to sleep between processing each entry in the queue
        ),
    ),
    
    // Proxy client settings
    'clients' => array(
        'rsi' => array(
            'username' => '',    // Handle of the RSI account you wish to use
            'password' => '',    // MD5 of the password of the RSI account you wish to use
        ),
    ),
);
