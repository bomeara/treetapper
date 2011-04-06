<?PHP
$_GET['pagetitle']="TreeTapper.org";
include('templates/template_pagestart.php');
include ('templates/dblogin.php'); //connection in $db
echo("<p>This is a site for finding tools to better understand biology using trees, and to identify areas where tools are missing. It's not yet fully operational, but poke around the menus for more info; you should also go to the <a href=\"http://treetapper-dev.blogspot.com/\">development blog</a> to see how the site is being created and its current status. I would really appreciate any suggestions you have, too: email me at <a href='bomeara@utk.edu'>bomeara@utk.edu</a>.");
	?>
	 <?PHP
	 echo ("</p><br />");
$personquery=pg_query($db,"SELECT count(person_id) from person");
$personcount=pg_fetch_result($personquery,0,0);
$posedquestionquery=pg_query($db,"SELECT count(posedquestion_id) from posedquestion");
$posedquestioncount=pg_fetch_result($posedquestionquery,0,0);
$referencequery=pg_query($db,"SELECT count(reference_id) from reference");
$referencecount=pg_fetch_result($referencequery,0,0);
$programquery=pg_query($db,"SELECT count(program_id) from program");
$programcount=pg_fetch_result($programquery,0,0);
$methodquery=pg_query($db,"SELECT count(method_id) from method");
$methodcount=pg_fetch_result($methodquery,0,0);
$actualmethodsquery=pg_query($db, "SELECT count(actualmethods_method) from actualmethods");
$actualmethodscount=pg_fetch_result($actualmethodsquery,0,0);
$actualsoftwarequery=pg_query($db, "SELECT count(actualsoftware_program) from actualsoftware");
$actualsoftwarecount=pg_fetch_result($actualsoftwarequery,0,0);
$generaltoposedtochartypequery=pg_query($db, "SELECT count(generaltoposedtochartype_posedquestion) from generaltoposedtochartype");
$generaltoposedtochartypecount=pg_fetch_result($generaltoposedtochartypequery,0,0);
$possiblemethodquery=pg_query($db, "SELECT count(*) FROM treetype, branchlengthtype, criterion");
$possiblemethodcount=$generaltoposedtochartypecount * pg_fetch_result($possiblemethodquery,0,0);
$possiblesoftwarequery=pg_query($db, "SELECT count(*) FROM dataformat, treeformat, applicationkind, platform");
$possiblesoftwarecount=$possiblemethodcount *  pg_fetch_result($possiblesoftwarequery,0,0);
echo "<p>It currently has information on ".number_format($posedquestioncount)." questions, ".number_format($methodcount)." <a href=\"".$treetapperbaseurl."/method\">methods</a> (covering fewer than ".number_format($actualmethodscount)." of >".number_format($possiblemethodcount)." types possible), ".number_format($programcount)." programs (covering fewer than ".number_format($actualsoftwarecount)." of >".number_format($possiblesoftwarecount)." types possible), ".number_format($referencecount)." references, and ".number_format($personcount)." <a href=\"".$treetapperbaseurl."/person\">people</a>. You can see the controlled vocabularies <a href=\"".$treetapperbaseurl."/vocabulary\">here</a>.</p>";
include ('templates/author_jump.php');

echo "<br /><p>Latest method added and approved: ";
$latestmethodresult=pg_query($db, "SELECT method_id, method_name,  method_description, age(method_adddate), person_id, person_first, person_last FROM method, person WHERE method_approved=1 AND method_addedby=person_id ORDER BY method_id DESC");
echo '"<a href="'.$treetapperbaseurl.'/method/'.pg_fetch_result($latestmethodresult,0,0).'">'.pg_fetch_result($latestmethodresult,0,1).'</a>" added '.pg_fetch_result($latestmethodresult,0,3).' ago by <a href="'.$treetapperbaseurl.'/person/'.pg_fetch_result($latestmethodresult,0,4).'">'.pg_fetch_result($latestmethodresult,0,5).' '.pg_fetch_result($latestmethodresult,0,6).'</a></p>';

echo "<p>Latest program added and approved: ";
$latestprogramresult=pg_query($db, "SELECT program_id, program_name,  program_description, age(program_adddate), person_id, person_first, person_last FROM program, person WHERE program_approved=1 AND program_addedby=person_id ORDER BY program_id DESC");
echo '"<a href="'.$treetapperbaseurl.'/program/'.pg_fetch_result($latestprogramresult,0,0).'">'.pg_fetch_result($latestprogramresult,0,1).'</a>" added '.pg_fetch_result($latestprogramresult,0,3).' ago by <a href="'.$treetapperbaseurl.'/person/'.pg_fetch_result($latestprogramresult,0,4).'">'.pg_fetch_result($latestprogramresult,0,5).' '.pg_fetch_result($latestprogramresult,0,6).'</a></p>';

