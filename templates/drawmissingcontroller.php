<?PHP
include ('yuiheadersPart1.php');
include ('yuiheadersPart2.php');
include ('dblogin.php'); //connection in $db
//header("Content-type: image/png");
$imgWidth=800;
$imgHeight=800;
$margin=30;
$minDim=min($imgWidth,$imgHeight);
$maxradius=0;
$markerradius=7;
$totaldepthcount=""; //global depth;
$fullstep=1.0;
$partstep=0.3;
$tablearraymoddate=array(1, 2, 3, 4, 8, 9, 12, 13, 21, 22, 24, 35, 36);



if (strlen($_GET['tablenames'])>0 && strlen($_GET['tableoptions'])>0) {
	$querystring=$_GET['tablenames'].$_GET['tableoptions'];
	$cacheroot=sha1($querystring);
	$tablenamesstring=str_replace("charactertype_","char",$_GET['tablenames']);
	$tablenamesarray = explode(',', $tablenamesstring);
	$tableoptionsarray = explode(',', $_GET['tableoptions']);
	$totaldepthcount=count($tablenamesarray);
	$runningradius=0;
	$numberofdivisions=1;
	if (count($tableoptionsarray)==$totaldepthcount) { //only continue if same number
		echo "</head>
<STYLE type=\"text/css\">
html, body {
height: 100%;
}
BODY {text-align: left}
A:link {text-decoration: none; color: navy;}
A:visited {text-decoration: none; color: silver;}
A:active {text-decoration: none; color: silver;}
A:hover {text-decoration: none; color: red;}
body {
margin:0;
padding:0;
	font-family:Optima, Helvetica, Verdana, Arial, sans-serif;
}
.yui-content { 
	background-color: white;
padding:1.0em 1.0em; /* content padding */ 
} 

</STYLE>\n
</head><body>";
flush();
		$existingfilequerystring="SELECT findneedquery_id, findneedquery_count, findneedquery_elapsedtime, AGE(findneedquery_moddate), extract(epoch from findneedquery_moddate) FROM findneedquery WHERE findneedquery_hash='".$cacheroot."'";
		$existingfilequery=pg_query($db, $existingfilequerystring);
		if (pg_numrows($existingfilequery)==1) {
			$querycount=pg_fetch_result($existingfilequery,0,1);
			$querycount++;
			$expectedtime=pg_fetch_result($existingfilequery,0,2)/60.0;
			$lastcacheupdate=pg_fetch_result($existingfilequery,0,4);
			$lastupdateelsewhere=0;
			foreach ($tablearraymoddate as $tableid) {
				$tablenamequerystring="SELECT tablelist_name FROM tablelist WHERE tablelist_id=$tableid";
				$tablenamequery=pg_query($db, $tablenamequerystring);
				$moddatequerystring="SELECT max(extract(epoch  from ".pg_fetch_result($tablenamequery,0,0)."_moddate)) FROM ".pg_fetch_result($tablenamequery,0,0);
				$moddatequery=pg_query($db, $moddatequerystring);
				$lastupdateelsewhere=max($lastupdateelsewhere, pg_fetch_result($moddatequery,0,0));
			}
			$updatestring="UPDATE findneedquery SET findneedquery_count=".$querycount." WHERE findneedquery_hash='".$cacheroot."'";
			$updatequery=pg_query($db, $updatestring);			
			echo "<div id='findmissingstatus'><br />Using cached file from ".pg_fetch_result($existingfilequery,0,3)." ago";
			if ($lastdateelsewhere>=$lastcacheupdate) {
				echo ". The new one should be ready in ".$expectedtime." minutes (if you keep this window open) and the page will update.<br /></div>";
			}
			else {
				echo ", which should reflect the current state of the database.<br /></div>";
			}
			flush();
						$mapstring=file_get_contents("cache_missingmethods/".$cacheroot.".html");
			echo "$mapstring";
			flush();
						if ($lastdateelsewhere>=$lastcacheupdate) {
				include('drawmissing.php');
				echo "<script type=\"text/javascript\">updateTargetURL();</script>"; #new figure is ready, so reload
			}
		}
		else {
			echo "<br /><div id='findmissingstatus'>This is the first time anyone has asked for this combination of options. Generating a graph may take some time (minutes). Leave this page open if you want to wait for it.</div><br />";
			flush();
						include('drawmissing.php');
			$mapstring=file_get_contents("cache_missingmethods/".$cacheroot.".html");
			echo "$mapstring";
			flush();
					}
		
		echo "</body>";
flush();
		//echo "done";
	}
	else {
		//echo "error";
	}
}
?>
