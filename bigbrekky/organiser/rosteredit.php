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
	<a href="rosterroster.php">ROSTER</a>
	<a href="rosteredit.php" id="current-tab">EDIT</a>
</div>

<?php
if (isset($_POST['submitrosteredit'])) {
	//if organiser has submitted the edit roster form
	if ($_POST['newevent'] == 1) {
		//organiser has added a new event
		//add event to events table
		DB::insert('events', array(
			'eventdate' => $_POST['eventdate'],
			'week' => $_POST['week'],
			'day' => $_POST['day'],
			'starttime' => $_POST['starttime'],
			'endtime' => $_POST['endtime'],
			'place' => $_POST['place'],
			'studentquota' => $_POST['studentquota'],
			'organiserquota' => $_POST['organiserquota']
		));

		//newly inserted events's eventid
		$eventid = DB::queryFirstRow('select last_insert_id()')['last_insert_id()'];
	} else {
		//organiser has edited an existing event
		$eventid = $_POST['eventid'];
		//update event in events table
		DB::update('events', array(
			'eventdate' => $_POST['eventdate'],
			'week' => $_POST['week'],
			'day' => $_POST['day'],
			'starttime' => $_POST['starttime'],
			'endtime' => $_POST['endtime'],
			'place' => $_POST['place'],
			'studentquota' => $_POST['studentquota'],
			'organiserquota' => $_POST['organiserquota']
		), 'eventid = %s', $eventid);
	}
	//delete all students previously allocated to this event
	DB::delete('studentevents', 'eventid = %s', $eventid);
	//get the newly allocated students' names
	$studentnames = [$_POST['student1'], $_POST['student2'], $_POST['student3'], $_POST['student4'], $_POST['student5'], $_POST['student6'], $_POST['student7'], $_POST['student8']];
	foreach ($studentnames as $name) {
		//get student's username from his name
		$username = DB::queryFirstRow('select username from users where lower(concat(fname, " ", lname)) = %s and usertype = "s"', $name)['username'];
		if ($username) {
			//if the entered name is valid
			//add newly allocated student to table
			DB::insert('studentevents', array(
				'username' => $username,
				'eventid' => $eventid
			));
		}
	}

	//delete all organisers previously allocated to this event
	DB::delete('organiserevents', 'eventid = %s', $eventid);
	//get the newly allocated organisers' names
	$organisernames = [$_POST['organiser1'], $_POST['organiser2'], $_POST['organiser3'], $_POST['organiser4']];
	foreach ($organisernames as $name) {
		//get student's username from his name
		$username = DB::queryFirstRow('select username from users where lower(concat(fname, " ", lname)) = %s and usertype = "o"', $name)['username'];
		if ($username) {
			//if the entered name is valid
			//add newly allocated organiser to organisers
			DB::insert('organiserevents', array(
				'username' => $username,
				'eventid' => $eventid
			));
		}
	}
	alert('info', 'ROSTER HAS BEEN SAVED');
}
?>

