<?php 
    namespace thalos_api; 
    use PDO;
?>

<!DOCTYPE html>

<html>
    <head>
        <title><?php echo $page_title; ?></title>
        <meta charset="UTF-8">
        
        <meta name="contact" content="webmaster@sc-api.com">
        <meta http-equiv="content-type" content="text/html; charset=utf-8;" />
        <meta http-equiv="content-style-type" content="text/css" />
        <meta http-equiv="content-language" content="en" />
        <meta http-equiv="imagetoolbar" content="no" />
        <meta name="resource-type" content="document" />
        <meta name="distribution" content="global" />
        <meta name="keywords" content="star citizen sc api application programers interface" />
        <meta name="description" content="The unofficial API of Star Citizen and related sources." />
        
        <?php
        if ($handle = opendir('sc_api/presentation_layer/css')) 
        {
            while (false !== ($entry = readdir($handle)))
            {
                if(preg_match('|.*\.css$|U',$entry))
                    echo '<link rel="stylesheet" href="sc_api/presentation_layer/css/'.$entry.'">
        ';
            }

            closedir($handle);
        }
        ?>
    </head>