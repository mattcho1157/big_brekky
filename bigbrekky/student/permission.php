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
	<title>Big Brekky | Permission</title>

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
				<li><a href="permission.php" id="current-page">PERMISSION</a></li>
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
	<h1>PARENTAL PERMISSION</h1> 
</div>

<div class="page-content">
	<h2>Download Form</h2><br>
	<?php
	$userPrefData = DB::queryFirstRow('select day from preferences where username = %s', $_SESSION['username']);
	if (!$userPrefData) {
		//student hasn't submitted retreat preferences
		echo '<p>You must submit your big brekky preferences before downloading the parental permission form.</p>';
	} else {
		//student has submitted retreat preferences
		//link to pdf file
		echo '
		<a class="button" href="http://localhost/bigbrekky/student/permissionform.pdf" target="_blank">OPEN FILE</a>

		<h2>Upload Completed Form</h2><br>';

		$pdfFileExists = file_exists('permissionforms/'.$_SESSION['username'].'.pdf');
		$pngFileExists = file_exists('permissionforms/'.$_SESSION['username'].'.png');
		$jpgFileExists = file_exists('permissionforms/'.$_SESSION['username'].'.jpg');
		
		//is student has already submitted a permission form
		if ($pdfFileExists || $pngFileExists || $jpgFileExists) {
			echo '<p>You have already submitted a permission form. If required, you can resubmit:</p>';
		}
		echo '
		<form action="permission.php" method="post" enctype="multipart/form-data">
			<input class="file" type="file" name="file" id="file">
			<input class="button" type="submit" value="UPLOAD" name="submitfile">
		</form>';
	}

	if (isset($_POST['submitfile'])) {
		//get file type and set file directory
		$fileType = strtolower(pathinfo(basename($_FILES["file"]["name"]), PATHINFO_EXTENSION));
		$file_dir = 'permissionforms/'.$_SESSION['username'].'.'.$fileType;
		
		if (move_uploaded_file($_FILES["file"]["tmp_name"], $file_dir)) {
			//if file has been successfully uploaded to server
			alert('info', 'YOU PERMISSION FORM HAS BEEN UPLOADED');
		} else {
			alert('danger', 'THERE WAS AN ERROR UPLOADING YOUR FILE');
		}
	}
	?>
</div>

</body>
</html>