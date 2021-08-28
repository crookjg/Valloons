<?php

session_start();

define('DB_NAME', 'valloon_game');
define('DB_USER', 'root');
define('DB_PASS', 'valloons@21');
define('DB_HOST', 'localhost');
define('DB_PORT', 3306); 
define('TITLE', 'Valloons');

$link = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT) or die("Could not connect to the database.");

?>
