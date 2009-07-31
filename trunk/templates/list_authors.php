<?PHP
include ('dblogin.php');
$querytext="";
if (strlen($_GET['query'])>0) {
		$querytext=pg_escape_string($_GET['query'].'%');
//	echo "$querytext<br>";
}
$sql="";
if (strlen($querytext)>0) {
//	echo "query text length is ".strlen($querytext)."<br>";
	$sql = "SELECT DISTINCT person_last, person_middle, person_first, person_id from person, referencetoperson WHERE person_id = referencetoperson_person AND person_last ILIKE '".$querytext."' ORDER BY person_last"; //Will only find people who are authors
//	echo "SQL is <br>".$sql."<br>";
}
else {
	$sql = "SELECT DISTINCT person_last, person_middle, person_first, person_id from person, referencetoperson WHERE person_id = referencetoperson_person ORDER BY person_last"; //Will only find people who are authors
}
$result = pg_query($db, $sql);
for ($lt = 0; $lt < pg_numrows($result); $lt++) {
	echo pg_fetch_result($result,$lt,0).", ".pg_fetch_result($result,$lt,2)." ".pg_fetch_result($result,$lt,1)."\t".pg_fetch_result($result,$lt,3)."\n";
}
?>
