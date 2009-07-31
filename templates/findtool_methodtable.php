<?php
//optionbox_generic?table=tablename
include ('dblogin.php'); //connection in $db
$query="SELECT DISTINCT ON (method_id) method_id, method_name, method_description FROM ";
$fromstring="method";
$wherestring=" WHERE method_approved=1";
//$query="SELECT DISTINCT ON (method_id) method_id, method_name, method_description FROM method, branchlengthtype, treetype, methodtotreetypetobranchlengthtype, methodtoreference, methodtocriterion, methodtocharactercombination, charactercombination, charactertype, criterion, methodtoposedquestion, generalquestion, posedquestion, posedquestiontocharactercombination, referencetoperson, person, reference, citationcount, citationsource WHERE methodtoreference_method=method_id AND methodtotreetypetobranchlengthtype_method=method_id AND methodtotreetypetobranchlengthtype_branchlengthtype=branchlengthtype_id AND methodtotreetypetobranchlengthtype_treetype=treetype_id AND methodtocriterion_method=method_id AND methodtocharactercombination_method=method_id AND methodtoposedquestion_method=method_id AND  methodtoposedquestion_posedquestion=posedquestion_id AND posedquestion_generalquestion=generalquestion_id AND methodtocriterion_criterion=criterion_id AND methodtoreference_reference=reference_id AND referencetoperson_person=person_id AND citationcount_reference=reference_id AND citationcount_source=citationsource_id AND methodtocharactercombination_charactercombination=charactercombination_id AND charactercombination_char1=charactertype_id AND charactercombination_char2=charactertype_id AND charactercombination_char3=charactertype_id AND posedquestiontocharactercombination_posedquestion=posedquestion_id AND posedquestiontocharactercombination_charactercombination=charactercombination_id AND method_approved=1 ";
//$query="SELECT DISTINCT ON (method_id) method_id, method_name, method_description FROM method ";


if (strlen($_GET['criterion'])>0) {
	if (is_numeric($_GET['criterion'])) {
		if ($_GET['criterion']>0) {
			$fromstring.=", criterion, methodtocriterion";
			$wherestring.=" AND criterion_id=".$_GET['criterion']." AND methodtocriterion_criterion=criterion_id AND methodtocriterion_method=method_id";
		}
	}
}

if (strlen($_GET['generalquestion'])>0) {
	if (is_numeric($_GET['generalquestion'])) {
		if ($_GET['generalquestion']>0) {
			$fromstring.=", generalquestion, posedquestion, methodtoposedquestion";
			$wherestring.=" AND generalquestion_id=".$_GET['generalquestion']." AND methodtoposedquestion_method=method_id AND methodtoposedquestion_posedquestion=posedquestion_id AND posedquestion_generalquestion=generalquestion_id";
			if (strlen($_GET['posedquestion'])>0) {
				if (is_numeric($_GET['posedquestion'])) {
					if ($_GET['posedquestion']>0) {
			//from string already set above
						$wherestring.=" AND posedquestion_id=".$_GET['posedquestion'];
					}
				}
			}
		}
	}
}



if (strlen($_GET['character1'])>0) {
	if (is_numeric($_GET['character1'])) {
		if ($_GET['character1']>0) {
			$fromstring.=", methodtocharactercombination, charactercombination, charactertype";
			$wherestring.=" AND charactercombination_char1=".$_GET['character1']." AND methodtocharactercombination_charactercombination=charactercombination_id AND charactercombination_char1=charactertype_id";
		}
	}
}

if (strlen($_GET['character2'])>0) {
	if (is_numeric($_GET['character2'])) {
		if ($_GET['character2']>0) {
			//from string already set above
			$wherestring.=" AND charactercombination_char2=".$_GET['character2'];
		}
	}
}

if (strlen($_GET['character3'])>0) {
	if (is_numeric($_GET['character3'])) {
		if ($_GET['character3']>0) {
			//from string already set above
			$wherestring.=" AND charactercombination_char3=".$_GET['character3'];
		}
	}
}

$hasbrlentype=0;
$hastreetype=0;

if (strlen($_GET['branchlengthtype'])>0) {
	if (is_numeric($_GET['branchlengthtype'])) {
		if ($_GET['branchlengthtype']>0) {
			$hasbrlentype=1;
		}
	}
}

if (strlen($_GET['treetype'])>0) {
	if (is_numeric($_GET['treetype'])) {
		if ($_GET['treetype']>0) {
			$hastreetype=1;
		}
	}
}

if ($hastreetype==1 || $hasbrlentype==1) {
	$fromstring.=", methodtotreetypetobranchlengthtype";
	$wherestring.=" AND methodtotreetypetobranchlengthtype_method=method_id";
	if ($hastreetype==1) {
		$fromstring.=", treetype";
		$wherestring.=" AND treetype_id=".$_GET['treetype'];
	}
	if ($hasbrlentype==1) {
		$fromstring.=", branchlengthtype";
		$wherestring.=" AND branchlengthtype_id=".$_GET['branchlengthtype'];
	}
}



if (strlen($_GET['authorid'])>0) {
	if (is_numeric($_GET['authorid'])) {
		if ($_GET['authorid']>0) {
			$fromstring.=", methodtoreference, reference, referencetoperson, person";
			$wherestring.=" AND methodtoreference_method=method_id AND methodtoreference_reference=reference_id AND referencetoperson_reference=reference_id AND referencetoperson_person=person_id AND person_id=".$_GET['authorid'];
		}
	}
}



//id / name / description / pdf / html
$query.="$fromstring";
$query.="$wherestring";
$methodresult = pg_query($db,$query) or die("Could not query the database.");
for ($lt = 0; $lt < pg_numrows($methodresult); $lt++) {
	$pdfcount=0;
	$htmlcount=0;
	echo(pg_fetch_result($methodresult,$lt,0)."\t".pg_fetch_result($methodresult,$lt,1)."\t".pg_fetch_result($methodresult,$lt,2)."\t");
	$pdfcount=pg_query($db,"SELECT max(citationcount_count) FROM citationcount, method, reference, methodtoreference, citationsource WHERE citationcount_source=citationsource_id AND citationsource_id=1 AND methodtoreference_method=method_id AND methodtoreference_reference=reference_id AND citationcount_reference=reference_id AND age(citationcount_adddate)<'14 days' AND method_id=".pg_fetch_result($methodresult,$lt,0));
	
	if (pg_numrows($pdfcount)>0) {
		echo (pg_fetch_result($pdfcount,0,0)."\t");
	}
	else {
		echo ("\t");
	}
	
	$htmlcount=pg_query($db,"SELECT max(citationcount_count) FROM citationcount, method, reference, methodtoreference, citationsource WHERE citationcount_source=citationsource_id AND citationsource_id=2 AND methodtoreference_method=method_id AND methodtoreference_reference=reference_id AND citationcount_reference=reference_id AND age(citationcount_adddate)<'14 days' AND method_id=".pg_fetch_result($methodresult,$lt,0));
	if (pg_numrows($htmlcount)>0) {
		echo (pg_fetch_result($htmlcount,0,0)."\tfalse\n");
	}
	else {
		echo ("\tfalse\n");
	}
}
if (pg_numrows($methodresult)==0) {
	echo ("\t");
}



?>