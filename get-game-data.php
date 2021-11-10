<?php

session_start();

include('config.php');

$gameID = isset($_POST['gameid']) ? $_POST['gameid'] : NULL;

if ($gameID != NULL) {
	$json = array('gameid' => $gameID, 'questions' => array());
	$getQuestionsSQL = "SELECT * FROM question WHERE game_id=? ORDER BY question_id ASC;";
	if ($getQ = mysqli_prepare($link, $getQuestionsSQL))
	{
		$getQ->bind_param("i", $gameID);
		if (mysqli_stmt_execute($getQ))
		{
			$questions = mysqli_stmt_get_result($getQ);
			while ($qRow = mysqli_fetch_assoc($questions))
			{
				$choices = array();
				$getAnswersSQL = "SELECT * FROM question_answers WHERE question_id=?;";
				if ($getA = mysqli_prepare($link, $getAnswersSQL)) {
					$getA->bind_param("i", $qRow['question_id']);
					if (mysqli_stmt_execute($getA)) {
						$answers = mysqli_stmt_get_result($getA);
						while ($aRow = mysqli_fetch_assoc($answers)) {
							array_push($choices, array(
								'answer' => $aRow['answer'],
								'active' => $aRow['active'],
								'correct' => $aRow['correct'],
							));
						}
					}
				}

				array_push($json['questions'], array(
					'question' => $qRow['question'],
					'question_id' => $qRow['question_id'],
					'active' => $qRow['active'],
					'points' => $qRow['points'],
					'choices' => $choices,
				));
			}
		}
	}
	echo json_encode($json);
} else {
	$json = array(
		'message' => 'No Game ID'
	);
	echo json_encode($json);
	flush();
}

?>
