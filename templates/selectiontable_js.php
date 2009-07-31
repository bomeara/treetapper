<?php
//optionbox_generic?table=tablename
include ('dblogin.php'); //connection in $db
$tablename=$_GET['table'];
$pending=0;
$minid=0;
$selectiontableid=0;
$authorid=0;
$authorname="";
$foreignkey=0;
if (strlen($_GET['includepending'])>0) {
	if (is_numeric($_GET['includepending'])) {
		$pending=$_GET['includepending'];
	}
}
if (strlen($_GET['minid'])>0) {
	if (is_numeric($_GET['minid'])) {
		$minid=$_GET['minid'];
	}
}
if (strlen($_GET['selectiontableid'])>0) {
	if (is_numeric($_GET['selectiontableid'])) {
		$selectiontableid=$_GET['selectiontableid'];
	}
}
if (strlen($_GET['authorid'])>0) {
	if (is_numeric($_GET['authorid'])) {
		$authorid=$_GET['authorid'];
	}
}
if (strlen($_GET['authorname'])>0) {
	if (is_numeric($_GET['authorname'])) {
		$authorname=pg_escape_string($_GET['authorname']);
	}
}
if (strlen($_GET['foreignkey'])>0) {
	if (is_numeric($_GET['foreignkey'])) {
		$foreignkey=pg_escape_string($_GET['foreignkey']);
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
	//checkbox / pending / author1 / year/ title/ journal/ volume / issue/ startpage / endpage/ ref id / most recent web hits / most recent pdf hits
	if (preg_match('/reference/',$tablename)) {
		$searchquery="SELECT reference_id, reference_title, reference_publicationdate, reference_publicationname, reference_volume, reference_issue, reference_startpage, reference_endpage, reference_approved, reference_doi, reference_pmid, reference_url FROM reference ORDER BY reference_publicationdate ASC";
		if ($authorid>0) {
			$searchquery="SELECT reference_id, reference_title, reference_publicationdate, reference_publicationname, reference_volume, reference_issue, reference_startpage, reference_endpage, reference_approved, reference_doi, reference_pmid, reference_url FROM reference, referencetoperson WHERE referencetoperson_person = $authorid AND referencetoperson_reference = reference_id ORDER BY reference_publicationdate ASC";
		}
		else if (strlen($authorname)>0) {
			$searchquery="SELECT reference_id, reference_title, reference_publicationdate, reference_publicationname, reference_volume, reference_issue, reference_startpage, reference_endpage, reference_approved, reference_doi, reference_pmid, reference_url FROM reference, referencetoperson WHERE referencetoperson_person = person_id AND referencetoperson_reference = reference_id AND person_last ILIKE $authorname ORDER BY reference_publicationdate ASC";
		}
		$searchall= pg_query($db,$searchquery) or die("Could not query the database.");
		if (pg_numrows($searchall)>0) {
			for ($lt = 0; $lt < pg_numrows($searchall); $lt++) {
				if (($pending==1 && (pg_fetch_result($searchall,$lt,8)>=0)) || (pg_fetch_result($searchall,$lt,8)>0)) {
					
					if ($pending==1) {
						if (pg_fetch_result($searchall,$lt,8)==1) {
							echo("false\t+\t");
						}
						else if (pg_fetch_result($searchall,$lt,8)==0) {
							echo("false\t.\t");
						}
					}
					else {
						echo("false\t+\t");
					}
					$searchforauthors=pg_query($db,"SELECT person_id, person_last, referencetoperson_authororder FROM person, referencetoperson WHERE referencetoperson_reference=".pg_fetch_result($searchall,$lt,0)." AND referencetoperson_person=person_id ORDER BY referencetoperson_authororder ASC");
					$authorlast="";
					if (pg_numrows($searchforauthors) == 1) {
						for ($la = 0; $la < pg_numrows($searchforauthors); $la++) {
							if (pg_fetch_result($searchforauthors,$la,2)==1) {
								echo pg_fetch_result($searchforauthors,$la,1);
								$authorlast=pg_fetch_result($searchforauthors,$la,1);
							}
						}
						echo "\t";
					}
					else if (pg_numrows($searchforauthors) == 2) {
						for ($la = 0; $la < pg_numrows($searchforauthors); $la++) {
							if (pg_fetch_result($searchforauthors,$la,2)==1) {
								echo pg_fetch_result($searchforauthors,$la,1);
								$authorlast=pg_fetch_result($searchforauthors,$la,1);
							}
						}
						echo " & ";
						for ($la = 0; $la < pg_numrows($searchforauthors); $la++) {
							if (pg_fetch_result($searchforauthors,$la,2)==2) {
								echo pg_fetch_result($searchforauthors,$la,1);
							}
						}
						echo "\t";
					}
					else {
						for ($la = 0; $la < pg_numrows($searchforauthors); $la++) {
							if (pg_fetch_result($searchforauthors,$la,2)==1) {
								echo pg_fetch_result($searchforauthors,$la,1);
								$authorlast=pg_fetch_result($searchforauthors,$la,1);
							}
						}
						echo " et al.\t";
					}
					$title=pg_fetch_result($searchall,$lt,1);
					$year=pg_fetch_result($searchall,$lt,2);
					echo pg_fetch_result($searchall,$lt,2)."\t".pg_fetch_result($searchall,$lt,1)."\t".pg_fetch_result($searchall,$lt,3)."\t".pg_fetch_result($searchall,$lt,4)."\t".pg_fetch_result($searchall,$lt,5)."\t".pg_fetch_result($searchall,$lt,6)."-".pg_fetch_result($searchall,$lt,7)."\t";
					
					if (strlen(pg_fetch_result($searchall,$lt,9))>0) {
						echo pg_fetch_result($searchall,$lt,9)." ";
					}
					if (strlen(pg_fetch_result($searchall,$lt,10))>0) {
						echo pg_fetch_result($searchall,$lt,10)." ";
					}
					if (strlen(pg_fetch_result($searchall,$lt,11))>0) {
						echo "<a href='".pg_fetch_result($searchall,$lt,11)."' target='_blank'>".pg_fetch_result($searchall,$lt,11)."</a> ";
					}
					
					echo "\t".pg_fetch_result($searchall,$lt,0);
					//web hits: citationsource_id=2
					//pdf hits: citationsource_id=1
					for ($i=2;$i>0;$i--) {
						$webhitsql="SELECT citationcount_count  FROM citationcount WHERE citationcount_reference='".pg_fetch_result($searchall,$lt,0)."' AND citationcount_source='".$i."' ORDER BY citationcount_adddate DESC";
						$webhitresult=pg_query($db,$webhitsql);
						if (pg_numrows($webhitresult)>0) {
							echo "\t".pg_fetch_result($webhitresult,0,0);
						}
						else {
							echo "\tunknown";
						}	
					}
					
					$webquerystring=str_replace(" ","+",str_replace('  ',' ',str_replace("'",'%27',(str_replace('&','','%22'.$authorlast.'%22+'.$year.'+%22'.$title.'%22')))));
					echo "\t".$webquerystring;
					echo "\n";
				}
			}
		}
	}
	else if (preg_match('/charactercombination/',$tablename)) {
		
	}
	else if (preg_match('/posedquestion/', $tablename)) {
		// checkbox / pending / name / description / id
		$foreignkeysearch="";
		if ($foreignkey>0) {
			$foreignkeysearch=" AND posedquestion_generalquestion=$foreignkey ";
		}
		$result2 = pg_query($db,"SELECT $tablename"."_id, $tablename"."_name, $tablename"."_approved, $tablename"."_description, generalquestion_name FROM $tablename, generalquestion WHERE $tablename"."_id>"."$minid $foreignkeysearch AND generalquestion_id=posedquestion_generalquestion ORDER BY $tablename"."_id ASC") or die("Could not query the database.");
		for ($lt = 0; $lt < pg_numrows($result2); $lt++) {
			if (($pending==1 && (pg_fetch_result($result2,$lt,2)>=0)) || (pg_fetch_result($result2,$lt,2)>0)) {
				if ($pending==1) {
					if (pg_fetch_result($result2,$lt,2)==1) {
					//	echo("<script type='text/javascript'>var oCheckButton".$tablename."_".$selectiontableid."_opt_".pg_fetch_result($result2,$lt,0)." = new YAHOO.widget.Button({ type: \"checkbox\", label: \"No characters\", id: \"checkbutton_".$tablename."_".$selectiontableid."_opt_".pg_fetch_result($result2,$lt,0)."\", name: \"checkbutton".$tablename."_".$selectiontableid."[]\", value: \"".pg_fetch_result($result2,$lt,0)."\", checked: false });</script>");
						//echo(pg_fetch_result($result2,$lt,0));
						echo("false\t+\t");
					}
					else if (pg_fetch_result($result2,$lt,2)==0) {
					//	echo("<script type='text/javascript'>var oCheckButton".$tablename."_".$selectiontableid."_opt_".pg_fetch_result($result2,$lt,0)." = new YAHOO.widget.Button({ type: \"checkbox\", label: \"No characters\", id: \"checkbutton_".$tablename."_".$selectiontableid."_opt_".pg_fetch_result($result2,$lt,0)."\", name: \"checkbutton".$tablename."_".$selectiontableid."[]\", value: \"".pg_fetch_result($result2,$lt,0)."\", checked: false });</script>");
						//echo(pg_fetch_result($result2,$lt,0));
						echo("false\t.\t");
					}
				}
				else {
					//echo("<script type='text/javascript'>var oCheckButton".$tablename."_".$selectiontableid."_opt_".pg_fetch_result($result2,$lt,0)." = new YAHOO.widget.Button({ type: \"checkbox\", label: \"No characters\", id: \"checkbutton_".$tablename."_".$selectiontableid."_opt_".pg_fetch_result($result2,$lt,0)."\", name: \"checkbutton".$tablename."_".$selectiontableid."[]\", value: \"".pg_fetch_result($result2,$lt,0)."\", checked: false });</script>");
					//echo(pg_fetch_result($result2,$lt,0));
					echo("false\t+\t");
				}
				echo(pg_fetch_result($result2,$lt,1)."\t".pg_fetch_result($result2,$lt,3)."\t".pg_fetch_result($result2,$lt,0)."\t".pg_fetch_result($result2,$lt,4)."\n");
			}
		}
	}
	else if (preg_match('/method/', $tablename)) {
		// checkbox / pending / name / description / id
		$foreignkeysearch="";
		if ($foreignkey>0) {
			$foreignkeysearch=" AND posedquestion_generalquestion=$foreignkey ";
		}
		$result2 = pg_query($db,"SELECT $tablename"."_id, $tablename"."_name, $tablename"."_approved, $tablename"."_description FROM $tablename WHERE $tablename"."_id>"."$minid $foreignkeysearch ORDER BY $tablename"."_id ASC") or die("Could not query the database.");
		for ($lt = 0; $lt < pg_numrows($result2); $lt++) {
			if (($pending==1 && (pg_fetch_result($result2,$lt,2)>=0)) || (pg_fetch_result($result2,$lt,2)>0)) {
				if ($pending==1) {
					if (pg_fetch_result($result2,$lt,2)==1) {
					//	echo("<script type='text/javascript'>var oCheckButton".$tablename."_".$selectiontableid."_opt_".pg_fetch_result($result2,$lt,0)." = new YAHOO.widget.Button({ type: \"checkbox\", label: \"No characters\", id: \"checkbutton_".$tablename."_".$selectiontableid."_opt_".pg_fetch_result($result2,$lt,0)."\", name: \"checkbutton".$tablename."_".$selectiontableid."[]\", value: \"".pg_fetch_result($result2,$lt,0)."\", checked: false });</script>");
						//echo(pg_fetch_result($result2,$lt,0));
						echo("false\t+\t");
					}
					else if (pg_fetch_result($result2,$lt,2)==0) {
					//	echo("<script type='text/javascript'>var oCheckButton".$tablename."_".$selectiontableid."_opt_".pg_fetch_result($result2,$lt,0)." = new YAHOO.widget.Button({ type: \"checkbox\", label: \"No characters\", id: \"checkbutton_".$tablename."_".$selectiontableid."_opt_".pg_fetch_result($result2,$lt,0)."\", name: \"checkbutton".$tablename."_".$selectiontableid."[]\", value: \"".pg_fetch_result($result2,$lt,0)."\", checked: false });</script>");
						//echo(pg_fetch_result($result2,$lt,0));
						echo("false\t.\t");
					}
				}
				else {
					//echo("<script type='text/javascript'>var oCheckButton".$tablename."_".$selectiontableid."_opt_".pg_fetch_result($result2,$lt,0)." = new YAHOO.widget.Button({ type: \"checkbox\", label: \"No characters\", id: \"checkbutton_".$tablename."_".$selectiontableid."_opt_".pg_fetch_result($result2,$lt,0)."\", name: \"checkbutton".$tablename."_".$selectiontableid."[]\", value: \"".pg_fetch_result($result2,$lt,0)."\", checked: false });</script>");
					//echo(pg_fetch_result($result2,$lt,0));
					echo("false\t+\t");
				}
				echo(pg_fetch_result($result2,$lt,1)."\t".pg_fetch_result($result2,$lt,3)."\t".pg_fetch_result($result2,$lt,0)."\t");
				$pdfcount=pg_query($db,"SELECT max(citationcount_count) FROM citationcount, method, reference, methodtoreference, citationsource WHERE citationcount_source=citationsource_id AND citationsource_id=1 AND methodtoreference_method=method_id AND methodtoreference_reference=reference_id AND citationcount_reference=reference_id AND age(citationcount_adddate)<'14 days' AND method_id=".pg_fetch_result($result2,$lt,0));

				if (pg_numrows($pdfcount)>0) {
					echo (pg_fetch_result($pdfcount,0,0)."\t");
				}
				else {
					echo ("\t");
				}
				
				$htmlcount=pg_query($db,"SELECT max(citationcount_count) FROM citationcount, method, reference, methodtoreference, citationsource WHERE citationcount_source=citationsource_id AND citationsource_id=2 AND methodtoreference_method=method_id AND methodtoreference_reference=reference_id AND citationcount_reference=reference_id AND age(citationcount_adddate)<'14 days' AND method_id=".pg_fetch_result($result2,$lt,0));
				if (pg_numrows($htmlcount)>0) {
					echo (pg_fetch_result($htmlcount,0,0)."\n");
				}
				else {
					echo ("\n");
				}
				
			}
		}
	}
	
	else {
		// checkbox / pending / name / description / id
		$result2 = pg_query($db,"SELECT $tablename"."_id, $tablename"."_name, $tablename"."_approved, $tablename"."_description FROM $tablename WHERE $tablename"."_id>"."$minid ORDER BY $tablename"."_id ASC") or die("Could not query the database.");
		for ($lt = 0; $lt < pg_numrows($result2); $lt++) {
			if (($pending==1 && (pg_fetch_result($result2,$lt,2)>=0)) || (pg_fetch_result($result2,$lt,2)>0)) {
				if ($pending==1) {
					if (pg_fetch_result($result2,$lt,2)==1) {
					//	echo("<script type='text/javascript'>var oCheckButton".$tablename."_".$selectiontableid."_opt_".pg_fetch_result($result2,$lt,0)." = new YAHOO.widget.Button({ type: \"checkbox\", label: \"No characters\", id: \"checkbutton_".$tablename."_".$selectiontableid."_opt_".pg_fetch_result($result2,$lt,0)."\", name: \"checkbutton".$tablename."_".$selectiontableid."[]\", value: \"".pg_fetch_result($result2,$lt,0)."\", checked: false });</script>");
						//echo(pg_fetch_result($result2,$lt,0));
						echo("false\t+\t");
					}
					else if (pg_fetch_result($result2,$lt,2)==0) {
					//	echo("<script type='text/javascript'>var oCheckButton".$tablename."_".$selectiontableid."_opt_".pg_fetch_result($result2,$lt,0)." = new YAHOO.widget.Button({ type: \"checkbox\", label: \"No characters\", id: \"checkbutton_".$tablename."_".$selectiontableid."_opt_".pg_fetch_result($result2,$lt,0)."\", name: \"checkbutton".$tablename."_".$selectiontableid."[]\", value: \"".pg_fetch_result($result2,$lt,0)."\", checked: false });</script>");
						//echo(pg_fetch_result($result2,$lt,0));
						echo("false\t.\t");
					}
				}
				else {
					//echo("<script type='text/javascript'>var oCheckButton".$tablename."_".$selectiontableid."_opt_".pg_fetch_result($result2,$lt,0)." = new YAHOO.widget.Button({ type: \"checkbox\", label: \"No characters\", id: \"checkbutton_".$tablename."_".$selectiontableid."_opt_".pg_fetch_result($result2,$lt,0)."\", name: \"checkbutton".$tablename."_".$selectiontableid."[]\", value: \"".pg_fetch_result($result2,$lt,0)."\", checked: false });</script>");
					//echo(pg_fetch_result($result2,$lt,0));
					echo("false\t+\t");
				}
				echo(pg_fetch_result($result2,$lt,1)."\t".pg_fetch_result($result2,$lt,3)."\t".pg_fetch_result($result2,$lt,0)."\n");
			}
		}
	}
}
else {
	echo "invalid table name of $tablename";
}
?>


