<?php

session_start();

include('config.php');

$gamename = $numq = NULL;
$game_name_err = $num_q_err = $badInsert = "";

if (isset($_POST['create-game']) && !empty($_POST['create-game']))
{
	if (isset($_POST['game-name']) && !empty($_POST['game-name'])) {
		$gamename = mysqli_real_escape_string($link, $_POST['game-name']);
	} else {
		$game_name_err = "The game requires a name.";
	}

	if (isset($_POST['num-questions']) && !empty($_POST['num-questions'])) {
		$numq = mysqli_real_escape_string($link, $_POST['num-questions']);
	} else {
		$num_q_err = "Please enter the number of questions the game will include.";
	}

	if (isset($gamename) && isset($numq) && empty($game_name_err) && empty($num_q_err)) {
		$insertGame = "INSERT INTO game(game_name, num_questions, teacher_id) VALUES(?, ?, ?);";
		echo $gamename . ' ' . $numq . ' ' . $_SESSION['user_id'];
		if ($insertGPrep = mysqli_prepare($link, $insertGame)) {
			$insertGPrep->bind_param("sii", $gamename, $numq, $_SESSION['user_id']);
			if (mysqli_stmt_execute($insertGPrep)) {
				$getGameID = "SELECT game_id FROM game WHERE game_name=? AND num_questions=? AND teacher_id=?;";
				if ($getGID = mysqli_prepare($link, $getGameID)) {
					$getGID->bind_param("sii", $gamename, $numq, $_SESSION['user_id']);
					if (mysqli_stmt_execute($getGID)) {
						$gameID = mysqli_stmt_get_result($getGID);
						if (mysqli_num_rows($gameID) == 1)
						{
							$_SESSION['game_id'] = mysqli_fetch_assoc($gameID)['game_id'];
							header("location: edit-game.php");
							exit;
						} else {
							$badInsert = "Too many similar games. Please edit the game by clicking the edit button to the left.";
						}
					} else {
						$badInsert = "Similar games exist. Please find your game in the list to the left.";
					}
				} else {
					$badInsert = "Something went wrong. Please find your game in the list to the left.";
				}
			} else {
				$badInsert = "Something went wrong. Please try again later.";
			}
		} else {
			$badInsert = "Something went wrong. Please contact the system administrator.";
		}
	} else {
		$badInsert = "Your form is missing data.";
	}
}
if (isset($_POST['approve-teacher']) && !empty($_POST['approve-teacher'])) {
	$teacherID = $_POST['teacher-id'];
	$updateTeacher = "UPDATE user SET verified=1 WHERE user_id=?;";
	if ($updatePrep = mysqli_prepare($link, $updateTeacher)) {
		$updatePrep->bind_param("i", $teacherID);
		if (mysqli_stmt_execute($updatePrep)) {
			header("Refresh:0");
		} else {
			echo 'Something went wrong.';
		}
	} else {
		echo 'The preparation did not complete.';
	}
}

