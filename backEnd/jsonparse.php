<?php
    //copied from http://www.kodingmadesimple.com/2014/12/how-to-insert-json-data-into-mysql-php.html
    $connect = mysql_connect("username","password","") or die('Could not connect: ' . mysql_error());

    mysql_select_db("employee", $connect);

    //read the json file contents
    $jsondata = file_get_contents('');
    
    //convert json object to php associative array
    $data = json_decode($jsondata, true);
    
    //put info into variables *example*
    $id = $data['empid'];
    $name = $data['personal']['name'];
    $gender = $data['personal']['gender'];
    $age = $data['personal']['age'];
    $streetaddress = $data['personal']['address']['streetaddress'];
    $city = $data['personal']['address']['city'];
    $state = $data['personal']['address']['state'];
    $postalcode = $data['personal']['address']['postalcode'];
    $designation = $data['profile']['designation'];
    $department = $data['profile']['department'];
    
    //insert into mysql table
    $sql = "INSERT INTO tbl_emp(empid, empname, gender, age, streetaddress, city, state, postalcode, designation, department)
    VALUES('$id', '$name', '$gender', '$age', '$streetaddress', '$city', '$state', '$postalcode', '$designation', '$department')";
    if(!mysql_query($sql,$connect))
    {
        die('Error : ' . mysql_error());
    }
?>