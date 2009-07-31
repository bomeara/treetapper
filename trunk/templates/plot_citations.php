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
	$rangequery=pg_query($db,"SELECT max(citationcount_count),min(extract(epoch from citationcount_adddate)), extract(epoch from current_date), min(citationcount_adddate), age(min(citationcount_adddate)) FROM citationcount WHERE citationcount_reference=".$refid);
	if (pg_numrows($rangequery)>0) {
		$maxcount=max(1,pg_fetch_result($rangequery,0,0)); //Prevent division by zero error
		$mindate=pg_fetch_result($rangequery,0,1);
		$maxdate=pg_fetch_result($rangequery,0,2);
		$startdatetext=pg_fetch_result($rangequery,0,3);
		$elapsedtimetext=pg_fetch_result($rangequery,0,4);
		$maxpdfcountquery=pg_query($db,"SELECT max(citationcount_count) FROM citationcount WHERE citationcount_source=1 AND citationcount_reference=".$refid); 
		if (pg_numrows($maxpdfcountquery)>0) {
			$maxpdfcount=max(1,pg_fetch_result($maxpdfcountquery,0,0)); //Prevent division by zero error
			$maxhtmlcountquery=pg_query($db,"SELECT max(citationcount_count) FROM citationcount WHERE citationcount_source=2 AND citationcount_reference=".$refid);
			if (pg_numrows($maxhtmlcountquery)>0) {
				
				$maxhtmlcount=max(1,pg_fetch_result($maxhtmlcountquery,0,0)); //Prevent division by zero error
	//$pdfcountarray=();
	//$pdfdatearray=();
	//$htmlcountarray=();
	//$htmldatearray=();
				$googlestringX="";
				$googlestringY="";
				$googlestring="";
				$pdfquery=pg_query($db,"SELECT citationcount_count, extract(epoch from citationcount_adddate) FROM citationcount, citationsource WHERE citationcount_source=citationsource_id AND citationsource_id=1 AND citationcount_reference=".$refid." ORDER BY citationcount_adddate ASC");
				for ($lp = 0; $lp < pg_numrows($pdfquery); $lp++) {
		//array_push($pdfcountarray, pg_fetch_result($pdfquery,$lp,0));
		//array_push($pdfdatearray, pg_fetch_result($pdfquery,$lp,1));
					$rescaleddate=sprintf("%01.1f",100.0*(pg_fetch_result($pdfquery,$lp,1)-$mindate)/($maxdate-$mindate));
					$rescaledcount=sprintf("%01.1f",100.0*pg_fetch_result($pdfquery,$lp,0)/$maxpdfcount);
					$googlestringX.=$rescaleddate.",";
					$googlestringY.=$rescaledcount.",";
				}
				$googlestringsub1=substr($googlestringX,0,-1);
				$googlestringX=$googlestringsub1;
				$googlestringsub1=substr($googlestringY,0,-1);
				$googlestringY=$googlestringsub1;
				$googlestring=$googlestringX.'|'.$googlestringY.'|';
				$googlestringX="";
				$googlestringY="";	
				$htmlquery=pg_query($db,"SELECT citationcount_count, extract(epoch from citationcount_adddate) FROM citationcount, citationsource WHERE citationcount_source=citationsource_id AND citationsource_id=2 AND citationcount_reference=".$refid." ORDER BY citationcount_adddate ASC");
				for ($lh = 0; $lh < pg_numrows($htmlquery); $lh++) {
		//array_push($htmlcountarray, pg_fetch_result($htmlquery,$lh,0));
		//array_push($htmldatearray, pg_fetch_result($htmlquery,$lh,1));
					$rescaleddate=sprintf("%01.1f",100.0*(pg_fetch_result($htmlquery,$lh,1)-$mindate)/($maxdate-$mindate));
					$rescaledcount=sprintf("%01.1f",100.0*pg_fetch_result($htmlquery,$lh,0)/$maxhtmlcount);
					$googlestringX.=$rescaleddate.",";
					$googlestringY.=$rescaledcount.",";
				}
				$googlestringsub1=substr($googlestringX,0,-1);
				$googlestringX=$googlestringsub1;
				$googlestringsub1=substr($googlestringY,0,-1);
				$googlestringY=$googlestringsub1;
				$googlestring.=$googlestringX.'|'.$googlestringY;
				echo '<img src="http://chart.apis.google.com/chart?cht=lxy&chs=600x125&chd=t:'.$googlestring.'&chco=ff0000,0000ff&chxt=x,y,r&chxl=0:|'.$startdatetext.'|today|
1:|0|html|'.$maxhtmlcount.'|
2:|0|pdf|'.$maxpdfcount.'&chco=ff0000,0000ff&chxt=x,y,r&chxr=0,0,50|1,0,271|2,0,4&chxs=2,ff0000,12|1,0000ff,12" alt="Citation chart" />';
			}
		}
	}
}
?>
