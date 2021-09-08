<?php

session_start();

include 'config.php';

if (!empty($_POST['login']) && isset($_POST['login'])) {
	$username = $password = NULL;
	$username_err = $password_err = $sql_err = "";
	
	if (!empty($_POST['username']) && isset($_POST['username'])) {
		$username = mysqli_real_escape_string($link, $_POST['username']);
	} else {
		$username_err = "Please enter a valid username.";
	}
	
	if (!empty($_POST['auth_string']) && isset($_POST['auth_string'])) {
		$password = mysqli_real_escape_string($link, $_POST['auth_string']);
	} else {
		$password_err = "Please enter a password.";
	}
	
	if (empty($username_err) && empty($password_err) && isset($username) && isset($password)) {
		$login_sql = "SELECT user_id, first_name, last_name, email, username, ut_id FROM user WHERE username=? AND authentication=?;";
		if ($login = $link->prepare($login_sql)) {
			$login->bind_param("ss", $username, $password);
		if ($login->execute()) {
				if ($login->num_rows == 1) {
					$login_res = $login->bind_result($userId, $first_name, $last_name, $email, $username, $ut_id);
				} else {
					$sql_err = "Too many results. Please contact the system administrator.";
				}
			} else {
				$sql_err = "Something went wrong. Please try again later.";
			}
		} else {
			$sql_err = "Something went wrong. Please contact the system administrator.";
		}
	} else {
		$sql_err = "Your form is missing data. Please try again.";
	}
}

if (!empty($_POST['register']) && isset($_POST['register'])) {

}

?>

<!DOCTYPE HTML>
<html lang="en">
<head>
	<title>Vallons - Login</title>
	<meta charset="utf-8">
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
		<div class="center-pills">
			<ul class="nav nav-pills">
				<li class="pill-item"><a class="pill-link active" id="pill-link-one" data-bs-toggle="pill" href="#login">Login</a></li>
				<li class="pill-item"><a class="pill-link" id="pill-link-two" data-bs-toggle="pill" href="#register">Register</a></li>
			</ul>
		</div>
		<div class="tab-content">
			<div class="row tab-pane active" id="login">
				<div class="col-md-6 login-form">
					<h4 class="center-align">Login</h4>
					<form method="POST" autocomplete="off" action="login.php">
						<span class="invalid-feedback"><?php if (!empty($sql_err)) echo $sql_err; ?></span>
						<div class="mb-3">
							<label class="form-label" for="username">Username</label>
							<input type="text" id="username" name="username" class="form-control <?php if (!empty($username_err)) echo 'is-invalid'; ?>" placeholder="Username" autocomplete="off" required>
							<span class="invalid-feedback"><?php if (!empty($username_err)) echo $username_err; ?></span>
						</div>
						<div class="mb-3">
							<label class="form-label" for="password">Password</label>
							<input type="password" id="password" name="auth_string" class="form-control <?php if (!empty($password_err)) echo 'is-invalid'; ?>" autocomplete="off" required>
							<span class="invalid-feedback"><?php if (!empty($password_err)) echo $password_err; ?></span>
						</div>
						<div class="btn-center">
							<input type="submit" class="btn btn-primary" name="login" value="Login">
						</div>
					</form>
				</div>
			</div>
			<div class="row tab-pane" id="register">
				<div class="col-md-8 login-form">
					<h4 class="center-align">Register</h4>
					<form method="POST" autocomplete="off" action="login.php">
						<span class="invalid-feedback"><?php if (!empty($sql_err)) echo $sql_err; ?></span>
						<div class="row">
							<div class="col-sm">
								<label class="form-label" for="first-name">First Name</label>
								<input type="text" id="first-name" name="first-name" class="form-control <?php if (!empty($firstname_err)) echo 'is-invalid'; ?>" placeholder="First Name" autocomplete="off" required>
								<span class="invalid-feedback"><?php if (!empty($firstname_err)) echo $firstname_err; ?></span>
							</div>
							<div class="col-sm">
								<label class="form-label" for="last-name">First Name</label>
								<input type="text" id="last-name" name="last-name" class="form-control <?php if (!empty($lastname_err)) echo 'is-invalid'; ?>" placeholder="Last Name" autocomplete="off" required>
								<span class="invalid-feedback"><?php if (!empty($lastname_err)) echo $lastname_err; ?></span>
							</div>
						</div>
						
					</form>
				</div>
			</div>
		</div>
	</div>			
</body>
</html>
