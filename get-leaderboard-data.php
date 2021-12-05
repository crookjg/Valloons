<?php

session_start();

include('config.php');

$gameID = isset($_POST['gameid']) ? $_POST['gameid'] : NULL;

if ($gameID != NULL) {
	$json = array("leaders" => array());
	$getLeadersSQL = "SELECT p.username FROM student_game as s JOIN user as p ON s.student_id=p.user_id WHERE s.game_id=? ORDER BY s.score DESC;";
	
	if ($getL = mysqli_prepare($link, $getLeadersSQL))
	{
		$getL->bind_param("i", $gameID);
		if (mysqli_stmt_execute($getL))
		{
			$leaders = mysqli_stmt_get_result($getL);
			while ($lRow = mysqli_fetch_assoc($leaders))
			{
				array_push($json['leaders'], $lRow['username']);
			}
		}
	}
	$leaders = array_values(array_unique($json['leaders'], SORT_REGULAR));
	echo json_encode($leaders);
} else {
	$json = array(
		'message' => 'No Game ID'
	);
	echo json_encode($json);
	flush();
}

?>
