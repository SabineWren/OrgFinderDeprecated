<?php

namespace thalos_api;

require_once('data_handlers/Item.php');
require_once('data_handlers/Postprocess.php');

require_once('globals_and_constants/QueryParameterConstants.php');

require_once('query_handlers/scrapers/rsi/RSIDossierScraper.php');
require_once('query_handlers/scrapers/rsi/RSIDossierOrgsScraper.php');
require_once('query_handlers/scrapers/rsi/RSIForumProfileScraper.php');
require_once('query_handlers/scrapers/rsi/RSIProfileThreadScraper.php');
require_once('query_handlers/scrapers/rsi/RSIProfilePostScraper.php');

require_once('query_handlers/scrapers/rsi/RSIOrgScraper.php');
require_once('query_handlers/scrapers/rsi/RSIOrgMembersScraper.php');
require_once('query_handlers/scrapers/rsi/RSIAllOrgScraper.php');
require_once('query_handlers/scrapers/wikia/WikiaOrgScraper.php');

require_once('query_handlers/scrapers/rsi/RSIPostScraper.php');
require_once('query_handlers/scrapers/rsi/RSIThreadScraper.php');
require_once('query_handlers/scrapers/rsi/RSIForumScraper.php');

require_once('query_templates/Output.php');
require_once('query_templates/data/rsi/accounts/RSIAccount.php');
require_once('query_templates/data/rsi/orgs/RSIOrg.php');
require_once('query_templates/data/rsi/forums/RSIPost.php');
require_once('query_templates/data/rsi/forums/RSIThread.php');
require_once('query_templates/data/rsi/forums/RSIForum.php');

/**
* Validates and executes user queries then applies necessary output templates.
*/
class Controller
{
    protected $DB;
    protected $Output;
    protected $handler_chain;
    
