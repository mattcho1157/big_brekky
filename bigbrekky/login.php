<?php
session_start();
require('globalfuncs.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<title>Big Brekky | Login</title>

	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="/bs/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="bigbrekky.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="/bs/js/bootstrap.min.js"></script>

	<link href="https://fonts.googleapis.com/css?family=Playfair+Display:400,700|Raleway&display=swap" rel="stylesheet">
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
			<a class="navbar-brand" href="./"><span><img src="img/logo.png"></span>BIG BREKKY</a>
		</div>

		<div class="collapse navbar-collapse" id="navbar">
			<ul class="nav navbar-nav">
				<li><a href="manual.php">MANUAL</a></li>
			</ul>
			<ul class="nav navbar-nav navbar-right">
				<li><a href="login.php" id="current-page"><span class="glyphicon glyphicon-user"></span> LOGIN</a></li>
			</ul>
		</div>
	</div>
</nav>

<div class="login-content row">
	<div class="col-xs-12 col-sm-6">
		<form name="loginform" method="post" action="login.php" spellcheck="false">
			<h2>Login</h2>
			<div class="form-group">
				<input class="form-control" type="text" name="username" placeholder="USERNAME" required>
			</div>
			<div class="form-group">
				<input class="form-control" type="password" name="pwd" placeholder="PASSWORD" required>
			</div>
			<br>
			<div class="form-group account-button">
				<input class="button" type="submit" name="submitlogin" value="LOGIN">
			</div>
		</form>
	</div>

	<div class="col-xs-12 col-sm-6">
		<form name="registerform" method="post" action="login.php" spellcheck="false">
			<h2>Register</h2>
			<div class="form-group">
				<input class="form-control" type="text" name="username" placeholder="USERNAME"  maxlength="30" required>
			</div>
			<div class="form-group">
				<input class="form-control" type="password" name="pwd" placeholder="PASSWORD" required>
			</div>
			<div class="form-group">
				<input class="form-control" type="password" name="confirmpwd" placeholder="CONFIRM PASSWORD" required>
			</div>
			<br>
			<div class="form-group account-button">
				<input class="button" type="submit" name="submitregister" value="REGISTER">
			</div>
		</form>
	</div>
</div>

<?php
if (isset($_POST['submitlogin'])) {
	//log in attempted

	//get submitted credentials and sanitise/hash them
	$username = strtolower(trim(stripslashes(htmlspecialchars($_POST['username']))));
	$pwd = sha1($_POST['pwd']);

	//connect to database
	connectDB();

	//attempt to get user's data
	$userData = DB::queryFirstRow('select * from users where username = %s', $username);
	if (!$userData) {
		//input username not found in database
		alert('danger', 'INVALID USERNAME OR PASSWORD');
	} else {
		//live user - check password
		if ($pwd != $userData['pwd']) {
			//invalid password
			alert('danger', 'INVALID USERNAME OR PASSWORD');
		} else {
			//user credentials match
			if ($userData['verified']) {
				//if 2FA successful - account verified
				//set session variables for checking login state
				$_SESSION['usertype'] = $userData['usertype'];
				$_SESSION['username'] = $userData['username'];
				$_SESSION['fname'] = $userData['fname'];
				$_SESSION['loginSuccessful'] = true;

				//redirect to previous page
				header('Location: '.$_SESSION['prevPage']);
				exit();
			} else {
				alert('danger', 'PLEASE VERIFY YOUR ACCOUNT VIA EMAIL TO LOGIN');
			}
		}
	}

} elseif (isset($_POST['submitregister'])) {
	//sign up attempted
	//get submitted credentials and sanitise/hash them
	$username = strtolower(trim(stripslashes(htmlspecialchars($_POST['username'])))); 
	$pwd = sha1($_POST['pwd']); 
	$confirmpwd = sha1($_POST['confirmpwd']);

	//connect to dabatase
	connectDB();

	//to check if username already exists
	$usersData = DB::queryFirstRow('select * from users where username = %s', $username);
	//to check if username is a registrable student
	$studentbaseData = DB::queryFirstRow('select * from studentbase where snumber = %s', $username);

	if ($usersData) {
		//username already exists
		alert('danger', 'USERNAME ALREADY REGISTERED');
	} elseif ($pwd == $confirmpwd) {
		//passwords match

		//generating url token for account verification
		$token = sha1(rand(0, 1000));

		if (!$studentbaseData) {
			//if the registering user is not a student, new organiser user
			//split the username into first and last names
			$splitusername = explode(" ", $username);
			if (count($splitusername) != 2) {
				//organiser username is not valid
				alert('danger', 'INVALID ORGANISER USERNAME - ENTER YOUR FIRST AND LAST NAMES SEPARATED BY A SPACE');
			} else {
				//valid organiser username
				//extract first and last names and capitalise first letter
				$fname = ucwords($splitusername[0]);
				$lname = ucwords($splitusername[1]);
				$email = $fname.$lname."@terrace.qld.edu.au";

				//organiser registration - add to users table
				DB::insert('users', array(
					'username' => $username,
					'email' => $email,
					'pwd' => $pwd,
					'fname' => $fname,
					'lname' => $lname,
					'usertype' => 'o',
					'verified' => 0,
					'token' => $token
				));

				//set session variables for checking login state
				$_SESSION['signupSuccessful'] = true;

				//send account-verification username
				require('sendmail.php');
				sendVerificationEmail($email, $username, $token);

				//redirect to previous page
				header('Location: '.$_SESSION['prevPage']);
				exit();
			}
		} else {
			//new student user
			$email = $studentbaseData['email'];
			$fname = $studentbaseData['fname'];
			$lname = $studentbaseData['lname'];

			//student registration - add to users table
			DB::insert('users', array(
				'username' => $username,
				'email' => $email,
				'pwd' => $pwd,
				'fname' => $fname,
				'lname' => $lname,
				'usertype' => 's',
				'verified' => 0,
				'token' => $token
			));

			$house = $studentbaseData['house'];
			$pc = $studentbaseData['pc'];
			$ylevel = $studentbaseData['ylevel'];
			$formfilepath = 'permissionforms/'.$username.'.pdf';

			//add student to students table
			DB::insert('students', array(
				'username' => $username,
				'house' => $house,
				'pc' => $pc,
				'ylevel' => $ylevel,
				'permitted' => 0,
				'formfilepath' => $formfilepath,
				'resubmitpref' => 0
			));

			//set session variables for checking login state
			$_SESSION['signupSuccessful'] = true;

			//send account-verification username
			require('sendmail.php');
			sendVerificationEmail($email, $username, $token);

			//redirect to previous page
			header('Location: '.$_SESSION['prevPage']);
			exit();
		}
	} else {
		//passwords do not match
		alert('danger', 'PASSWORDS DO NOT MATCH');
	}
}
?>

</body>
</html>