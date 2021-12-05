<?php

session_start();

include ('config.php');

if ($_SESSION['loggedin'] != true)
	header("location: index.php");

if (!empty($_POST['play-game']) && isset($_POST['play-game'])) {
	$gameID = $_POST['game-id'];

	$getGameInfo = "SELECT * FROM game WHERE game_id=?;";

	if ($getGD = mysqli_prepare($link, $getGameInfo)) {
		$getGD->bind_param("i", $gameID);
		if (mysqli_stmt_execute($getGD))
		{
			$gameData = mysqli_stmt_get_result($getGD);
			while ($row = mysqli_fetch_assoc($gameData))
			{
				$gameID = $row['game_id'];
				$gameName = stripslashes($row['game_name']);
				$topic = stripslashes($row['topic']);
				$teacherID = $row['teacher_id'];
				$dateCreated = $row['date_created'];
				$published = $row['published'];
			}
		}
	}
}

?>
<!DOCTYPE HTML>
<html>
<head>
	<title></title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="style/style.css">
	<link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
	<script src="bootstrap/js/bootstrap.bundle.min.js"></script>
	<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<!--	<script src="https://cdn.jsdelivr.net/npm/phaser@3.55.2/dist/phaser-arcade-physics.min.js"></script>-->
	<script src="https://cdn.jsdelivr.net/npm/phaser@3.55.2/dist/phaser.min.js"></script>

	<script src="script/boot.js"></script>
	<script src="script/directions.js"></script>
	<script src="script/intro.js"></script>
	<script src="script/question.js"></script>
	<script src="script/end.js"></script>
</head>
<body>
<?php
	include('header.php');
?>
	<main class="container ex-space">
		<div class="d-none" id="gameid"><?php echo $gameID; ?></div>
		<h2 class="center-align"><?php echo $gameName; ?></h2>
		<script src="script/game.js"></script>
	</main>
</body>
</html>
