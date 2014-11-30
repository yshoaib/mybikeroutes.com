<?php
//start session
session_start();

//create new nonce
$nonce = md5(uniqid(rand(), true));
//add new nonce to session
$_SESSION['nonce'] = $nonce;
echo $nonce;
?>