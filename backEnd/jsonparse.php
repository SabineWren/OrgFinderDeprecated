<?php
    //copied from http://www.kodingmadesimple.com/2014/12/how-to-insert-json-data-into-mysql-php.html
    $connect = mysqli_connect("localhost","root","", "cognitiondb") or die('Could not connect: ' . mysqli_error());
    
    $clear = "DELETE FROM tbl_OrgSize;
              DELETE FROM tbl_OrgMemberHistory;
              DELETE FROM tbl_OrgArchetypes;
              DELETE FROM tbl_Archetypes;
              DELETE FROM tbl_OrgFluencies;
              DELETE FROM tbl_OrgLocated;
              DELETE FROM tbl_OrgRegions;
              DELETE FROM tbl_SecondaryFocus;
              DELETE FROM tbl_PrimaryFocus;
              DELETE FROM tbl_Performs;
              DELETE FROM tbl_Commits;
              DELETE FROM tbl_Commitments;
              DELETE FROM tbl_ExclusiveOrgs;
              DELETE FROM tbl_FullOrgs;
              DELETE FROM tbl_RolePlayOrgs;
              DELETE FROM tbl_Represents;
              DELETE FROM tbl_Affiliated;
              DELETE FROM tbl_Main;
              DELETE FROM tbl_RepresentsCog;
              DELETE FROM tbl_Organizations;
              DELETE FROM tbl_FromCountry;
              DELETE FROM tbl_Persons;";
              mysqli_query($connect, $clear);
    
    
    //read the json file contents
    $x = 1;
    $null = 0;
    do
    {
        $lines = file_get_contents('http://sc-api.com/?api_source=live&system=organizations&action=all_organizations&source=rsi&start_page='.$x.'&end_page='.$x.'&items_per_page=50&sort_method=&sort_direction=ascending&expedite=0&format=pretty_json');
        //convert json object to php associative array
        $data = json_decode($lines);
        if ($data != "null")
        {
            if ( is_array($data) || is_object($data))
            {
                foreach ($data->data as $org)
                {
                    //put info into variables
                    $sid = $org->sid;
                    $name = $org->title;
                    $icon = $org->logo;
                
                    //insert into mysql table
                    $sql = "INSERT INTO tbl_Organizations(SID, Name, Icon)
                    VALUES('$sid', '$name', '$icon')";
                    mysqli_query($connect, $sql);
                    $x++;
                }
            }
            else {
                    $null++;
            }
        }
        else {
            $null++;
        }
       
    }while($null != 3);
    
    /*$SIDstr = "SELECT SID FROM tbl_Organizations";
    $SIDres = mysqli_query($connect, $orgSID);
    
    while ($SID = mysqli_fetch_assoc($SIDres))
    {
        $lines = file_get_contents('http://sc-api.com/?api_source=live&system=organizations&action=single_organization&target_id='.$SID['sid'].'&expedite=0&format=pretty_json');
    }
    
    $defrag = "ALTER TABLE tbl_Countries ENGINE=INNODB;
               ALTER TABLE tbl_Organizations ENGINE=INNODB;
               ALTER TABLE tbl_Commitments ENGINE=INNODB;
               ALTER TABLE tbl_Commits ENGINE=INNODB;
               ALTER TABLE tbl_RolePlayOrgs ENGINE=INNODB;
               ALTER TABLE tbl_Archetypes ENGINE=INNODB;
               ALTER TABLE tbl_OrgArchetypes ENGINE=INNODB;
               ALTER TABLE tbl_FullOrgs ENGINE=INNODB;
               ALTER TABLE tbl_ExclusiveOrgs ENGINE=INNODB;
               ALTER TABLE tbl_Fluencies ENGINE=INNODB;
               ALTER TABLE tbl_OrgFluencies ENGINE=INNODB;";
    mysqli_query($connect, $defrag);
    */
    echo "Insert of new org data is done";
?>  