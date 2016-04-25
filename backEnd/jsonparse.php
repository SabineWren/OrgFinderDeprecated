<?php
    //copied from http://www.kodingmadesimple.com/2014/12/how-to-insert-json-data-into-mysql-php.html
    //$connect = mysqli_connect("64.71.77.121","overclock","Cogintstr07", "cognitiondb") or die('Could not connect: ' . mysqli_error());

    //read the json file contents
    for($x = 1; $x <= 1160; $x++)
    {
        $y = 0;
        $lines = file_get_contents('http://sc-api.com/?api_source=live&system=organizations&action=all_organizations&source=rsi&start_page='.$x.'&end_page='.$x.'&items_per_page=255&sort_method=&sort_direction=ascending&expedite=0&format=pretty_json');
        //convert json object to php associative array
        $data = json_decode($lines, true);
        foreach ($data as $org)
        {
          foreach($org)
           {
            //put info into variables
            $sid = $org['sid'];
            $name = $org['title'];
            $icon = $org['logo'];
           }
            
        
            echo "$sid \n";
            echo "$name \n";
            echo "$icon \n";
        
            //insert into mysql table
            //$sql = "INSERT INTO tbl_Organizations(SID, Name, Icon)
            //VALUES('$sid', '$name', '$icon')";
            //mysqli_query($connect, $sql);
       }
       
       unset($lines);
       unset($data);
    }
?>  