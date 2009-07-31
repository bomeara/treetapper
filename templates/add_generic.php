<?php
include_once ('dblogin.php'); //connection in $db
require ('checkauth.php');
$tablename=$_GET['table'];
$validtablename=0;
$tabledescription="";
$tableid="";
$simpletables = array(8, 1,2,4,12, 21); //simple tables just have name, description, addedby, approved, and adddate/moddate: no references, no foreign keys to anything but person, no need to add dependent info. table ids from tablelist table
$simpletableswithref = array(9, 35, 26); //as above, plus a foreign key pointing to a single reference
$slashedname="'".addslashes($tablename)."'";
$sql = "SELECT tablelist_description, tablelist_id FROM tablelist WHERE tablelist_name ILIKE $slashedname";
$result = pg_query($db, $sql);
if (pg_numrows($result)==1) {
	$validtablename=1;
	$tabledescription=pg_fetch_result($result,0,0);
	$tableid=pg_fetch_result($result,0,1);
}
if ($validtablename==1) {
	if (!$_POST['genericname'] || !$_POST['genericdescription']) {
		if (in_array($tableid, $simpletables)) {
			echo "<input type='button' value='+' onclick='activatesimpletable(\"Make new ".strtolower($tabledescription)."\")'>";
		}
		else if (in_array($tableid, $simpletableswithref) ) {
			//do ref thing
			
		}
		else if ($tableid==22) { //posed question
			//echo "<input type='button' value='+' onclick='window.open('".$treetapperbaseurl."/add_posedquestion.php','mywindow','')'>";
			echo "<button onClick=\"window.open('".$treetapperbaseurl."/add_posedquestion.php')\">+</button>";
		}
	}
	else {

		$result = pg_query($db,"SELECT personhiddeninfo_personid, personhiddeninfo_trustlevel FROM personhiddeninfo WHERE personhiddeninfo_password='$_SESSION[pass]' AND personhiddeninfo_email='$_SESSION[user]'") or die("Couldn't query the user-database.");
		$personid=pg_fetch_result($result,0,0);
		$persontrust=pg_fetch_result($result,0,1);
		$approved=0;
		if ($persontrust>=4) {
			$approved=1;
		}
		$validinput=1;
		if (strlen($_POST['genericname']) < 5) {
			$validinput=0;
		}
		else if (strlen($_POST['genericdescription']) < 10) {
			$validinput=0;
		}
		if ($validinput==1 && $approved==1) {
			if (in_array($tableid, $simpletables)) {
				$escquestion = pg_escape_string($_POST['genericname']);
				$escdescription = pg_escape_string($_POST['genericdescription']);
				echo "INSERT INTO $tablename ($tablename"."_name, $tablename"."_description, $tablename"."_addedby, $tablename"."_approved) VALUES ('{$escquestion}', '{$escdescription}', '$personid', '$approved')";
				
	//pg_exec($db,"INSERT INTO $tablename ($tablename"."_name, $tablename"."_description, $tablename"."_addedby, $tablename"."_approved) VALUES ('{$escquestion}', '{$escdescription}', '$personid', '$approved')");
				
			}
		}
	}
	}
?>
