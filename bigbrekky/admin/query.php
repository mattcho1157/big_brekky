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
	<title>Big Brekky | Admin</title>

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
				<li><a href="query.php" id="current-page">QUERY</a></li>
				<li><a href="semesterreport.php">SEMESTER REPORT</a></li>
			</ul>
			<ul class="nav navbar-nav navbar-right">
				<li><a href="?logout"><span class="glyphicon glyphicon-user"></span> LOGOUT</a></li>
			</ul>
		</div>
	</div>
</nav>

<div class="banner general-banner">
	<h1>QUERY</h1>
</div>

<div class="page-content">
	<form name="adminquery" method="post" action="query.php" class="textarea-form">
		<h2>Query Engine</h2>
		<textarea name="query" class="form-control" id="query-textarea"></textarea>
		<input class="button" type="submit" name="submitquery" value="SUBMIT QUERY">
	</form>


	<?php
	if (isset($_POST['submitquery'])) {
		//since we're catching errors, error handler not needed
		DB::$error_handler = false;
		DB::$throw_exception_on_error = true;
		try {
			$query = trim($_POST['query']);
			$queryresults = DB::query($query);
			echo '
				<div class="panel panel-success">
					<div class="panel-heading">QUERY</div>
					<div class="panel-body" id="query-panel">'.$query.'</div>
				</div>';

			//if the query returns a results table
			if (is_array($queryresults)) {
				$keys = array_keys($queryresults[0]);

				echo '
				<div class="panel panel-info">
					<div class="panel-heading">RESULTS TABLE</div>
					<div class="panel-body" style="padding-top:30px;">
						<div class="table-responsive">
							<table class="table table-hover retreats-table">';
				//getting table headings
				echo '<tr>';
				foreach ($keys as $key) {
					echo '<th>'.$key.'</th>';
				}
				echo '</tr>';

				//iterating through each row and displaying data
				foreach ($queryresults as $rowdata) {
					echo '<tr>';
					foreach ($rowdata as $key => $value) {
						echo '<td>'.$value.'</td>';
					}
					echo '</tr>';
				}
				echo '
							</table>
						</div>
					</div>
				</div>';
			} else {
				echo '
				<div class="panel panel-info">
					<div class="panel-heading">RESULTS TABLE</div>
					<div class="panel-body">QUERY SUCCESSFUL</div>
				</div>';
			}
		} catch (MeekroDBException $e) {
			//catch meekroDB error
			echo '
				<div class="panel panel-danger">
					<div class="panel-heading">ERROR</div>
					<div class="panel-body" id="query-panel">'.$e->getMessage().'</div>
				</div>';
		}
	}
		
	?>
</div>

</body>
</html>