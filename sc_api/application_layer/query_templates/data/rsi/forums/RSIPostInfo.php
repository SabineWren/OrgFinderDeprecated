<?php

namespace thalos_api;

require_once(__DIR__.'/../../../Template.php');

class RSIPostInfo extends Template
{
    public $post_time;
    public $last_edit_time;
    public $post_text;
    public $signature;
    public $post_id;
    public $thread_id;
    public $thread_title;
    public $permalink;
}