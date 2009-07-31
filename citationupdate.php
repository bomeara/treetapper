<?PHP
include ('templates/dblogin.php');
$updatecountsql="SELECT count(citationcount_id) FROM citationcount WHERE age(citationcount_adddate)<'1 day'";
$updatecountresult=pg_query($db,$updatecountsql); 
$updatecounttoday=pg_fetch_result($updatecountresult,0,0);
echo ("started with $updatecounttoday updates<br>\n");
$searchAPIlimit=4000; //we're limited to a certain number of queries per day
$updatefrequency='14 days'; //Record updates frequently to track citations over time.
if ($updatecounttoday+2 < $searchAPIlimit) { //don't run if we've already run it too many times
	$reflistsql="SELECT reference_id, reference_title, reference_publicationdate, person_last FROM reference, person, referencetoperson WHERE referencetoperson_person=person_id AND referencetoperson_reference=reference_id AND referencetoperson_authororder=1 AND reference_approved=1";
	$reflistresult=pg_query($db,$reflistsql);
	for ($lt = 0; $lt < pg_numrows($reflistresult); $lt++) {
		if ($updatecounttoday+2 < $searchAPIlimit) { //only run more queries if we haven't hit the limits
			$refid=pg_fetch_result($reflistresult,$lt,0);
			$title=pg_fetch_result($reflistresult,$lt,1);
			$year=pg_fetch_result($reflistresult,$lt,2);
			$authorlast=pg_fetch_result($reflistresult,$lt,3);
			if (strlen($title)>0 && strlen($authorlast)>0 && strlen($year)>0) { //do not search on incomplete references
				$citationcountsql="SELECT age(citationcount_adddate) FROM citationcount WHERE citationcount_reference = '$refid' AND age(citationcount_adddate)<'$updatefrequency'";
				$citationcountresult=pg_query($db,$citationcountsql) or die ("could not connect");
				if (pg_numrows($citationcountresult)==0) { //there are either no, or only old, entries, so we'll update
					for ($citationsourceid=1; $citationsourceid<=2; $citationsourceid++) {
						$updatecounttoday=$updatecounttoday+1; // $updatecounttoday++ wasn't working
						$citationsourceapisql="SELECT citationsource_api FROM citationsource WHERE citationsource_id='$citationsourceid'";
						$citationsourceapiresult=pg_query($db, $citationsourceapisql);
						$key=pg_fetch_result($citationsourceapiresult,0,0);
						$webquerystring=str_replace(" ","+",str_replace('  ',' ',str_replace("'",'%27',(str_replace('&','','%22'.$authorlast.'%22+'.$year.'+%22'.$title.'%22'))))); //the %22 is the equivalent of quotes, making us search for phrases
						$request="";
						if ($citationsourceid==1) { //Use Yahoo PDF api
							$request= 'http://search.yahooapis.com/WebSearchService/V1/webSearch?appid='.$key.'&similar_ok=1&query='.$webquerystring.'&type=all&format=pdf&results=1&output=xml';
						}
						else if ($citationsourceid==2) { //Use Yahoo api for all web page types, not just PDFs
							$request= 'http://search.yahooapis.com/WebSearchService/V1/webSearch?appid='.$key.'&similar_ok=1&query='.$webquerystring.'&type=all&results=1&output=xml';
						}
						$results = file_get_contents($request);
						if ($results != false) {
							$response = file_get_contents($request);				
							if ($response != false) {
								$phpobject = simplexml_load_string($response);
								foreach($phpobject->attributes() as $name=>$attr) {
									$res[$name]=$attr;
								}
								if (isset ($res['totalResultsAvailable'])) {
									$newcount=$res['totalResultsAvailable'];
									$newcountsql="INSERT INTO citationcount (citationcount_source, citationcount_reference, citationcount_count) VALUES ('$citationsourceid', '$refid', '$newcount')";
									$newcountresult=pg_query($db,$newcountsql) or die ("could not insert $newcountsql into db");
								}
							}
						}
					}
				}
			}
		}
	}
}
echo ("ended with $updatecounttoday updates<br>\n");
?>