    /**
    * Executes a user query.
    *
    * @param array  $query      The user query array.
    * @param bool   $fallback   DO NOT SET MANUALLY. Used to allow for
    *                           a fallthrough from the cache to live data.
    *
    * @return array
    */
    public function Query($query, $fallback = false, $query_chain = null)
    {
        set_time_limit(300);
        
        // Initiate query and apply first steps
        $this->QueryPrepass($query, $fallback);
         if($GLOBALS['TRACE'])echo "completed prepass\n";
        
        $query_handlers = array();
        
        if($query_chain == null)
        {
            $this->handler_chain = new QueryChain(array('name'=>'root'));
             if($GLOBALS['TRACE'])echo "created querychain\n";
        }
        else
        {
            $this->handler_chain = $query_chain;
        }
        
        switch($this->Output->request_stats->input_query->system)
        {
            case QuerySystems::Accounts:
                switch($this->Output->request_stats->input_query->action)
                {
                    default:
                    case AccountActions::Dossier:
                        switch($this->Output->request_stats->input_query->api_source)
                        {
                            case APISources::Live:
                                $query_handlers[] = 'RSIDossierScraper';
                                if($fallback)
                                {
                                    $query_handlers[] = 'RSIForumProfileScraper';
                                }
                                break;
                            default:
                            case APISources::Cache:
                                $query_handlers[] = 'RSIAccountCacher';
                                break;
                        }
                        break;
                    case AccountActions::Memberships:
                        switch($this->Output->request_stats->input_query->api_source)
                        {
                            default:
                            case APISources::Live:
                                $query_handlers[] = 'RSIDossierOrgsScraper';
                                break;
                        }
                        break;
                    case AccountActions::Forum_Profile:
                        switch($this->Output->request_stats->input_query->api_source)
                        {
                            case APISources::Live:
                                $query_handlers[] = 'RSIForumProfileScraper';
                                if($fallback)
                                {
                                    $query_handlers[] = 'RSIDossierScraper';
                                }
                                break;
                            default:
                            case APISources::Cache:
                                $query_handlers[] = 'RSIAccountCacher';
                                break;
                        }
                        break;
                    case AccountActions::Threads:
                        switch($this->Output->request_stats->input_query->api_source)
                        {
                            default:
                            case APISources::Live:
                                $query_handlers[] = 'RSIProfileThreadScraper';
                                break;
                        }
                        break;
                    case AccountActions::Posts:
                        switch($this->Output->request_stats->input_query->api_source)
                        {
                            default:
                            case APISources::Live:
                                $query_handlers[] = 'RSIProfilePostScraper';
                                break;
                        }
                        break;
                    case AccountActions::Full_Profile:
                        switch($this->Output->request_stats->input_query->api_source)
                        {
                            case APISources::Live:
                                $query_handlers[] = 'RSIDossierScraper';
                                $query_handlers[] = 'RSIDossierOrgsScraper';
                                $query_handlers[] = 'RSIForumProfileScraper';
                                break;
                            default:
                            case APISources::Cache:
                                $query_handlers[] = 'RSIAccountCacher';
                                break;
                        }
                        break;
                    case AccountActions::All_Accounts:
                        switch($this->Output->request_stats->input_query->api_source)
                        {
                            case APISources::Live:
                            default:
                            case APISources::Cache:
                                $query_handlers[] = 'RSIAllAccountCacher';
                                break;
                        }
                        break;
                }
                break;

            case QuerySystems::Organizations:
                switch($this->Output->request_stats->input_query->action)
                {
                    case OrganizationActions::All_Organizations:
                        switch($this->Output->request_stats->input_query->data_source)
                        {
                            default:
                            case OrganizationSources::RSI:
                                switch($this->Output->request_stats->input_query->api_source)
                                {
                                    case APISources::Live:
                                        $query_handlers[] = 'RSIAllOrgScraper';
                                        break;
                                    default:
                                    case APISources::Cache:
                                        $query_handlers[] = 'RSIAllOrgCacher';
                                        break;
                                }
                                break;
                            case OrganizationSources::Wikia:
                                break;
                        }
                        break;

                    case OrganizationActions::Single_Organization:
                        switch($this->Output->request_stats->input_query->api_source)
                        {
                            case APISources::Live:
                                $query_handlers[] = 'RSIOrgScraper';
                                break;
                            default:
                            case APISources::Cache:
                                $query_handlers[] = 'RSIOrgCacher';
                                break;
                        }
                        break;

                    case OrganizationActions::Organization_Members:
                        switch($this->Output->request_stats->input_query->api_source)
                        {
                            case APISources::Live:
                                $query_handlers[] = 'RSIOrgMembersScraper';
                                break;
                            default:
                            case APISources::Cache:
                                $query_handlers[] = 'RSIOrgMembersCacher';
                                break;
                        }
                        break;

                    default:
                        break;
                }
                break;
            
            case QuerySystems::Forums:
                switch($this->Output->request_stats->input_query->action)
                {
                    default:
                    case ForumActions::Posts:
                        switch($this->Output->request_stats->input_query->api_source)
                        {
                            default:
                            case APISources::Live:
                                $query_handlers[] = 'RSIPostScraper';
                                break;
                        }
                        break;
                    case ForumActions::Threads:
                        switch($this->Output->request_stats->input_query->api_source)
                        {
                            default:
                            case APISources::Live:
                                $query_handlers[] = 'RSIThreadScraper';
                                break;
                        }
                        break;
                    case ForumActions::Forums:
                        switch($this->Output->request_stats->input_query->api_source)
                        {
                            default:
                            case APISources::Live:
                                $query_handlers[] = 'RSIForumScraper';
                                break;
                        }
                        break;
                }
                break;

            default;
                break;
        }
        
        if($GLOBALS['TRACE'])echo "built queryhandlers\n";
        
        // For each Scraper/Cacher requested...
        foreach($query_handlers as $handler)
        {
        	if($GLOBALS['TRACE'])echo "creating handler == $handler\n";
            $handler = __NAMESPACE__.'\\'.$handler;
            $handler = new $handler($this->Output, $this->handler_chain);
            if($GLOBALS['TRACE'])echo "created handler:\n";
            
            // Perform data collection steps
            $handler->PerformQuery();
        }
        if($GLOBALS['TRACE'])echo "completed queries\n";
        
        // If we have no data and the user requested it from the cache...
        if($this->Output->data == null
            && $this->Output->request_stats->resolved_query->api_source == APISources::Cache)
        {
            // Restart the query but request the live data.
            // Use the $fallback flag rather than manually changing the query
            //    so the user can be informed of the fallback.
            return $this->Query($query, true, $this->handler_chain);
        }
        
        // Correct the input_query settings to reflect fallback
        $this->Output->request_stats->input_query->api_source = ($fallback?APISources::Cache:$query['api_source']);
        
        // Mold the output data to the necessary output template
        $this->QueryTemplatingPass();
        
        // Apply final steps
        $this->QueryPostpass();
        
        // Return the array of our output
        return $this->Output->GetAll();
    }
    
