<?PHP

include ('dblogin.php'); //connection in $db
$id="";
$hasid=false;
if (strlen($_GET['id'])>0) {
	if (is_numeric($_GET['id'])) {
		$id=$_GET['id'];
		$hasid=true;
	}
}
if ($hasid) {
	$personquery = pg_query($db,"SELECT person_id, person_first, person_middle, person_last, person_url, person_adddate, person_moddate, person_suffix FROM person WHERE person_id=".$id." ORDER BY person_id ASC") or die("Could not query the database.");
	if (pg_numrows($personquery)==0) {
		if ($_GET['include']!=1) {
			$_GET['pagetitle']="TreeTapper: Person";
			include('template_pagestart.php');
		}
		echo ("Sorry, no info on person with ID ".$id);
	}
	else if (pg_numrows($personquery)==1) {
		if ($_GET['include']!=1) {
			$_GET['pagetitle']="TreeTapper: ".pg_fetch_result($personquery,0,1)." ".pg_fetch_result($personquery,0,2)." ".pg_fetch_result($personquery,0,3)." ".pg_fetch_result($personquery,0,7);
			include('template_pagestart.php');
		}		
		echo ("<p>".pg_fetch_result($personquery,0,1)." ".pg_fetch_result($personquery,0,2)." ".pg_fetch_result($personquery,0,3)." ".pg_fetch_result($personquery,0,7)."</p>");
		if (strlen(pg_fetch_result($personquery,0,4))>0) {
			echo "<br>Web site: <a href=\"".pg_fetch_result($personquery,0,4)."\">".pg_fetch_result($personquery,0,4)."</a><br>";
		}
		
		include ('googlecharts_1.02.php');
		$minyearquery=pg_query($db,"select min(reference_publicationdate) from reference, referencetoperson WHERE referencetoperson_person=".$id." AND referencetoperson_reference=reference_id");
		$minyear=pg_fetch_result($minyearquery,0,0);
		$maxyearquery=pg_query($db,"select max(reference_publicationdate) from reference, referencetoperson WHERE referencetoperson_person=".$id." AND referencetoperson_reference=reference_id");
		$maxyear=pg_fetch_result($maxyearquery,0,0);
		$yeararray=array();
		$publicationcountarray=array();
		for ($chosenyear=$minyear; $chosenyear<=$maxyear; $chosenyear++) {
			$refcountquery=pg_query($db,"select count(reference_id) from reference, referencetoperson WHERE referencetoperson_person=".$id." AND referencetoperson_reference=reference_id AND reference_publicationdate=".$chosenyear);
			array_push($publicationcountarray,pg_fetch_result($refcountquery,0,0));
			array_push($yeararray,$chosenyear);
		}
		if (sizeof($publicationcountarray)>1) {
		echo "<p><br />Publications by year (note that these are publications in TreeTapper's database, not necessarily an exhaustive list)</p>";
		$referencebyyearchart=new googleChart($publicationcountarray);
		$referencebyyearchart->setType('bary');
		$referencebyyearchart->barWidth=30;
		$referencebyyearchart->setLabelsMinMax(min(1+max($publicationcountarray),5),'left');
		//$referencebyyearchart->setLabels("$minyear | $maxyear",'bottom');
		$referencebyyearchart->setLabels($yeararray,'bottom');
		//$referencebyyearchart->title='References by year';
		$referencebyyearchart->dimensions='1000x150';
		$referencebyyearchart->colors='999999';
		$referencebyyearchart->draw();
		echo "<br />";
		}
		//insert dbgraphnav image
		//echo "<iframe valign=center frameborder=no id=\"dbgraphnav\" name=\"dbgraphnav\" scrolling=no src=\"$treetapperbaseurl"."/dbgraphnav/main.php?id=".$id."&type=person\" marginwidth=0 marginheight=0 vspace=0 hspace=0 style=\"overflow:visible; width:100%; display:none\">Navigation image from <a href=\"http://code.google.com/p/dbgraphnav/\">dbgraphnav</a> [a Google Summer of Code 2008 project by <a href=\"http://thefire.us/\">Paul McMillan</a>, mentored by <a href=\"http://www.brianomeara.info\">Brian O'Meara</a> and organized through <a href=\"http://www.nescent.org\">NESCent</a>].</iframe>\n";
		echo "<br>Coauthorship network graph (click to navigate, mouseover for info)<br>";
		echo "<div id=\"networkgraph\">";
		echo "<iframe valign=center frameborder=no id=\"dbgraphnav\" name=\"dbgraphnav\" scrolling=no src=\"$treetapperbaseurl"."/dbgraphnav/ajax-loader.gif\" marginwidth=0 marginheight=0 vspace=0 hspace=0 style=\"overflow:visible; width:100%; display:none\">Navigation image from <a href=\"http://code.google.com/p/dbgraphnav/\">dbgraphnav</a> [a Google Summer of Code 2008 project by <a href=\"http://thefire.us/\">Paul McMillan</a>, mentored by <a href=\"http://www.brianomeara.info\">Brian O'Meara</a> and organized through <a href=\"http://www.nescent.org\">NESCent</a>].</iframe>\n</div>";
							
		
		
		

		
		
		
		echo "<br>Collaborators<br>";
/*		$referenceidquery=pg_query($db, "SELECT reference_id FROM referencetoperson, reference WHERE referencetoperson_person=".$id." AND referencetoperson_reference = reference_id");
		$collaboratorarray=array();
		for ($lr = 0; $lr < pg_numrows($referenceidquery); $lr++) {
			$referenceid=pg_fetch_result($referenceidquery,$lr,0);
			$collaboratorseach=pg_query($db, "SELECT referencetoperson_person FROM referencetoperson WHERE referencetoperson_reference=".$referenceid." AND referencetoperson_person != ".$id);
			if ( pg_numrows($collaboratorseach)>0) {
				for (0 = 0; 0 < pg_numrows($collaboratorseach); 0++) {
					$collaboratorarray[pg_fetch_result($collaboratorseach,0,0)]++;
				}
			}
		}
		arsort($collaboratorarray, SORT_NUMERIC);
		foreach ($collaboratorarray as $key => $val) {
			$collabnamenquery = pg_query($db,"SELECT person_id, person_first, person_middle, person_last FROM person WHERE person_id=".$key) or die("Could not query the database.");
			echo "<br><a href=\"".$treetapperbaseurl."/person/".$key."\">".pg_fetch_result($collabnamenquery,0,1)." ".pg_fetch_result($collabnamenquery,0,2)." ".pg_fetch_result($collabnamenquery,0,3)."</a>: $val co-authored paper";
			if ($val>1) {
				echo "s";
			}
			$totalpapers = pg_query($db, "SELECT count(referencetoperson_id) FROM referencetoperson WHERE referencetoperson_person=".$key);
			echo ", ".pg_fetch_result($totalpapers,0,0)." paper";
			if (pg_fetch_result($totalpapers,0,0)>1) {
				echo "s total";
			}
			else {
				echo " total";
			}
			
		}	
		*/
		echo "<div id=\"collaborators\">";
		include("$treetapperbaseurl/templates/htmltable_person.php?collaboratorid=".$id);
		echo "</div>";
		
		echo "<script type=\"text/javascript\">";
		echo "YAHOO.util.Event.addListener(window, \"load\", function() {";
		echo "loadintoIframe('dbgraphnav', '".$treetapperbaseurl."/dbgraphnav/main.php?id=".$id."&type=person&depth=2');\n
			YAHOO.example.XHR_Text = new function() {";
		echo "this.formatName = function(elCell, oRecord, oColumn, oData) {";
		echo "\nelCell.innerHTML = '<a name=\"' + oRecord.getData('Last') + oRecord.getData('First')  + oRecord.getData('Middle') + '\" href=\"".$treetapperbaseurl."/person/' + oRecord.getData('ID') + '\">' + oRecord.getData('Last') + ', ' + oRecord.getData('First') + ' ' + oRecord.getData('Middle') + '</a>';\n";
		
		echo "};
		
		var mySortFunction = function(a, b) {

			if(!YAHOO.lang.isValue(a)) { 
					return (!YAHOO.lang.isValue(b)) ? 0 : 1; 
			} 
			else if(!YAHOO.lang.isValue(b)) { 
				return -1; 
			} 
			var comp = YAHOO.util.Sort.compare; 
			var compState = comp(parseInt(a.col1), parseInt(b.col1)); 
			return compState;
		};
		
		this.formatInteger = function(elCell, oRecord, oColumn, oData) {
			elCell.innerHTML = 1*parseInt(oData);
		};
			
		
		
		
		var myColumnDefs = [
			{key:\"Name\",sortable:true},
			{key:\"Coauthored references\",   sortable:true, sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_DESC}},
			{key:\"Total references\", sortable:true, sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_DESC}},
			{key:\"Methods\",  sortable:true, sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_DESC}},
			{key:\"Programs\",  sortable:true, sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_DESC}},
			{key:\"URL\", sortable:false}
			];
		
		//For SEO, turn off ajax loading
		//this.myDataSource = new YAHOO.util.DataSource(\"templates/text_person.php?\");
		//this.myDataSource.responseType = YAHOO.util.DataSource.TYPE_TEXT;
		
		this.myDataSource = new YAHOO.util.DataSource(YAHOO.util.Dom.get(\"collaboratorhtmltable\")); 
		this.myDataSource.responseType = YAHOO.util.DataSource.TYPE_HTMLTABLE; 
		
		this.myDataSource.responseSchema = {
fields: [\"Name\",\"URL\",{key: \"Total references\", parser: YAHOO.util.DataSource.parseNumber},{key: \"Methods\", parser: YAHOO.util.DataSource.parseNumber},{key: \"Programs\", parser: YAHOO.util.DataSource.parseNumber},{key: \"Coauthored references\", parser: YAHOO.util.DataSource.parseNumber}]
		};

		//Turn off dynamic loading for SEO
		//this.myDataTable = new YAHOO.widget.DataTable(\"collaborators\", myColumnDefs this.myDataSource,  {initialRequest:\"collaboratorid=".$id."\", renderLoopSize : 20});
		this.myDataTable = new YAHOO.widget.DataTable(\"collaborators\", myColumnDefs, this.myDataSource);

		";
		echo "};
			});";
		echo "
	</script>";

		echo "<br>References<br>";
		$_GET['table'] = 'reference';
		$_GET['authorid'] = $id;
		include ('selectiontable_generic.php');
	}
	else {
		if ($_GET['include']!=1) {
			$_GET['pagetitle']="TreeTapper: Person";
			include('template_pagestart.php');
		}		
		echo ("ERROR: There are ".pg_numrows($personquery)." people with ID ".$id);
	}
	
/*	for (0 = 0; 0 < pg_numrows($personquery); 0++) {
		echo (pg_fetch_result($personquery,0,0)."\t".pg_fetch_result($personquery,0,1)." ".pg_fetch_result($personquery,0,2)." ".pg_fetch_result($personquery,0,3)."\t".pg_fetch_result($personquery,0,4)."\t".pg_fetch_result($personquery,0,5)."\t".pg_fetch_result($personquery,0,6)."\n");
	}	*/
	
}
else { // show info for all people
	if ($_GET['include']!=1) {
		$_GET['pagetitle']="TreeTapper: People";
		include('template_pagestart.php');
	}
	
	$totalpeoplesqlresults=pg_query($db, "SELECT count(person_id) FROM person");
	$totalpeople=pg_fetch_result($totalpeoplesqlresults,0,0);
	echo "There are ".$totalpeople." people in the database. Here they are, arranged alphabetically [it may take several seconds to load]:<br>";
	echo "<div id=\"person\"></div>";
	echo "<script type=\"text/javascript\">";
	echo "YAHOO.util.Event.addListener(window, \"load\", function() {";
	echo "
		YAHOO.example.XHR_Text = new function() {";
	echo "this.formatName = function(elCell, oRecord, oColumn, oData) {";
	echo "\nelCell.innerHTML = '<a name=\"' + oRecord.getData('Last') + oRecord.getData('First')  + oRecord.getData('Middle') + '\" href=\"".$treetapperbaseurl."/person/' + oRecord.getData('ID') + '\">' + oRecord.getData('Last') + ', ' + oRecord.getData('First') + ' ' + oRecord.getData('Middle') + '</a>';\n";
	
	echo "};
	

	
	
	var myColumnDefs = [
		{label:\"Name\", formatter:this.formatName, sortable:true},
		{key:\"References\", formatter:YAHOO.widget.DataTable.formatNumber, sortable:true, sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_DESC}},
		{key:\"Methods\", formatter:YAHOO.widget.DataTable.formatNumber, sortable:true, sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_DESC}},
		{key:\"Programs\", formatter:YAHOO.widget.DataTable.formatNumber, sortable:true, sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_DESC}},
		{key:\"URL\", sortable:false}
		];
	
	
	this.myDataSource = new YAHOO.util.DataSource(\"templates/text_person.php?\");
	this.myDataSource.responseType = YAHOO.util.DataSource.TYPE_TEXT;
	this.myDataSource.responseSchema = {
recordDelim: \"\\n\",
fieldDelim: \"\\t\",
fields: [\"ID\",\"First\",\"Middle\",\"Last\",\"URL\",{key: \"References\", parser: YAHOO.util.DataSource.parseNumber},{key: \"Methods\", parser: YAHOO.util.DataSource.parseNumber},{key: \"Programs\", parser: YAHOO.util.DataSource.parseNumber}]
	};
	var oConfigs = {
initialRequest:\"\",
		renderLoopSize : 200
	};
	this.myDataTable = new YAHOO.widget.DataTable(\"person\", myColumnDefs,
												  this.myDataSource, {renderLoopSize: 100}, oConfigs);
	";
	echo "};
	});";
	echo "
</script>";
	
}

if ($_GET['include']!=1) {
	include('template_pageend.php');
}
?>
