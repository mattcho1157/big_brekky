<?php
session_start();
require('../globalfuncs.php');
checklogout();
connectDB();

if (!isset($_SESSION['usertype'])) {
	header('Location: http://localhost/bigbrekky/index.php');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<title>Big Brekky | Enrol</title>

	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="/bs/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="../bigbrekky.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="/bs/js/bootstrap.min.js"></script>

	<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500&family=Raleway:wght@200;300;400;700&display=swap" rel="stylesheet">
	<link rel="icon" href="../img/logo.png">
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
			<a class="navbar-brand" href="../"><span><img src="../img/logo.png"></span>BIG BREKKY</a>
		</div>

		<div class="collapse navbar-collapse" id="navbar">
			<ul class="nav navbar-nav">
				<li><a href="../manual.php">MANUAL</a></li>
				<li><a href="enrol.php" id="current-page">ENROL</a></li>
				<li><a href="permission.php">PERMISSION</a></li>
				<li><a href="roster.php">ROSTER</a></li>
				<li><a href="issues.php">ISSUES</a></li>
			</ul>
			<ul class="nav navbar-nav navbar-right">
				<li><a href="?logout"><span class="glyphicon glyphicon-user"></span> LOGOUT</a></li>
			</ul>
		</div>
	</div>
</nav>

<div class="banner general-banner">
	<h1>ENROL</h1> 
</div>

<?php
//retrieving user's preferences data
$userPrefData = DB::query('select day from preferences where username = %s order by prefnum', $_SESSION['username']);

//student year level
$studentYear = DB::queryFirstRow('select ylevel from students where username = %s', $_SESSION['username'])['ylevel'];

//preferences form submitted & user has not already submitted preferences
if (isset($_POST['submitpref']) && !$userPrefData) {
	$pref1 = $_POST['pref1week'].' '.$_POST['pref1day'];
	$pref2 = $_POST['pref2week'].' '.$_POST['pref2day'];
	if ($studentYear != 10) {
		//student not in year 10 can choose up to 5 preferences
		$pref3 = $_POST['pref3week'].' '.$_POST['pref3day'];
		$pref4 = $_POST['pref4week'].' '.$_POST['pref4day'];
		$pref5 = $_POST['pref5week'].' '.$_POST['pref5day'];
	} else {
		//student in year 10 can only choose up to 2 preferences
		$pref3 = $pref4 = $pref5 = 'none none';
	}

	//discard preferences where the student has selected none for week or day
	$prefArray = [$pref1, $pref2, $pref3, $pref4, $pref5];
	for ($i=0; $i<5; $i++) { 
		//split each preference into week and day
		$pref = explode(' ', $prefArray[$i]);
		//if the week or day is 'none', discard that preference
		if ($pref[0] == 'none' || $pref[1] == 'none') {
			unset($prefArray[$i]);
		}
	}
	//re-indexing the array
	$prefArray = array_values($prefArray);

	if (count($prefArray) != 0) {
		//student has selected at least 1 preference
		if (count($prefArray) !== count(array_unique($prefArray))) {
			//duplicate preferences selected
			alert('danger', 'YOU CANNOT SELECT DUPLICATE WEEKDAYS');
		} else {
			for ($i=0; $i<count($prefArray); $i++) { 
				//split preference into week and day
				$pref = explode(' ', $prefArray[$i]);
				//add each preference to preferences table
				DB::insert('preferences', array(
					'username' => $_SESSION['username'],
					'prefnum' => $i+1, 
					'week' => $pref[0],
					'day' => $pref[1]
				));
			}

			alert('info', 'PLEASE DOWNLOAD THE PARENTAL PERMISSION FORM');
		}
	} else {
		//student has not selected any preferences
		alert('danger', 'PLEASE SELECT A WEEKDAY');
	}
}

//for checking if user has submitted the preference-resubmission-request form
$userResubmitpref = DB::queryFirstRow('select resubmitpref from students where username = %s', $_SESSION['username'])['resubmitpref'];
//resubmission-request form submitted & user has not already requested
if (isset($_POST['submitrequest']) && !$userResubmitpref) {
	//student's provided reason for resubmission request
	$reason = $_POST['resubmitreason'];

	//send admin an email (my email address used for testing)
	require('../sendmail.php');
	sendResubmitprefEmail($_SESSION['username'], $reason);

	alert('info', 'YOUR REQUEST HAS BEEN SENT');

	//update students' resubmitpref status to true
	DB::update('students', array(
		'resubmitpref' => 1,
	), 'username = %s', $_SESSION['username']);
}
?>

<div class="page-content">
<?php
//retrieving user's preferences data
$userPrefData = DB::query('select * from preferences where username = %s order by prefnum', $_SESSION['username']);

if (!$userPrefData) {
	//student has not already submitted preferences form
	echo '
	<form name="prefform" method="post" action="enrol.php" class="enrol-form form-horizontal">
		<h2>Preference Selection</h2>';

	if ($studentYear != 10) {
		//student is not in Year 10 - cannot choose Fridays
		echo '<p>Fridays are only available to Year 10 students.</p>';
		for ($i=1; $i<=5; $i++) { 
			echo '
			<div class="form-group form-select-group">
				<label class="control-label" for="pref'.$i.'">Preference '.$i.'</label>
				<select name="pref'.$i.'week" class="pref-select" id="pref'.$i.'week">
					<option value="none" selected>NONE</option>
					<option value="a">WEEK A</option>
					<option value="b">WEEk B</option>
				</select>
				<select name="pref'.$i.'day" class="pref-select" id="pref'.$i.'day">
					<option value="none" selected>NONE</option>
					<option value="mon">MON</option>
					<option value="tue">TUE</option>
					<option value="wed">WED</option>
					<option value="thu">THU</option>
				</select>
			</div>';
		}
	} else {
		//student is in Year 10 - can only choose Fridays
		echo '<p>Year 10 students can only choose Fridays.</p>';
		for ($i=1; $i<=2; $i++) { 
			echo '
			<div class="form-group form-select-group">
				<label class="control-label" for="pref'.$i.'">Preference '.$i.'</label>
				<select name="pref'.$i.'week" class="pref-select" id="pref'.$i.'week">
					<option value="none" selected>NONE</option>
					<option value="a">WEEK A</option>
					<option value="b">WEEk B</option>
				</select>
				<select name="pref'.$i.'day" class="pref-select" id="pref'.$i.'day">
					<option value="none" selected>NONE</option>
					<option value="fri">FRI</option>
				</select>
			</div>';
		}
	}
	
	echo '
		<input class="button" type="submit" name="submitpref" value="SUBMIT">
	</form>';
} else {
	//student has already submitted preferences form

	//displaying student preferences
	echo '<h2>Your Selected Preferences</h2>';
	for ($i=0; $i<count($userPrefData); $i++) {
		$prefweek = strtoupper($userPrefData[$i]['week']);
		$prefday = strtoupper($userPrefData[$i]['day']);
		echo '<h3>Preference '.($i+1).': Week '.$prefweek.' '.$prefday.'</h3>';
	}

	echo '<h2>Request For Resubmission</h2>';

	//checking if user has already requested for a resubmission
	$resubmitpref = DB::queryFirstRow('select resubmitpref from students where username = %s', $_SESSION['username'])['resubmitpref'];
	if ($resubmitpref) {
		//user has already requested for a resubmission
		echo '
		<p>You have made a request for a resubmission. You will shortly be notified of our approval.</p>';
	} else {
		//user has not already requested for a resubmission
		//form for resubmission request
		echo '
		<form name="prefform" method="post" action="enrol.php" class="textarea-form">
			<p>Please state the reason why you would like to change your Big Brekky preferences. You will shortly be notified of our approval.</p>
			<textarea class="form-control" name="resubmitreason" maxlength="500"></textarea>
			<input class="button" type="submit" name="submitrequest" value="REQUEST">
		</form>';

	}
}
?>
</div>

</body>
</html>