    /**
    * Initialize the query.
    *
    * @param array  $query      The configuration option's name.
    * @param bool   $fallback   DO NOT SET MANUALLY. Used to allow for
    *                           a fallthrough from the cache to live data.
    *
    * @return null
    */
    protected function QueryPrepass($query, $fallback)
    {
        //$this->DB = new DBInterface();
        //$this->DB->Connect();
        
        // Correct the input_query settings to reflect fallback
        $query['api_source'] = ($fallback?APISources::Live:$query['api_source']);
        
        // Initialize the Output object with the user-supplied query array
        $this->Output = new Output($query);
        
        //$this->DB->UpdateUser($this->Output->request_stats->request_ip);
    }
    
    /**
    * Apply any finishing steps to the query data.
    *
    * @return null
    */
    protected function QueryPostpass()
    {
        // Calculate the time our query took
        $this->Output->request_stats->performance->processing_time = microtime(true) - $this->Output->request_stats->timestamp
                - $this->Output->request_stats->performance->network_io_time;
        
        // Calculate the time our query took
        $this->Output->request_stats->performance->total_time = microtime(true) - $this->Output->request_stats->timestamp;
        
        // Count the items in our data array
        $this->Output->request_stats->items_returned = count($this->Output->data);
        
        // Set query status
        $this->Output->request_stats->query_status = (count($this->Output->data) > 0)? 'success' : 'failed';
        
        $this->Output->request_stats->performance->handler_chain = $this->handler_chain->GetAll();
        $this->Output->request_stats->performance->handler_chain = $this->Output->request_stats->performance->handler_chain['children'];
        
        //$this->DB->Disconnect();
    }
    
