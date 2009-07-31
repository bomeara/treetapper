<?PHP

include ('dblogin.php'); //connection in $db
$methodid="";
$hasid=false;
if (strlen($_GET['id'])>0) {
	if (is_numeric($_GET['id'])) {
		$methodid=$_GET['id'];
		$hasid=true;
	}
}
if ($hasid) {
	$methodquery = pg_query($db,"SELECT method_name, method_description FROM method WHERE method_id=".$methodid) or die("Could not query the database.");
	if (pg_numrows($methodquery)==0) {
		if ($_GET['include']!=1) {
			$_GET['pagetitle']="TreeTapper: Method";
			include('template_pagestart.php');
		}
		echo ("Sorry, no info on method with ID ".$methodid);
		if ($_GET['include']!=1) {
			include('template_pageend.php');
		}
	}
	else  {
		if ($_GET['include']!=1) {
			$_GET['pagetitle']="TreeTapper: ".pg_fetch_result($methodquery,0,0);
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
		echo ("<p><b>".pg_fetch_result($methodquery,0,0)."</b>: ".pg_fetch_result($methodquery,0,1)."</p>");

		$referencesql = "SELECT methodtoreference_reference FROM methodtoreference WHERE methodtoreference_method=$methodid";
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
		
		$referencesql = "SELECT methodtoreference_reference FROM methodtoreference WHERE methodtoreference_method=$methodid";
		$referencequery = pg_query($db, $referencesql);
		echo "<p><br>Reference(s):</p>";
		for  ($lr = 0; $lr< pg_numrows($referencequery); $lr++) {
						include('formatsingle_reference.php');
			echo "<br />";
		}
		
		echo "<p><br>Criteria ";
		$_GET['table'] = 'criterion';
		include 'helppanel_generic.php';
		echo ": <ul>";
		$criteriasql="SELECT criterion_name FROM criterion, methodtocriterion WHERE methodtocriterion_criterion=criterion_id AND methodtocriterion_method = $methodid";
		$criteriaquery=pg_query($db, $criteriasql);
		for ($lc=0; $lc< pg_numrows($criteriaquery); $lc++) {
			echo "<li>".pg_fetch_result($criteriaquery,$lc,0)."</li>";
		}
		echo "</ul>";
		
		echo "<p><br>Questions ";
		$_GET['table'] = 'posedquestion';
		include 'helppanel_generic.php';
		echo  " addressed by the method: \n<ul>";
		$posedquestionsql="SELECT posedquestion_name, posedquestion_description, generalquestion_name FROM posedquestion, generalquestion, methodtoposedquestion WHERE methodtoposedquestion_posedquestion=posedquestion_id AND posedquestion_generalquestion=generalquestion_id AND methodtoposedquestion_method = $methodid";
		$posedquestionquery=pg_query($db, $posedquestionsql);
		for ($lp=0; $lp< pg_numrows($posedquestionquery); $lp++) {
			echo "<li>".pg_fetch_result($posedquestionquery,$lp,0)." (".pg_fetch_result($posedquestionquery,$lp,1)."). Topic area: ".pg_fetch_result($posedquestionquery,$lp,2)."</li>";
		}
		echo "</ul>\n";
		
		echo "<p><br>Character combinations ";
		$_GET['table'] = 'charactertype';
		include 'helppanel_generic.php';
		echo " addressed by the method: \n<ul>";
		$characteridsql="SELECT charactercombination_char1, charactercombination_char2, charactercombination_char3 FROM charactercombination, methodtocharactercombination WHERE methodtocharactercombination_charactercombination=charactercombination_id AND methodtocharactercombination_method = $methodid";
		$characteridquery=pg_query($db, $characteridsql);
		if (is_numeric(pg_fetch_result($characteridquery,0,0))) {
			for ($lci=0; $lci< pg_numrows($characteridquery); $lci++) {
				$char1query=pg_query($db,"SELECT charactertype_name FROM charactertype WHERE charactertype_id=".pg_fetch_result($characteridquery,$lci,0));
				echo "<li>".pg_fetch_result($char1query,0,0);
				if (is_numeric(pg_fetch_result($characteridquery,$lci,1))) {
					$char2query=pg_query($db,"SELECT charactertype_name FROM charactertype WHERE charactertype_id=".pg_fetch_result($characteridquery,$lci,1));
					echo "-".pg_fetch_result($char2query,0,0);
				}
				if (is_numeric(pg_fetch_result($characteridquery,$lci,2))) {
					$char3query=pg_query($db,"SELECT charactertype_name FROM charactertype WHERE charactertype_id=".pg_fetch_result($characteridquery,$lci,2));
					echo "-".pg_fetch_result($char3query,0,0);
				}
				echo "</li>";
			}
		}
		else {
				echo "<li>None</li>";
		}
		echo "</ul>\n";
		
		echo "<p><br>Software ";
		$_GET['table'] = 'program';
		include 'helppanel_generic.php';
		echo  " implementing the method: \n<ul>";
		$softwaresql="SELECT DISTINCT program_id, program_name, program_description, program_open FROM program, programtomethodtocharactercombination, methodtocharactercombination WHERE programtomethodtocharactercombination_methodtocharactercombination=methodtocharactercombination_id AND programtomethodtocharactercombination_program=program_id AND methodtocharactercombination_method = $methodid ORDER BY program_open ASC";
		$softwarequery=pg_query($db, $softwaresql);
		for ($ls=0; $ls< pg_numrows($softwarequery); $ls++) {
			echo '<li><a href="'.$treetapperbaseurl.'/software/'.pg_fetch_result($softwarequery,$ls,0).'">'.pg_fetch_result($softwarequery,$ls,1)."</a> (".pg_fetch_result($softwarequery,$ls,2).")";
			if (pg_fetch_result($softwarequery,$ls,3)==1) {
				echo " [open source]";
			}
			echo "</li>";
		}
		if (pg_numrows($softwarequery)==0) {
			echo "<li>none in database</li>";
		}	
		echo "</ul>\n";
		
		if ($_GET['include']!=1) {
			include('template_pageend.php');
		}
	}

	
	
}
else { // show info for all methods
	if ($_GET['include']!=1) {
		$_GET['pagetitle']="TreeTapper: All Methods";
		include('template_pagestart.php');
	}
	$totalmethodsqlresults=pg_query($db, "SELECT count(method_id) FROM method");
	$totalmethod=pg_fetch_result($totalmethodsqlresults,0,0);
	echo "There are ".$totalmethod." methods in the database.<br>";
	$_GET['table'] = "method";
	echo "<div id=\"method\"></div>";
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
				
				
				this.myDataTable = new YAHOO.widget.DataTable(\"method\", myColumnDefs,
															  this.myDataSource, {initialRequest:\"table=method\"});
				
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