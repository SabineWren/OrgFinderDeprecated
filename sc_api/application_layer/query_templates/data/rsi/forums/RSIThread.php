<?php

namespace thalos_api;

require_once(__DIR__.'/../../../Template.php');
require_once(__DIR__.'/../accounts/RSIAccount.php');
require_once('RSIPostInfo.php');

class RSIThread extends Template
{
    public $thread_title;
    public $thread_id;
    public $thread_replies;
    public $thread_views;
    
    public $original_poster;
    public $original_post;
    
    public $recent_poster;
    public $recent_post;
    
    public function __construct($input_array)
    {
        $this->original_poster = new RSIAccount($input_array['original_poster']);
        $this->original_post = new RSIPostInfo($input_array['original_poster']);
        
        $this->recent_poster = new RSIAccount($input_array['recent_poster']);
        $this->recent_post = new RSIPostInfo($input_array['recent_poster']);
        
        parent::__construct($input_array);
    }
}