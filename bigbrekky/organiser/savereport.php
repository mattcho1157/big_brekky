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

<div class="page-content savereport-content">
	<a href="report.php" class="button">BACK</a>
	<?php
	if (isset($_POST['addreport'])) {
		//if new report has been added
		$reportid = 'null';
		$eventid = $_POST['eventid'];
		$week = $_POST['week'];
		$day = $_POST['day'];

		//students attending this event
		$eventStudents = DB::query('select username, concat(fname, " ", lname) as name from users where username in (select username from studentevents where eventid = %s) order by lname', $eventid);
		//all students are initially set as absent
		$absentStudents = array_column($eventStudents, 'username');	
	} else if (isset($_POST['editreport'])) {
		//if existing report is being edited
		$reportid = $_POST['reportid'];
		$eventid = $_POST['eventid'];
		$week = $_POST['week'];
		$day = $_POST['day'];

		//students attending this event
		$eventStudents = DB::query('select username, concat(fname, " ", lname) as name from users where username in (select username from studentevents where eventid = %s) order by lname', $eventid);
		//students previously marked as absent
		$absentStudents = array_column(DB::query('select username from absences where reportid = %s', $reportid), 'username');
	} else {
		header('Location: report.php');
		exit();
	}
	?>
	<form name="savereportform" method="post" action="report.php">
		<h2 style="margin-bottom: 60px;"><?php echo 'WEEK '.$week.' '.$day; ?> REPORT</h2>

		<h3>1. SELECT PRESENT STUDENTS:</h3>
		<div class="rollcheckbox">
			<ul> 
			<?php
			foreach ($eventStudents as $student) {
				if (in_array($student['username'], $absentStudents)) {
					//if student is/was absent
					echo '
					<li><label><input type="checkbox" name="presentStudents[]" value="'.$student['username'].'"> &nbsp;'.$student['name'].'</label></li>';
				} else {
					echo '
					<li><label><input type="checkbox" name="presentStudents[]" value="'.$student['username'].'" checked> '.$student['name'].'</label></li>';
				}
			}
			?>
			</ul>
		</div>
		<?php
		$report = DB::queryFirstRow('select * from reports where reportid = %s', $reportid);
		?>
		<h3>2. NUMBER OF SERVINGS</h3>
		<input type="number" name="servings" step="1" class="form-control servings" value="<?php echo $report['servings']; ?>">
		<h3>3. FEEDBACK</h3>
		<div class="textarea-form">
		<textarea class="form-control feedback" name="feedback" maxlength="500"><?php echo $report['feedback']; ?></textarea></div>
		<input type="hidden" name="reportid" value="<?php echo $reportid; ?>">
		<input type="hidden" name="eventid" value="<?php echo $eventid; ?>">
		<input class="button" type="submit" name="submitreport" value="SAVE REPORT">
	</form>
</div>

</body>
</html>