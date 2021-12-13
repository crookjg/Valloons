<?php

session_start();

include 'config.php';

$login_err = $reg_err = "";

if ($_SESSION['loggedin'] == true)
	header("location: dashboard.php");

if (!empty($_POST['login']) && isset($_POST['login'])) {
	$user = $password = NULL;
	$username_err = $password_err =  "";
	
	if (!empty($_POST['user']) && isset($_POST['user'])) {
		$user = mysqli_real_escape_string($link, $_POST['user']);
	} else {
		$username_err = "Please enter a valid username or email.";
	}
	
	if (!empty($_POST['auth_string']) && isset($_POST['auth_string'])) {
		$password = mysqli_real_escape_string($link, $_POST['auth_string']);
	} else {
		$password_err = "Please enter a password.";
	}
	
	if (empty($username_err) && empty($password_err) && isset($user) && isset($password)) {
		$getPwdSQL = "SELECT user_id, first_name, last_name, username, email, authentication, verified, ut_id FROM user WHERE username=? OR email=?;";
		$get_pwd = $link->prepare($getPwdSQL);
		$get_pwd->bind_param("ss", $user, $user);
		$get_pwd->execute();
		$get_pwd->bind_result($user_id, $firstname, $lastname, $username, $email, $authentication, $verified, $ut_id);
		
		if ($get_pwd->fetch() && password_verify($password, $authentication)) {
			$_SESSION = array();
			$_SESSION['loggedin'] = true;
			$_SESSION['user_id'] = $user_id;
			$_SESSION['first_name'] = $firstname;
			$_SESSION['last_name'] = $lastname;
			$_SESSION['email'] = $email;
			$_SESSION['username'] = $username;
			$_SESSION['ut_id'] = $ut_id;
			$_SESSION['time'] = time();
			if ($_SESSION['ut_id'] == 2)
			{
				$_SESSION['verified'] = $verified;
				if ($_SESSION['verified'] == 0) {
					header("location: logout.php");
					exit;
				}
			} else {
				$_SESSION['verified'] = NULL;
			}
			header("location: dashboard.php");
			exit;
		} else {
			$login_err = "Incorrect username or password.";
		}
	} else {
		$login_err = "Something went wrong. Please try again later.";
	}
}

if (!empty($_POST['register-student']) && isset($_POST['register-student'])) {
	$stufname = $stulname = $stuuname = $student_email = $stu_password = $stu_usertype = NULL;
	$stufname_err = $stulname_err = $stuuname_err = $stu_email_err = $stu_pwd_err = $stu_usertype_err = "";

	if (!empty($_POST['first-name']) && isset($_POST['first-name'])) {
		$stufname = mysqli_real_escape_string($link, $_POST['first-name']);
	} else {
		$stufname_err = "Please enter your first name.";
	}

	if (!empty($_POST['last-name']) && isset($_POST['last-name'])) {
		$stulname = mysqli_real_escape_string($link, $_POST['last-name']);
	} else {
		$stulname_err = "Please enter your last name.";
	}

	if (!empty($_POST['username']) && isset($_POST['username'])) {
		$stuuname = mysqli_real_escape_string($link, $_POST['username']);
	} else {
		$stuuname_err = "Please enter a username.";
	}

	if (!empty($_POST['email']) && isset($_POST['email']) && filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
		$student_email = mysqli_real_escape_string($link, $_POST['email']);
	} else {
		$stu_email_err = "Please enter a valid school email address.";
	}

	if (!empty($_POST['password']) && isset($_POST['password'])) {
		$stu_password = mysqli_real_escape_string($link, $_POST['password']);
		$auth_string = password_hash($stu_password, PASSWORD_BCRYPT);
	} else {
		$stu_pwd_err = "Please enter a password. Passwords must contain at least 8 characters, including 1 number and 1 special character.";
	}

	// usertype is student, url is NULL
	$stu_usertype = 3;
	$url = NULL;

	if (!empty($stufname) && !empty($stulname) && !empty($stuuname) && !empty($student_email) && !empty($stu_password) && !empty($stu_usertype) && empty($_POST['no-entry']) && !isset($_POST['no-entry'])) {
		$register_sql = "INSERT INTO user (first_name, last_name, email, username, authentication, teacher_url, ut_id) VALUES (?, ?, ?, ?, ?, ?, ?);";
		if ($registration = mysqli_prepare($link, $register_sql)) {
			$registration->bind_param("ssssssi", $stufname, $stulname, $student_email, $stuuname, $auth_string, $url, $stu_usertype);
			if (mysqli_stmt_execute($registration)) {
				$mail_sent = send_verification_email($student_email, $stufname, $stulname, $stuuname, $stu_password);
				if ($mail_sent) {
					$login_err = "Registration complete. Login and verify your email address.";
				} else {
					$login_err = "Registration complete. You can login!";
				}
			} else {
				$reg_err = "Something went wrong. Please try again.";
				$get_err = "SELECT username, email FROM user;";
				$info = array('emails' => array(), 'users' => array());
				if ($getE = mysqli_prepare($link, $get_err)) {
					if (mysqli_stmt_execute($getE)) {
						$err_info = mysqli_stmt_get_result($getE);
						while ($row = mysqli_fetch_assoc($err_info)) {
							array_push($info['emails'], $row['email']);
							array_push($info['users'], $row['username']);
						}
					}
				}

				if (in_array($student_email, $info['emails'])) {
					$stu_email_err = "The email address is already taken.";
				} else if (in_array($uname, $info['users'])) {
					$stuuname_err = "The username is taken.";
				}		
			}
		} else {
			$reg_err = "Something went wrong. Please contact the system administrator.";
		}
	} else {
		$reg_err = "Your form is missing data. Please try again.";
	}
}

