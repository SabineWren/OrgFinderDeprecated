<?php

namespace thalos_api;

require_once(__DIR__.'/../../../Template.php');
require_once(__DIR__.'/../accounts/RSIAccount.php');
require_once('RSIPostInfo.php');

class RSIPost extends Template
{
    public $author;
    public $post;
    
    public function __construct($input_array)
    {
        $this->author = new RSIAccount($input_array);
        $this->post = new RSIPostInfo($input_array);
        
        parent::__construct($input_array);
    }
}