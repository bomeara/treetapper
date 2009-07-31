<?PHP
$_GET['pagetitle']="TreeTapper: Controlled vocabularies";
include('templates/template_pagestart.php');
include ('templates/dblogin.php');
$vocabtables = array(1,2,4,8,9,12,13,21,22,24,35,36);
echo "<style>
.subhead {font-size:131%;} /* for setting 17px */
</style>";
echo "<div id=\"bodytext\" align=\"left\">Here are tables for each of the controlled vocabularies of the database. <br><br>";
$sql= "SELECT tablelist_name, tablelist_description, tablelist_id FROM tablelist ORDER BY tablelist_id ASC";
$result = pg_query($db, $sql);
for ($lt = 0; $lt < pg_numrows($result); $lt++) {
	if (in_array(pg_fetch_result($result,$lt,2), $vocabtables)) {
		echo "\n<br><p class=\"subhead\"><a name=\"".pg_fetch_result($result,$lt,0)."\">".pg_fetch_result($result,$lt,1);
		$tableid=pg_fetch_result($result,$lt,2);
		$tablename=pg_fetch_result($result,$lt,0);
		 $_GET['table'] = $tablename;
//		include 'templates/add_generic.php';
		echo "</p>\n";
		echo "<div id=\"".$tablename."\"></div>";
		echo "
<script type=\"text/javascript\">";
		echo "YAHOO.util.Event.addListener(window, \"load\", function() {";
		echo "
			YAHOO.example.XHR_Text = new function() {
	//alert(\"Now in selectiontable_generic for ".pg_fetch_result($result,$lt,0)."\");
				
				
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
					
					
					this.myDataTable = new YAHOO.widget.DataTable(\"".$tablename."\", myColumnDefs,
																  this.myDataSource, {initialRequest:\"table=".$tablename."\"});
					
					//this.myDataSource.setInterval(5000, this.myDataTable.get('initialRequest'),this.myDataTable.onDataReturnIntializeTable,this.myDataTable);
					";
		echo "};
		
		
		});";
		echo "
</script>";
		
	}
}
echo "</div>";
include('templates/template_pageend.php');
?>
