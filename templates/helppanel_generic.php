<?php
include ('dblogin.php'); //connection in $db
$tablename=$_GET['table'];
//This section to infer that we have a valid table name
$validtablename=0;
$sql= "SELECT tablelist_name, tablelist_description, tablelist_id FROM tablelist ORDER BY tablelist_id ASC";
$description="Description";
$result = pg_query($db, $sql);
for ($lt = 0; $lt < pg_numrows($result); $lt++) {
	if(strcmp(pg_fetch_result($result,$lt,0),$tablename)==0) {
		$validtablename=1;
		$description=pg_fetch_result($result,$lt,1);
	}
}
if ($validtablename==1) {
	$result2 = pg_query($db,"SELECT $tablename"."_name, $tablename"."_description FROM $tablename ") or die("Couldn't query the database.");
/*	echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01//EN\" \"http://www.w3.org/TR/html4/strict.dtd\">
<html>
<head>
";
	include 'yuiheadersPart1.php'; //Has script sources
	echo "<title>Get help</title>";
	include 'yuiheadersPart2.php'; //Has style info, other script info
	echo "</head>
<body class=\"yui-skin-sam\">
";*/

echo("

<script>
YAHOO.namespace(\"help.help\");

function helpinit".$tablename."() {
			// Instantiate a Panel from script
	YAHOO.help.help.".$tablename."panel = new YAHOO.widget.Panel(\"".$tablename."panel\", { width:\"520px\", visible:false, draggable:true, close:true , fixedcenter:true, effect:[{effect:YAHOO.widget.ContainerEffect.FADE,duration:0.5}, 
		{effect:YAHOO.widget.ContainerEffect.SLIDE,duration:0.5}]} );
	YAHOO.help.help.".$tablename."panel.setHeader(\"".$description."\");
	YAHOO.help.help.".$tablename."panel.setBody(\"<div align='left'><ul>");
for ($lt = 0; $lt < pg_numrows($result2); $lt++) {
	//$output="<br><b>".pg_fetch_result($result2,$lt,0)."</b>: ".pg_fetch_result($result2,$lt,1);
	$name=pg_fetch_result($result2,$lt,0);
	$description=pg_fetch_result($result2,$lt,1);
	$output="<li><b>$name</b>: $description</li>";	
	echo("$output");
}
echo("</ul></div>\");
	YAHOO.help.help.".$tablename."panel.render(document.body);
	YAHOO.util.Event.addListener(\"help".$tablename."\", \"click\", YAHOO.help.help.".$tablename."panel.show, YAHOO.help.help.".$tablename."panel, true);
}

YAHOO.util.Event.addListener(window, \"load\", helpinit".$tablename."());
</script>


<button id=\"help".$tablename."\">?</button> 

");
//echo("</body>
//
//</html>
//");
}
else {
	echo "invalid table name of $tablename";
}
?>
