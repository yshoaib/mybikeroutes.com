<?php
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
?>