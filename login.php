<?php

session_start();

include 'config.php';

$login_err = $reg_err = "";

if (!empty($_POST['login']) && isset($_POST['login'])) {
	$username = $password = NULL;
	$username_err = $password_err =  "";
	
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
		$getPwdSQL = "SELECT user_id, first_name, last_name, email, authentication, ut_id FROM user WHERE username=?;";
		$get_pwd = $link->prepare($getPwdSQL);
		$get_pwd->bind_param("s", $username);
		$get_pwd->execute();
		$get_pwd->bind_result($user_id, $firstname, $lastname, $email, $authentication, $ut_id);
		
		if ($get_pwd->fetch() && password_verify($password, $authentication)) {
			$_SESSION = array();
			$_SESSION['loggedin'] = true;
			$_SESSION['user_id'] = $user_id;
			$_SESSION['name'] = $firstname . " " . $lastname;
			$_SESSION['email'] = $email;
			$_SESSION['username'] = $username;
			$_SESSION['ut_id'] = $ut_id;
			header("location: dashboard.php");
			exit;
		} else {
			$login_err = "Incorrect username or password.";
		}
	} else {
		$login_err = "Something went wrong. Please try again later.";
	}
}

if (!empty($_POST['register']) && isset($_POST['register'])) {
	$firstname = $lastname = $uname = $email = $password = $usertype = NULL;
	$firstname_err = $lastname_err = $uname_err = $email_err = $pwd_err = $usertype_err = "";

	if (!empty($_POST['first-name']) && isset($_POST['first-name'])) {
		$firstname = mysqli_real_escape_string($link, $_POST['first-name']);
	} else {
		$firstname_err = "Please enter your first name.";
	}

	if (!empty($_POST['last-name']) && isset($_POST['last-name'])) {
		$lastname = mysqli_real_escape_string($link, $_POST['last-name']);
	} else {
		$lastname_err = "Please enter your last name.";
	}

	if (!empty($_POST['username']) && isset($_POST['username'])) {
		$uname = mysqli_real_escape_string($link, $_POST['username']);
	} else {
		$uname_err = "Please enter a username.";
	}

	if (!empty($_POST['email']) && isset($_POST['email'])) {
		$email = mysqli_real_escape_string($link, $_POST['email']);
	} else {
		$email_err = "Please enter a valid school email address.";
	}

	if (!empty($_POST['password']) && isset($_POST['password'])) {
		$password = mysqli_real_escape_string($link, $_POST['password']);
		$auth_string = password_hash($password, PASSWORD_BCRYPT);
	} else {
		$pwd_err = "Please enter a password. Passwords must contain at least 8 characters, including 1 number and 1 special character.";
	}

	if (!empty($_POST['acct-type']) && isset($_POST['acct-type'])) {
		$usertype = $_POST['acct-type'];
	} else {
		$usertype_err = "Please select an account type.";
	}

	if (!empty($firstname) && !empty($lastname) && !empty($uname) && !empty($email) && !empty($password) && !empty($usertype)) {
		$register_sql = "INSERT INTO user (first_name, last_name, email, username, authentication, ut_id) VALUES (?, ?, ?, ?, ?, ?);";
		if ($registration = mysqli_prepare($link, $register_sql)) {
			$registration->bind_param("sssssi", $firstname, $lastname, $email, $uname, $auth_string, $usertype);
			if (mysqli_stmt_execute($registration)) {
				$mail_sent = send_verification_email($email, $firstname, $lastname, $uname, $password);
				if ($mail_sent) {
					$login_err = "Registration complete. Login and verify your email address.";
				} else {
					$login_err = "Registration complete. You can login!";
				}
			} else {
				$reg_err = "Something went wrong. Please try again.";
				$uname_err = "That username is taken.";
			}
		} else {
			$reg_err = "Something went wrong. Please contact the system administrator.";
		}
	} else {
		$reg_err = "Your form is missing data. Please try again.";
	}
}

