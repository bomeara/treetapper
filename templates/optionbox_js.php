<?php
//optionbox_generic?table=tablename
include ('dblogin.php'); //connection in $db
$tablename=$_GET['table'];
$pending=0;
if (strlen($_GET['includepending'])>0) {
	$pending=$_GET['includepending'];
}
$hasforeignkey=0;
if (strlen($_GET['foreignkey'])>0) {
	if (is_numeric($_GET['foreignkey'])) {
		$foreignkey=$_GET['foreignkey'];
		$hasforeignkey=1;
	}
}

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
	if (preg_match('/reference/',$tablename)) {
		$limitbyperson="";
		if ($hasforeignkey==1) {
			$limitbyperson=" AND person_id=$foreignkey ";
		}
		$result2= pg_query($db,"SELECT reference_id, reference_title, reference_publicationdate, reference_publicationname, reference_volume, reference_issue, reference_startpage, reference_endpage, reference_approved, person_last FROM reference, person, referencetoperson WHERE reference_id=referencetoperson_reference AND person_id=referencetoperson_person AND referencetoperson_authororder=1 $limitbyperson ORDER BY person_last") or die("Could not query the database.");
		echo("ANY\t0");
		for ($lt = 0; $lt < pg_numrows($result2); $lt++) {
			if (($pending==1 && (pg_fetch_result($result2,$lt,8)>=0)) || (pg_fetch_result($result2,$lt,8)>0)) {
				echo("\n");
				if ($pending==1) {
					if (pg_fetch_result($result2,$lt,2)==1) {
						echo("Approved: ");
					}
					else if (pg_fetch_result($result2,$lt,2)==0) {
						echo("Pending:  ");
					}
				}
				$titlestring=pg_fetch_result($result2,$lt,1);
				$maxlengthtitle=30;
				if (strlen($titlestring)>$maxlengthtitle) {
					$titlestring=substr($titlestring,0,($maxlengthtitle-3));
					$titlestring=$titlestring.'...';
				}
				$maxlengthjournal=20;
				$journalstring=pg_fetch_result($result2,$lt,3);
				if (strlen($journalstring)>$maxlengthjournal) {
					$journalstring=substr($journalstring,0,($maxlengthjournal-3));
					$journalstring=$journalstring.'...';
				}
				
				echo(pg_fetch_result($result2,$lt,9)." ".pg_fetch_result($result2,$lt,2).". \"".$titlestring."\" ".$journalstring." ".pg_fetch_result($result2,$lt,4).": ".pg_fetch_result($result2,$lt,6)."-".pg_fetch_result($result2,$lt,7)."\t".pg_fetch_result($result2,$lt,0));
			}
		}
	}
	else {
		$limitbyforeignkey="";
		if ($hasforeignkey==1) {
			if (preg_match('/posedquestion/',$tablename)) {
				$limitbyforeignkey=" WHERE posedquestion_generalquestion=".$foreignkey;
			}
		}
		$result2 = pg_query($db,"SELECT $tablename"."_id, $tablename"."_name, $tablename"."_approved FROM $tablename $limitbyforeignkey ORDER BY $tablename"."_approved DESC") or die("Could not query the database.");
		echo("ANY\t0");
		for ($lt = 0; $lt < pg_numrows($result2); $lt++) {
			if (($pending==1 && (pg_fetch_result($result2,$lt,2)>=0)) || (pg_fetch_result($result2,$lt,2)>0)) {
				echo("\n");
				if ($pending==1) {
					if (pg_fetch_result($result2,$lt,2)==1) {
						echo("Approved: ");
					}
					else if (pg_fetch_result($result2,$lt,2)==0) {
						echo("Pending:  ");
					}
				}
				echo(pg_fetch_result($result2,$lt,1)."\t".pg_fetch_result($result2,$lt,0));
			}
		}
	}
}
else {
	echo "invalid table name of $tablename";
}
?>