if (!empty($_POST['register-teacher']) && isset($_POST['register-teacher'])) {
	$firstname = $lastname = $uname = $teacher_email = $password = $verified = $usertype = NULL;
	$firstname_err = $lastname_err = $uname_err = $email_err = $pwd_err = $url_err = "";

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

	if (!empty($_POST['email']) && isset($_POST['email']) && filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
		$teacher_email = mysqli_real_escape_string($link, $_POST['email']);
	} else {
		$email_err = "Please enter a valid school email address.";
	}

	if (!empty($_POST['password']) && isset($_POST['password'])) {
		$password = mysqli_real_escape_string($link, $_POST['password']);
		$auth_string = password_hash($password, PASSWORD_BCRYPT);
	} else {
		$pwd_err = "Please enter a password. Passwords must contain at least 8 characters, including 1 number and 1 special character.";
	}

	if (!empty($_POST['url']) && isset($_POST['url'])) {
		$url = mysqli_real_escape_string($link, $_POST['url']);
	} else {
		$url_err = "Please enter the url to your teacher homepage.";
	}

	// usertype is teacher
	$usertype = 2;

	if (!empty($firstname) && !empty($lastname) && !empty($uname) && !empty($teacher_email) && !empty($password) && !empty($usertype) && empty($_POST['no-entry']) && !isset($_POST['no-entry'])) {
		$register_sql = "INSERT INTO user (first_name, last_name, email, username, authentication, teacher_url, ut_id) VALUES (?, ?, ?, ?, ?, ?, ?);";
		if ($registration = mysqli_prepare($link, $register_sql)) {
			$registration->bind_param("ssssssi", $firstname, $lastname, $teacher_email, $uname, $auth_string, $url, $usertype);
			if (mysqli_stmt_execute($registration)) {
				$mail_sent = send_verification_email($teacher_email, $firstname, $lastname, $uname, $password);
				if ($mail_sent) {
					$login_err = "Registration complete. Login and verify your email address.";
				} else {
					$login_err = "Registration complete. You can login!";
				}
			} else {
				$reg_err = "Something went wrong. Please try again.";
				$get_err = "SELECT username, email FROM user;";
				$info = array('emails' => array(), 'users' => array());
				if ($getE = mysqli_prepare($link, $get_err)) {
					if (mysqli_stmt_execute($getE)) {
						$err_info = mysqli_stmt_get_result($getE);
						while ($row = mysqli_fetch_assoc($err_info)) {
							array_push($info['emails'], $row['email']);
							array_push($info['users'], $row['username']);
						}
					}
				}

				if (in_array($teacher_email, $info['emails'])) {
					$email_err = "The email address is already taken.";
				} else if (in_array($uname, $info['users'])) {
					$uname_err = "The username is taken.";
				}		
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
			<li class="pill-item"><a class="pill-link <?php if (empty($reg_err) && empty($firstname_err) && empty($lastname_err) && empty($uname_err) && empty($pwd_err)) echo 'active'; ?>" id="pill-link-one" data-bs-toggle="pill" href="#login">Login</a></li>
			<li class="pill-item"><a class="pill-link <?php if (!empty($reg_err) || !empty($firstname_err) || !empty($lastname_err) || !empty($uname_err) || !empty($pwd_err)) echo 'active'; ?>" id="pill-link-two" data-bs-toggle="pill" href="#register">Register</a></li>
			</ul>
		</div>
		<div class="tab-content">
			<div class="row tab-pane <?php if (empty($reg_err) && empty($firstname_err) && empty($lastname_err) && empty($uname_err) && empty($pwd_err) && empty($ut_err)) echo 'active'; ?>" id="login">
				<div class="center-align">
					<span class="feedback <?php if (!empty($login_err)) echo 'needed-feedback'; ?>"><?php if (!empty($login_err)) echo $login_err; ?></span>
				</div>				
				<div class="col-md-6 login-form">
					<h4 class="center-align">Login</h4>
					<form method="POST" autocomplete="off" action="index.php">
						<div class="mb-3">
							<label class="form-label" for="username">Username or Email Address</label>
							<input type="text" id="username" name="user" class="form-control <?php if (!empty($username_err)) echo 'is-invalid'; ?>" placeholder="Username / Email" autocomplete="off" required>
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
			<div class="row tab-pane <?php if (!empty($reg_err) || !empty($firstname_err) || !empty($lastname_err) || !empty($uname_err) || !empty($email_err) || !empty($pwd_err) || !empty($stufname_err) 
				|| !empty($stulname_err) || !empty($stuuname_err) || !empty($stu_email_err) || !empty($stu_pwd_err)) echo 'active'; ?>" id="register">
				<div class="center-align">
					<span class="feedback <?php if (!empty($reg_err)) echo 'needed-feedback'; ?>"><?php if (!empty($reg_err)) echo $reg_err; ?></span>
				</div>
				<div class="col-md-8 login-form">
					<h4 class="center-align">Register</h4>
					<div class="center-pills" id="register-pills">
						<ul class="nav nav-pills">
						<li class="pill-item"><a class="pill-link <?php if (empty($reg_err) || (!empty($reg_err) && (!empty($stufname_err) || !empty($stulname_err) || !empty($stuuname_err) 
							|| !empty($stu_email_err) || !empty($stu_pwd_err)))) echo 'active'; ?>" id="register-one" data-bs-toggle="pill" href="#student-register">Student</a></li>
						<li class="pill-item"><a class="pill-link <?php if (!empty($reg_err) && (!empty($firstname_err) || !empty($lastname_err) || !empty($uname_err) || !empty($email_err) 
							|| !empty($pwd_err))) echo 'active'; ?>" id="register-two" data-bs-toggle="pill" href="#teacher-register">Teacher</a></li>
						</ul>
					</div>
					<div class="tab-content">
					<div class="row tab-pane <?php if (empty($reg_err) || (!empty($stufname_err) || !empty($stulname_err) || !empty($stuuname_err) || !empty($stu_email_err) || !empty($stu_pwd_err))) echo 'active'; ?>" id="student-register">
							<form method="POST" autocomplete="off" action="index.php">
								<div class="row mb-3">
									<div class="col-sm">
										<label class="form-label" for="first-name">First Name</label>
										<input type="text" id="first-name" name="first-name" class="form-control <?php if (!empty($stufname_err)) echo 'is-invalid'; ?>" placeholder="First Name" autocomplete="off" value="<?php echo $stufname; ?>" required>
										<span class="invalid-feedback"><?php if (!empty($stufname_err)) echo $stufname_err; ?></span>
									</div>
									<div class="col-sm">
										<label class="form-label" for="last-name">Last Name</label>
										<input type="text" id="last-name" name="last-name" class="form-control <?php if (!empty($stulname_err)) echo 'is-invalid'; ?>" placeholder="Last Name" autocomplete="off" value="<?php echo $stulname; ?>" required>
										<span class="invalid-feedback"><?php if (!empty($stulname_err)) echo $stulname_err; ?></span>
									</div>
								</div>
								<div class="row mb-3">
									<div class="col-sm">
										<label class="form-label" for="username">Username</label>
										<input type="text" id="username" name="username" class="form-control <?php if (!empty($stuuname_err)) echo 'is-invalid'; ?>" placeholder="Username" autocomplete="off" value="<?php echo $stuuname; ?>" required>
										<span class="invalid-feedback"><?php if (!empty($stuuname_err)) echo $stuuname_err; ?></span>
									</div>
									<div class="col-sm" id="student-email">
										<label class="form-label" for="email">Email</label>
										<input type="email" id="stu-email" name="email" class="form-control <?php if (!empty($stu_email_err)) echo 'is-invalid'; ?>" placeholder="Email Address" autocomplete="off" value="<?php echo $student_email; ?>" required>
										<span class="invalid-feedback"><?php if (!empty($stu_email_err)) echo $stu_email_err; ?></span>
									</div>
								</div>
								<div class="row mb-3">
									<div class="col-sm">
										<label class="form-label" for="password">Password</label>
										<input type="password" id="password" name="password" class="form-control <?php if (!empty($stu_password_err)) echo 'is-invalid'; ?>" placeholder="password" 
											autocomplete="off" pattern="^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$" 
											oninvalid="this.setCustomValidity('Please enter a password containing each of the following: at least one letter, one number, and one special character.')" 
										required>
										<span class="invalid-feedback"><?php if (!empty($stu_password_err)) echo $stu_password_err; ?></span>
									</div>
									<div class="col-sm btn-center">
										<input type="hidden" name="no-entry">
										<input type="submit" class="btn btn-primary" id="student-register-btn" name="register-student" value="Register">
									</div>
								</div>
							</form>
						</div>
						<div class="row tab-pane <?php if (!empty($reg_err) && (!empty($firstname_err) || !empty($lastname_err) || !empty($uname_err) || !empty($pwd_err))) echo 'active'; ?>" id="teacher-register">
							<form method="POST" autocomplete="off" action="index.php">
								<div class="row mb-3">
									<div class="col-sm">
										<label class="form-label" for="first-name">First Name</label>
										<input type="text" id="first-name" name="first-name" class="form-control <?php if (!empty($firstname_err)) echo 'is-invalid'; ?>" placeholder="First Name" autocomplete="off" value="<?php echo $firstname; ?>" required>
										<span class="invalid-feedback"><?php if (!empty($firstname_err)) echo $firstname_err; ?></span>
									</div>
									<div class="col-sm">
										<label class="form-label" for="last-name">Last Name</label>
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
									<div class="col-sm" id="teacher-email">
										<label class="form-label" for="email">Email</label>
										<input type="email" id="tea-email" name="email" class="form-control <?php if (!empty($email_err)) echo 'is-invalid'; ?>" placeholder="Email Address" autocomplete="off" pattern=".+@.+\.edu$" value="<?php echo $teacher_email; ?>" oninvalid="this.setCustomValidity('Please enter a school email ending in .edu.')" required>
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
									<div class="col-sm" id="teacher-url">
										<label class="form-label" for="url">Teacher Homepage</label>
										<input type="text" name="url" class="form-control <?php if (!empty($url_err)) echo 'is-invalid'; ?>" oninvalid="this.setCustomValidity('Please enter your teacher hompage url.')" required>
										<span class="invalid-feedback"><?php if (!empty($url_err)) echo $url_err; ?></span>
									</div>
								</div>
								<div class="row mb-3">
									<div class="col-sm btn-center">
										<input type="hidden" name="no-entry">
										<input type="submit" class="btn btn-primary" id="teacher-register-btn" name="register-teacher" value="Register">
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>			
</body>
</html>

