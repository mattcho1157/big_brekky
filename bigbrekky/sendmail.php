<?php
require_once('email/SMTP.php');
require_once('email/PHPMailer.php');
require_once('email/Exception.php');

use \PHPMailer\PHPMailer\PHPMailer;
use \PHPMailer\PHPMailer\Exception;

function sendVerificationEmail($address, $username, $token) {
	try {
		$mail = new PHPMailer(true); // Passing `true` enables exceptions
		//settings
		$mail->SMTPDebug = 0; // Enable verbose debug output
		$mail->isSMTP(); // Set mailer to use SMTP
		$mail->Host = 'smtp.gmail.com';
		$mail->SMTPAuth = true; // Enable SMTP authentication
		$mail->Username = 'Gtdigisol2020@gmail.com'; // SMTP username
		$mail->Password = 'securepassword'; // SMTP password
		$mail->SMTPSecure = 'ssl';
		$mail->Port = 465;

		$mail->setFrom('Gtdigisol2020@gmail.com', 'Digisol Test');

		//recipient
		//default 2FA email for testing purposes only
		$address = "s0157581@terrace.qld.edu.au";
		$mail->addAddress($address, $username); // Add a recipient
		$verificationlink = 'http://localhost/bigbrekky/index.php?username='.urlencode($username).'&token='.$token;
		
		//content
		$mail->isHTML(true); // Set email format to HTML
		$mail->Subject = 'Big Brekky Account Verification';

		$mail->Body = '
		<h1>BIG BREKKY ACCOUNT VERIFICATION</h1>
		<h3>Username: '.$username.'</h3>
		<p style="font-size: 16px;">Please click this link to verify your account: </p>
		<a style="font-size: 16px; text-decoration: none;" href="'.$verificationlink.'">VERIFY</a>';

		$mail->AltBody = "BIG BREKKY ACCOUNT VERIFICATION\r\n"."Please click this link to verify your account:\r\n".$verificationlink;

		$mail->send();
	} 
	catch(Exception $e) {
		echo 'Message could not be sent.';
		echo 'Mailer Error: '.$mail->ErrorInfo;
	}
}

function sendResubmitprefEmail($username, $reason) {
	try {
		$mail = new PHPMailer(true); // Passing `true` enables exceptions
		//settings
		$mail->SMTPDebug = 0; // Enable verbose debug output
		$mail->isSMTP(); // Set mailer to use SMTP
		$mail->Host = 'smtp.gmail.com';
		$mail->SMTPAuth = true; // Enable SMTP authentication
		$mail->Username = 'Gtdigisol2020@gmail.com'; // SMTP username
		$mail->Password = 'securepassword'; // SMTP password
		$mail->SMTPSecure = 'ssl';
		$mail->Port = 465;

		$mail->setFrom('Gtdigisol2020@gmail.com', 'Digisol Test');

		//recipient
		$mail->addAddress('s0157581@terrace.qld.edu.au', 'Admin');     // Add a recipient
		
		//content
		$mail->isHTML(true); // Set email format to HTML
		$mail->Subject = 'Big Brekky Student Preferences Resubmission Request';

		$mail->Body = '
		<h1>BIG BREKKY STUDENT PREFERENCES RESUBMISSION REQUEST</h1>
		<h3>Student Username: '.$username.'</h3>
		<p style="font-size: 16px;"> Reason: "'.$reason.'"</p>';

		$mail->AltBody = "BIG BREKKY STUDENT PREFERENCES RESUBMISSION REQUEST\r\nStudent Username: ".$username."\r\nReason: ".$reason;

		$mail->send();
	} 
	catch(Exception $e) {
		echo 'Message could not be sent.';
		echo 'Mailer Error: '.$mail->ErrorInfo;
	}
}

?>