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
	<a href="rosterpref.php">PREFERENCES</a>
	<a href="rosterroster.php" id="current-tab">ROSTER</a>
	<a href="rosteredit.php">EDIT</a>
</div>

<div class="page-content">
	<h2>Term Roster</h2>
	<div class="panel-group" id="accordion">
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

	//array for caching student/mentor positions where enrolled > quota
	$exceedingPositions = array();

	//display roster in weekly blocks
	for ($weeknum=1; $weeknum<=$numOfWeeks; $weeknum++) {
		$weekLetter = $weeknum % 2 == 0 ? 'B' : 'A';
		//retrieve names of students and organisers allocated to events in this week, grouped by rows of events - student and organiser names are concatenated in their respective columns
		$events = DB::query('
			select e.*, group_concat(distinct concat(upper(susers.lname), " ", susers.fname) order by susers.lname) as students, group_concat(distinct concat(upper(ousers.lname), " ", ousers.fname) order by ousers.lname) as organisers
			from events e
				left join studentevents s on e.eventid = s.eventid
				left join users susers on s.username = susers.username
				left join organiserevents o on e.eventid = o.eventid
				left join users ousers on o.username = ousers.username
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
				<table class="table table-hover roster-table" style="min-width:1000px;">
				<thead>
					<tr>
						<th rowspan="2"></th>
						<th rowspan="2">TIME</th>
						<th rowspan="2">PLACE</th>
						<th rowspan="1" colspan="2">STUDENTS</th>
						<th rowspan="1" colspan="2">MENTORS</th>
					</tr>
					<tr>
						<th>ENROLLED</th>
						<th>AVAILABLE</th>
						<th>ENROLLED</th>
						<th>AVAILABLE</th>
					</tr>
				</thead>
				<tbody>';

			//list all allocated events in this week as table rows
			foreach ($events as $event) {
				$starttime = date('H:i', strtotime($event['starttime']));
				$endtime = date('H:i', strtotime($event['endtime']));
				$studentList = $event['students'];
				$organiserList = $event['organisers'];

				$students = str_replace(',', '<br>', $studentList);
				$organisers = str_replace(',', '<br>', $organiserList);
				$numStudents = $studentList == '' ? 0 : count(explode(',', $studentList));
				$numOrganisers = $organiserList == '' ? 0 : count(explode(',', $organiserList));
				$studentPosAvailable = $event['studentquota'] - $numStudents;
				$organiserPosAvailable = $event['organiserquota'] - $numOrganisers;
				echo '
				<tr>
					<th>'.strtoupper($event['day']).'</th>
					<td>'.$starttime.' - '.$endtime.'</td>
					<td>'.$event['place'].'</td>
					<td>'.$students.'</td>
					<td>'.(string)$studentPosAvailable.'</td>
					<td>'.$organisers.'</td>
					<td>'.(string)$organiserPosAvailable.'</td>
				</tr>';
				//if available position less than 0, append into $exceedingPositions array
				if ($studentPosAvailable < 0) {
					array_push($exceedingPositions, 'Week '.strtoupper($event['week']).' '.strtoupper($event['day']).' Students');
				}
				if ($organiserPosAvailable < 0) {
					array_push($exceedingPositions, 'Week '.strtoupper($event['week']).' '.strtoupper($event['day']).' Organisers');
				}
			}
			echo '
				</tbody>
				</table>
				</div>
			</div>
			</div>';
		}
	}

	if (count($exceedingPositions) > 0) {
		echo '<p id="exceedingpositions"><b>EXCEEDING QUOTA:</b> '.join(", ",$exceedingPositions).'</p>';
	}
	?>
</div>

</body>
</html>