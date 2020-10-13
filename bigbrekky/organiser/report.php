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
	<title>Big Brekky | Report</title>

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
				<li><a href="report.php" id="current-page">REPORT</a></li>
				<li><a href="students.php">STUDENTS</a></li>
				<li><a href="rosterpref.php">ROSTER</a></li>
			</ul>
			<ul class="nav navbar-nav navbar-right">
				<li><a href="?logout"><span class="glyphicon glyphicon-user"></span> LOGOUT</a></li>
			</ul>
		</div>
	</div>
</nav>

<div class="banner general-banner">
	<h1>REPORT</h1>
</div>

<?php
if (isset($_POST['submitreport'])) {
	$reportid = $_POST['reportid'];
	$eventid = $_POST['eventid'];
	$servings = $_POST['servings'];
	$feedback = $_POST['feedback'];
	$recordDate = date('Y-m-d H:i:s');

	//list of present students from saved report
	$presentStudents = isset($_POST['presentStudents']) ? $_POST['presentStudents'] : [];
	//list of students that were meant to attend the event
	$eventStudents = array_column(DB::query('select username from studentevents where eventid = %s', $eventid), 'username');
	//list of absent students from saved report
	$absentStudents = array_diff($eventStudents, $presentStudents);
	if ($reportid == 'null') {
		//new report was created
		//insert new report into reports table
		DB::insert('reports', array(
			'eventid' => $eventid,
			'servings' => $servings,
			'feedback' => $feedback,
			'recorddate' => $recordDate
		));
		//newly inserted reports's rid
		$newreportid = DB::queryFirstRow('select last_insert_id()')['last_insert_id()'];

		foreach ($absentStudents as $student) {
			//insert absent students into absences table
			DB::insert('absences', array(
				'username' => $student,
				'reportid' => $newreportid
			));
		}

	} else {
		//existing report was edited
		//update report in reports table
		DB::update('reports', array(
			'servings' => $servings,
			'feedback' => $feedback
		), 'reportid = %s', $reportid);

		//delete all absent students for this report from absences
		DB::delete('absences', 'reportid = %s', $reportid);
		foreach ($absentStudents as $student) {
			//insert absent students into absences table
			DB::insert('absences', array(
				'username' => $student,
				'reportid' => $reportid
			));
		}
	}
	alert('info', 'REPORT HAS BEEN SAVED');
}

?>

<div class="page-content">
	<form name="addreportform" method="post" action="savereport.php">
		<?php
		//retrieving today's event
		$currentDate = date('Y-m-d');
		$eventToday = DB::queryFirstRow('select * from events where eventdate = %s', $currentDate);
		//if event exists for today, allow organiser to create a new report with corresponding POST variables
		if ($eventToday) {
			//retrieving today's report
			$reportToday = DB::queryFirstRow('select * from reports where date(recorddate) = %s', $currentDate);
			if (!$reportToday) {
				//if an organiser has not already submitted a report today
				echo '
				<input type="hidden" name="eventid" value="'.$eventToday['eventid'].'">
				<input type="hidden" name="week" value="'.$eventToday['week'].'">
				<input type="hidden" name="day" value="'.strtoupper($eventToday['day']).'">
				<input class="button" type="submit" name="addreport" value="ADD NEW REPORT">';
			} else {
				echo '<p>A REPORT HAS ALREADY BEEN SUBMITTED TODAY</p>';
			}
		} else {
			echo '<p>THERE IS NO EVENT TODAY</p>';
		}
		?>
	</form>

	<h2>Recent Reports</h2>
	<?php
	//retrieve 10 most recent existing reports completed in the current year
	$reports = DB::query('select * from reports where year(recorddate) = %i order by recorddate desc limit 10', date('Y'));
	//if reports exist
	if ($reports) {
		echo '
		<div class="table-responsive">
			<table class="table table-hover reportsummary">
				<thead>
					<tr>
						<th>WEEK</th>
						<th>DAY</th>
						<th>COMPLETED</th>
						<th></th>
					</tr>
				</thead>
				<tbody>';
		//iterating through each report to display data in table
		foreach ($reports as $report) {
			$event = DB::queryFirstRow('select * from events where eventid = %s', $report['eventid']);
			$date = date_format(date_create($report['recorddate']), 'j M - g:i A');
			echo '
					<tr>
						<td>'.$event['week'].'</td>
						<td>'.strtoupper($event['day']).'</td>
						<td>'.$date.'</td>
						<td>
							<form name="editreportform" method="post" action="savereport.php">
								<input type="hidden" name="reportid" value="'.$report['reportid'].'">
								<input type="hidden" name="eventid" value="'.$event['eventid'].'">
								<input type="hidden" name="week" value="'.$event['week'].'">
								<input type="hidden" name="day" value="'.strtoupper($event['day']).'">
								<input class="button" type="submit" name="editreport" value="EDIT">
							</form>
						</td>
					</tr>';
		}
		echo '
				</tbody>
			</table>
		</div>';
	} else {
		echo '<h3>NONE</h3>';
	}

	?>

</div>

</body>
</html>