<div class="page-content">
	<h2>Select Week & Day</h2>
	<form name="weekdayform" method="post" action="rosteredit.php">
		<table class="weekday-table"><tr>
		<th>WEEK: </th>
		<td>
			<select class="form-control" name="week" id="week">
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
				//select options for week numbers in current term
				for ($weeknum=1; $weeknum<=$numOfWeeks; $weeknum++) { 
					echo '<option value="'.$weeknum.'">'.$weeknum.'</option>';
				}
				?>
			</select>
		</td>
		<th id="groupby-heading">DAY: </th>
		<td>
			<select class="form-control" name="day" id="day">
				<option value="mon" selected>MON</option>
				<option value="tue">TUE</option>
				<option value="wed">WED</option>
				<option value="thu">THU</option>
				<option value="fri">FRI</option>
			</select>
		</td>
		</tr></table>
		<input class="button" type="submit" name="submitweekday" value="PROCEED">
	</form>

	<?php
	if (isset($_POST['submitweekday'])) {
		//if week and day submitted
		$week = $_POST['week'];
		$day = $_POST['day'];
		//retrieve event with the selected week and day
		$existingEvent = DB::queryFirstRow('select * from events where week = %s and day = %s', $week, $day);
		if ($existingEvent) {
			//if event already exists on that day
			$newevent = 0;
			$eventid = $existingEvent['eventid'];
			$heading = 'Edit Existing';
			//create strings and arrays for automatic insertion of input field values
			$eventdate = $existingEvent['eventdate'];
			$place = $existingEvent['place'];
			$starttime = $existingEvent['starttime'];
			$endtime = $existingEvent['endtime'];
			$studentquota = $existingEvent['studentquota'];
			$organiserquota = $existingEvent['organiserquota'];
			//initialising array for displaying the right number of student name input fields
			$studentquotaselected = ['','','','','','','',''];
			$studentquotaselected[$studentquota-1] = 'selected';
			//initialising array for displaying the right number of organiser name input fields
			$organiserquotaselected = ['','','','','','','',''];
			$organiserquotaselected[$organiserquota-1] = 'selected';
			//retrieve student names allocated to this event
			$studentnames = array_column(DB::query('select concat(fname, " ", lname) as name from users where username in (select username from studentevents where eventid = %s) order by lname', $eventid), 'name');
			$numStudents = count($studentnames);
			//add empty strings to remaining positions to make an array of 8 elements
			$studentnames = array_merge($studentnames, array_fill(0, 8-$numStudents, ''));
			//retrieve organiser names allocated to this event
			$organisernames = array_column(DB::query('select concat(fname, " ", lname) as name from users where username in (select username from organiserevents where eventid = %s) order by lname', $eventid), 'name');
			$numOrganisers = count($organisernames);
			//add empty strings to remaining positions to make an array of 8 elements
			$organisernames = array_merge($organisernames, array_fill(0, 4-$numOrganisers, ''));
		} else {
			//if event does not already exist on that day
			$newevent = 1;
			$eventid = '';
			$heading = 'Add New';
			//create empty strings and arrays for input field values
			$eventdate = '';
			$place = $starttime = $endtime = '';
			$studentquotaselected = $organiserquotaselected = ['','','','','','','',''];
			$studentnames = ['','','','','','','',''];
			$organisernames = ['','','','',''];
		}

		//generate the edit roster form
		echo'
		<h2>'.$heading.' Event</h2>
		<h3>Week '.$week.' '.strtoupper($day).'</h3>
		<form name="rosteredit" method="post" action="rosteredit.php" class="form-horizontal roster-edit-form">
			<div class="form-group" style="width: 200px; margin: 0 auto 30px auto;">
				<label class="control-label" for="eventdate">DATE</label>
				<input type="date" class="form-control" name="eventdate" id="eventdate" value="'.$eventdate.'" required>
			</div>
			<div class="form-group" style="width: 400px; margin: 0 auto 30px auto;">
				<label class="control-label" for="place">PLACE</label>
				<input type="text" class="form-control" name="place" id="place" maxlength="80" value="'.$place.'" required>
			</div>
			<table class="roster-edit-table">
				<tr>
					<td><label class="control-label" for="starttime">START TIME</label></td>
					<td><label class="control-label" for="endtime">END TIME</label></td>
				</tr>
				<tr>
					<td><input class="form-control" type="time" name="starttime" id="starttime" value="'.$starttime.'"></td>
					<td><input class="form-control" type="time" name="endtime" id="endtime" value="'.$endtime.'"></td>
				</tr>
			</table>
			<table class="roster-edit-table" style="margin-bottom: 40px;">
				<tr>
					<td><label class="control-label" for="studentquota">STUDENT QUOTA</label></td>
					<td><label class="control-label" for="organiserquota">ORGANISER QUOTA</label></td>
				</tr>
				<tr>
					<td>
					<select class="quota-select" name="studentquota" id="studentquota">';
		//input select option for student quota
		for ($i=1; $i<=8; $i++) { 
			echo '<option value="'.$i.'" '.$studentquotaselected[$i-1].'>'.$i.'</option>';
		}
		echo '</select></td><td>
		<select class="quota-select" name="organiserquota" id="organiserquota">';
		//input select option for organiser quota
		for ($i=1; $i<=4; $i++) { 
			echo '<option value="'.$i.'" '.$organiserquotaselected[$i-1].'>'.$i.'</option>';
		}
		echo'</select></td></tr><tr>
		<td><label class="control-label">STUDENT NAMES</label></td>
		<td><label class="control-label">ORGANISER NAMES</label></td>
		</tr><tr><td>';
		//input text fields for student names
		for ($i=1; $i<=8; $i++) { 
			echo '<input type="text" class="form-control" name="student'.$i.'" value="'.$studentnames[$i-1].'">';
		}
		echo '</td><td>';
		//input text fields for organiser names
		for ($i=1; $i<=4; $i++) { 
			echo '<input type="text" class="form-control" name="organiser'.$i.'" value="'.$organisernames[$i-1].'">';
		}
		echo '</td></tr></table>
		<input type="hidden" name="newevent" value="'.$newevent.'">
		<input type="hidden" name="week" value="'.$week.'">
		<input type="hidden" name="day" value="'.$day.'">
		<input type="hidden" name="eventid" value="'.$eventid.'">
		<input class="button" type="submit" name="submitrosteredit" value="SUBMIT">
		</form>';
	}
	?>
</div>

</body>
</html>