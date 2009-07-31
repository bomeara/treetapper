<?php
//optionbox_generic?table=tablename
include ('dblogin.php'); //connection in $db
$table1name=$_GET['table1'];
$table2name=$_GET['table2'];
$selectiontableid=0;
$validtablename1=0;
$validtablename2=0;
$validtablename=0;
$underscorename=$table1name."_".$table2name."_".$selectiontableid;
$sql = "select relname from pg_stat_user_tables order by relname;";
$result = pg_query($db, $sql);
for ($lt = 0; $lt < pg_numrows($result); $lt++) {
	if(strcmp(pg_fetch_result($result,$lt,0),$table1name)==0) {
		$validtablename1=1;
	}
}
for ($lt = 0; $lt < pg_numrows($result); $lt++) {
	if(strcmp(pg_fetch_result($result,$lt,0),$table2name)==0) {
		$validtablename2=1;
	}
}
$validtablename=0.5*($validtablename1+$validtablename2);
if (strlen($_GET['selectiontableid'])>0) {
	if (is_numeric($_GET['selectiontableid'])) {
		$selectiontableid=$_GET['selectiontableid'];
	}
}
if ($validtablename==1) {
	//echo "<script type=\"text/javascript\">YAHOO.namespace(\"example.container\");\nfunction init() {\nYAHOO.example.container.wait.show();\n};\n</script>\n";
	$table1sql="SELECT $table1name"."_id, $table1name"."_name, $table1name"."_description FROM $table1name";
	$table1namearray=array();
	$table1descarray=array();
	$table1result=pg_query($db,$table1sql) or die ("could not connect");
	for ($lt=0; $lt<pg_numrows($table1result); $lt++) {
		$table1namearray[pg_fetch_result($table1result,$lt,0)]=pg_fetch_result($table1result,$lt,1);
		$table1descarray[pg_fetch_result($table1result,$lt,0)]=pg_fetch_result($table1result,$lt,2);		
	}
	$table2sql="SELECT $table2name"."_id, $table2name"."_name, $table2name"."_description FROM $table2name";
	$table2namearray=array();
	$table2descarray=array();
	$table2result=pg_query($db,$table2sql) or die ("could not connect");
	for ($lt=0; $lt<pg_numrows($table2result); $lt++) {
		$table2namearray[pg_fetch_result($table2result,$lt,0)]=pg_fetch_result($table2result,$lt,1);
		$table2descarray[pg_fetch_result($table2result,$lt,0)]=pg_fetch_result($table2result,$lt,2);		
	}
	//Make the empty table
	echo "<br><fieldset id=\"field_".$underscorename."\" ><legend>Combinations of $table1name and $table2name </legend>";
	echo "<div id=\"id_".$underscorename."_mouseovercheckbox\">Click on <input type=\"checkbox\" name=\"id_".$underscorename."_mouseovercheckbox_box\" > to activate selection by simply mousing over, rather than by clicking: </div>";
	echo "<div id=\"id_".$underscorename."\"></div>";
	echo "<script type=\"text/javascript\">
function checkABox(id) 
{ 
	if (document.getElementsByName(\"id_".$underscorename."_mouseovercheckbox_box\")[0].checked) {
		if (document.getElementById(id).checked) {
			document.getElementById(id).checked = false; 
		}
		else {
			document.getElementById(id).checked = true; 
		}
	}
    return false; /* prevent default href being followed */ 
} 
</script>";
	echo "<table style=\"text-align: center; width: 100%;\" border=\"1\" cellpadding=\"0\"
cellspacing=\"0\" id='selectiontable_".$underscorename."'><br>";
	echo "\n<tr><td>".$table1name."\\".$table2name."</td>\n";
	foreach ($table2namearray as $table2id => $table2name) {
		echo "<td id=\"tablecell_header_".$table2id."\">".$table2namearray[$table2id]."</td>\n";
	}
	echo "</tr>";
	foreach ($table1namearray as $table1id => $table1name) {
		echo "\n<tr><td id=\"tablecell_header_".$table1id."\">".$table1namearray[$table1id]."</td>";
		foreach ($table2namearray as $table2id => $table2name) {
			echo "<td id=\"tablecell_".$table1id."_".$table2id."\">";
			echo "<input type=\"checkbox\" name=\"checkbuttoncombid_".$underscorename."[]\" id=\"checkbutton_".$underscorename."_".$table1id."_".$table2id."\" value=\"".$table1id."_".$table2id."\" onmouseover=\"checkABox('checkbutton_".$underscorename."_".$table1id."_".$table2id."')\">";
			echo "</td>\n";
		}
		echo "</tr>";
	}
	echo "</table>";
	echo "</fieldset>";
	
	echo "<script type=\"text/javascript\">\nYAHOO.util.Event.addListener(window, \"load\", function() {"; //beginning of script
	echo "\nYAHOO.example.EnhanceButtons = new function() {\n";
	//$count=0;
	echo "function checkA".$underscorename."_mouseovercheckbox_Box(name) 
	{ 
		alert(\"Initial mouseover\");
		if (document.getElementsByName(\"id_".$underscorename."_mouseovercheckbox_box\")[0].checked) {
			if (document.getElementsByName(name)[0].checked) {
				document.getElementsByName(name)[0].checked = false; 
				alert(\"Changing to false\");
			}
			else {
				document.getElementsByName(name)[0].checked = true; 
				alert(\"Changing to true\");

			}
		}
	};\n ";
	foreach ($table1namearray as $table1id => $table1name) {
		foreach ($table2namearray as $table2id => $table2name) {
			$tooltiptext="'".$table1name."' (row)<br>with '".$table2name."' (col)";
			//$count++;
//			echo "\nvar oCheckButton".$underscorename."_".$table1id."_".$table2id." = new YAHOO.widget.Button({ type: \"checkbox\", label: \"+\", id: \"checkbutton_".$underscorename."_".$table1id."_".$table2id."\", name: \"checkbuttoncombid_".$underscorename."[]\", value: \"".$table1id."_".$table2id."\", container: \"tablecell_".$table1id."_".$table2id."\", checked: false });\n";
			//echo "\nvar oCheckButton".$underscorename."_".$table1id."_".$table2id." = new YAHOO.widget.Button(\"checkbutton_".$underscorename."_".$table1id."_".$table2id."\", {label:\"+\"});\n";
			//echo "\nvar tt".$underscorename."_".$table1id."_".$table2id." = new YAHOO.widget.Tooltip(\"tt".$underscorename."_".$table1id."_".$table2id."\", { context: oCheckButton".$underscorename."_".$table1id."_".$table2id.".get(\"element\").getElementsByTagName(\"button\")[0], text:\"".$tooltiptext."\" });\n";
		//	echo "YAHOO.util.Event.addListener(document.getElementsByName(\"checkbuttoncombid_".$underscorename."\"),\"mouseover\", checkABox,\"checkbutton_".$underscorename."_".$table1id."_".$table2id."\");\n"; 
		}
	}
	
	echo "};\n";
	echo "\nYAHOO.example.EnhanceTable = new function() {\n";
	echo "var myColumnDefs = [\n{key:\"Types\", label:\"\", sortable:false}";
	foreach ($table2namearray as $table2id => $table2name) {
		echo ",\n{key:\"".$table2id."\", label:\"".$table2id."\", sortable:false}";
	}
	echo "\n];\n";
	
	echo "       this.myDataSource = new YAHOO.util.DataSource(YAHOO.util.Dom.get(\"selectiontable_".$underscorename."\"));
	this.myDataSource.responseType = YAHOO.util.DataSource.TYPE_HTMLTABLE;
	this.myDataSource.responseSchema = {
fields: [{key:\"Types\"}";
	foreach ($table2namearray as $table2id => $table2name) {
		echo ",\n{key:\"".$table2id."\"}";
	}
	echo "\n]\n";
	
	echo "};
	
	this.myDataTable = new YAHOO.widget.DataTable(\"markup\", myColumnDefs, this.myDataSource,{sortedBy:{key:\"Types\"} } );\n};";
	//echo "\nYAHOO.example.container.wait.show();\n";
	
	
	echo "});\n";
	echo "\n</script>"; //end of script
	
}
else {
	echo "invalid table name of $tablename";
}
?>