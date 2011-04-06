<?php
//optionbox_generic?table=tablename
include ('dblogin.php'); //connection in $db
$tablename=$_GET['table'];

//This section to infer that we have a valid table name
$validtablename=0;
$sql = "select relname from pg_stat_user_tables order by relname;";
$result = pg_query($db, $sql);
for ($lt = 0; $lt < pg_numrows($result); $lt++) {
	if(strcmp(pg_fetch_result($result,$lt,0),$tablename)==0) {
	//	echo (pg_fetch_result($result,$lt,0)." = ".$tablename."\n<br>");
		$validtablename=1;
	}
//	else {
//		echo (pg_fetch_result($result,$lt,0)." != ".$tablename."\n<br>");
//	}
}
if ($validtablename==1) {
	if (preg_match('/person/',$tablename)) {
	// id / name / url  / add date / mod date
		$result2 = pg_query($db,"SELECT person_id, person_first, person_middle, person_last, person_url, person_adddate, person_moddate FROM person ORDER BY $tablename"."_id ASC") or die("Could not query the database.");
		for ($lt = 0; $lt < pg_numrows($result2); $lt++) {
			echo (pg_fetch_result($result2,$lt,0)."\t".pg_fetch_result($result2,$lt,1)." ".pg_fetch_result($result2,$lt,2)." ".pg_fetch_result($result2,$lt,3)."\t".pg_fetch_result($result2,$lt,4)."\t".pg_fetch_result($result2,$lt,5)."\t".pg_fetch_result($result2,$lt,6)."\n");
		}	
		
	}
	else {
	// id / pending / name / description / added by / add date / mod date
		$result2 = pg_query($db,"SELECT $tablename"."_id, $tablename"."_name, $tablename"."_description, person_first, person_last, $tablename"."_adddate, $tablename"."_moddate, $tablename"."_approved FROM $tablename, person WHERE $tablename"."_addedby=person_id ORDER BY $tablename"."_id ASC") or die("Could not query the database.");
		for ($lt = 0; $lt < pg_numrows($result2); $lt++) {
			echo ("<a href='/".$tablename."/".pg_fetch_result($result2,$lt,0)."'>".pg_fetch_result($result2,$lt,0)."</a>\t");
			if (pg_fetch_result($result2,$lt,7)==1) {
				echo "+\t";
			}
			else if (pg_fetch_result($result2,$lt,7)<0) {
				echo "-\t";
			}
			else if (pg_fetch_result($result2,$lt,7)==0) {
				echo " \t";
			}
			else {
				echo "?\t";
			}
			
			echo(pg_fetch_result($result2,$lt,1)."\t".pg_fetch_result($result2,$lt,2)."\t".pg_fetch_result($result2,$lt,5)."\n");
		}	
	}
}
else {
	echo "invalid table name of $tablename";
}
?>


