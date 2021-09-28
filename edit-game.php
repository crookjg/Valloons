<?php

session_start();

include('config.php');

if ((isset($_POST['edit-game']) && !empty($_POST['edit-game'])) || isset($_SESSION['game_id'])) {
	if (isset($_POST['edit-game']) && !empty($_POST['edit-game'])) {
		$gameID = $_POST['game-id'];
	} else if (isset($_SESSION['game_id'])) {
		$gameID = $_SESSION['game_id'];
		$_SESSION['game_id'] = NULL;
	} else {
		$gameID = NULL;
	}

	$getGameData = "SELECT * FROM game WHERE game_id=?;";
}

?>
<!DOCTYPE HTML>
<html>
<head>
	<title>Valloons</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="style/style.css">
	<link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
	<script src="bootstrap/js/bootstrap.bundle.min.js"></script>
	<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
</head>
<body>
<?php
	include('header.php');
?>
	<main>
	</main>
</body>
</html>
