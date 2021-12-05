<?php

session_start();

include('config.php');

if ($_SESSION['loggedin'] != true)
	header("location: index.php");

$gameID = $gameName = $topic = $teacherID = $dateCreated = $published = NULL;
$game_err = $q_err = $a_err = $post_err = $post_sucs = NULL;

if ((isset($_POST['edit-game']) && !empty($_POST['edit-game'])) || isset($_SESSION['game_id']) || isset($gameID)) {
	if (isset($_POST['edit-game']) && !empty($_POST['edit-game'])) {
		$gameID = $_POST['game-id'];
	} else if (isset($_SESSION['game_id'])) {
		$gameID = $_SESSION['game_id'];
		$_SESSION['game_id'] = NULL;
	} else if (isset($gameID)) {
		$gameID = $gameID;
	} else {
		$gameID = NULL;
		header("location: dashboard.php");
	}

	getGameData();
}

function getGameData() {
	global $link, $gameID, $gameName, $topic, $teacherID, $dateCreated, $published;

	$getGameData = "SELECT * FROM game WHERE game_id=?;";
	if ($getGD = mysqli_prepare($link, $getGameData)) {
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

/*** Update Queries ***/

if (isset($_POST['update-game']) && !empty($_POST['update-game'])) {
	if (isset($_POST['game-id']) && !empty($_POST['game-id'])) {
		$gameID = $_POST['game-id'];
	} else {
		header("location: dashboard.php");
	}

	if (isset($_POST['game-name']) && !empty($_POST['game-name'])) {
		$gameName = mysqli_real_escape_string($link, $_POST['game-name']);
	} else {
		$game_err = "Game name is required.";
	}

	if (isset($_POST['topic']) && !empty($_POST['topic'])) {
		$topic = mysqli_real_escape_string($link, $_POST['topic']);
	} else {
		$topic = NULL;
	}

	if (isset($_POST['published']) && !empty($_POST['published'])) {
		$published = 1;
	} else {
		$published = 0;
	}

	$updateGame = "UPDATE game SET game_name=?, topic=?, published=? WHERE game_id=?;";
	if (isset($gameID) && isset($gameName) && empty($game_err)) {
		if ($uG = mysqli_prepare($link, $updateGame)) {
			$uG->bind_param("ssii", $gameName, $topic, $published, $gameID);
			if (mysqli_stmt_execute($uG)) {
				$post_sucs = "Game successfully updated.";
			} else {
				$post_err = "Game could not be updated. Please try again later.";
			}
		} else {
			$post_err = "Something went wrong. Please contact the system administrator.";
		}
	} else {
		$post_err = "Your form is missing data.";
	}
	$gameID = $gameID;
	getGameData();
}
if (isset($_POST['update-question']) && !empty($_POST['update-question'])) {
	if (isset($_POST['question-id']) && !empty($_POST['question-id'])) {
		$questionID = $_POST['question-id'];
	} else {
		$post_err = "Your form is missing data. Please try again later.";
	}

	if (isset($_POST['question']) && !empty($_POST['question'])) {
		$question = mysqli_real_escape_string($link, $_POST['question']);
	} else {
		$q_err = "A question is required.";
	}

	if (isset($_POST['active']) && !empty($_POST['active'])) {
		$q_active = 1;
	} else {
		$q_active = 0;
	}

	$updateQuestion = "UPDATE question SET question=?, active=? WHERE question_id=?;";
	if (isset($question) && isset($questionID) && isset($q_active) && empty($q_err)) {
		if ($uQ = mysqli_prepare($link, $updateQuestion)) {
			$uQ->bind_param("sii", $question, $q_active, $questionID);
			if (mysqli_stmt_execute($uQ)) {
				$post_sucs = "Question successfully updated.";
			} else {
				$post_err = "Question could not be updated. Please try again later.";
			}
		} else {
			$post_err = "Something went wrong with updating the question. Please contact the system administrator.";
		}
	} else {
		$post_err = "Your question is missing data.";
	}
	$gameID = $_POST['game-id'];
	getGameData();
}
if (isset($_POST['update-answer']) && !empty($_POST['update-answer'])) {
	if (isset($_POST['answer-id']) && !empty($_POST['answer-id'])) {
		$answerID = $_POST['answer-id'];
	} else {
		$post_err = "Your form is missing data. Please try again later.";
	}

	if (isset($_POST['question-id']) && !empty($_POST['question-id'])) {
		$questionID = $_POST['question-id'];
	} else {
		$post_err = "Your form is missing data. Please try again later.";
	}

	if (isset($_POST['game-id']) && !empty($_POST['game-id'])) {
		$gameID = $_POST['game-id'];
	} else {
		$post_err = "Your form is missing data. Please try again later.";
	}

	if (strcmp($_POST['answer'], '0') == 0 && empty($_POST['answer'])) {
		$answer = addslashes($_POST['answer']);
	} else if (isset($_POST['answer']) && !empty($_POST['answer'])) {
		$answer = mysqli_real_escape_string($link, $_POST['answer']);
	} else {
		$a_err = "Must provide an answer.";
	}

	if (isset($_POST['correct']) && !empty($_POST['correct'])) {
		$a_correct = 1;
	} else {
		$a_correct = 0;
	}

	if (isset($_POST['active']) && !empty($_POST['active'])) {
		$a_active = 1;
	} else {
		$a_active = 0;
	}

	$updateAns = "UPDATE question_answers SET answer=?, active=?, correct=? WHERE answer_id=?;";
	if (isset($answerID) && isset($answer) && empty($a_err) && isset($a_correct) && isset($a_active)) {
		if ($uA = mysqli_prepare($link, $updateAns)) {
			$uA->bind_param("siii", $answer, $a_active, $a_correct, $answerID);
			if (mysqli_stmt_execute($uA)) {
				$post_sucs = "Answer successfully updated.";
			} else {
				$post_err = "Answer could not be updated. Please try again later.";
			}
		} else {
			$post_err = "Something went wrong. Please contact the system administrator.";
		}
	} else {
		$post_err = "Your form is missing data and/or has errors.";
	}
	$gameID = $gameID;
	getGameData();
}

/*** Creation Queries ***/
if (isset($_POST['add-question']) && !empty($_POST['add-question'])) {
	if (isset($_POST['game-id']) && !empty($_POST['game-id'])) {
		$gameID = $_POST['game-id'];
	} else {
		$post_err = "Your form is missing data. Please try again later.";
	}

	if (isset($_POST['question']) && !empty($_POST['question'])) {
		$question = mysqli_real_escape_string($link, $_POST['question']);
	} else {
		$q_err = "Your form is missing a question.";
	}

	if (isset($_POST['active']) && !empty($_POST['active'])) {
		$q_active = 1;
	} else {
		$q_active = 0;
	}

	$addQuestion = "INSERT INTO question(game_id, question, active) VALUES (?, ?, ?);";
	if (isset($gameID) && isset($question) && isset($q_active) && empty($post_err) && empty($q_err)) {
		if ($aQ = mysqli_prepare($link, $addQuestion)) {
			$aQ->bind_param("isi", $gameID, $question, $q_active);
			if (mysqli_stmt_execute($aQ)) {
				$post_sucs = "Question added to the game.";
			} else {
				$post_err = "The question could not be added. Please try again later.";
			}
		} else {
			$post_err = "Something went wrong. Please contact the system administrator.";
		}
	} else {
		$post_err = "Your form is missing data and/or contains errors.";
	}
	$gameID = $gameID;
	getGameData();
}
if (isset($_POST['create-answer']) && !empty($_POST['create-answer'])) {
	if (isset($_POST['game-id']) && !empty($_POST['game-id'])) {
		$gameID = $_POST['game-id'];
	} else {
		$post_err = "Your form is missing data. Please try again later.";
	}

	if (isset($_POST['question-id']) && !empty($_POST['question-id'])) {
		$questionID = $_POST['question-id'];
	} else {
		$post_err = "Your form is missing data. Please try again later.";
	}

	if (strcmp($_POST['answer'], '0') == 0 && empty($_POST['answer'])) {
		$answer = addslashes($_POST['answer']);
	} else if (isset($_POST['answer']) && !empty($_POST['answer'])) {
		$answer = mysqli_real_escape_string($link, $_POST['answer']);
	} else {
		$a_err = "Must provide an answer.";
	}

	if (isset($_POST['correct']) && !empty($_POST['correct'])) {
		$a_correct = 1;
	} else {
		$a_correct = 0;
	}

	if (isset($_POST['active']) && !empty($_POST['active'])) {
		$a_active = 1;
	} else {
		$a_active = 0;
	}

	$createAnswer = "INSERT INTO question_answers(question_id, answer, active, correct) VALUES (?, ?, ?, ?);";
	if (isset($questionID) && isset($answer) && isset($a_correct) && isset($a_active) && empty($post_err) && empty($a_err)) {
		if ($cA = mysqli_prepare($link, $createAnswer)) {
			$cA->bind_param("isii", $questionID, $answer, $a_active, $a_correct);
			if (mysqli_stmt_execute($cA)) {
				$post_sucs = "Successfully added answer.";
			} else {
				$post_err = "The answer could not be added to the question. Please try again later.";
			}
		} else {
			$post_err = "Something went wrong with adding an answer. Please contact the system administrator.";
		}
	} else {
		$post_err = "Your answer form is missing data and/or contains errors.";
	}
	$gameID = $gameID;
	getGameData();
}

/*** Delete Queries ***/
if (isset($_POST['delete-game']) && !empty($_POST['delete-game'])) {
	if (isset($_POST['game-id']) && !empty($_POST['game-id'])) {
		$gameID = $_POST['game-id'];
	} else {
		$post_err = "Your form is missing data.";
	}

	$deleteGame = "DELETE FROM game WHERE game_id=?;";
	if ($dG = mysqli_prepare($link, $deleteGame)) {
		$dG->bind_param("i", $gameID);
		if (mysqli_stmt_execute($dG)) {
			header("location: dashboard.php");
			exit;
		} else {
			$post_err = "Could not delete game data from database.";
		}
	} else {
		$post_err = "Something went wrong. Please try again later.";
	}
}
if (isset($_POST['delete-question']) && !empty($_POST['delete-question'])) {
	if (isset($_POST['game-id']) && !empty($_POST['game-id']))
	{
		$gameID = $_POST['game-id'];
	} else {
		$post_err = "Your form is missing data.";
	}

	if (isset($_POST['question-id']) && !empty($_POST['question-id'])) {
		$questionID = $_POST['question-id'];
	} else {
		$post_err = "Your form is missing data. Please try again later.";
	}

	$deleteQuestion = "DELETE FROM question WHERE question_id=? AND game_id=?;";
	if ($dQ = mysqli_prepare($link, $deleteQuestion)) {
		$dQ->bind_param("ii", $questionID, $gameID);
		if (mysqli_stmt_execute($dQ)) {
			$post_sucs = "Successfully removed question from game.";
		} else {
			$post_err = "Could not remove question from game.";
		}
	} else {
		$post_err = "Could not remove question. Please try again later.";
	}
	$gameID = $gameID;
	getGameData();
}
if (isset($_POST['delete-answer']) && !empty($_POST['delete-answer'])) {
	if (isset($_POST['game-id']) && !empty($_POST['game-id']))
	{
		$gameID = $_POST['game-id'];
	} else {
		$post_err = "Your form is missing data.";
	}

	if (isset($_POST['question-id']) && !empty($_POST['question-id'])) {
		$questionID = $_POST['question-id'];
	} else {
		$post_err = "Your form is missing data. Please try again later.";
	}

	if (isset($_POST['answer-id']) && !empty($_POST['answer-id'])) {
		$answerID = $_POST['answer-id'];
	} else {
		$post_err = "Your form is missing the necessary data.";
	}

	$deleteAns = "DELETE FROM question_answers WHERE answer_id=? AND question_id=?;";
	if ($dA = mysqli_prepare($link, $deleteAns)) {
		$dA->bind_param("ii", $answerID, $questionID);
		if (mysqli_stmt_execute($dA)) {
			$post_sucs = "Successfully removed answer from question.";
		} else {
			$post_err = "Could not remove answer from question.";
		}
	} else {
		$post_err = "Could not remove answer. Please try again later.";
	}
	$gameID = $gameID;
	getGameData();
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
	<div class="container ex-space">
		<span class="valid-feedback <?php if (!empty($post_sucs)) echo ' show-feedback'; ?>"><?php if (!empty($post_sucs)) echo $post_sucs; ?></span>
		<span class="invalid-feedback <?php if (!empty($post_err)) echo ' show-feedback'; ?>"><?php if (!empty($post_err)) echo $post_err; ?></span>
		<div>
			<div class="row">
				<div class="col">
					<h2>Game Information</h2>
				</div>
				<div class="col float-end">
					<form method="POST" action="game.php">
						<input type="hidden" name="game-id" value="<?php echo $gameID; ?>">
						<input type="submit" name="play-game" class="btn btn-primary float-end" value="Play">
					</form>
				</div>
			</div>
			<table class="table table-striped table-bordered table-hover">
				<thead>
					<tr>
						<th class="col">Name</th>
						<th class="col">Topic</th>
						<th class="col">Created On</th>
						<th class="col">Published</th>
						<th class="col"></th>
						<th class="col"></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<form method="POST">
							<input type="hidden" name="game-id" value="<?php echo $gameID; ?>">
							<td><input type="text" name="game-name" class="form-control" value="<?php echo $gameName; ?>"></td>
							<td><input type="text" name="topic" class="form-control" value="<?php echo $topic; ?>"></td>
							<td><?php echo $dateCreated; ?></td>
							<td>
<?php
	echo ('<input type="checkbox" name="published" class="form-check form-check-input"');
	if ($published == 1) {
		echo (' checked');
	}
	echo ('>');
?>
							</td>
							<td>
								<input type="submit" name="update-game" value="Update" class="btn btn-primary">
							</td>
							<td>
								<input type="hidden" name="game-id" value="<?php echo $gameID; ?>">
								<input type="submit" name="delete-game" value="Delete" class="btn btn-primary">
							</td>
						</form>
					<tr>
				</tbody>
			</table>
		</div>
		<hr>
		<div class="row ex-space">
			<div class="col-md-10">
				<h2>Questions</h2>
				<div class="accordion">
<?php
	$getQuestionsSQL = "SELECT * FROM question WHERE game_id=? ORDER BY question_id ASC;";
	if ($getQ = mysqli_prepare($link, $getQuestionsSQL))
	{
		$getQ->bind_param("i", $gameID);
		if (mysqli_stmt_execute($getQ))
		{
			$questions = mysqli_stmt_get_result($getQ);
			while ($qRow = mysqli_fetch_assoc($questions))
			{
				echo ('
				<div class="accordion-item">
					<h3 class="accordion-header" id="' . $qRow['question_id'] . '">
					<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-' . $qRow['question_id'] . '" aria-expanded="true" aria-controls="collapse-' . $qRow['question_id'] . '">' . $qRow['question'] . '</button>
					</h3>
					<div id="collapse-' . $qRow['question_id'] . '" class="accordion-collapse collapse" aria-labelledby="collapse-' . $qRow['question_id'] . '">
						<div class="accordion-body">
							<h3>Question Information</h3>
							<table class="table table-striped table-bordered table-hover">
								<thead>
									<tr>
										<th class="col">Question</th>
										<th class="col">Active?</th>
										<th class="col"></th>
										<th class="col"></th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<form method="POST">
											<td><input type="text" name="question" class="form-control" value="' . stripslashes($qRow['question']) . '" required></td>
											<td><input type="checkbox" name="active" class="form-check form-check-input"
				');
				if ($qRow['active'] == 1) {
					echo (' checked');
				}
				echo ('							></td>
											<td>
												<input type="hidden" name="game-id" value="' . $gameID . '">
												<input type="hidden" name="question-id" value="' . $qRow['question_id'] . '">
												<input type="submit" name="update-question" class="btn btn-primary" value="Update">
											</td>
											<td><input type="submit" name="delete-question" class="btn btn-primary" value="Delete"></td>
										</form>
									</tr>
								</tbody>
							</table>
							<h3>Answers</h3>
							<span class="invalid-feedback" aria-describedby="answer">' . $a_err . '</span>
							<table class="table table-striped table-bordered table-hover">
								<thead>
									<tr>
										<th class="col">Answer</th>
										<th class="col">Correct?</th>
										<th class="col">Active?</th>
										<th class="col"></th>
										<th class="col"></th>
									</tr>
								</thead>
								<tbody>
				');

				$getAnswersSQL = "SELECT * FROM question_answers WHERE question_id=?;";
				if ($getA = mysqli_prepare($link, $getAnswersSQL)) {
					$getA->bind_param("i", $qRow['question_id']);
					if (mysqli_stmt_execute($getA)) {
						$answers = mysqli_stmt_get_result($getA);
						while ($aRow = mysqli_fetch_assoc($answers)) {
							echo ('
									<form method="POST">
										<tr>
											<td><input type="text" id="answer" name="answer" class="form-control');
							if (!empty($a_err)) {
								echo ' is-invalid';
							}
							echo ('				" value="' . stripslashes($aRow['answer']) . '"></td>
											<td><input type="checkbox" name="correct" class="form-check form-check-input"
							');
							if ($aRow['correct'] == 1) {
								echo ' checked';
							}
							echo ('></td><td><input type="checkbox" name="active" class="form-check form-check-input"');
							if ($aRow['active'] == 1) {
								echo ' checked';
							}
							echo ('></td>
											<td>
												<input type="hidden" name="answer-id" value="' . $aRow['answer_id'] . '">
												<input type="hidden" name="question-id" value="' . $qRow['question_id'] . '">
												<input type="hidden" name="game-id" value="' . $gameID . '">
												<input type="submit" class="btn btn-primary" name="update-answer" value="Update">
											</td>
											<td><input type="submit" class="btn btn-primary" name="delete-answer" value="Delete"></td>
										</tr>
									</form>
							');
						}
					}
				}
				echo('				</tbody>
							</table>
							<h3>Add Answer</h3>
							<form method="POST">
								<div class="row">
									<div class="col-md-3">
										<label for="answer">Answer Text</label>
										<input type="text" name="answer" class="form-control">
									</div>
									<div class="col-md-2">
										<label for="correct">Correct?</label>
										<input type="checkbox" name="correct" class="form-check form-check-input">
									</div>
									<div class="col-md-2">
										<label for="active">Active?</label>
										<input type="checkbox" name="active" class="form-check form-check-input">
									</div>
									<div class="col-md-2 mt-3">
										<input type="hidden" name="game-id" value="' . $gameID . '">
										<input type="hidden" name="question-id" value="' . $qRow['question_id'] . '">
										<input type="submit" name="create-answer" class="btn btn-primary" value="Add">
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>		
				');
			}
		}
	}
?>
				</div>
			</div>
			<div class="col-md-2">
				<h2>Add Question</h2>
				<form method="POST">
					<div class="row mb-3">
						<div class="col">
							<label for="question">Question</label>
							<input type="text" name="question" class="form-control <?php if (!empty($q_err)) echo 'is-invalid'; ?>">
							<span class="invalid-feedback"><?php if (!empty($q_err)) echo $q_err; ?></span>
						</div>
					</div>
					<div class="row mb-3">
						<div class="col">
							<label for="active">Active?</label>
							<input type="checkbox" name="active" class="form-check form-check-input">
						</div>
					</div>
					<div class="row mb-3">
						<div class="col">
							<input type="hidden" name="game-id" value="<?php echo $gameID; ?>">
							<input type="submit" name="add-question" class="btn btn-primary" value="Add">
						</div>
					</div>
				</form>
			</div>
		</div>
		<hr>
		<div class="row ex-space">
			<div class="col-md-8">
				<h2>Game Results</h2>
			</div>
			<div class="col-md-4">
				<input type="text" id="email-search" onkeyup="searchEmails()" placeholder="Search By Email..." class="form-control">
			</div>
		</div>
		<div class="row">
			<table class="table table-striped table-bordered table-hover" id="game-results">
				<thead>
					<tr>
						<th class="col">First Name</th>
						<th class="col">Last Name</th>
						<th class="col">Email</th>
						<th class="col">Score</th>
						<th class="col">Date Finished</th>
					</tr>
				</thead>
				<tbody>
<?php
	$getGameResults = "SELECT sg.score, sg.date_finished, u.first_name, u.last_name, u.email FROM student_game sg JOIN user u ON sg.student_id=u.user_id WHERE sg.game_id=? ORDER BY sg.date_finished DESC;";
	if ($getGR = mysqli_prepare($link, $getGameResults)) {
		$getGR->bind_param("i", $gameID);
		if (mysqli_stmt_execute($getGR)) {
			$res = mysqli_stmt_get_result($getGR);
			while ($row = mysqli_fetch_assoc($res)) {
				echo ('
					<tr>
						<td>' . $row['first_name'] . '</td>
						<td>' . $row['last_name'] . '</td>
						<td>' . $row['email'] . '</td>
						<td>' . $row['score'] . '</td>
						<td>' . $row['date_finished'] . '</td>
					</tr>
					');
			}
		} else	echo('<tr></tr>');
	} else echo('<tr>No Data</tr>');
?>
				</tbody>
			</table>
		</div>
	</div>
</body>

<script>
function searchEmails() {
	var input, filter, table, tr, td, i, j, txtVal;
	input = document.getElementById('email-search');
	filter = input.value.toUpperCase();
	table = document.getElementById('game-results');
	tr = table.getElementsByTagName('tr');

	for (i = 0; i < tr.length; i++) {
		td = tr[i].getElementsByTagName('td')[2];
		if (td) {
			txtVal = td.textContent || td.innerText;
			if (txtVal.toUpperCase().indexOf(filter) > -1) {
				tr[i].style.display = "";
			} else {
				tr[i].style.display = "none";
			}
		}
	}
}
</script>


</html>
