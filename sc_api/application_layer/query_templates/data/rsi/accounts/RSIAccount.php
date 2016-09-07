<?php

namespace thalos_api;

require_once(__DIR__.'/../../../Template.php');
require_once(__DIR__.'/../orgs/RSIOrgMember.php');

class RSIAccount extends Template
{
    public $handle;
    public $citizen_number;
    public $status;
    public $moniker;
    public $avatar;
    public $enlisted;
    public $title;
    public $title_image;
    public $bio;
    public $website_link;
    public $website_title;
    public $country;
    public $region;
    public $fluency;
    public $discussion_count;
    public $post_count;
    public $last_forum_visit;
    public $forum_roles;
    public $organizations;
    public $date_added;
    public $last_scrape_date;
    
    public function __construct($input_array)
    {
        // If we'er provided with an array of orgs
        if(isset($input_array['organizations']))
        {
            // For each org in the array
            foreach($input_array['organizations'] as $index=>$org)
            {
                // Create a new OrgMember template
                $this->organizations[] = new RSIOrgMember($org);
            }
        }
        
        parent::__construct($input_array);
    }
}