function show_games_made() {
	global $link;
	echo ('
			<div class="row">
				<h2>Games</h2>
			</div>
			<table class="table table-striped table-bordered table-hover">
				<thead>
					<tr>
						<th class="col">Name</th>
						<th class="col">Questions</th>
						<th class="col">Created</th>
						<th class="col"></th>
					</tr>
				</thead>
				<tbody>
	');
	$getGames = "SELECT * FROM game WHERE teacher_id=? ORDER BY game_id DESC;";
	if ($getGamesPrep = mysqli_prepare($link, $getGames))
	{
		$getGamesPrep->bind_param("i", $_SESSION['user_id']);
		if (mysqli_stmt_execute($getGamesPrep)) {
			$games = mysqli_stmt_get_result($getGamesPrep);
			while ($row = mysqli_fetch_assoc($games)) {
				echo ('
					<tr>
						<td class="col">' . $row['game_name'] . '</td>
						<td class="col">' . $row['num_questions'] . '</td>
						<td class="col">' . $row['date_created'] . '</td>
						<td class="col">
							<form action="edit-game.php" method="POST">
								<input type="hidden" name="game-id" value="' . $row['game_id'] . '">
								<input type="submit" class="btn btn-primary" name="edit-game" value="Edit">
							</form>
						</td>
					</tr>
				');
			}
		}
	}
	echo ('
				</tbody>
			</table>
	');

}

function new_game_form() {
	global $game_name_err, $num_q_err, $badInsert, $gamename, $numq;
	echo('
		<div class="row">
			<h2>Create Game</h2>
		</div>
		<span>' . $badInsert . '</span>
		<form method="POST" action="dashboard.php">
			<div class="row mb-3">
				<div class="col">
					<label for="game-name" class="form-label">Game Name</label>
					<input type="text" name="game-name" class="form-control
	');
	if (!empty($game_name_err)) echo 'is-invalid';
	echo('
					" id="game-name" value="' . $gamename . '" required>
					<span class="invalid-feedback">
	');
	if (!empty($game_name_err)) 
		echo $game_name_err;
	echo('
					</span>
				</div>
				<div class="col">
					<label for="num-questions" class="form-label">Number of Questions</label>
					<input type="number" name="num-questions" class="form-control
	');
	if (!empty($num_q_err)) echo 'is-invalid';
	echo('
					" id="num-questions" min="0" value="' . $numq . '" required>
					<span class="invalid-feedback">
	');
	if (!empty($num_q_err)) 
		echo $num_q_err;
	echo('
					</span>
				</div>
			</div>
			<input type="submit" class="btn btn-primary" name="create-game" value="Create Game">
		</form>
		');
}

function approve_teachers() {
	global $link;

	echo ('
			<div class="row">
				<h2>Teacher Approval</h2>
			</div>
			<table class="table table-striped table-bordered table-hover">
				<thead>
					<tr>
						<th class="col">Name</th>
						<th class="col">Username</th>
						<th class="col">Email</th>
						<th class="col">URL</th>
						<th class="col"></th>
					</tr>
				</thead>
				<tbody>
	');

	$getTeacherList = "SELECT * FROM user WHERE ut_id=2 AND verified=0;";
	if ($getTPrep = mysqli_prepare($link, $getTeacherList)) {
		if (mysqli_stmt_execute($getTPrep)) {
			$teachers = mysqli_stmt_get_result($getTPrep);
			while ($row = mysqli_fetch_assoc($teachers)) {
				echo('
					<tr>
						<td>' . $row['first_name'] . ' ' . $row['last_name'] . '</td>
						<td>' . $row['username'] . '</td>
						<td>' . $row['email'] . '</td>
						<td><a href="' . $row['teacher_url'] . '" target="_blank">' . $row['teacher_url'] . '</a></td>
						<td>
							<form method="POST" action="dashboard.php">
								<input type="hidden" name="teacher-id" value="' . $row['user_id'] . '">
								<input type="submit" name="approve-teacher" class="btn btn-primary" value="Approve">
							</form>
						</td>
					</tr>
				');
			}
		}
	}
	echo('
				</tbody>
			</table>
	');
}

function not_verified() {
	echo '<h2 class="center-align">Sorry, but your account has not been verified yet.</h2>';
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
	<div class="container">
		<h1 class="center-align">Welcome, <?php echo($_SESSION['first_name'] . ' ' . $_SESSION['last_name']); ?></h1>
<?php
if ($_SESSION['ut_id'] == 1) {
	echo('<article>');
	approve_teachers();
	echo('</article>');
}

if (($_SESSION['ut_id'] == 2 && $_SESSION['verified'] == 1) || $_SESSION['ut_id'] == 1)
{
	echo('<article class="left-half">');
	show_games_made();
	echo('</article>');
	echo('<aside class="right-half">');
	new_game_form();
	echo('</aside>');
} else if ($_SESSION['ut_id'] == 2 && $_SESSION['verified'] == 0) {
	not_verified();
} else {
	echo('<article class="left-half">');
	show_games_played();
	echo('</article>');
}
?>
	</div>
</body>
</html>