    /**
    * Apply necessary output templates to the query data.
    *
    * @return null
    */
    protected function QueryTemplatingPass()
    {
        switch($this->Output->request_stats->input_query->system)
        {
            case QuerySystems::Accounts:
                switch($this->Output->request_stats->input_query->action)
                {
                    default:
                    case AccountActions::Dossier:
                    case AccountActions::Forum_Profile:
                    case AccountActions::Full_Profile:
                        if(gettype($this->Output->data) == 'array')
                            $this->Output->data = $this->ApplyOutputTemplate($this->Output->data, 'RSIAccount');
                        break;
                    case AccountActions::All_Accounts:
                        if(gettype($this->Output->data) == 'array')
                        {
                            foreach($this->Output->data as $entry)
                            {
                                $entry['handle'] = $entry['id'];
                                $data[] = $this->ApplyOutputTemplate($entry, 'RSIAccount');
                            }
                            $this->Output->data = $data;
                        }
                        break;
                    case AccountActions::Threads:
                        if(gettype($this->Output->data) == 'array')
                        {
                            foreach($this->Output->data as $entry)
                            {
                                $data[] = $this->ApplyOutputTemplate($entry, 'RSIThread');
                            }
                            $this->Output->data = $data;
                        }
                        break;
                    case AccountActions::Posts:
                        if(gettype($this->Output->data) == 'array')
                        {
                            foreach($this->Output->data as $entry)
                            {
                                $data[] = $this->ApplyOutputTemplate($entry, 'RSIPost');
                            }
                            $this->Output->data = $data;
                        }
                        break;
                }
                break;

            case QuerySystems::Organizations:
                switch($this->Output->request_stats->input_query->action)
                {
                    case OrganizationActions::All_Organizations:
                        switch($this->Output->request_stats->input_query->data_source)
                        {
                            default:
                            case OrganizationSources::RSI:
                                switch($this->Output->request_stats->input_query->api_source)
                                {
                                    case APISources::Live:
                                        if(gettype($this->Output->data) == 'array')
                                        {
                                            foreach($this->Output->data as $entry)
                                            {
                                                $data[] = $this->ApplyOutputTemplate($entry, 'RSIOrg');
                                            }
                                            $this->Output->data = $data;
                                        }
                                        break;
                                    default:
                                    case APISources::Cache:
                                        if(gettype($this->Output->data) == 'array')
                                        {
                                            foreach($this->Output->data as $entry)
                                            {
                                                $entry['sid'] = $entry['id'];
                                                $data[] = $this->ApplyOutputTemplate($entry, 'RSIOrg');
                                            }
                                            $this->Output->data = $data;
                                        }
                                        break;
                                }
                                break;
                            case OrganizationSources::Wikia:
                                break;
                        }
                        break;

                    case OrganizationActions::Single_Organization:
                        if(gettype($this->Output->data) == 'array')
                            $this->Output->data = $this->ApplyOutputTemplate($this->Output->data, 'RSIOrg');
                        break;

                    case OrganizationActions::Organization_Members:
                        if(gettype($this->Output->data) == 'array')
                        {
                            foreach($this->Output->data as $entry)
                            {
                                $entry['sid'] = $this->Output->request_stats->resolved_query->target_id;
                                $data[] = $this->ApplyOutputTemplate($entry, 'RSIOrgMember');
                            }
                            $this->Output->data = $data;
                        }
                        break;

                    default:
                        break;
                }
                break;
            
            case QuerySystems::Forums:
                switch($this->Output->request_stats->input_query->action)
                {
                    case ForumActions::Posts:
                        if(gettype($this->Output->data) == 'array')
                        {
                            foreach($this->Output->data as $entry)
                            {
                                $data[] = $this->ApplyOutputTemplate($entry, 'RSIPost');
                            }
                            $this->Output->data = $data;
                        }
                        break;
                    case ForumActions::Threads:
                        if(gettype($this->Output->data) == 'array')
                        {
                            foreach($this->Output->data as $entry)
                            {
                                $data[] = $this->ApplyOutputTemplate($entry, 'RSIThread');
                            }
                            $this->Output->data = $data;
                        }
                        break;
                    case ForumActions::Forums:
                        if(gettype($this->Output->data) == 'array')
                        {
                            foreach($this->Output->data as $entry)
                            {
                                $data[] = $this->ApplyOutputTemplate($entry, 'RSIForum');
                            }
                            $this->Output->data = $data;
                        }
                        break;
                }
                break;

            default;
                break;
        }
    }
    
    /**
    * Split a class' namespace from the classname.
    *
    * @param string     $name   The class' name.
    *
    * @return array
    */
    public static function parse_classname($name)
    {
        // Split namespace from classname
        return array(
          'namespace' => array_slice(explode('\\', $name), 0, -1),
          'classname' => join('', array_slice(explode('\\', $name), -1)),
        );
    }
    
    /**
    * Make data conform to a specified output template.
    *
    * @param array    $data             The data that requires morphing.
    * @param string   $template_name    The class name of the template.
    *
    * @return array
    */
    public function ApplyOutputTemplate($data, $template_name)
    {
        // Prepend our namespace
        $template_name = __NAMESPACE__ . '\\'.$template_name;
        
        // Initialize the requested template with the supplied data
        $template = new $template_name($data);
        
        // Return the template's formatted data
        return $template->GetAll();
    }
}
