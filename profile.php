<?php

session_start();

include 'config.php';

if ($_SESSION['loggedin'] != true)
	header("location: index.php");

$firstname = $_SESSION['first_name'];
$lastname = $_SESSION['last_name'];
$uname = $_SESSION['username'];
$email = $_SESSION['email'];
if ($_SESSION['ut_id'] == 1)
	$userType = "Administrator";
else if ($_SESSION['ut_id'] == 2)
	$userType = "Teacher";
else
	$userType = "Student";

if (!empty($_POST['update']) && isset($_POST['update'])) {
	$upd_err = "";

	$prep = "UPDATE user SET first_name=?, last_name=?, email=?, ";

	if (!empty($_POST['first-name']) && isset($_POST['first-name'])) {
		$firstname = mysqli_real_escape_string($link, $_POST['first-name']);
	}

	if (!empty($_POST['last-name']) && isset($_POST['last-name'])) {
		$lastname = mysqli_real_escape_string($link, $_POST['last-name']);
	}

	if (!empty($_POST['email']) && isset($_POST['email'])) {
		$email = mysqli_real_escape_string($link, $_POST['email']);
	} else {
		$email_err = "Please enter a valid school email address.";
	}

	if (!empty($_POST['password']) && isset($_POST['password'])) {
		$password = mysqli_real_escape_string($link, $_POST['password']);
		$auth_string = password_hash($password, PASSWORD_BCRYPT);
		$prep .= "authentication=?, ";
	}

	$prep .= "ut_id=? WHERE user_id=?;";

	if ($prepped = mysqli_prepare($link, $prep)) {
		if (!empty($password)) {
			$prepped->bind_param("ssssii", $firstname, $lastname, $email, $auth_string, $_SESSION['ut_id'], $_SESSION['user_id']);
		} else {
			$prepped->bind_param("sssii", $firstname, $lastname, $email, $_SESSION['ut_id'], $_SESSION['user_id']);
		}

		if (mysqli_stmt_execute($prepped)) {
			$upd_err = "Information updated successfully.";
			$_SESSION['first_name'] = $firstname;
			$_SESSION['last_name'] = $lastname;
			$_SESSION['email'] = $email;
		} else {
			echo($exec->error);
			$upd_err = "Could not update. Please try again later.";
		}
	} else {
		$upd_err = "Something went wrong. Please try again later.";
	}
}

?>
<!DOCTYPE HTML>
<html>
<head>
<title><?php echo $_SESSION['first_name'] . ' ' . $_SESSION['last_name']; ?>'s Settings</title>
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
	<main class="container ex-space">
		<form method="POST" action="profile.php" class="prof-form">
			<span class="feedback <?php if (!empty($upd_err)) echo 'needed-feedback'; ?>"><?php if (!empty($upd_err)) echo $upd_err; ?></span>
			<div class="row mb-3">
				<div class="col-sm">
					<label class="form-label" for="first-name">First Name</label>
					<input type="text" id="first-name" name="first-name" class="form-control <?php if (!empty($firstname_err)) echo 'is-invalid'; ?>" placeholder="First Name" autocomplete="off" value="<?php echo $firstname; ?>" required>
					<span class="invalid-feedback"><?php if (!empty($firstname_err)) echo $firstname_err; ?></span>
				</div>
			</div>
			<div class="row mb-3">
				<div class="col-sm">
					<label class="form-label" for="last-name">Last Name</label>
					<input type="text" id="last-name" name="last-name" class="form-control <?php if (!empty($lastname_err)) echo 'is-invalid'; ?>" placeholder="last Name" autocomplete="off" value="<?php echo $lastname; ?>" required>
					<span class="invalid-feedback"><?php if (!empty($lastname_err)) echo $lastname_err; ?></span>
				</div>
			</div>
			<div class="row mb-3">
				<div class="col-sm">
					<label class="form-label" for="email">Email</label>
					<input type="email" id="email" name="email" class="form-control <?php if (!empty($email_err)) echo 'is-invalid'; ?>" placeholder=" School Email Address" autocomplete="off" 
						pattern=".+@.+\.edu$" value="<?php echo $email; ?>" oninvalid="this.setCustomValidity('Please enter a school email ending in .edu.')">
					<span class="invalid-feedback"><?php if (!empty($email_err)) echo $email_err; ?></span>

				</div>
			</div>
			<div class="row mb-3">
				<div class="col-sm">
					<label class="form-label" for="username">Username</label>
					<input type="text" id="username" name="username" class="form-control <?php if (!empty($uname_err)) echo 'is-invalid'; ?>" placeholder="Username" autocomplete="off" value="<?php echo $uname; ?>" readonly>
					<span class="invalid-feedback"><?php if (!empty($uname_err)) echo $uname_err; ?></span>

				</div>
			</div>
			<div class="row mb-3">
				<div class="col-sm">
					<label class="form-label" for="password">Password</label>
					<input type="password" id="password" name="password" class="form-control <?php if (!empty($password_err)) echo 'is-invalid'; ?>" placeholder="password" autocomplete="off" 
						pattern="^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$" oninvalid="this.setCustomValidity('Please enter a password containing each of the following: at least one letter, one number, and one special character.')">
					<span class="invalid-feedback"><?php if (!empty($password_err)) echo $password_err; ?></span>
				</div>
			</div>
			<div class="row mb-3">
				<div class="col-sm">
					<label class="form-label" for="acct-type">You are a:</label>
					<input type="text" class="form-control" value="<?php echo $userType; ?>" readonly>
				</div>
			</div>
			<div class="row mb-3">
				<div class="col-sm btn-center">
					<input type="submit" class="btn btn-primary" name="update" value="Update">
				</div>
			</div>
		</form>
	</main>
</body>
</html>