echo "<p>Latest person added and approved: ";
$latestpersonresult=pg_query($db, "SELECT person_id, person_first, person_last, age(person_adddate) FROM person ORDER BY person_id DESC");
echo '"<a href="'.$treetapperbaseurl.'/person/'.pg_fetch_result($latestpersonresult,0,0).'">'.pg_fetch_result($latestpersonresult,0,1).' '.pg_fetch_result($latestpersonresult,0,2).'</a>" added '.pg_fetch_result($latestpersonresult,0,3).' ago</p>';

echo "<br /><p>Please note that during initial testing of this website, user interactions may be tracked.</p>";

/*
include ('templates/googlecharts_1.02.php');
//CHART WITH LABEL AND MIN/MAX VALUES ON RIGHT
$personarray=array();
$referencearray=array();
$methodarray=array();
$programarray=array();
$vocabtablearray=array();
$referencebyyeararray=array();
$yeararray=array();
$referencebyyearnonzeroarray=array();
$yearnonzeroarray=array();
$medianPDFcitationsbyyeararray=array();
$medianHTMLcitationsbyyeararray=array();

for ($daysago=365; $daysago>=0;$daysago--) {
	$persontotalquery=pg_query($db, "SELECT count(person_id) from person WHERE age(person_adddate)>='".$daysago." days'");
	array_push($personarray, pg_fetch_result($persontotalquery,0,0));
	$referencetotalquery=pg_query($db, "SELECT count(reference_id) from reference WHERE age(reference_adddate)>='".$daysago." days'");
	array_push($referencearray, pg_fetch_result($referencetotalquery,0,0));
	$methodtotalquery=pg_query($db, "SELECT count(method_id) from method WHERE age(method_adddate)>='".$daysago." days'");
	array_push($methodarray, pg_fetch_result($methodtotalquery,0,0));
	$programtotalquery=pg_query($db, "SELECT count(program_id) from program WHERE age(program_adddate)>='".$daysago." days'");
	array_push($programarray, pg_fetch_result($programtotalquery,0,0));
	$vocabtables = array(1,2,4,8,9,12,13,21,22,24,35,36);
	$vocabtablessql= "SELECT tablelist_name, tablelist_description, tablelist_id FROM tablelist ORDER BY tablelist_id ASC";
	$vocabtablesresult = pg_query($db, $vocabtablessql);
	$vocabtotalforday=0;
	for ($lt = 0; $lt < pg_numrows($vocabtablesresult); $lt++) {
		if (in_array(pg_fetch_result($vocabtablesresult,$lt,2), $vocabtables)) {
			$tablename=pg_fetch_result($vocabtablesresult,$lt,0);
			$vocabquerypertable=pg_query($db,"SELECT count($tablename"."_id) from $tablename WHERE age($tablename"."_adddate)>='".$daysago." days'");
			$vocabtotalforday+=pg_fetch_result($vocabquerypertable,0,0);
		}
	}
	array_push($vocabtablearray,$vocabtotalforday);
}
$minyearquery=pg_query($db,"select min(reference_publicationdate) from reference");
$minyear=pg_fetch_result($minyearquery,0,0);
//$minyear=1950;
$maxyearquery=pg_query($db,"select max(reference_publicationdate) from reference");
$maxyear=pg_fetch_result($maxyearquery,0,0);
for ($year=$minyear; $year<=$maxyear; $year++) {
	$refbyyearquery=pg_query($db,"SELECT count(reference_id) FROM reference WHERE reference_publicationdate='".$year."'");
	array_push($referencebyyeararray,pg_fetch_result($refbyyearquery,0,0));
	array_push($yeararray,$year);
	if (pg_fetch_result($refbyyearquery,0,0)>0) {
		array_push($referencebyyearnonzeroarray,pg_fetch_result($refbyyearquery,0,0));
		array_push($yearnonzeroarray,$year);
		
	}
///*	$pdfbyyearresult=pg_query($db,"SELECT citationcount_count FROM reference, citationcount WHERE reference_id=citationcount_reference AND age(citationcount_adddate)<'14 days' AND citationcount_source=1 AND reference_publicationdate='".$year."' ORDER BY citationcount_count DESC");
	$pdfthisyeararray=array();
	for ($lt=0; $lt<pg_numrows($pdfbyyearresult); $lt++) {
		array_push($pdfthisyeararray,pg_fetch_result($pdfbyyearresult,$lt,0));
	}
	$htmlbyyearresult=pg_query($db,"SELECT citationcount_count FROM reference, citationcount WHERE reference_id=citationcount_reference AND age(citationcount_adddate)<'14 days' AND citationcount_source=2 AND reference_publicationdate='".$year."' ORDER BY citationcount_count DESC");
	$htmlthisyeararray=array();
	for ($lt=0; $lt<pg_numrows($htmlbyyearresult); $lt++) {
		array_push($htmlthisyeararray,pg_fetch_result($htmlbyyearresult,$lt,0));
	}
	//pdf
	$n = count($pdfthisyeararray);
	if ($n>=10) {
		$h = intval($n / 2);	
		if($n % 2 == 0) {
			array_push($medianPDFcitationsbyyeararray,($pdfthisyeararray[$h] + $pdfthisyeararray[$h-1]) / 2);
		} else {
			array_push($medianPDFcitationsbyyeararray,$pdfthisyeararray[$h]);
		}
	}
	else {
		array_push($medianPDFcitationsbyyeararray,0);
	}
		//html
	$n = count($htmlthisyeararray);
	if ($n>=10) {
		$h = intval($n / 2);	
		if($n % 2 == 0) {
			array_push($medianHTMLcitationsbyyeararray,($htmlthisyeararray[$h] + $htmlthisyeararray[$h-1]) / 2);
		} else {
			array_push($medianHTMLcitationsbyyeararray,$htmlthisyeararray[$h]);
		}
	}
	else {
		array_push($medianHTMLcitationsbyyeararray,0);
	}
	// * /
}

$referencebyyearchart=new googleChart($referencebyyeararray);
//$referencebyyearchart->setType('bary');
//$referencebyyearchart->barWidth=2;
$referencebyyearchart->setLabelsMinMax(5,'right');
//$referencebyyearchart->setLabels($yeararray,'bottom');
$referencebyyearchart->setLabels("$minyear | $maxyear",'bottom');
$referencebyyearchart->title='References by year';
//$referencebyyearchart->draw();

///*
$pdfhitsbyyearchart=new googleChart($medianPDFcitationsbyyeararray);
$pdfhitsbyyearchart->setLabelsMinMax(5,'right');
$pdfhitsbyyearchart->setLabels("$minyear | $maxyear",'bottom');
$pdfhitsbyyearchart->title='Median PDF hits by year (a proxy for citations; only showing years with over 10 references)';
$pdfhitsbyyearchart->draw();

$htmlhitsbyyearchart=new googleChart($medianHTMLcitationsbyyeararray);
$htmlhitsbyyearchart->setLabelsMinMax(5,'right');
$htmlhitsbyyearchart->setLabels("$minyear | $maxyear",'bottom');
$htmlhitsbyyearchart->title='Median HTML hits by year (a proxy for citations; only showing years with over 10 references)';
$htmlhitsbyyearchart->draw();* /

$referencebyyearpie=new googleChart($referencebyyearnonzeroarray);
$referencebyyearpie->setType('pie');
$referencebyyearpie->setLabels($yearnonzeroarray);
$referencebyyearpie->title='References by year';
//$referencebyyearpie->draw();


$datelabels='12 months ago | 9 months ago | 6 months ago | 3 months ago | today';

$vocabchart= new googleChart($vocabtablearray);
$vocabchart->setLabelsMinMax(5,'right'); //call before other funcs that make labels
$vocabchart->setLabels($datelabels,'bottom');
$vocabchart->title='Terms defined in database';
$vocabchart->draw();


$personchart= new googleChart($personarray);
$personchart->setLabelsMinMax(5,'right'); //call before other funcs that make labels
$personchart->setLabels($datelabels,'bottom');
$personchart->title='People in database';
$personchart->draw();

$referencechart= new googleChart($referencearray);
$referencechart->setLabelsMinMax(5,'right'); //call before other funcs that make labels
$referencechart->setLabels($datelabels,'bottom');
$referencechart->title='References in database';
$referencechart->draw();

if (max($methodarray)>10) {
	$methodchart= new googleChart($methodarray);
	$methodchart->setLabelsMinMax(5,'right'); //call before other funcs that make labels
	$methodchart->setLabels($datelabels,'bottom');
	$methodchart->title='Methods in database';
	$methodchart->draw();
}

if (max($programarray)>10) {
	$programchart= new googleChart($programarray);
	$programchart->setLabelsMinMax(5,'right'); //call before other funcs that make labels
	$programchart->setLabels($datelabels,'bottom');
	$programchart->title='Programs in database';
	$programchart->draw();
}

*/

include('templates/template_pageend.php');
?>

