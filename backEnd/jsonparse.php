<?php
    //copied from http://www.kodingmadesimple.com/2014/12/how-to-insert-json-data-into-mysql-php.html
    $connect = mysqli_connect("localhost","root","", "cognitiondb") or die('Could not connect: ' . mysqli_error());

    //read the json file contents
    
    $lines = file('orgs.txt');
    //foreach ($lines as $org)
    //{
        $jsondata = file_get_contents('http://sc-api.com/?api_source=cache&start_date=&end_date=&system=organizations&action=single_organization&target_id='.$org.'&format=pretty_json');
        //convert json object to php associative array
        $data = json_decode($jsondata, true);
        
        //put info into variables *example*
        $sid = $data['data']['sid'];
        $name = $data['data']['title'];
        $icon = $data['data']['logo'];
        
        //insert into mysql table
        $sql = "INSERT INTO tbl_Organizations(SID, Name, Icon)
        VALUES('$sid', '$name', '$icon')";
        mysqli_query($connect, $sql);
   // }
?>