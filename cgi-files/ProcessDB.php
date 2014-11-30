<?php
require("DBInfo.php");

function ConnectDB()
{
    global $hostname, $username, $password, $database;

    $link=mysql_connect($hostname, $username, $password);
    mysql_select_db($database, $link) or die ("Unable to connect to MySQL\n");

//    print "Connected to MySQL\n";
    return $link;
}

function CloseDB($link)
{
    mysql_close($link);
}

function ExecuteDBQuery($sql)
{
   $result =  mysql_query($sql);
   
   if (!$result)
   {
       die('Invalid query: ' . mysql_error() . '\n');
   }

   return $result;
}

function INSERT_storePoints($points, $drawStyle, $weight, $distance, $routeName)
{
//   $sql = 'INSERT storePoints VALUES ( "", "' .  $points . '","' . $drawStyle . '")';
//     $sql = 'INSERT storePoints VALUES ( "", "' .  $points . '","' . $drawStyle . '",' . $weight . ',' . $distance . ')';

  $tempRouteID = GetRouteID($routeName);
  if($tempRouteID < 0)
  {
      $routeID  = GetMaxRouteID();
      $routeID = $routeID + 1;
      $sql = 'INSERT routeNames VALUES (' . $routeID . ',' . '"' . $routeName . '")';
//      echo $sql . "\n";
      ExecuteDBQuery($sql);
  }
  else
  {
      $routeID = $tempRouteID;
  }

   $sql = 'INSERT storePoints VALUES ( "", "' .  $points . '","' . $drawStyle . '",' . $weight . ',' . $distance . ',' . $routeID . ')';
//   echo $sql . "\n";
   $result = ExecuteDBQuery($sql);
   return $result;
}


function SELECT_storePoints($selectSnap)
{
   if($selectSnap)
   {
       $sql = 'SELECT * FROM storePoints WHERE drawStyle ="snap" ';
   }
   else
   {
       $sql = 'SELECT * FROM storePoints';
   }
//   echo $sql . "\n";
   $result = ExecuteDBQuery($sql);
   return $result;
}

/*
function SELECT_SnapStorePoints()
{
   $sql = 'SELECT * FROM storePoints WHERE drawStyle ="snap" ';
//   echo $sql . "\n";
   $result = ExecuteDBQuery($sql);
   return $result;
}
*/

function DELETE_storePoints()
{
   $sql = 'DELETE FROM storePoints';
//   echo $sql . "\n";
   $result = ExecuteDBQuery($sql);
   return $result;
}

function GetRouteID($routeName)
{
    $routeID= -999;

    $sql = 'SELECT * FROM routeNames WHERE routeName="' . $routeName . '"';
    $result = ExecuteDBQuery($sql);
    
    $row = mysql_fetch_array($result);
    if($row)
    {
        $routeID = $row['routeID'];
    }

    return $routeID;
}

function GetMaxRouteID()
{
   $sql = 'SELECT max(routeID) AS maxID FROM routeNames';
   $result = ExecuteDBQuery($sql);
   $row = mysql_fetch_array($result);
   $maxID = $row['maxID'];

   return $maxID;
}

function DELETE_route($routeName)
{
   $routeID = GetRouteID($routeName);

   if($routeID >= 0)
   {
       $sql = 'DELETE FROM routeNames WHERE routeID= ' . $routeID;
       ExecuteDBQuery($sql);

       $sql = 'DELETE FROM storePoints WHERE routeID=' . $routeID;
       ExecuteDBQuery($sql);
   }
}

?>