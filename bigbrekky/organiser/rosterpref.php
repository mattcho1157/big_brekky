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
	<title>Big Brekky | Roster</title>

	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="/bs/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="../bigbrekky.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="/bs/js/bootstrap.min.js"></script>

	<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500&family=Raleway:wght@200;300;400;700&display=swap" rel="stylesheet">
	<link rel="icon" href="../img/logo.png">

	<script>
	$(document).ready(function(){
		//called if week or day select inputs are changed
		$('#pref-week, #pref-day').change(function(){
			//create XMLHttpRequest object
			var xmlhttp = new XMLHttpRequest();
			//function called whenever readyState of request changes
			xmlhttp.onreadystatechange = function() {
				//if server response is ready
				if (this.readyState == 4 && this.status == 200) {
					//readyState = 4 -> request finished and response is ready
					//status = 200 -> returns "OK" for request status
					//list the students preferences for the selected week and day
					document.getElementById('listpref').innerHTML = this.responseText;
				}
			}
			//send request to .php on the server - parameters week & day inputs are added
			xmlhttp.open('GET', 'listpref.php?week=' + $('#pref-week').val() + '&day=' + $('#pref-day').val(), true);
			xmlhttp.send();
		}).change();
	});
	</script>
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
				<li><a href="report.php">REPORT</a></li>
				<li><a href="students.php">STUDENTS</a></li>
				<li><a href="rosterpref.php" id="current-page">ROSTER</a></li>
			</ul>
			<ul class="nav navbar-nav navbar-right">
				<li><a href="?logout"><span class="glyphicon glyphicon-user"></span> LOGOUT</a></li>
			</ul>
		</div>
	</div>
</nav>

<div class="banner general-banner">
	<h1>ROSTER</h1>
</div>

<div class="roster-tab">
	<a href="rosterpref.php" id="current-tab">PREFERENCES</a>
	<a href="rosterroster.php">ROSTER</a>
	<a href="rosteredit.php">EDIT</a>
</div>

<div class="page-content">
	<h2>Student Preferences</h2>
	<form>
	<table class="weekday-table"><tr>
	<th>WEEK: </th>
	<td>
		<select class="form-control" name="pref-week" id="pref-week">
			<option value="a" selected>A</option>
			<option value="b">B</option>
		</select>
	</td>
	<th id="groupby-heading">DAY: </th>
	<td>
		<select class="form-control" name="pref-day" id="pref-day">
			<option value="mon" selected>MON</option>
			<option value="tue">TUE</option>
			<option value="wed">WED</option>
			<option value="thu">THU</option>
			<option value="fri">FRI</option>
		</select>
	</td>
	</tr></table>
	</form>
	<div id="listpref"></div>
</div>

</body>
</html>