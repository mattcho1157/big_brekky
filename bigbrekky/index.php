<?php
session_start();
require('globalfuncs.php');
checklogout();

$_SESSION['prevPage'] = 'index.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<title>Big Brekky | Home</title>

	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="/bs/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="bigbrekky.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="/bs/js/bootstrap.min.js"></script>

	<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500&family=Raleway:wght@200;300;400&display=swap" rel="stylesheet">
	<link rel="icon" href="img/logo.png">
</head>
<body>

<nav class="navbar navbar-default navbar-fixed-top">
	<div class="container">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" id="current-page" href="./"><span><img src="img/logo.png"></span>BIG BREKKY</a>
		</div>

		<div class="collapse navbar-collapse" id="navbar">
			<ul class="nav navbar-nav">
				<li><a href="manual.php">MANUAL</a></li>
				<?php navbarlinks(); ?>
		</div>
	</div>
</nav>

<div class="index-banner">
	<div class="index-banner-content">
		<h1>BIG BREKKY</h1>
		<h2>Eddie's Van</h2>
		<p>Providing free breakfast for homeless people in and around the gardens of inner-city Brisbane each weekday morning</p>
	</div>
</div>

<?php
//alert message pop-up for account login / registration / logout
if (isset($_SESSION['loginSuccessful'])) {
	alert('info', 'LOG IN SUCCESSFUL - '.strtoupper($_SESSION['fname']));
	unset($_SESSION['loginSuccessful']);

} elseif (isset($_SESSION['signupSuccessful'])) {
	alert('info', 'REGISTRAION SUCCESSFUL - PLEASE ACTIVATE YOUR ACCOUNT VIA EMAIL');
	unset($_SESSION['signupSuccessful']);

} elseif (isset($_SESSION['logoutSuccessful'])) {
	alert('info', 'YOU HAVE BEEN LOGGED OUT');
	unset($_SESSION['logoutSuccessful']);
}

//alert message pop-up for account activation
if (isset($_GET['username']) && isset($_GET['token'])) {
	//retrieve username & token from users table
	connectDB();
	$accountData = DB::queryFirstRow('select * from users where username = %s', urldecode($_GET['username']));
	//if account has already been activated
	if ($accountData['verified']) {
		alert('info', 'YOUR ACCOUNT HAS ALREADY BEEN ACTIVATED');
	} else {
		//if get variables and correct username,token match
		if (urldecode($_GET['username']) == $accountData['username'] && $_GET['token'] == $accountData['token']) {
			//update user's activation status
			DB::update('users', array(
				'token' => NULL,
				'verified' => 1
			), 'username = %s', $accountData['username']);

			alert('info', 'ACCOUNT HAS BEEN VERIFIED - PLEASE PROCEED TO LOGIN');
		}
	}
}


?>

</body>
</html>