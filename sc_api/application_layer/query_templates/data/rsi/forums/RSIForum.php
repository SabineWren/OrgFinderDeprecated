<?php

namespace thalos_api;

require_once(__DIR__.'/../../../Template.php');

class RSIForum extends Template
{
    public $forum_title;
    public $forum_id;
    public $forum_description;
    public $forum_url;
    public $forum_rss;
    public $forum_discussion_count;
    public $forum_post_count;
    
    public function __construct($input_array)
    {
        parent::__construct($input_array);
    }
}