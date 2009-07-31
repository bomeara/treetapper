<?php
$numresults=0;
$title = "null";
$pubdate = "null";
$pubname = "null";
$volume = "null";
$issue = "null";
$startpage = "null";
$endpage = "null";
$url = "null";
$pmid = "null";

header("Content-Type:text/xml");
echo "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\" ?><rss version=\"2.0\"> ";

function printxml () {
	global $numresults, $title, $pubdate, $pubname, $volume, $issue, $startpage, $endpage, $url, $pmid;
	echo "<numresults>$numresults</numresults>
<title>$title</title>
<pubdate>$pubdate</pubdate>
<pubname>$pubname</pubname>
<volume>$volume</volume>
<issue>$issue</issue>
<startpage>$startpage</startpage>
<endpage>$endpage</endpage>
<doi>$doi</doi>
<url>$url</url>
<pmid>$pmid</pmid>
";
}

if ($_REQUEST["inputformat"]=="doi") {
	$doi=$_REQUEST["inputtext"];
	include ('templates/dblogin.php'); //connection in $db
	$myresult = pg_query($db,"SELECT reference_title, reference_publicationdate, reference_publicationname, reference_volume, reference_issue, reference_startpage, reference_endpage, reference_url, reference_pmid FROM reference WHERE reference_doi='$doi'") or die("Couldn't query the user-database.");
	$numresults=pg_numrows($myresult);
	
	//for ($lt = 0; $lt < pg_numrows($myresult); $lt++) {
	
	if ($numresults>0) {
		$lt=0; //Only return first match
		$title = pg_fetch_result($myresult, $lt, 0);
		$pubdate = pg_fetch_result($myresult, $lt, 1);
		$pubname = pg_fetch_result($myresult, $lt, 2);
		$volume = pg_fetch_result($myresult, $lt, 3);
		$issue = pg_fetch_result($myresult, $lt, 4);
		$startpage = pg_fetch_result($myresult, $lt, 5);
		$endpage = pg_fetch_result($myresult, $lt, 6);
		$url = pg_fetch_result($myresult, $lt, 7);
		$pmid = pg_fetch_result($myresult, $lt, 8);
	}
	printxml();
	}
else if ($_REQUEST["inputformat"]=="url") {
	$url=$_REQUEST["inputtext"];
	include ('templates/dblogin.php'); //connection in $db
	$myresult = pg_query($db,"SELECT reference_title, reference_publicationdate, reference_publicationname, reference_volume, reference_issue, reference_startpage, reference_endpage, reference_doi, reference_pmid FROM reference WHERE reference_url='$url'") or die("Couldn't query the user-database.");
	$numresults=pg_numrows($myresult);
	//for ($lt = 0; $lt < pg_numrows($myresult); $lt++) {
	if ($numresults>0) {
		$lt=0; //Only return first match
		$title = pg_fetch_result($myresult, $lt, 0);
		$pubdate = pg_fetch_result($myresult, $lt, 1);
		$pubname = pg_fetch_result($myresult, $lt, 2);
		$volume = pg_fetch_result($myresult, $lt, 3);
		$issue = pg_fetch_result($myresult, $lt, 4);
		$startpage = pg_fetch_result($myresult, $lt, 5);
		$endpage = pg_fetch_result($myresult, $lt, 6);
		$doi = pg_fetch_result($myresult, $lt, 7);
		$pmid = pg_fetch_result($myresult, $lt, 8);
	}
	printxml();
	}
else if ($_REQUEST["inputformat"]=="pmid") {
	$pmid=$_REQUEST["inputtext"];
	include ('templates/dblogin.php'); //connection in $db
	$myresult = pg_query($db,"SELECT reference_title, reference_publicationdate, reference_publicationname, reference_volume, reference_issue, reference_startpage, reference_endpage, reference_doi, reference_url FROM reference WHERE reference_pmid='$pmid'") or die("Couldn't query the user-database.");
	$numresults=pg_numrows($myresult);
	//for ($lt = 0; $lt < pg_numrows($myresult); $lt++) {
	if ($numresults>0) {
		
		$lt=0; //Only return first match
		$title = pg_fetch_result($myresult, $lt, 0);
		$pubdate = pg_fetch_result($myresult, $lt, 1);
		$pubname = pg_fetch_result($myresult, $lt, 2);
		$volume = pg_fetch_result($myresult, $lt, 3);
		$issue = pg_fetch_result($myresult, $lt, 4);
		$startpage = pg_fetch_result($myresult, $lt, 5);
		$endpage = pg_fetch_result($myresult, $lt, 6);
		$doi = pg_fetch_result($myresult, $lt, 7);
		$url = pg_fetch_result($myresult, $lt, 8);
	}
	printxml();
	}
else {
		$pmid=$_REQUEST["inputtext"];
		include ('templates/dblogin.php'); //connection in $db
		$myresult = pg_query($db,"SELECT reference_title, reference_publicationdate, reference_publicationname, reference_volume, reference_issue, reference_startpage, reference_endpage, reference_doi, reference_url, reference_pmid FROM reference") or die("Couldn't query the user-database.");
		$numresults=pg_numrows($myresult);
		for ($lt = 0; $lt < pg_numrows($myresult); $lt++) {
			$title = pg_fetch_result($myresult, $lt, 0);
			$pubdate = pg_fetch_result($myresult, $lt, 1);
			$pubname = pg_fetch_result($myresult, $lt, 2);
			$volume = pg_fetch_result($myresult, $lt, 3);
			$issue = pg_fetch_result($myresult, $lt, 4);
			$startpage = pg_fetch_result($myresult, $lt, 5);
			$endpage = pg_fetch_result($myresult, $lt, 6);
			$doi = pg_fetch_result($myresult, $lt, 7);
			$url = pg_fetch_result($myresult, $lt, 8);
			$pmid = pg_fetch_result($myresult, $lt, 9);
			printxml();
		}
}
echo "</rss>";
?>