function send_verification_email($email, $firstname, $lastname, $uname, $password) {
	$hash = md5(rand(0,1000));

	$to = $email;
	$subject = "Valloons Verification";
	$message = '
Thanks for signing up! Your account has been created with the following username: ' . $uname . '. 
You can now login after you verify your account.

Please click this link to activate your account:
http://99.182.224.179/verify.php?email=' . $email . '&hash=' . $hash . '

';

	$headers = 'From:accounts@valloons.tk' . "\r\n";
	return mail($to, $subject, $message, $headers);
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
			<li class="pill-item"><a class="pill-link <?php if (empty($reg_err) && empty($firstname_err) && empty($lastname_err) && empty($uname_err) && empty($pwd_err) && empty($ut_err)) echo 'active'; ?>" id="pill-link-one" data-bs-toggle="pill" href="#login">Login</a></li>
			<li class="pill-item"><a class="pill-link <?php if (!empty($reg_err) || !empty($firstname_err) || !empty($lastname_err) || !empty($uname_err) || !empty($pwd_err) || !empty($ut_err)) echo 'active'; ?>" id="pill-link-two" data-bs-toggle="pill" href="#register">Register</a></li>
			</ul>
		</div>
		<div class="tab-content">
			<div class="row tab-pane <?php if (empty($reg_err) && empty($firstname_err) && empty($lastname_err) && empty($uname_err) && empty($pwd_err) && empty($ut_err)) echo 'active'; ?>" id="login">
				<div class="center-align">
					<span class="feedback <?php if (!empty($login_err)) echo 'needed-feedback'; ?>"><?php if (!empty($login_err)) echo $login_err; ?></span>
				</div>				
				<div class="col-md-6 login-form">
					<h4 class="center-align">Login</h4>
					<form method="POST" autocomplete="off" action="login.php">
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
						<div class="btn-center mb-3">
							<input type="submit" class="btn btn-primary" name="login" value="Login">
						</div>
					</form>
				</div>
			</div>
			<div class="row tab-pane <?php if (!empty($reg_err) || !empty($firstname_err) || !empty($lastname_err) || !empty($uname_err) || !empty($pwd_err) || !empty($ut_err)) echo 'active'; ?>" id="register">
				<div class="center-align">
					<span class="feedback <?php if (!empty($reg_err)) echo 'needed-feedback'; ?>"><?php if (!empty($reg_err)) echo $reg_err; ?></span>
				</div>
				<div class="col-md-8 login-form">
					<h4 class="center-align">Register</h4>
					<form method="POST" autocomplete="off" action="login.php">
						<div class="row mb-3">
							<div class="col-sm">
								<label class="form-label" for="first-name">First Name</label>
								<input type="text" id="first-name" name="first-name" class="form-control <?php if (!empty($firstname_err)) echo 'is-invalid'; ?>" placeholder="First Name" autocomplete="off" value="<?php echo $firstname; ?>" required>
								<span class="invalid-feedback"><?php if (!empty($firstname_err)) echo $firstname_err; ?></span>
							</div>
							<div class="col-sm">
								<label class="form-label" for="last-name">First Name</label>
								<input type="text" id="last-name" name="last-name" class="form-control <?php if (!empty($lastname_err)) echo 'is-invalid'; ?>" placeholder="Last Name" autocomplete="off" value="<?php echo $lastname; ?>" required>
								<span class="invalid-feedback"><?php if (!empty($lastname_err)) echo $lastname_err; ?></span>
							</div>
						</div>
						<div class="row mb-3">
							<div class="col-sm">
								<label class="form-label" for="username">Username</label>
								<input type="text" id="username" name="username" class="form-control <?php if (!empty($uname_err)) echo 'is-invalid'; ?>" placeholder="Username" autocomplete="off" value="<?php echo $uname; ?>" required>
								<span class="invalid-feedback"><?php if (!empty($uname_err)) echo $uname_err; ?></span>
							</div>
							<div class="col-sm">
								<label class="form-label" for="email">Email</label>
								<input type="email" id="email" name="email" class="form-control <?php if (!empty($email_err)) echo 'is-invalid'; ?>" placeholder=" School Email Address" autocomplete="off" 
									pattern=".+@.+\.edu$" value="<?php echo $email; ?>" oninvalid="this.setCustomValidity('Please enter a school email ending in .edu.')" required>
								<span class="invalid-feedback"><?php if (!empty($email_err)) echo $email_err; ?></span>
							</div>
						</div>
						<div class="row mb-3">
							<div class="col-sm">
								<label class="form-label" for="password">Password</label>
								<input type="password" id="password" name="password" class="form-control <?php if (!empty($password_err)) echo 'is-invalid'; ?>" placeholder="password" autocomplete="off" 
									pattern="^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$" oninvalid="this.setCustomValidity('Please enter a password containing each of the following: at least one letter, one number, and one special character.')" required>
								<span class="invalid-feedback"><?php if (!empty($password_err)) echo $password_err; ?></span>
							</div>
							<div class="col-sm">
								<label class="form-label" for="acct-type">You are a:</label>
								<select id="acct-type" name="acct-type" class="form-select" required>
<?php
$acctTypeSQL = "SELECT ut_id, user_type FROM user_type WHERE user_type <> 'Administrator';";
if ($acctType = mysqli_prepare($link, $acctTypeSQL)) {
	echo 'Prepped.';
	if (mysqli_stmt_execute($acctType)) {
		echo 'Executed';
		$types = mysqli_stmt_get_result($acctType);
		while ($row = mysqli_fetch_assoc($types)) {
			echo('<option value="' . $row['ut_id'] . '">' . $row['user_type'] . '</option>');
		}
	}
}
?>
								</select>
								<span class="invalid-feedback"><?php if (!empty($lastname_err)) echo $lastname_err; ?></span>
							</div>
						</div>
						<div class="row mb-3">
							<div class="col-sm btn-center">
								<input type="submit" class="btn btn-primary" name="register" value="Register">
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>			
</body>
</html>
