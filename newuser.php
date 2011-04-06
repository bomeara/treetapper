<?php
$_GET['pagetitle']="TreeTapper: New user";
include('templates/template_pagestart.php');
echo('<script type="text/javascript">
function validateForm()
{
	if (document.forms["newuser"]["firstname"].value==null || document.forms["newuser"]["firstname"].value=="") {
	  alert("First name must be filled out");
	  return false;
	}
	else if (document.forms["newuser"]["lastname"].value==null || document.forms["newuser"]["lastname"].value=="") {
	  alert("First name must be filled out");
	  return false;
	}
	else if (document.forms["newuser"]["email"].value==null || document.forms["newuser"]["email"].value=="") {
	  alert("Email must be filled out");
	  return false;
	}
	else if (document.forms["newuser"]["emailagain"].value==null || document.forms["newuser"]["emailagain"].value=="") {
	  alert("Email must be filled out twice");
	  return false;
	}
	else if (document.forms["newuser"]["emailagain"].value!=document.forms["newuser"]["email"].value) {
	  alert("The two email addresses do not match");
	  return false;
	}
	else if (document.forms["newuser"]["newpassword"].value==null || document.forms["newuser"]["newpassword"].value=="") {
	  alert("One of the key requirements for password-protecting a site is actually having a password: please add one");
	  return false;
	}

  
}
</script>');
echo(' <script type="text/javascript">
 var RecaptchaOptions = {
    theme : "white"
 };
 </script>');
      echo("<form name='newuser' method='post' onsubmit='return validateForm();' action='add_newuser.php'>");
      echo ("First name:<br /><input type=\"text\" size=\"75\" name='firstname' id='firstname' ><br /><br />");
      echo ("Middle initial:<br /><input type=\"text\" size=\"75\" name='middleinitial' id='middleinitial' ><br /><br />");
      echo ("Last name:<br /><input type=\"text\" size=\"75\" name='lastname' id='lastname' ><br /><br />");
      echo ("Suffix (Jr., IV, etc.):<br /><input type=\"text\" size=\"75\" name='suffix' id='suffix' ><br /><br />");
      echo ("Home page:<br /><input type=\"text\" size=\"75\" name='url' id='url' ><br /><br />");
      echo ("Email (enter <b>twice</b> for verification):<br /><input type=\"text\" size=\"75\" name='email' id='email' >");
      echo ("<br /><input type=\"text\" size=\"75\" name='emailagain' id='emailagain' ><br /><br />");
      echo ("Password (at least eight characters):<br /><input type=\"password\" size=\"75\" name='newpassword' id='newpassword' ><br />");

		echo('<br />Verify that you are human before being added:<br />');

          require_once('recaptchalib.php');
          $publickey = "6Ldn5MESAAAAACkuCwOeZOWUgPJC5qRCPx8oNARj"; // you got this from the signup page
          echo recaptcha_get_html($publickey);
      echo("<input type=submit /></form>");

include('templates/template_pageend.php');
?>
