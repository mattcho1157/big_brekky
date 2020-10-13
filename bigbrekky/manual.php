<?php
session_start();
require('globalfuncs.php');
checklogout();

$_SESSION['prevPage'] = 'manual.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<title>Big Brekky | Manual</title>

	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="/bs/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="bigbrekky.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="/bs/js/bootstrap.min.js"></script>

	<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500&family=Raleway:wght@200;300;400;700&display=swap" rel="stylesheet">
	<link rel="icon" href="img/logo.png">
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
			<a class="navbar-brand" href="./"><span><img src="img/logo.png"></span>BIG BREKKY</a>
		</div>

		<div class="collapse navbar-collapse" id="navbar">
			<ul class="nav navbar-nav">
				<li><a href="manual.php" id="current-page">MANUAL</a></li>
				<?php navbarlinks(); ?>
		</div>
	</div>
</nav>

<div class="banner" id="info-banner">
	<h1>MANUAL</h1> 
</div>

<div class="info-content">
	<h2>Procedure</h2>
	<table class="procedure-table">
		<tr>
			<th>6:15</th>
			<td>Arrive at Eddie’s Place – Waterford Place (Water St)</td>
		</tr>
		<tr>
			<th>6:15-6:30</th>
			<td>Pack van with the following:
			<ol>
				<li>Sausages from refrigerator (cover with glad wrap)</li>
				<li>4 eggs from refrigerator</li>
				<li>4 loaves of bread from refrigerator (3 buttered & 1 unbuttered)</li>
				<li>Check and fill sauce bottles</li>
				<li>BBQ equipment (i.e. the Morning Van container)</li>
				<li>BBQ and Gas bottle</li>
				<li>Table</li>
				<li>Clothes – check and fill container</li>
				<li>Debrief with students</li>
			</ol>
			</td>
		</tr>
		<tr>
			<th>6:30</th>
			<td>Drive to site for the day – Wickham Park</td>
		</tr>
		<tr>
			<th>6:45-7:40</th>
			<td>Cook and serve breakfast and chat</td>
		</tr>
		<tr>
			<th>7:40-7:50</th>
			<td>Clean-up and debrief</td>
		</tr>
	</table><br>

	<h2>Risk Management</h2>
	<p><b><i>Keep an eye out for the safety of you and your team. If there are any concerns, notify your Team Captain.</i></b></p>
	<p>
		<b>Personal details</b>
		<ul><li>No volunteer is to give out: surname, phone number or other personal details of yourself or anyone in your team.</li></ul>
	</p>
	<p>
		<b>Leaving the Van area</b>
		<ul>
			<li>ALWAYS remain in clear sight of the van and the Team Captain.</li>
			<li>All Team Members must remain within 20 meters of the van.</li>
			<li>No volunteer will go away from the van with one of the patrons.</li>
			<li>If you need to go to the toilet, let the Team Captain know and they will organise another volunteer to go with you. NEVER go on your own. </li>
			<li>If you feel threatened or uncomfortable in anyway, leave the conversation you are currently in and notify your Team Captain.</li>
		</ul>
	</p>
	<p>
		<b>If a fight breaks out</b>
		<p>The Team Captain will decide if it is appropriate to leave and will call a ‘LEAVING NOW’ situation:</p>
		<ol>
			<li>Follow the Team Captain’s directions.</li>
			<li>Immediately get into the van and be ready to depart.</li>
			<li>Do not worry about packing up.</li>
		</ol>
	</p>
</div>

<div class="info-quote">
	<p><i>"Love one another. As I have loved you, so you must love one another."</i><br><br>John: 13 34-35</p>
</div>

<div class="info-content-below row">
	<div class="col-xs-12 col-sm-6">
		<h2>Contacts</h2>
		<p><strong>Mrs. Judy McGuire</strong><br>(07) 3214 5245</p>
		<p><strong>Mr. Charles Brauer</strong><br>CharlesBrauer@terrace.qld.edu.au</p>
		<p><strong>Mr. Paul Antenucci</strong><br>PaulAntenucci@terrace.qld.edu.au</p>
	</div>
	<div class="col-xs-12 col-sm-6">
		<h2>Location</h2>
		<img src="img/googlemap.jpg" style="max-width: 100%; height: 220px;">
	</div>
</div>

<?php
//alert message pop-up for account log in / sign up
if (isset($_SESSION['loginSuccessful'])) {
	alert('info', 'LOG IN SUCCESSFUL'.$_SESSION['fname']);
	unset($_SESSION['loginSuccessful']);

} elseif (isset($_SESSION['signupSuccessful'])) {
	alert('info', 'SIGN UP SUCCESSFUL - PLEASE ACTIVATE YOUR ACCOUNT VIA EMAIL');
	unset($_SESSION['signupSuccessful']);
}
?>

</body>
</html>