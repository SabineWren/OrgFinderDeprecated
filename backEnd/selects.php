<?php
           $cnx = @mysqli_connect('localhost','root','', 'cognitiondb');
           $request = $cnx->prepare("SELECT * FROM tbl_organizations LIMIT 10 OFFSET ?");
           $request->bind_param("d", $pagenum);
           
           $pagenum = $_GET['pagenum'];
           
           if($cnx->connect_error)
           {
                die("Connection failed: " . $cnx->connect_error);
                exit("@Schon: help");
           }
           
           if($pagenum < 0)
           {
              exit("@Schon: help"); 
           }
          
           $pagenum = $pagenum * 10;
           $request->execute();
           
           $meta = $request->result_metadata();
           
           while ($field = $meta->fetch_field()) 
           {
                $parameters[] = &$row[$field->name];
           }

           call_user_func_array(array($request, 'bind_result'), $parameters);
           while ($request->fetch()) 
           {
               foreach($row as $key => $val) 
               {
                    $x[$key] = $val;
               }
                $results[] = $x;
            }
            
            echo json_encode($results);
?>