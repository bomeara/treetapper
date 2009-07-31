<?PHP
$tablename=$_GET['table'];
if (strlen($tablename)==0) {
	$tablename=$_POST['table'];
}
$selectiontableid=0;

$validtablename=0;
$sql = "select relname from pg_stat_user_tables order by relname;";
$result = pg_query($db, $sql);
for ($lt = 0; $lt < pg_numrows($result); $lt++) {
	if(strcmp(pg_fetch_result($result,$lt,0),$tablename)==0) {
	//	echo (pg_fetch_result($result,$lt,0)." = ".$tablename."\n<br>");
		$validtablename=1;
	}
	//else {
	//	echo (pg_fetch_result($result,$lt,0)." != ".$tablename."\n<br>");
	//}
}
$includepending=0;
if (strlen($_GET['includepending'])>0) {
	if (is_numeric($_GET['includepending'])) {
		$pending=$_GET['includepending'];
	}
}

if (strlen($_GET['selectiontableid'])>0) {
	if (is_numeric($_GET['selectiontableid'])) {
		$selectiontableid=$_GET['selectiontableid'];
	}
}
if ($validtablename==1) {
	$cookiename=$tablename."_cookie";
	if (strlen($_GET['cookiename'])>0) {
		$cookiename=$_GET['cookiename'];
	}
	//checkbox / pending / author1 / year/ title/ journal/ volume / issue/ startpage / endpage/ ref id
	if (preg_match('/reference/',$tablename)) {
		$authorid=0;
		$authorname="";
		if (strlen($_GET['authorid'])>0) {
			if (is_numeric($_GET['authorid'])) {
				$authorid=$_GET['authorid'];
			}
		}		
		else if (strlen($_POST['authorid'])>0) {
			if (is_numeric($_POST['authorid'])) {
				$authorid=$_POST['authorid'];
			}
		}		
		else if (strlen($_POST['query'])>0) {
			if (is_numeric($_POST['query'])) {
				$authorid=$_POST['query'];
			}
			else {
				$authorname=$_POST['query'];
			}
		}
		
		
//		echo "Choose reference(s): ";
//		include ('add_generic.php');
		echo "<div id=\"".$tablename."\"></div>";
		echo "<script type=\"text/javascript\">";
		echo "
	function checkboxcookie".$cookiename." ( recordid ) {
	var checkboxid=\"checkbutton_".$tablename."_".$selectiontableid."_opt_\" + recordid;
	if (document.getElementById(checkboxid).checked==true) {
		alert(\"Check box changed, recordid = \" + recordid + \" [true]\");
		YAHOO.util.Cookie.setSub(\"".$cookiename."\",  \"reference_\" +  recordid, \"1\");
	}
	else if (document.getElementById(checkboxid).checked==false) {
		alert(\"Check box changed, recordid = \" + recordid + \" [false]\");
		YAHOO.util.Cookie.removeSub(\"".$cookiename."\", \"reference_\" +  recordid);
	}
	else {
		alert(\"Check box neither true nor false, recordid = \" + recordid + \" checkboxid = \" + checkboxid + \" [?]\");
	}
};\n";
		echo "maxID_".$tablename."_".$selectiontableid."=0;";
		echo "YAHOO.util.Event.addListener(window, \"load\", function() {";
		echo "
			YAHOO.example.XHR_Text = new function() {";
		echo "this.formatCheckButton = function(elCell, oRecord, oColumn, oData) {";
		echo "\nvar truefalse=oRecord.getData('Select');\nif(truefalse.match(\"true\")) {\n";
		//echo "\nelCell.innerHTML = '<input type=checkbox id= \"checkbutton_".$tablename."_".$selectiontableid."_opt_' + oRecord.getData('ID') + '\"   onClick= \"checkboxcookie".$cookiename." (' + oRecord.getData('ID') + ')\" name= \"checkbutton".$tablename."_".$selectiontableid."[]\"  value= ' + oRecord.getData('ID') + '  checked />';\n"; 
		echo "\nelCell.innerHTML = '<input type=checkbox id= \"checkbutton_".$tablename."_".$selectiontableid."_opt_' + oRecord.getData('ID') + '\"   onClick= \"checkboxcookie".$cookiename." (' + oRecord.getData('ID') + ')\" name= \"checkbutton".$tablename."_".$selectiontableid."[]\"  value= ' + oRecord.getData('ID') + '  checked />';\n"; 
		echo "}\nelse {";
		echo "\nelCell.innerHTML = '<input type=checkbox id= \"checkbutton_".$tablename."_".$selectiontableid."_opt_' + oRecord.getData('ID') + '\"  onClick= \"checkboxcookie".$cookiename." (' + oRecord.getData('ID') + ')\" name= \"checkbutton".$tablename."_".$selectiontableid."[]\"  value= ' + oRecord.getData('ID') + '   />';\n"; 
		echo "}\n";
		
		echo "};
		

		
		this.formatAddTooltip=function(elCell, oRecord, oColumn, oData)
		{
			elCell.title= oRecord.getData('Description');
			elCell.innerHTML = oData;
		};
		
		this.getMaxID=function(elCell, oRecord, oColumn, oData)
		{
			if (maxID_".$tablename."_".$selectiontableid."==undefined) {
				maxID_".$tablename."_".$selectiontableid."=0;
				}
			maxID_".$tablename."_".$selectiontableid."=Math.max(oData,maxID_".$tablename."_".$selectiontableid.");
			elCell.innerHTML = oData;
		};
		
		this.formatWebHits=function(elCell, oRecord, oColumn, oData)
		{
			var hitcount=oData;
			if (hitcount != parseInt(hitcount)) {
				elCell.innerHTML='';
				}
			else {
				
				elCell.innerHTML= '<a href=\"http://search.yahoo.com/web/advanced?ei=UTF-8&p=' + oRecord.getData('WebQueryString') + '\" target=\"_blank\">'+ oData + '</a>';
				}
		};
		
		this.formatPDFHits=function(elCell, oRecord, oColumn, oData)
		{
			var hitcount=oData;
			if (hitcount != parseInt(hitcount)) {
				elCell.innerHTML='';
			}
			else {
				
				elCell.innerHTML= '<a href=\"http://search.yahoo.com/search?n=10&ei=UTF-8&va_vt=any&vo_vt=any&ve_vt=any&vp_vt=any&vd=all&vf=pdf&vm=p&p=' + oRecord.getData('WebQueryString') + '\" target=\"_blank\">'+ oData + '</a>';
			}
		};
		
		
		
		var myColumnDefs = [
			{label:\"Select\", formatter:this.formatCheckButton},
			{key:\"Author\", sortable:true},
			{key:\"Year\", sortable:true},
			{key:\"Title\", sortable:true},
			{key:\"Journal\", sortable:true},
			{key:\"Volume\", sortable:true},
			{key:\"Issue\", sortable:true},
			{key:\"Pages\", sortable:true},
			{key:\"WebHits\", sortable:true, formatter:this.formatWebHits},
			{key:\"PDFHits\", sortable:true, formatter:this.formatPDFHits},
			//{key:\"Link\", sortable:true},
			//{key:\"ID\", formatter:this.getMaxID, sortable:true},
			{key:\"App.\", sortable:true}
			];
		
		";
		$initialRequest="";
		echo ("this.myDataSource = new YAHOO.util.DataSource(\"templates/selectiontable_js.php?\");\n");
		if ($authorid>0) {
			//echo ("this.myDataSource = new YAHOO.util.DataSource(\"templates/selectiontable_js.php?table=".$tablename."&includepending=1&authorid=".$authorid."&\");\n");
			$initialRequest="table=".$tablename."&includepending=".$includepending."&authorid=".$authorid;
		}
		else if (strlen($authorname)>0) {
			//echo ("this.myDataSource = new YAHOO.util.DataSource(\"templates/selectiontable_js.php?table=".$tablename."&includepending=1&authorname=".$authorname."&\");\n");
			$initialRequest="table=".$tablename."&includepending=".$includepending."&authorname=".$authorname;
		}
		else {
			//echo ("this.myDataSource = new YAHOO.util.DataSource(\"templates/selectiontable_js.php?table=".$tablename."&includepending=1&\");\n");	
			$initialRequest="table=".$tablename."&includepending=".$includepending."";			
		}
		echo "
		this.myDataSource.responseType = YAHOO.util.DataSource.TYPE_TEXT;
		this.myDataSource.responseSchema = {
recordDelim: \"\\n\",
fieldDelim: \"\\t\",
fields: [\"Select\",\"App.\",\"Author\",\"Year\",\"Title\",\"Journal\",\"Volume\",\"Issue\",\"Pages\",\"Link\",\"ID\",\"WebHits\",\"PDFHits\",\"WebQueryString\"]
		};
		this.myDataTable = new YAHOO.widget.DataTable(\"".$tablename."\", myColumnDefs,
													  this.myDataSource, {initialRequest:\"".$initialRequest."\", scrollable:false, renderLoopSize : 20});
		
		this.myDataTable.subscribe(\"rowMouseoverEvent\", this.myDataTable.onEventHighlightRow); 
		this.myDataTable.subscribe(\"rowMouseoutEvent\",  this.myDataTable.onEventUnhighlightRow); 
		
	/*	this.myDataTable.subscribe('checkboxClickEvent', function(oArgs){
			YAHOO.util.Event.stopEvent(oArgs.event);
			var elCheckbox = oArgs.target;
			var record = this.getRecord(elCheckbox);
			var recordKey = record.getData('ID');
			var newValue = elCheckbox.checked;
			if (elCheckbox.checked == true) {
				//YAHOO.util.Cookie.setSub(\"".$cookiename."\", recordKey, \"1\");
				YAHOO.util.Cookie.setSub(\"".$cookiename."\", \"cow\", recordKey);
				document.getElementById(\"checkbutton_".$tablename."_".$selectiontableid."_opt_' + record.getData('ID') + '\").checked=true;
			}
			else {
				//YAHOO.util.Cookie.removeSub(\"".$cookiename."\", recordKey);
				YAHOO.util.Cookie.removeSub(\"".$cookiename."\", \"cow\");
				document.getElementById(\"checkbutton_".$tablename."_".$selectiontableid."_opt_' + record.getData('ID') + '\").checked=false;
			}
			oRecord.setData(\"check\",elCheckbox.checked); 
		}); */
		";
		echo "};
		
		
		
		});";
		echo "
</script>";
		
	}
	else if (preg_match('/charactercombination/',$tablename)) {
	}
	else if (preg_match('/method/',$tablename)) {
		echo "<div id=\"".$tablename."\"></div>";
		echo "
<script type=\"text/javascript\">";
		echo "maxID_".$tablename."_".$selectiontableid."=0;";
		echo "YAHOO.util.Event.addListener(window, \"load\", function() {";
		echo "
			YAHOO.example.XHR_Text = new function() {
	//alert(\"Now in selectiontable_generic for ".$tablename."\");
				this.formatCheckButton = function(elCell, oRecord, oColumn, oData) {";
		echo "\nvar truefalse=oRecord.getData('Select');\nif(truefalse.match(\"true\")) {\n";
		echo "\nelCell.innerHTML = '<input type=checkbox id= \"checkbutton_".$tablename."_".$selectiontableid."_opt_' + oRecord.getData('ID') + '\"  name= \"checkbutton".$tablename."_".$selectiontableid."[]\" value= ' + oRecord.getData('ID') + '  checked />';\n"; 
		echo "}\nelse {";
		echo "\nelCell.innerHTML = '<input type=checkbox id= \"checkbutton_".$tablename."_".$selectiontableid."_opt_' + oRecord.getData('ID') + '\"  name= \"checkbutton".$tablename."_".$selectiontableid."[]\" value= ' + oRecord.getData('ID') + '   />';\n"; 
		echo "}\n";
		echo "};
		
		this.formatAddTooltip=function(elCell, oRecord, oColumn, oData)
		{
			elCell.title= oRecord.getData('Description');
			elCell.innerHTML = oData;
		};
		
		this.getMaxID=function(elCell, oRecord, oColumn, oData)
		{
			if (maxID_".$tablename."_".$selectiontableid."==undefined) {
				maxID_".$tablename."_".$selectiontableid."=0;
			}
			maxID_".$tablename."_".$selectiontableid."=Math.max(oData,maxID_".$tablename."_".$selectiontableid.");
			elCell.innerHTML = oData;
		};
		
		
		var myColumnDefs = [
			{label:\"Select\", formatter:this.formatCheckButton},
			{key:\"Name\", formatter:this.formatAddTooltip},
			{key:\"ID\", formatter:this.getMaxID, sortable:true, visible:false},
			{key:\"PDF\",  sortable:true, sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_DESC}},
			{key:\"HTML\", sortable:true, sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_DESC}}";
		
		if ($includepending==1) {
			echo ",
			{key:\"Accepted\", sortable:true}";
		}
		echo "
			];
		
		";
		$initialRequest="table=".$tablename."&includepending=".$includepending."";
		echo "
			this.myDataSource = new YAHOO.util.DataSource(\"templates/selectiontable_js.php?\");
			//this.myDataSource.setInterval(500,\"\",\"\",\"\");
		this.myDataSource.responseType = YAHOO.util.DataSource.TYPE_TEXT;
		this.myDataSource.responseSchema = {
recordDelim: \"\\n\",
fieldDelim: \"\\t\",
fields: [\"Select\",\"Accepted\",\"Name\",\"Description\",\"ID\",{key: \"PDF\", parser: YAHOO.util.DataSource.parseNumber},{key: \"HTML\", parser: YAHOO.util.DataSource.parseNumber}]
		};
		
		
		this.myDataTable = new YAHOO.widget.DataTable(\"".$tablename."\", myColumnDefs,
													  this.myDataSource, {initialRequest:\"".$initialRequest."\", scrollable:false, renderLoopSize : 20});
		
		this.myDataTable.subscribe(\"rowMouseoverEvent\", this.myDataTable.onEventHighlightRow); 
		this.myDataTable.subscribe(\"rowMouseoutEvent\",  this.myDataTable.onEventUnhighlightRow); 
		
		/*		this.myDataTable.subscribe('checkboxClickEvent', function(oArgs){
			var elCheckbox = oArgs.target;
		var newValue = elCheckbox.checked;
		var record = this.getRecord(elCheckbox);
		var recordKey = record.getData('ID');
		alert(\"Have done something \" + newValue + recordKey);
		});*/
					//this.myDataSource.setInterval(10000, this.myDataTable.get(\"templates/selectiontable_js.php?table=".$tablename."&minid=\" + maxID_".$tablename."_".$selectiontableid." + \"&includepending=".$includepending."\"),this.myDataTable.onDataReturnAppendRows,this.myDataTable);
			//	this.myDataSource.setInterval(10000, this.myDataTable.get(\"templates/selectiontable_js.php?table=".$tablename."&minid=5&includepending=".$includepending."\"),this.myDataTable.onDataReturnAppendRows,this.myDataTable);
					//this.myDataSource.setInterval(2000, this.myDataTable.get('initialRequest'),this.myDataTable.onDataReturnIntializeTable,this.myDataTable);
		";
		echo "};
		
		
		});";
		echo "
</script>";		
	}
	else if (preg_match('/posedquestion/',$tablename)) {
		echo "Choose specific question(s):";
		echo " ";
		include ('add_generic.php');
		echo "<div id=\"".$tablename."\"></div>";
		echo "
<script type=\"text/javascript\">";
		echo "maxID_".$tablename."_".$selectiontableid."=0;";
		echo "YAHOO.util.Event.addListener(window, \"load\", function() {";
		echo "
			YAHOO.example.XHR_Text = new function() {
	//alert(\"Now in selectiontable_generic for ".$tablename."\");
				this.formatCheckButton = function(elCell, oRecord, oColumn, oData) {";
		echo "\nvar truefalse=oRecord.getData('Select');\nif(truefalse.match(\"true\")) {\n";
		echo "\nelCell.innerHTML = '<input type=checkbox id= \"checkbutton_".$tablename."_".$selectiontableid."_opt_' + oRecord.getData('ID') + '\"  name= \"checkbutton".$tablename."_".$selectiontableid."[]\"  value= ' + oRecord.getData('ID') + '  checked />';\n"; 
		echo "}\nelse {";
		echo "\nelCell.innerHTML = '<input type=checkbox id= \"checkbutton_".$tablename."_".$selectiontableid."_opt_' + oRecord.getData('ID') + '\"  name= \"checkbutton".$tablename."_".$selectiontableid."[]\"  value= ' + oRecord.getData('ID') + '   />';\n"; 
		echo "}\n";
		echo "};
		
		this.formatAddTooltip=function(elCell, oRecord, oColumn, oData)
		{
			elCell.title= oRecord.getData('Description');
			elCell.innerHTML = oData;
		};
		
		this.getMaxID=function(elCell, oRecord, oColumn, oData)
		{
			if (maxID_".$tablename."_".$selectiontableid."==undefined) {
				maxID_".$tablename."_".$selectiontableid."=0;
			}
			maxID_".$tablename."_".$selectiontableid."=Math.max(oData,maxID_".$tablename."_".$selectiontableid.");
			elCell.innerHTML = oData;
		};
		
		
		var myColumnDefs = [
			{label:\"Select\", formatter:this.formatCheckButton},
			{key:\"Topic\", sortable:true},
			{key:\"Name\", formatter:this.formatAddTooltip},
			{key:\"ID\", formatter:this.getMaxID, sortable:true},
			{key:\"Accepted\", sortable:true}
			];
		
		";
		$initialRequest="table=".$tablename."&includepending=".$includepending."";
		echo "
			this.myDataSource = new YAHOO.util.DataSource(\"templates/selectiontable_js.php?\");
			//this.myDataSource.setInterval(500,\"\",\"\",\"\");
		this.myDataSource.responseType = YAHOO.util.DataSource.TYPE_TEXT;
		this.myDataSource.responseSchema = {
recordDelim: \"\\n\",
fieldDelim: \"\\t\",
fields: [\"Select\",\"Accepted\",\"Name\",\"Description\",\"ID\",\"Topic\"]
		};
		
		
		this.myDataTable = new YAHOO.widget.DataTable(\"".$tablename."\", myColumnDefs,
													  this.myDataSource, {initialRequest:\"".$initialRequest."\", scrollable:false, renderLoopSize : 20});
		
		this.myDataTable.subscribe(\"rowMouseoverEvent\", this.myDataTable.onEventHighlightRow); 
		this.myDataTable.subscribe(\"rowMouseoutEvent\",  this.myDataTable.onEventUnhighlightRow); 
		
		/*		this.myDataTable.subscribe('checkboxClickEvent', function(oArgs){
			var elCheckbox = oArgs.target;
		var newValue = elCheckbox.checked;
		var record = this.getRecord(elCheckbox);
		var recordKey = record.getData('ID');
		alert(\"Have done something \" + newValue + recordKey);
		});*/
					//this.myDataSource.setInterval(10000, this.myDataTable.get(\"templates/selectiontable_js.php?table=".$tablename."&minid=\" + maxID_".$tablename."_".$selectiontableid." + \"&includepending=".$includepending."\"),this.myDataTable.onDataReturnAppendRows,this.myDataTable);
			//	this.myDataSource.setInterval(10000, this.myDataTable.get(\"templates/selectiontable_js.php?table=".$tablename."&minid=5&includepending=".$includepending."\"),this.myDataTable.onDataReturnAppendRows,this.myDataTable);
					//this.myDataSource.setInterval(2000, this.myDataTable.get('initialRequest'),this.myDataTable.onDataReturnIntializeTable,this.myDataTable);
		";
		echo "};
		
		
		});";
		echo "
</script>";
	}

	else {
		if (preg_match('/charactertype/',$tablename)) {
			echo "Choose character type(s):";
		}
		else if (preg_match('/treetype/',$tablename)) {
			echo "Choose tree type(s):";
		}
		else if (preg_match('/criterion/',$tablename)) {
			echo "Choose criterion (criteria):";
		}
		else if (preg_match('/platform/',$tablename)) {
			echo "Choose platform(s):";
		}
		else if (preg_match('/applicationkind/',$tablename)) {
			echo "Choose kind(s) of application:";
		}
		else if (preg_match('/generalquestion/',$tablename)) {
			echo "Choose general question(s):";
		}
		else if (preg_match('/branchlengthtype/',$tablename)) {
			echo "Choose branch length type(s):";
		}
		else if (preg_match('/treeformat/',$tablename)) {
			echo "Choose tree format(s):";
		}
		else if (preg_match('/dataformat/',$tablename)) {
			echo "Choose data format(s):";
		}
		else if (preg_match('/program/',$tablename)) {
			echo "Choose program(s):";
		}
		echo " ";
		include ('add_generic.php');
		echo "<div id=\"".$tablename."\"></div>";
		echo "
<script type=\"text/javascript\">";
		echo "maxID_".$tablename."_".$selectiontableid."=0;";
		echo "YAHOO.util.Event.addListener(window, \"load\", function() {";
		echo "
			YAHOO.example.XHR_Text = new function() {
	//alert(\"Now in selectiontable_generic for ".$tablename."\");
				this.formatCheckButton = function(elCell, oRecord, oColumn, oData) {";
				echo "\nvar truefalse=oRecord.getData('Select');\nif(truefalse.match(\"true\")) {\n";
				echo "\nelCell.innerHTML = '<input type=checkbox id= \"checkbutton_".$tablename."_".$selectiontableid."_opt_' + oRecord.getData('ID') + '\"  name= \"checkbutton".$tablename."_".$selectiontableid."[]\"  value= ' + oRecord.getData('ID') + '  checked />';\n"; 
				echo "}\nelse {";
				echo "\nelCell.innerHTML = '<input type=checkbox id= \"checkbutton_".$tablename."_".$selectiontableid."_opt_' + oRecord.getData('ID') + '\"  name= \"checkbutton".$tablename."_".$selectiontableid."[]\"  value= ' + oRecord.getData('ID') + '   />';\n"; 
				echo "}\n";
				echo "};
				
				this.formatAddTooltip=function(elCell, oRecord, oColumn, oData)
				{
					elCell.title= oRecord.getData('Description');
					elCell.innerHTML = oData;
				};
				
				this.getMaxID=function(elCell, oRecord, oColumn, oData)
				{
					if (maxID_".$tablename."_".$selectiontableid."==undefined) {
						maxID_".$tablename."_".$selectiontableid."=0;
					}
					maxID_".$tablename."_".$selectiontableid."=Math.max(oData,maxID_".$tablename."_".$selectiontableid.");
					elCell.innerHTML = oData;
				};
				
				
				var myColumnDefs = [
					{label:\"Select\", formatter:this.formatCheckButton},
					{key:\"Name\", formatter:this.formatAddTooltip},
					{key:\"ID\", formatter:this.getMaxID, sortable:true},
					{key:\"Accepted\", sortable:true}
					];
				
				";
				$initialRequest="table=".$tablename."&includepending=".$includepending."";
				echo "
				this.myDataSource$tablename = new YAHOO.util.DataSource(\"templates/selectiontable_js.php?\");
			//this.myDataSource$tablename.setInterval(500,\"\",\"\",\"\");
				this.myDataSource$tablename.responseType = YAHOO.util.DataSource.TYPE_TEXT;
				this.myDataSource$tablename.responseSchema = {
recordDelim: \"\\n\",
fieldDelim: \"\\t\",
fields: [\"Select\",\"Accepted\",\"Name\",\"Description\",\"ID\"]
				};
					
					
					this.myDataTable = new YAHOO.widget.DataTable(\"".$tablename."\", myColumnDefs,
																  this.myDataSource$tablename, {initialRequest:\"".$initialRequest."\", scrollable:false, renderLoopSize : 20});
					
				this.myDataTable.subscribe(\"onmouseover\",  this.myDataSource$tablename.setInterval(10000, this.myDataTable.get('initialRequest'),this.myDataTable.onDataReturnIntializeTable,this.myDataTable)); //every 10 seconds, update
				//this.myDataTable.subscribe(\"onmouseout\",  this.myDataSource$tablename.setInterval(10000, this.myDataTable.get('initialRequest'),this.myDataTable.onDataReturnIntializeTable,this.myDataTable)); 
				
				
				this.myDataTable.subscribe(\"rowMouseoverEvent\", this.myDataTable.onEventHighlightRow); 
				this.myDataTable.subscribe(\"rowMouseoutEvent\",  this.myDataTable.onEventUnhighlightRow); 
				
				//this.myDataTable.subscribe(\"rowMouseoverEvent\", (this.myDataTable.getDataSource()).setInterval(2000, this.myDataTable.get('initialRequest'),this.myDataTable.onDataReturnIntializeTable,this.myDataTable)); 

				
			/*		this.myDataTable.subscribe('checkboxClickEvent', function(oArgs){
						var elCheckbox = oArgs.target;
						var newValue = elCheckbox.checked;
						var record = this.getRecord(elCheckbox);
						var recordKey = record.getData('ID');
						alert(\"Have done something \" + newValue + recordKey);
					});*/
					//this.myDataSource$tablename.setInterval(10000, this.myDataTable.get(\"templates/selectiontable_js.php?table=".$tablename."&minid=\" + maxID_".$tablename."_".$selectiontableid." + \"&includepending=".$includepending."\"),this.myDataTable.onDataReturnAppendRows,this.myDataTable);
			//	this.myDataSource$tablename.setInterval(10000, this.myDataTable.get(\"templates/selectiontable_js.php?table=".$tablename."&minid=5&includepending=".$includepending."\"),this.myDataTable.onDataReturnAppendRows,this.myDataTable);
					//this.myDataSource$tablename.setInterval(2000, this.myDataTable.get('initialRequest'),this.myDataTable.onDataReturnIntializeTable,this.myDataTable);
					";
		echo "};
		
		
		});";
		echo "
</script>";
	}
}
else {
	echo "invalid table name of $tablename";
}

?>
