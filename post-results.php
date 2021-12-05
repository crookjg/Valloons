<?php

session_start();

include('config.php');

$gameID = isset($_POST['gameid']) ? $_POST['gameid'] : NULL;
$score = isset($_POST['score']) ? $_POST['score'] : NULL;

if ($gameID != NULL && $score != NULL) {
	$addScore = "INSERT INTO student_game (student_id, game_id, score) VALUES (?, ?, ?);";
	if ($addS = mysqli_prepare($link, $addScore)) {
		$addS->bind_param("iii", $_SESSION['user_id'], $gameID, $score);
		if (mysqli_stmt_execute($addS))
		{
			echo true;
		} else {
			return false;
		}
	}
} else {
	return false;
}

?>
