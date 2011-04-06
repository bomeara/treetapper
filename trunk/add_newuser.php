<?php
$_GET['pagetitle']="TreeTapper: New user";
include('templates/template_pagestart.php');
require_once('recaptchalib.php');
$privatekey = "6Ldn5MESAAAAANQ-u1jVzj8RnG33cVIV94cS5f_I";
$resp = recaptcha_check_answer ($privatekey,
							$_SERVER["REMOTE_ADDR"],
							$_POST["recaptcha_challenge_field"],
							$_POST["recaptcha_response_field"]);

if (!$resp->is_valid) {
	// What happens when the CAPTCHA was entered incorrectly
	die ("The reCAPTCHA wasn't entered correctly. Please go back and try it again.");
} else {
	include ('templates/dblogin.php'); //connection in $db	
	$validinput=1;
	if (strlen($_POST['firstname']) < 1) {
		$validinput=0;
		echo "Input name: ".$_POST['firstname']." was too short<br>";
	}
	if (strlen($_POST['lastname']) < 1) {
		$validinput=0;
		echo "Input name: ".$_POST['lastname']." was too short<br>";
	}
	if (strlen($_POST['email']) < 4) {
		$validinput=0;
		echo "Input email: ".$_POST['email']." was too short<br>";
	}
	if (strcmp($_POST['email'], $_POST['emailagain']) != 0) {
		$validinput=0;
		echo "Input emails: ".$_POST['email']." and ".$_POST['emailagain']." did not match.<br>";
	}
	if (strlen($_POST['newpassword']) < 8) {
		$validinput=0;
		echo "Input password was too short<br>";
	}
	if ($validinput==1) {
		$escfirstname=pg_escape_string($_POST['firstname']);
		$escmiddleinitial=pg_escape_string($_POST['middleinitial']);
		$esclastname=pg_escape_string($_POST['lastname']);
		$escsuffix=pg_escape_string($_POST['suffix']);
		$escurl=pg_escape_string($_POST['url']);
		$escemail=pg_escape_string($_POST['email']);
		$escpassword=pg_escape_string(md5($_POST['newpassword']));
#		$personquery = pg_query($db,"SELECT person_id, person_first, person_middle, person_last, person_url, person_adddate, person_moddate, person_suffix FROM person WHERE (person_last=".$esclastname." AND substring(person_first from 1 for 1)=substring('",$escfirstname,"' from 1 for 1) ) ORDER BY person_id ASC") or die("Could not query the database.");
		$personquery = pg_query($db,"SELECT person_id, person_first, person_middle, person_last, person_url, person_adddate, person_moddate, person_suffix FROM person WHERE (person_last='".$esclastname."' ) ORDER BY person_id ASC") or die("Could not query the database.");
		if (pg_numrows($personquery)==0) {
			echo ("Welcome");
		}
		else {
			echo ("There are ".pg_numrows($personquery)." people with that name");

		}
#TODO
#		TODO: get $personid
#TODO		
#		$insertpersonsql="INSERT INTO personhiddeninfo (personhiddeninfo_password, personhiddeninfo_email, personhiddeninfo_trustlevel, personhiddeninfo_personid) VALUES ('".$escpassword."', '".$escemail."', '".$personid."', '1')";
#UNCOMMENT WHEN READY TO GO		$insertionresult = pg_query($db, $insertpersonsql)

		$to      = $_POST['email'];
		$subject = 'Welcome to TreeTapper';
		$message = 'Welcome to TreeTapper, '.$escfirstname."\r\nFor future reference, please note that you registered using\r\n\r\nEmail: ".$escemail."\r\nPassword: ".$escpassword."\r\n\r\nPlease keep this email for future reference.";
		$headers = 'From: bomeara@utk.edu' . "\r\n" .
			'Reply-To: bomeara@utk.edu' . "\r\n" .
			'X-Mailer: PHP/' . phpversion();
		
		mail($to, $subject, $message, $headers);
	}
}
include('templates/template_pageend.php');
?>
