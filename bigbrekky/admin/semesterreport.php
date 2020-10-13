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
				<li><a href="query.php">QUERY</a></li>
				<li><a href="semesterreport.php" id="current-page">SEMESTER REPORT</a></li>
			</ul>
			<ul class="nav navbar-nav navbar-right">
				<li><a href="?logout"><span class="glyphicon glyphicon-user"></span> LOGOUT</a></li>
			</ul>
		</div>
	</div>
</nav>

<div class="banner general-banner">
	<h1>SEMESTER REPORT</h1>
</div>

<div class="page-content">
	<p>Only students that were present during each event are listed.</p><br><br>
	<div class="panel-group" id="accordion" style="width: 800px; margin: 0 auto;">
	<?php
	//finding the number of weeks in current term
	$currentDate = date('Y-m-d');
	$term = DB::query('select * from termweeks order by term');
	if ($currentDate < $term[1]['startdate']) {
		//current date is in term 1
		$numOfWeeks = $term[0]['weeknum'];
	} elseif ($currentDate < $term[2]['startdate']) {
		//current date is in term 2
		$numOfWeeks = $term[1]['weeknum'];
	} elseif ($currentDate < $term[3]['startdate']) {
		//current date is in term 3
		$numOfWeeks = $term[2]['weeknum'];
	} else {
		//current date is in term 4
		$numOfWeeks = $term[3]['weeknum'];
	}

	//display roster in weekly blocks
	for ($weeknum=1; $weeknum<=$numOfWeeks; $weeknum++) {
		$weekLetter = $weeknum % 2 == 0 ? 'B' : 'A';
		//retrieve names of students and organisers allocated to events in this week, grouped by rows of events - student and organiser names are concatenated in their respective columns
		$events = DB::query('
			select e.*, group_concat(distinct s.username) as students
			from events e
				left join studentevents s on e.eventid = s.eventid
			where e.week = '.$weeknum.' 
			group by e.eventid');

		if ($events) {
			//if there are events allocated during this week
			echo '
			<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">
				<a data-toggle="collapse" data-parent="#accordion" href="#collapse'.$weeknum.'">Week '.$weeknum.$weekLetter.'</a>
				</h3>
			</div>
			<div id="collapse'.$weeknum.'" class="panel-collapse collapse">
				<div class="panel-body table-responsive">
				<table class="table table-hover roster-table">
				<thead>
					<tr>
						<th></th>
						<th>DATE</th>
						<th>STUDENTS</th>
					</tr>
				</thead>
				<tbody>';

			//list all allocated events in this week as table rows
			foreach ($events as $event) {
				$eventdate = date_format(date_create($event['eventdate']), 'j M Y');
				//students allocated to this event
				$studentList = explode(',', $event['students']);
				//students that were absent for this event
				$absentStudents = array_column(DB::query('select username from absences where reportid in (select reportid from reports where eventid = %s)', $event['eventid']), 'username');
				//students that were present for this event
				$present_students = implode(',', array_diff($studentList, $absentStudents));
				$present_students = str_replace(',', '<br>', $present_students);
				echo '
				<tr>
					<th>'.strtoupper($event['day']).'</th>
					<td>'.$eventdate.'</td>
					<td>'.$present_students.'</td>
				</tr>';
			}
			echo '</tbody></table></div></div></div>';
		}
	}
	?>
</div>

</body>
</html>