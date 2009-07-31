<?PHP
//include('template_pagestart.php');
include ('dblogin.php'); //connection in $db

?>




<script type="text/javascript">
function fillInPosedQuestion() {
	maxID_posedquestion_0=0;
	this.formatCheckButton = function(elCell, oRecord, oColumn, oData) {
		var truefalse=oRecord.getData('Select');
		if(truefalse.match("true")) {
			elCell.innerHTML = '<input type=checkbox id= "checkbutton_posedquestion_0_opt_' + oRecord.getData('ID') + '"  name= "checkbuttonposedquestion_0[]"  value= ' + oRecord.getData('ID') + '  checked />'; 
		}
		else {
			elCell.innerHTML = '<input type=checkbox id= "checkbutton_posedquestion_0_opt_' + oRecord.getData('ID') + '"  name= "checkbuttonposedquestion_0[]"  value= ' + oRecord.getData('ID') + '   />';
		}
	};
	
	this.formatAddTooltip=function(elCell, oRecord, oColumn, oData)
	{
		elCell.title= oRecord.getData('Description');
		elCell.innerHTML = oData;
	};
	
	this.getMaxID=function(elCell, oRecord, oColumn, oData)
	{
		if (maxID_posedquestion_0==undefined) {
			maxID_posedquestion_0=0;
		}
		maxID_posedquestion_0=Math.max(oData,maxID_posedquestion_0);
		elCell.innerHTML = oData;
	};
	
	
	var myColumnDefs = [
		{label:"Select", formatter:this.formatCheckButton},
		{key:"Name", formatter:this.formatAddTooltip},
		{key:"ID", formatter:this.getMaxID, sortable:true},
		{key:"Accepted", sortable:true}
		];
	this.myDataSource = new YAHOO.util.DataSource('templates/selectiontable_js.php?');
	this.myDataSource.responseType = YAHOO.util.DataSource.TYPE_TEXT;
	this.myDataSource.responseSchema = {
recordDelim: "\n",
fieldDelim: "\t",
fields: ["Select","Accepted","Name","Description","ID"]
	};
	this.myDataTable = new YAHOO.widget.DataTable("posedquestion", myColumnDefs,
												  this.myDataSource, {initialRequest:'table=posedquestion&includepending=1&foreignkey=' + document.getElementById("generalquestionselectbox").value,  scrollable:false});
	
}; 
</script>

<div id="generalquestiondiv">
<br>Select category: <select name="generalquestionselectbox" id="generalquestionselectbox" onChange="fillInPosedQuestion()">
<?PHP
$_GET['table'] = 'generalquestion';
include ('optionbox_generic.php');
?>
</select>
<?PHP
echo "<input type='button' value='+' onclick=\"window.open('".$treetapperbaseurl."/add_generalquestion.php')\">";
?>
</p><p>
</div>
<br>Select question(s) [select category first]
<?PHP
echo "<input type='button' value='+' onclick=\"window.open('".$treetapperbaseurl."/add_posedquestion.php')\">";
?>
</p><p><br><div id="posedquestion"></div>

<?PHP
//include('template_pageend.php');
?>
