<nav class="navbar navbar-light bg-light justify-content-between sticky-top navbar-expand-md">
	<div class="container">
		<a class="navbar-brand" href="/"><img src="media/logo.png" class="logo" alt="Valloons"></a>
		<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarToggler" aria-controls="navbarToggler" aria-expanded="false" aria-label="Toggle Navigation">
			<span class="navbar-toggler-icon"></span>
		</button>
		<div class="collapse navbar-collapse" id="navbarToggler">
			<ul class="navbar-nav ml-auto">
				<li class="nav-item">
					<a class="nav-link" href="about.php">About</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="contact.php">Contact</a>
				</li>
<?php
if ($_SESSION['loggedin'] == true)
{
	echo('<li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>');
} else {
	echo('<li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>');
}
?>
			</ul>
		</div>
	</div>
</nav>

