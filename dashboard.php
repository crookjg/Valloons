<?php

session_start();

include('config.php');

$gamename = $topic = NULL;
$game_name_err = $badInsert = "";

if (isset($_POST['create-game']) && !empty($_POST['create-game']))
{
	if (isset($_POST['game-name']) && !empty($_POST['game-name'])) {
		$gamename = mysqli_real_escape_string($link, $_POST['game-name']);
	} else {
		$game_name_err = "The game requires a name.";
	}

	if (isset($_POST['topic']) && !empty($_POST['topic'])) {
		$topic = mysqli_real_escape_string($link, $_POST['topic']);
	} else {
		$topic = NULL;
	}

	if (isset($gamename) && empty($game_name_err)) {
		$insertGame = "INSERT INTO game(game_name, topic, teacher_id) VALUES(?, ?, ?);";
		echo $gamename . ' ' . $numq . ' ' . $_SESSION['user_id'];
		if ($insertGPrep = mysqli_prepare($link, $insertGame)) {
			$insertGPrep->bind_param("ssi", $gamename, $topic, $_SESSION['user_id']);
			if (mysqli_stmt_execute($insertGPrep)) {
				$getGameID = "SELECT game_id FROM game WHERE game_name=? AND topic=? AND teacher_id=?;";
				if ($getGID = mysqli_prepare($link, $getGameID)) {
					$getGID->bind_param("ssi", $gamename, $topic, $_SESSION['user_id']);
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
						<th class="col">Topic</th>
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
						<td class="col">' . stripslashes($row['game_name']) . '</td>
						<td class="col">' . stripslashes($row['topic']) . '</td>
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
					<label for="topic" class="form-label">Topic</label>
					<input type="text" name="topic" class="form-control" id="topic" value="' . $topic . '">
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

function show_games_played() {
	global $link;

	echo ('
			<div class="row">
				<h2>Games Played</h2>
			</div>
			<table class="table table-striped table-bordered table-hover">
				<thead>
					<tr>
						<th class="col">Name</th>
						<th class="col">Topic</th>
						<th class="col">Score</th>
						<th class="col">Date Finished</th>
					</tr>
				</thead>
				<tbody>
	');

	$getGamesPlayed = "SELECT sg.game_id, sg.score, sg.date_finished, g.game_name, g.topic FROM student_game AS sg JOIN game AS g ON sg.game_id=g.game_id WHERE sg.student_id=? ORDER BY sg.date_finished DESC;";
	if ($getGP = mysqli_prepare($link, $getGamesPlayed)) {
		$getGP->bind_param("i", $_SESSION['user_id']);
		if (mysqli_stmt_execute($getGP)) {
			$games = mysqli_stmt_get_result($getGP);
			while ($row = mysqli_fetch_assoc($games)) {
				echo ('
					<tr>
						<td>' . stripslashes($row['game_name']) . '</td>
						<td>' . stripslashes($row['topic']) . '</td>
						<td>' . $row['score'] . '</td>
						<td>' . $row['date_finished'] . '</td>
					</tr>
					');
			}
		} else	echo('<tr></tr>');
	}
	echo ('
				</tbody>
			</table>
	');
}

function show_public_games() {
	global $link;

	echo ('
			<div class="row">
				<div class="col">
					<h2>Playable Games</h2>
				</div>
				<div class="col float-right">
					<input type="text" id="game-search" onkeyup="searchGames()" placeholder="Search for a Game..." class="form-control">
				</div>
			</div>
			<table class="table table-striped table-bordered table-hover" id="games">
				<thead>
					<tr>
						<th class="col">Name</th>
						<th class="col">Topic</th>
						<th class="col"></th>
					</tr>
				</thead>
				<tbody>
	');
	$getGames = "SELECT * FROM game WHERE published=1 ORDER BY date_created DESC;";
	if ($getGamesPrep = mysqli_prepare($link, $getGames)) {
		if (mysqli_stmt_execute($getGamesPrep)) {
			$games = mysqli_stmt_get_result($getGamesPrep);
			while ($row = mysqli_fetch_assoc($games)) {
				echo ('
					<tr>
						<td class="col">' . stripslashes($row['game_name']) . '</td>
						<td class="col">' . stripslashes($row['topic']) . '</td>
						<td class="col">
							<form action="game.php" method="POST">
								<input type="hidden" name="game-id" value="' . $row['game_id'] . '">
								<input type="submit" class="btn btn-primary" name="play-game" value="Play">
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
		<h1 class="center-align">Welcome, <?php echo($_SESSION['username']); ?></h1>

<?php
if ($_SESSION['ut_id'] == 1) {
	echo('<article>');
	approve_teachers();
	echo('</article>');
}

if ($_SESSION['loggedin'] == true) {
	if (($_SESSION['ut_id'] == 2 && $_SESSION['verified'] == 1) || $_SESSION['ut_id'] == 1)
	{
		echo('
			<div class="row ex-space">
				<div class="col">
		');
		show_games_made();	
			
		echo('
				</div>
				<div class="col">
		');
		new_game_form();
		echo('
				</div>
			</div>		
		');	
		echo('
			<div class="row ex-space">
				<div class="col">
		');
		show_games_played();
		echo('
				</div>
				<div class="col">
		');
		show_public_games();
		echo('
				</div>
			</div>
		');
	} else if ($_SESSION['ut_id'] == 2 && $_SESSION['verified'] == 0) {
		not_verified();
	} else {
		echo('<article class="left-half">');
		show_games_played();
		echo('</article>');
		echo('<aside class="right-half">');
		show_public_games();
		echo('</aside>');
	}
} else {
	header("location: index.php");
}
?>
	</div>
</body>

<script>
function searchGames() {
	var input, filter, table, tr, td, i, j, txtVal;
	input = document.getElementById('game-search');
	filter = input.value.toUpperCase();
	table = document.getElementById('games');
	tr = table.getElementsByTagName('tr');

	for (i = 0; i < tr.length; i++) {
		td = tr[i].getElementsByTagName('td');
		for (j = 0; j < td.length; j++) {
			if (td[j].innerHTML.toUpperCase().indexOf(filter) > -1) {
				tr[i].style.display = "";
				break;
			} else {
				tr[i].style.display = "none";
			}
		}
	}
}
</script>
</html>
