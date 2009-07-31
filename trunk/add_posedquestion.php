<?php
$_GET['pagetitle']="TreeTapper: Add specific question";
if (!$_POST['question'] || !$_POST['description']) {
	include('templates/template_pagestart.php');
	include ('templates/checkauth.php');
	echo "<h3>Add specific question</h3><br>";
	echo ("<br><br><div id=\"form\" align=\"left\">
<form action='$_SERVER[PHP_SELF]' method='post'>");
	echo ("<p>General question:<br>");
	echo "<select name='generalquestion' id='generalquestion'>";
	$_GET['table'] = 'generalquestion';
	include ('templates/optionbox_generic.php');
	echo "</select>";
	include ('templates/helppanel_generic.php');
	echo "</p>";
	/*echo ("<p>Character type 1:<br>");
	echo "<select name='chartype1' id='chartype1'>";
	$_GET['table'] = 'charactertype';
	include ('templates/optionbox_generic.php');
	echo "</select>";
	include ('templates/helppanel_generic.php');
	echo "</p>";
	echo ("<p>Character type 2:<br>");
	echo "<select name='chartype2' id='chartype2'>";
	$_GET['table'] = 'charactertype';
	include ('templates/optionbox_generic.php');
	echo "</select>";
	include ('templates/helppanel_generic.php');
	echo "</p>";
	echo ("<p>Character type 3:<br>");
	echo "<select name='chartype3' id='chartype3'>";
	$_GET['table'] = 'charactertype';
	include ('templates/optionbox_generic.php');
	echo "</select>";
	include ('templates/helppanel_generic.php');
	echo "</p>";*/
	echo ("New question:<br><input type=\"text\" size=\"100\" name='question' id='question'><br><br>
Brief description:<br><input type=\"text\" size=\"200\" name='description' id='description'><br>
<br>
<input type='submit' value='Submit'>
</form></div>");
	include('templates/template_pageend.php');

}
else {
	include('templates/template_pagestart.php');
	include ('templates/checkauth.php');
	include ('templates/dblogin.php'); //connection in $db
/*	$result = pg_query($db,"SELECT personhiddeninfo_personid, personhiddeninfo_trustlevel FROM personhiddeninfo WHERE personhiddeninfo_password='$_SESSION[pass]' AND personhiddeninfo_email='$_SESSION[user]'") or die("Couldn't query the user-database.");
	$personid=pg_fetch_result($result,0,0);
	$persontrust=pg_fetch_result($result,0,1);
	$approved=0;
	if ($persontrust>=4) {
		$approved=1;
	}*/
	$validinput=1;
	if (strlen($_POST[question]) < 5) {
		$validinput=0;
	}
	else if (strlen($_POST[description]) < 10) {
		$validinput=0;
	}
/*	else if ($_POST[chartype1]==0 && ($_POST[chartype2]>0 || $_POST[chartype3]>0)) {
		$validinput=0;
	}
	else if ($_POST[chartype2]==0 && $_POST[chartype3]>0) {
		$validinput=0;
	} */
	else if (strlen($_POST[generalquestion])==0) {
		$validinput=0;
	}
	if ($validinput==1) {
//		$result2=pg_query($db,"SELECT 
		$escquestion = pg_escape_string($_POST[question]);
		$escdescription = pg_escape_string($_POST[description]);

		
		pg_exec($db,"INSERT INTO posedquestion (posedquestion_name, posedquestion_description, posedquestion_addedby, posedquestion_approved, posedquestion_generalquestion) VALUES ('{$escquestion}', '{$escdescription}', '$personid', '$approved', '$_POST[generalquestion]')");
		echo "Data has been entered, <a href=\"".$treetapperbaseurl."/templates/selectXtocharactercombination.php?table=posedquestion\">click here</a> to add character types to this question";
	}
	echo "<p><br>";
	//$_GET['table'] = 'posedquestion';
	//$_GET['includeinform']='0';
	//include ('templates/selectXtocharactercombination.php');
	echo "</p>";
	include('templates/template_pageend.php');

}
?>
