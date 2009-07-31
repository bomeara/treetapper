<?PHP
include ('dblogin.php'); //connection in $db
$refid="";
$hasid=false;
if (strlen($_GET['refid'])>0) {
	if (is_numeric($_GET['refid'])) {
		$refid=$_GET['refid'];
		$hasid=true;
	}
}
if ($hasid) {
	$referencesql="SELECT reference_title, reference_publicationdate, reference_publicationname, reference_volume, reference_issue, reference_startpage, reference_endpage, reference_doi, reference_pmid FROM reference WHERE reference_id=$refid";
	$referencequery=pg_query($db,$referencesql);
	if (pg_numrows($referencequery)==1) {
		$authorstring="";
		$searchforauthors=pg_query($db,"SELECT person_id, person_last, person_first, person_middle, referencetoperson_authororder FROM person, referencetoperson WHERE referencetoperson_reference=".$refid." AND referencetoperson_person=person_id ORDER BY referencetoperson_authororder ASC");
		$authorlast="";
		if (pg_numrows($searchforauthors) > 0) {
			for ($la = 0; $la < pg_numrows($searchforauthors); $la++) {
				if ($la>0 && pg_numrows($searchforauthors)>2) {
					$authorstring.=", ";
				}
				if (($la==pg_numrows($searchforauthors)-1) && ($la>0)) {
					$authorstring.=" and ";
				}
				$authorstring.='<a href="'.$treetapperbaseurl.'/person/'.pg_fetch_result($searchforauthors,$la,0).'">'.pg_fetch_result($searchforauthors,$la,2)." ".substr(pg_fetch_result($searchforauthors,$la,3), 0, 1)." ".pg_fetch_result($searchforauthors,$la,1).'</a>';
			}
		}
		echo $authorstring." ".pg_fetch_result($referencequery,0,1).' "<a href="'.$treetapperbaseurl.'/reference/'.$refid.'">'.pg_fetch_result($referencequery,0,0).'</a>" '.pg_fetch_result($referencequery,0,2).' '.pg_fetch_result($referencequery,0,3).'('.pg_fetch_result($referencequery,0,4).'): '.pg_fetch_result($referencequery,0,5).'-'.pg_fetch_result($referencequery,0,6);
	}
}
?>
