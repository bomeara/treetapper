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
	//else {
	//	echo (pg_fetch_result($result,$lt,0)." != ".$tablename."\n<br>");
	//}
}
if ($validtablename==1) {
	$result2 = pg_query($db,"SELECT $tablename"."_id, $tablename"."_name FROM $tablename ") or die("Couldn't query the database.");
	echo("<option value=0>ANY</option>");
	for ($lt = 0; $lt < pg_numrows($result2); $lt++) {
		echo("\n<option value=".pg_fetch_result($result2,$lt,0).">".pg_fetch_result($result2,$lt,1)."</option>");
	}
}
else {
	echo "invalid table name of $tablename";
}
?>
