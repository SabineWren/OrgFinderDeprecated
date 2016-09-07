<?php

namespace thalos_api;

require_once('LoginClient.php');

/**
* Superclass for the web scrapers.
*/
abstract class Scraper
{
    protected $Output;
    protected $query_chain_entry;
    protected $Client;
    
    public function __construct($Output, $query_chain_parent = null)
    {
        $this->Output = $Output;
        
        // Perform any specific settings validation
        $this->ValidateLocalSettings();
        
        $name = Controller::parse_classname(get_class($this));
        
        if($query_chain_parent != null)
        {
            $this->query_chain_entry = new QueryChain(array('name'=>$name['classname']));
            $query_chain_parent->children[] = $this->query_chain_entry;
        }
        
        $this->Client = new LoginClient();
    }
    
    public abstract function PerformQuery();
    
    protected abstract function ValidateLocalSettings();
    
    public function GetPage($target, $authenticate = false)
    {
        $start = microtime(true);
        
        $contents = $this->Client->GetPage($target);
        
        $this->Output->request_stats->performance->network_io_time += microtime(true) - $start;
        
        return $contents;
    }
    
    public static function ScrapePage_Merged($page_text, $pattern_array)
    {
        $output = array();
        
        // Build a single pattern string with 
        // each pattern in the array
        $pattern='|';
        foreach($pattern_array as $index=>$match)
        {
            // Append the new pattern with a connecting pattern
            $pattern.=$match->pattern.'.*';
        }
        $pattern.='|Us';
        
        // If the search succeeds
        if(preg_match($pattern, $page_text, $matches))
        {
            // For each of the supplied patterns
            foreach($pattern_array as $index=>$match)
            {
                // Apply any specified filters on the returned data
                $output[$match->name] = Scraper::ApplyPostprocessing($match->postprocesses, $matches[$index]);
            }
            
            return $output;
        }
        
        return false;
    }
    
    public static function ScrapePage($source_text, $pattern_array, $delimiter)
    {
        $output = array();
        $return = array();
        
        $blocks = Scraper::ExtractWorkingText($source_text, $delimiter);
        
        foreach($blocks as $working_block)
        {
            // For each of the supplied patterns
            foreach($pattern_array as $index=>$match)
            {
                // If this Item has its own pattern
                if($match->pattern != null)
                {
                    // If the search succeeds
                    if(preg_match('|'.$match->pattern.'|Us', $working_block, $matches))
                    {
                        // Apply any specified filters on the returned data
                        $output[$match->name] = Scraper::ApplyPostprocessing($match->postprocesses, $matches[1]);
                    }
                    else
                    {
                        // Apply any specified filters on null data
                        $output[$match->name] = Scraper::ApplyPostprocessing($match->postprocesses, null);
                    }
                }
                // Else if the most recent search returned multiple items,
                // use the next match for this Item
                else if(isset($matches[1]))
                {
                    // Apply any specified filters on the returned data
                    $output[$match->name] = Scraper::ApplyPostprocessing($match->postprocesses, $matches[1]);
                }
                else
                {
                    // Apply any specified filters on null data
                    $output[$match->name] = Scraper::ApplyPostprocessing($match->postprocesses, null);
                }

                // If the most recent search returned multiple items
                // and they've not been exhausted
                if(isset($matches[2]))
                {
                    // For each of the remaining matched items
                    foreach($matches as $loc=>$item)
                    {
                        // If on the second match or higher
                        if($loc >= 2)
                        {
                            // Shift the match left within the array
                            $matches[$loc-1] = $item;

                            // Remove the old data
                            unset($matches[$loc]);
                        }
                    }
                }
                else
                {
                    // Make sure the array doesn't give any false-positives
                    unset($matches);
                }
            }
            
            $return[] = $output;
            unset($output);
        }

        return $return;
    }
    
    private static function ExtractWorkingText($source, $delimiter)
    {
        // Split the source on our specified delimiter
        $blocks = preg_split('|'.$delimiter.'|', $source);
        
        // Unset the "whole match" block
        unset($blocks[0]);
        
        // Return our array of text blocks
        return $blocks;
    }
    
    private static function ApplyPostprocessing($functions_array, $input)
    {
        if(isset($functions_array)
            && $functions_array != null)
        {
            // For each of the filters requested
            foreach($functions_array as $function)
            {
                $params = array();
                
                // For each of the parameters in this function
                foreach($function->arguments as $param)
                {
                    // If the user requested this parameter be our matched data
                    if($param == '$?')
                    {
                        $params[] = $input;
                    }
                    // Else, use whatever the user supplied
                    else
                    {
                        $params[] = $param;
                    }
                }
                
                // Run the function
                $input = @call_user_func_array($function->callback, $params);
            }
        }
        
        // Return the item after all filters
        return $input;
    }
    
    public static function StrConcat($string, $strings_array)
    {
        // If the first parameter is a valid string
        if(isset($string) 
            && isset($strings_array))
        {
            // If the second paramter is an array of strings
            if(gettype($strings_array) == 'array')
            {
                // For each of the member strings
                foreach($strings_array as $string)
                {
                    $string .= $string;
                }
            }
            // Else if the second parameter is a string
            else
            {
                $string .= $strings_array;
            }

            return $string;
        }
        
        return null;
    }
}
