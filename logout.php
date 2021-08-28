<?php

session_start();

$_SESSION['loggedin'] = false;
$_SESSION['name'] = "";
	
session_destroy();
header('location: index.php');
exit;

?>
