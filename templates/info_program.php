<?PHP

include ('dblogin.php'); //connection in $db
$programid="";
$hasid=false;
if (strlen($_GET['id'])>0) {
	if (is_numeric($_GET['id'])) {
		$programid=$_GET['id'];
		$hasid=true;
	}
}
if ($hasid) {
	$programquery = pg_query($db,"SELECT program_name, program_description, program_version, program_cost, program_open, program_url FROM program WHERE program_id=".$programid) or die("Could not query the database.");
	if (pg_numrows($programquery)==0) {
		if ($_GET['include']!=1) {
			$_GET['pagetitle']="TreeTapper: Program";
			include('template_pagestart.php');
		}
		echo ("Sorry, no info on program with ID ".$programid);
		if ($_GET['include']!=1) {
			include('template_pageend.php');
		}
	}
	else  {
		if ($_GET['include']!=1) {
			$_GET['pagetitle']="TreeTapper: ".pg_fetch_result($programquery,0,0);
			include('template_pagestart.php');
		}
		echo "<style type=\"text/css\">
		blockquote,ul,ol,dl { 
		/*giving blockquotes and lists room to breathe*/ 
		margin:1em; 
		} 
		ol,ul,dl { 
			/*bringing lists on to the page with breathing room */ 
			margin-left:2em; 
		} 
		ol li { 
			/*giving OL's LIs generated numbers*/ 
			list-style: decimal outside;     
		} 
		ul li { 
			/*giving UL's LIs generated disc markers*/ 
		list-style: disc outside; 
		} 
		</style>";
		echo ("<p><b>".pg_fetch_result($programquery,0,0)." ".pg_fetch_result($programquery,0,2)."</b>: ".pg_fetch_result($programquery,0,1)."</p>");
		
		echo ("<p><br />Cost: \$".pg_fetch_result($programquery,0,3)."</p>");
		if (pg_fetch_result($programquery,0,4)==1) {
			echo ("<p><br /><a href=\"http://en.wikipedia.org/wiki/Open-source_software\">Open source</a>: yes</p>");
		}
		else {
			echo ("<p><br /><a href=\"http://en.wikipedia.org/wiki/Open-source_software\">Open source</a>: no</p>");
		}
		if (strlen(pg_fetch_result($programquery,0,5))>0) {
			echo ("<p><br />URL: <a href=\"".pg_fetch_result($programquery,0,5)."\">".pg_fetch_result($programquery,0,5)."</a></p>");
		}

		$referencesql = "SELECT programtoreference_reference FROM programtoreference WHERE programtoreference_program=$programid";
		$referencequery = pg_query($db, $referencesql);
		$refidmax=0;
		$refcountmax=0;
		for  ($lr = 0; $lr< pg_numrows($referencequery); $lr++) {
			$refcountquery = pg_query($db, "SELECT max(citationcount_count) FROM citationcount WHERE citationcount_reference=".pg_fetch_result($referencequery,$lr,0));
			if (pg_numrows($refcountquery)>0) {
				if (pg_fetch_result($refcountquery,0,0)>$refcountmax) {
					$refcountmax=pg_fetch_result($refcountquery,0,0);
					$refidmax=pg_fetch_result($referencequery,$lr,0);
				}
			}
		}
		if ($refidmax>0) {
			echo "<p><br>Citations(s) (based on web hits only through time [<a href=\"http://treetapper-dev.blogspot.com/2008/01/updated-schema.html\" target=\"_blank\">why</a>]):</p>";
			$_GET['refid']=$refidmax;
			include('plot_citations.php');
			echo "<br />";
		}
		
		$referencesql = "SELECT programtoreference_reference FROM programtoreference WHERE programtoreference_program=$programid";
		$referencequery = pg_query($db, $referencesql);
		echo "<p><br>Reference(s):</p>";
		for  ($lr = 0; $lr< pg_numrows($referencequery); $lr++) {
						include('formatsingle_reference.php');
			echo "<br />";
		}
		
		echo "<p><br>Data formats ";
		$_GET['table'] = 'dataformat';
		include 'helppanel_generic.php';
		echo ": <ul>";
		$datasql="SELECT dataformat_name FROM dataformat, programtodataformat WHERE programtodataformat_dataformat=dataformat_id AND programtodataformat_program = $programid";
		$dataquery=pg_query($db, $datasql);
		for ($ldq=0; $ldq< pg_numrows($dataquery); $ldq++) {
			echo "<li>".pg_fetch_result($dataquery,$ldq,0)."</li>";
		}
		echo "</ul>";
		
		echo "<p><br>Tree formats ";
		$_GET['table'] = 'treeformat';
		include 'helppanel_generic.php';
		echo ": <ul>";
		$treesql="SELECT treeformat_name FROM treeformat, programtotreeformat WHERE programtotreeformat_treeformat=treeformat_id AND programtotreeformat_program = $programid";
		$treequery=pg_query($db, $treesql);
		for ($ldq=0; $ldq< pg_numrows($treequery); $ldq++) {
			echo "<li>".pg_fetch_result($treequery,$ldq,0)."</li>";
		}
		echo "</ul>";

		$characternamearray=array();
		$charnamesql="SELECT charactertype_id, charactertype_name FROM charactertype";
		$charnameresult=pg_query($db, $charnamesql);
		for ($lchar=0; $lchar<pg_numrows($charnameresult); $lchar++) {
			$characternamearray[pg_fetch_result($charnameresult,$lchar,0)]=pg_fetch_result($charnameresult,$lchar,1);
		}
		$characternamearray[0]="No character required"; // override the actual name, "None"		
		echo "<p><br>Methods ";
		$_GET['table'] = 'method';
		include 'helppanel_generic.php';
		echo " and character types ";
		$_GET['table']='charactertype';
		include 'helppanel_generic.php';
		echo ": <ul>";
		
		$methodsql="SELECT method_name, method_id FROM method";
		$methodquery=pg_query($db, $methodsql);
		for ($lms=0; $lms<pg_numrows($methodquery); $lms++) {
			$charsql="SELECT charactercombination_char1, charactercombination_char2, charactercombination_char3 FROM charactercombination, programtomethodtocharactercombination, methodtocharactercombination WHERE programtomethodtocharactercombination_program='".$programid."' AND programtomethodtocharactercombination_methodtocharactercombination = methodtocharactercombination_id AND methodtocharactercombination_method='".pg_fetch_result($methodquery,$lms,1)."' AND methodtocharactercombination_charactercombination=charactercombination_id";
			$charquery=pg_query($db, $charsql);
			if (pg_numrows($charquery)>0) {
				echo "<li>".pg_fetch_result($methodquery,$lms,0);
				for ($lmchar=0; $lmchar<pg_numrows($charquery); $lmchar++) {
					$namestring=$characternamearray[pg_fetch_result($charquery,$lmchar,0)];
					if (pg_fetch_result($charquery,$lmchar,1)>0) {
						$namestring.="-".$characternamearray[pg_fetch_result($charquery,$lmchar,1)];	
					}
					if (pg_fetch_result($charquery,$lmchar,2)>0) {
						$namestring.="-".$characternamearray[pg_fetch_result($charquery,$lmchar,2)];	
					}
						echo "<br>   $namestring";
				}
				echo "</li>";
			}
		}
		echo "</ul>";
		
		echo "<p><br />Application kind and platform";
		$_GET['table']='applicationkind';
		include 'helppanel_generic.php';
		echo ": <ul>";
		$kindsql="SELECT DISTINCT applicationkind_name, applicationkind_id FROM applicationkind, programtoplatformappkind WHERE programtoplatformappkind_program= $programid AND programtoplatformappkind_applicationkind=applicationkind_id";
		$kindquery=pg_query($db,$kindsql);
		for ($ldq=0; $ldq<pg_numrows($kindquery); $ldq++) {
			$kindname=pg_fetch_result($kindquery,$ldq,0);
			echo "<li>".$kindname.": ";
			$kindid=pg_fetch_result($kindquery,$ldq,1);
			$platformsql="SELECT platform_name FROM platform, programtoplatformappkind WHERE programtoplatformappkind_program= $programid AND programtoplatformappkind_applicationkind= $kindid AND programtoplatformappkind_platform=platform_id ORDER BY platform_name";
			$platformquery=pg_query($db,$platformsql);
			for ($ldk=0; $ldk<pg_numrows($platformquery); $ldk++) {
				echo pg_fetch_result($platformquery,$ldk,0);
				if ($ldk<pg_numrows($platformquery)-1) {
					echo ", ";
				}
			}
			echo "</li>";
		}
			
		echo "</ul>";
		
		
		if ($_GET['include']!=1) {
			include('template_pageend.php');
		}
	}

	
	
}
else { // show info for all programs
	if ($_GET['include']!=1) {
		$_GET['pagetitle']="TreeTapper: All Programs";
		include('template_pagestart.php');
	}
	$totalprogramsqlresults=pg_query($db, "SELECT count(program_id) FROM program");
	$totalprogram=pg_fetch_result($totalprogramsqlresults,0,0);
	echo "There are ".$totalprogram." programs in the database.<br>";
	$_GET['table'] = "program";
	echo "<div id=\"program\"></div>";
	echo "
<script type=\"text/javascript\">";
	echo "YAHOO.util.Event.addListener(window, \"load\", function() {";
	echo "
		YAHOO.example.XHR_Text = new function() {
			
			
			var myColumnDefs = [
				{key:\"ID\", sortable:true},
				{key:\"Name\", sortable:true},
				{key:\"Description\", sortable:true},
				{key:\"Date added\", sortable:true},
				{key:\"Accepted\", sortable:true}
				];
			
			
			this.myDataSource = new YAHOO.util.DataSource(\"templates/vocabtable_js.php?\");
			this.myDataSource.responseType = YAHOO.util.DataSource.TYPE_TEXT;
			this.myDataSource.responseSchema = {
recordDelim: \"\\n\",
fieldDelim: \"\\t\",
fields: [\"ID\",\"Accepted\",\"Name\",\"Description\",\"Date added\"]
			};
				
				
				this.myDataTable = new YAHOO.widget.DataTable(\"program\", myColumnDefs,
															  this.myDataSource, {initialRequest:\"table=program\"});
				
					//this.myDataSource.setInterval(5000, this.myDataTable.get('initialRequest'),this.myDataTable.onDataReturnIntializeTable,this.myDataTable);
				";
	echo "};
	
	
	});";
	echo "
</script>";
	if ($_GET['include']!=1) {
		include('template_pageend.php');
	}
}
?>