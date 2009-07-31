<?PHP
include ('dblogin.php');
$hascollaboratorid=false;
$collaboratorid="";
if (strlen($_GET['collaboratorid'])>0) {
	if (is_numeric($_GET['collaboratorid'])) {
		$collaboratorid=$_GET['collaboratorid'];
		$hascollaboratorid=true;
	}
}
$personsql="";
if ($hascollaboratorid) {
	$referenceidquery=pg_query($db, "SELECT reference_id FROM referencetoperson, reference WHERE referencetoperson_person=".$collaboratorid." AND referencetoperson_reference = reference_id");
	$collaboratorarray=array();
	for ($lr = 0; $lr < pg_numrows($referenceidquery); $lr++) {
		$referenceid=pg_fetch_result($referenceidquery,$lr,0);
		$collaboratorseach=pg_query($db, "SELECT referencetoperson_person FROM referencetoperson WHERE referencetoperson_reference=".$referenceid." AND referencetoperson_person != ".$collaboratorid);
		if ( pg_numrows($collaboratorseach)>0) {
			for ($lt = 0; $lt < pg_numrows($collaboratorseach); $lt++) {
				$collaboratorarray[pg_fetch_result($collaboratorseach,$lt,0)]++;
			}
		}
	}
	arsort($collaboratorarray, SORT_NUMERIC);
	echo '	    <table id="collaboratorhtmltable"> 
<thead> 
<tr> 
		<th>Name</th> 
		<th>URL</th> 
		<th>Total references</th> 
		<th>Methods</th> 
		<th>Programs</th> 
		<th>Coauthored references</th> 
</tr> 
 </thead> <tbody> ';
	foreach ($collaboratorarray as $key => $val) {
		$collabnamenquery = pg_query($db,"SELECT person_id, person_first, person_middle, person_last, person_url FROM person WHERE person_id=".$key) or die("Could not query the database.");
		
		$personid=$key;
		$personfirst=pg_fetch_result($collabnamenquery,0,1);
		$personmiddle=pg_fetch_result($collabnamenquery,0,2);
		$personlast=pg_fetch_result($collabnamenquery,0,3);	
		$personurl=pg_fetch_result($collabnamenquery,0,4);		
		$totalpapers = pg_query($db, "SELECT count(referencetoperson_id) FROM referencetoperson WHERE referencetoperson_person=".$key);
		$referencesql = "SELECT reference_id FROM person, reference, referencetoperson WHERE person_id = referencetoperson_person AND reference_id = referencetoperson_reference AND person_id = ".$personid;
		$referenceresult=pg_query($db,$referencesql);
		$personrefcount=pg_numrows($referenceresult);
		$personmethodcount=0;
		$personprogramcount=0;
		for ($lr = 0; $lr < pg_numrows($referenceresult); $lr++) {
			$referenceid2=pg_fetch_result($referenceresult,$lr,0);
			$methodsql= "SELECT count(method_id) FROM method, methodtoreference WHERE methodtoreference_method = method_id AND methodtoreference_reference = ".$referenceid2;
			$methodresult = pg_query($db, $methodsql);
			$personmethodcount+=pg_fetch_result($methodresult,0,0);
			$programsql= "SELECT count(program_id) FROM program, programtoreference WHERE programtoreference_program = program_id AND programtoreference_reference = ".$referenceid2;
			$programresult = pg_query($db, $programsql);
			$personprogramcount+=pg_fetch_result($programresult,0,0);		
		}
		//person id, first, middle, last, url, refcount, method count, program count, collab count
		echo "<tr><td><a href='".$treetapperbaseurl."/person/".$personid."'>$personfirst $personmiddle $personlast"."</a></td><td>$personurl</td><td>$personrefcount</td><td>$personmethodcount</td><td>$personprogramcount</td><td>$val</td></tr>\n";

	}	
	echo "</tbody> </table>\n";
}
else {
	echo '	    <table id="personhtmltable"> 
	<thead> 
	<tr> 
	<th>Name</th> 
	<th>URL</th> 
	<th>Total references</th> 
	<th>Methods</th> 
	<th>Programs</th> 
	</tr> 
	</thead> <tbody> ';
	
	$personsql = "SELECT person_id, person_first, person_middle, person_last, person_url FROM person ORDER BY person_last"; 
	$personresult = pg_query($db, $personsql);
	for ($lp = 0; $lp < pg_numrows($personresult); $lp++) {
		$personid=pg_fetch_result($personresult,$lp,0);
		$personfirst=pg_fetch_result($personresult,$lp,1);
		$personmiddle=pg_fetch_result($personresult,$lp,2);
		$personlast=pg_fetch_result($personresult,$lp,3);	
		$personurl=pg_fetch_result($personresult,$lp,4);
		$referencesql = "SELECT reference_id FROM person, reference, referencetoperson WHERE person_id = referencetoperson_person AND reference_id = referencetoperson_reference AND person_id = ".$personid;
		$referenceresult=pg_query($db,$referencesql);
		$personrefcount=pg_numrows($referenceresult);
		$personmethodcount=0;
		$personprogramcount=0;
		for ($lr = 0; $lr < pg_numrows($referenceresult); $lr++) {
			$referenceid=pg_fetch_result($referenceresult,$lr,0);
			$methodsql= "SELECT count(method_id) FROM method, methodtoreference WHERE methodtoreference_method = method_id AND methodtoreference_reference = ".$referenceid;
			$methodresult = pg_query($db, $methodsql);
			$personmethodcount+=pg_fetch_result($methodresult,0,0);
			$programsql= "SELECT count(program_id) FROM program, programtoreference WHERE programtoreference_program = program_id AND programtoreference_reference = ".$referenceid;
			$programresult = pg_query($db, $programsql);
			$personprogramcount+=pg_fetch_result($programresult,0,0);		
		}
	//person id, first, middle, last, url, refcount, method count, program count;
		echo "<tr><td><a href='".$treetapperbaseurl."/person/".$personid."'>$personfirst $personmiddle $personlast"."</a></td><td>$personurl</td><td>$personrefcount</td><td>$personmethodcount</td><td>$personprogramcount</td></tr>\n";
	}
	echo "</tbody> </table>\n";
}
?>
