<?php

    function Perms($N, $Src, $Got, &$All) 
    {
       global $dumpFile;
       writeToFile("- Perms(): \$N: " . $N . "\t", $dumpFile, 0);
       writeToFile("\$Src: " .  implode(",", $Src) . "\t", $dumpFile, 0);
       writeToFile("\$Got: " . $Got . "\t", $dumpFile, 0);
       writeToFile("\$All: " . implode(",", $All) . "\t", $dumpFile, 0);
       writeToFile("\n", $dumpFile, 0);

       if ($N == 0) {
          $All[count($All)] = $Got;
          return;
       }
       for ($j = 0; $j < count($Src); $j++) 
       {
           $temp1 = array();
           $temp1 = array_slice($Src, 0, $j);
           $temp2 = array();
           $temp2 = array_slice($Src,$j+1);


           Perms($N-1, array_merge($temp1, $temp2), $Got + $Src[j], $All); 
       }
       return;
    }

    function TestPerms($S) 
    {
       global $dumpFile;

       writeToFile("- TestPerms(): \$S: " . $S . "\n", $dumpFile, 0);
       $arr_grand_container = array();
       for($k = 1; $k <= count($S); $k++) 
       {
           $All = array();

           //Added: Yasir
           $srcArray = split("),", $S);

           $patterns = array("/\(/", "/\)/");
           $replace = array("","");

           $srcArray = preg_replace($patterns, $replace, $srcArray);
//           foreach ($srcArray as &$str)
//           {
//               $str = preg_replace($patterns, $replace, $str);
//           }

           //Changed: Yasir
//           Perms($k, $S, "", $All);
           Perms($k, $srcArray, "", $All);


           writeToFile("- TestPerms(): value of array ALL after iteration number " . $k . " is " . implode(",",$All) . "\n", $dumpFile, 0);
           for($iter2 = 0; $iter2 < count($All); $iter2++)
           {
              array_push($arr_grand_container, $All[iter2]);
           }
       }

        print_r($arr_grand_container);
    }

    function writeToFile($data, $dFile, $empty_file)
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
 
    $dumpFile = "dumpFile";
    $SetAllRoutes = $_REQUEST["AllRoutes"];
    writeToFile("Time: " . date("l dS \of F Y h:i:s A") . " INPUT VALUE " . $SetAllRoutes . "\n", $dumpFile, 0);
    TestPerms($SetAllRoutes);
    writeToFile("\n", $dumpFile, 0);
?>
