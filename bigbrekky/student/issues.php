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
	<title>Big Brekky | Issues</title>

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
				<li><a href="enrol.php">ENROL</a></li>
				<li><a href="permission.php">PERMISSION</a></li>
				<li><a href="roster.php">ROSTER</a></li>
				<li><a href="issues.php" id="current-page">ISSUES</a></li>
			</ul>
			<ul class="nav navbar-nav navbar-right">
				<li><a href="?logout"><span class="glyphicon glyphicon-user"></span> LOGOUT</a></li>
			</ul>
		</div>
	</div>
</nav>

<div class="banner general-banner">
	<h1>REPORT ISSUES</h1> 
</div>

<?php
if (isset($_POST['submitissue'])) {
	//if student has submitted an issue
	//set username to null if student has requested for anonymous submission
	$username = isset($_POST['anonymous']) ? null : $_SESSION['username'];
	DB::insert('issues', array(
		'eventid' => $_POST['eventid'],
		'username' => $username,
		'issue' => $_POST['issue']
	));
	//add the text "ANONYMOUSLY" if student has requested for anonymous submission
	$alertsuffix = isset($_POST['anonymous']) ? ' ANONYMOUSLY' : '';
	alert('info', 'YOUR ISSUE HAS BEEN SUBMITTED'.$alertsuffix);
}
?>

<div class="info-content" style="margin-top: 60px;">
	<p>During a conversation with a patron, they may talk to you about something that you find distressing, saddening or uncomfortable. In such cases, it is essential that you either talk to you Team Captain or report any issues encountered via this form:</p>
</div>

<form name="issueform" method="post" action="issues.php" class="textarea-form" style="margin-bottom: 60px;">
	<h2>Select Big Brekky Session</h2>
	<div class="form-group form-select-group">	
		<?php
		//get all events that the student is allocated to and has already taken place
		$studentEventsOccured = DB::query('select s.eventid, e.* from studentevents s left join events e on s.eventid = e.eventid where s.username = %s and e.eventdate <= CURDATE() order by e.eventdate', $_SESSION['username']);

		if ($studentEventsOccured) {
			//if at least one allocated event has occured
			echo '<select name="eventid" class="pref-select" style="width:150px;">';
			//list all the remaining events within a drop-down selection input
			foreach ($studentEventsOccured as $event) {
				echo '<option value="'.$event['eventid'].'">Week '.$event['week'].' '.strtoupper($event['day']).'</option>';
			}
			echo '
			</select>
			<h2>Issues Encountered</h2>
			<textarea class="form-control" name="issue" maxlength="500"></textarea>
			<div class="checkbox" style="margin-bottom:40px;">
				<label><input type="checkbox" name="anonymous" value="1">Anonymous Submission</label>
			</div>
			<input class="button" type="submit" name="submitissue" value="SUBMIT">';
		} else {
			echo '<p>You have not participated in any sessions yet.</p>';
		}
		?>
	</div>
</form>

</body>
</html>