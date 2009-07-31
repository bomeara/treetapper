<?php
	//optionbox_generic?table=tablename
	include ('dblogin.php'); //connection in $db
	$query="SELECT DISTINCT ON (program_id) program_id, program_name, program_description FROM ";
	$fromstring="program";
	$wherestring=" WHERE program_approved=1";
	//$query="SELECT DISTINCT ON (method_id) method_id, method_name, method_description FROM method, branchlengthtype, treetype, methodtotreetypetobranchlengthtype, methodtoreference, methodtocriterion, methodtocharactercombination, charactercombination, charactertype, criterion, methodtoposedquestion, generalquestion, posedquestion, posedquestiontocharactercombination, referencetoperson, person, reference, citationcount, citationsource WHERE methodtoreference_method=method_id AND methodtotreetypetobranchlengthtype_method=method_id AND methodtotreetypetobranchlengthtype_branchlengthtype=branchlengthtype_id AND methodtotreetypetobranchlengthtype_treetype=treetype_id AND methodtocriterion_method=method_id AND methodtocharactercombination_method=method_id AND methodtoposedquestion_method=method_id AND  methodtoposedquestion_posedquestion=posedquestion_id AND posedquestion_generalquestion=generalquestion_id AND methodtocriterion_criterion=criterion_id AND methodtoreference_reference=reference_id AND referencetoperson_person=person_id AND citationcount_reference=reference_id AND citationcount_source=citationsource_id AND methodtocharactercombination_charactercombination=charactercombination_id AND charactercombination_char1=charactertype_id AND charactercombination_char2=charactertype_id AND charactercombination_char3=charactertype_id AND posedquestiontocharactercombination_posedquestion=posedquestion_id AND posedquestiontocharactercombination_charactercombination=charactercombination_id AND method_approved=1 ";
	//$query="SELECT DISTINCT ON (method_id) method_id, method_name, method_description FROM method ";
	
	
	if (strlen($_GET['dataformat'])>0) {
		if (is_numeric($_GET['dataformat'])) {
			if ($_GET['dataformat']>0) {
				$fromstring.=", programtodataformat";
				$wherestring.=" AND programtodataformat_id=".$_GET['dataformat']." AND programtodataformat_program=program_id";
			}
		}
	}
	
	if (strlen($_GET['treeformat'])>0) {
		if (is_numeric($_GET['treeformat'])) {
			if ($_GET['treeformat']>0) {
				$fromstring.=", programtotreeformat";
				$wherestring.=" AND programtotreeformat_id=".$_GET['treeformat']." AND programtotreeformat_program=program_id";
			}
		}
	}
	
	if (isset($_GET['opensourcecheckbox'])) {
		$wherestring.=" AND program_open='1'";
	}
	
	
	if (isset($_GET['freeprogramcheckbox'])) {
		$wherestring.=" AND program_open='1'";
	}
	
	if (strlen($_GET['applicationkind'])>0) {
		if (is_numeric($_GET['applicationkind'])) {
			if ($_GET['applicationkind']>0) {
				$wherestring.="  AND programtoplatformappkind_applicationkind=".$_GET['applicationkind']." AND programtoplatformappkind_program=program_id";
				$fromstring.=", programtoplatformappkind";
			}
		}
	}
	
	if (strlen($_GET['platform'])>0) {
		if (is_numeric($_GET['platform'])) {
			if ($_GET['platform']>0) {
				$wherestring.=" AND programtoplatformappkind_platform=".$_GET['platform'];
				if (strlen($_GET['applicationkind'])>0) { //we don't want to call programtoplatformappkind twice
					if (is_numeric($_GET['applicationkind'])) {
						if ($_GET['applicationkind']>0) {
						}
						else {
							$wherestring.=" AND programtoplatformappkind_program=program_id";
							$fromstring.=", programtoplatformappkind";
						}
					}
				}
			}
		}
	}
	
	if (isset($_GET['checkbuttonmethod_0'])) {
		$fromstring.=", programtomethodtocharactercombination";
		$methodcount=0;
		foreach ($_GET['checkbuttonmethod_0'] as $checkbuttonmethodorder => $checkbuttonmethodid) {
			if ($methodcount>0) {
				$wherestring.=" OR ";
			}
			else {
				$wherestring.=" AND ( ";
			}
			$wherestring.=" programtomethodtocharactercombination_method='".$checkbuttonmethodid."'";
		}
		$wherestring.=" ) AND programtomethodtocharactercombination_program=program_id";
	}
	
	if (strlen($_GET['character1'])>0) {
		if (!isset($_GET['checkbuttonmethod_0'])) { 
			$fromstring.=", programtomethodtocharactercombination"; //this wasn't declared elsewhere
		}
			if (is_numeric($_GET['character1'])) {
			if ($_GET['character1']>0) {
				$fromstring.=", charactercombination";
				$wherestring.=" AND charactercombination_char1=".$_GET['character1']." AND programtomethodtocharactercombination_charactercombination=charactercombination_id";
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
	
	
	
/*	if (strlen($_GET['generalquestion'])>0) {
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
	
	
	*/
	//id / name / description / pdf / html
	$query.="$fromstring";
	$query.="$wherestring";
	$programresult = pg_query($db,$query) or die("Could not query the database.");
	for ($lt = 0; $lt < pg_numrows($programresult); $lt++) {
		$pdfcount=0;
		$htmlcount=0;
		echo(pg_fetch_result($programresult,$lt,0)."\t".pg_fetch_result($programresult,$lt,1)."\t".pg_fetch_result($programresult,$lt,2)."\t");
		$pdfcount=pg_query($db,"SELECT max(citationcount_count) FROM citationcount, reference, programtoreference, citationsource WHERE citationcount_source=citationsource_id AND citationsource_id=1 AND programtoreference_reference=reference_id AND citationcount_reference=reference_id AND age(citationcount_adddate)<'14 days' AND programtoreference_program=".pg_fetch_result($programresult,$lt,0));
		
		if (pg_numrows($pdfcount)>0) {
			echo (pg_fetch_result($pdfcount,0,0)."\t");
		}
		else {
			echo ("\t");
		}
		
		$htmlcount=pg_query($db,"SELECT max(citationcount_count) FROM citationcount, reference, programtoreference, citationsource WHERE citationcount_source=citationsource_id AND citationsource_id=2 AND programtoreference_reference=reference_id AND citationcount_reference=reference_id AND age(citationcount_adddate)<'14 days' AND programtoreference_program=".pg_fetch_result($programresult,$lt,0));
		if (pg_numrows($htmlcount)>0) {
			echo (pg_fetch_result($htmlcount,0,0)."\tfalse\n");
		}
		else {
			echo ("\tfalse\n");
		}
	}
	if (pg_numrows($programresult)==0) {
		echo ("\t");
	}
	
	
	
	?>