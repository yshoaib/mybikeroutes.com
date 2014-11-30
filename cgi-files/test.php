<?php

      check_REF();
//      disabled for now
//      check_Nonce();

      require("ProcessDB.php");
      $link = ConnectDB();

      function check_REF()
      {
          if(empty($_POST['ref']))
          {
              echo 'failed';
              die;
          }
      }
      
      function check_Nonce()
      {
          session_start();
          if( empty($_POST['nonce']) || $_POST['nonce'] != $_SESSION['nonce'] )
          {
              echo 'failed';
              //Unset nonce therefore making it unusable
              unset($_SESSION['nonce']);
              die;
          }
          else
          {
              echo 'passed';
          }
      }

      function read_file($dFile)
      {
          //Read File
          $fHand = fopen($dFile, 'r');
          $fSize = filesize($dFile);
          if($fSize > 0)
          {
              $data = fread($fHand, $fSize);
              fclose($fHand);
          }
          echo $data;
      }

      function write_SimFile($data, $dFile, $empty_file)
      {
          if($empty_file == 1)
          {
              $fHand = fopen($dFile, 'w') or die("Can't open file");
              fclose($fHand);
          }
          
          //Write File
          $fHand = fopen($dFile, 'a') or die("Can't open file");
          fwrite($fHand, $data);
          fclose($fHand);
      }
 
     function write_file($point, $dFile, $empty_file)
      {
          if($empty_file == 1)
          {
              $fHand = fopen($dFile, 'w') or die("Can't open file");
              fclose($fHand);
         }
        
         if($point != null)
         {
             //Write File
             $fHand = fopen($dFile, 'a') or die("Can't open file");
             if($point == '0')
             {
                 fwrite($fHand, "\n");
             }
             else
             {
                 fwrite($fHand, $point . ";");
             }
             fclose($fHand);
         }
      }

      function write_DB($point, $drawStyle, $weight, $distance, $routeName, $empty_file)
      {
          if($empty_file == 1)
          {
              DELETE_storePoints();
          }
        
          if($point != null)
          {
              //Use DB
//              echo "\n";
              INSERT_storePoints($point,$drawStyle,$weight, $distance, $routeName);
//              echo "\n";
          }
      }
            
      function read_DB($selectSnap)
      {
          $result = SELECT_storePoints($selectSnap);
/*
          if($selectSnap)
          {
              $result = SELECT_SnapStorePoints();
          }
          else
          {
              $result = SELECT_storePoints();
          }
*/

          while($row = mysql_fetch_array($result))
          {
              echo $row['points'] . ";" . $row['drawStyle'] .  ";" . $row['weight'] . "\n";
          }
      }


      function delete_route_DB($routeName)
      {
           DELETE_route($routeName);
      }

      function getGeoLocationCheapWay($ip)
      { 
	   $NetGeoURL = "http://netgeo.caida.org/perl/netgeo.cgi?target=".$ip; 
  
	   if($NetGeoFP = fopen($NetGeoURL,r))
	   {	 
             ob_start();
 
             fpassthru($NetGeoFP);
             $NetGeoHTML = ob_get_contents();
             ob_end_clean();

 	      fclose($NetGeoFP);
 	    }
 
	    preg_match ("/LAT:(.*)/i", $NetGeoHTML, $temp) or die("Could not find element LAT");
 	    $lat = $temp[1];
 	    preg_match ("/LONG:(.*)/i", $NetGeoHTML, $temp) or die("Could not find element LONG");
 	    $lng = $temp[1];

	    $lat = str_replace("<br>", "", $lat);
	    $lat = str_replace(" ", "", $lat);
	    $lng = str_replace("<br>", "", $lng);
	    $lng = str_replace(" ", "", $lng);

 	    echo $lat . "," . $lng;
      }

      $dFile = "data";
      $logFile = "logFile";
      //Read Request
      $point = $_POST["hdnPoint"];
      $readWrite = $_POST["readWrite"];
      $drawStyle = $_POST["drawStyle"];
      $weight = $_POST["weight"];
      $distance = $_POST["dist"];
      $routeName = $_POST["routeName"];
      $selectSnap = $_POST["selectSnap"];
      $finalNumNodes=$_POST["finalNumNodes"];
      $dijkExecTime=$_POST["dijkExecTime"];
      $pathCost=$_POST["pathCost"];
      $pathDistance=$_POST["pathDistance"];
      $ip=$_SERVER['REMOTE_ADDR'];
      $browser=$_SERVER['HTTP_USER_AGENT'];

     
      if($readWrite == 0)
      {
          read_DB($selectSnap);
//          write_SimFile("IP: " . $ip . " ; Time: " . date("l dS \of F Y h:i:s A") . " ; Read_DB($selectSnap)\n",$logFile, 0);
      }
      else if($readWrite == 1)
      {
          write_DB($point, $drawStyle, $weight, $distance, $routeName, 0);
//          write_SimFile("IP: " . $ip . " ; Time: " . date("l dS \of F Y h:i:s A") . " ; Write_DB, Point: " . $point . "\n", $logFile, 0);
      }
      else if($readWrite == 2)
      {
          write_DB($point, $drawStyle, $weight, $distance, $routeName, 0);
//          write_SimFile("IP: " . $ip . " ; Time: " . date("l dS \of F Y h:i:s A") . " ; Write_DB, Point: " . $point . "\n", $logFile, 0);

          read_DB($selectSnap);
//          write_SimFile("IP: " . $ip . " ; Time: " . date("l dS \of F Y h:i:s A") . " ; Read_DB($selectSnap)\n", $logFile, 0);
      }
      else if($readWrite == 3)
      {
          write_DB(null, null, null, null, null, 1);
//          write_SimFile("IP: " . $ip . " ; Time: " . date("l dS \of F Y h:i:s A") . " ; Write_DB, Point: " . null . " ; Erase\n", $logFile, 0);

          read_DB($selectSnap);
//          write_SimFile("IP: " . $ip . " ; Time: " . date("l dS \of F Y h:i:s A") . " ; Read_DB($selectSnap)\n", $logFile, 0);
      }
      else if($readWrite == 4)
      {
          getGeoLocationCheapWay($ip);
      }
      else if($readWrite == 5)
      {
          delete_route_DB($routeName);
          read_DB($selectSnap);
      }
      else if($readWrite == 6)
      {
          write_SimFile("IP: " . $ip . " ; Time: " . date("l dS \of F Y h:i:s A") . "\n  FinalNumNodes: " . $finalNumNodes . "\n  DijkExecTime: " . $dijkExecTime. "\n  PathCost: " . $pathCost . "\n  PathDistance: " . $pathDistance . "\n  Browser: " . $browser . "\n\n" , $logFile, 0);
      }
      
      CloseDB($link);
  ?>
