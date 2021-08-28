
<header>
	<h1><a href="/">Valloons</a></h1>
	<nav>
		<ul>
			<li><a href="about.php">About</a></li>
			<li><a href="contact.php">Contact</a></li>
<?php
if ($_SESSION['loggedin'] == true)
{
	echo('<li><a href="logout.php">Logout</a></li>');
} else {
	echo('<li><a href="login.php">Login</a></li>');
}
?>
		</ul>
	</nav>
</header>
