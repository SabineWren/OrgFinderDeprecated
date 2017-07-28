<?php

namespace thalos_api;

require_once(__DIR__.'/../../../Template.php');

class RSIOrgMember extends Template
{
    public $sid;
    public $handle;
    public $rank;
    public $stars;
    public $roles;
    public $type;
    public $visibility;
}