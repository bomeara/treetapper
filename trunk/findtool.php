<?PHP
	$_GET['pagetitle']="TreeTapper: Find tool";
	include('templates/template_pagestart.php');
	echo "<p>Select options here. As you do, the methods table below will update. Select a row in that table to fill the other table with software containing the chosen method</p>";
	?>

<script src="templates/js_updateoptionbox.js"></script>

<script type="text/javascript">
updateAll = function() {
	myProgramsSource.sendRequest(
								'criterion=' + document.getElementById('criterion').value 
								+ '&generalquestion=' + document.getElementById('generalquestion').value
								+ '&posedquestion=' + document.getElementById('posedquestion').value
								+ '&branchlengthtype=' + document.getElementById('branchlengthtype').value
								+ '&treetype=' + document.getElementById('treetype').value
								+ '&character1=' + document.getElementById('character1').value
								+ '&character2=' + document.getElementById('character2').value
								+ '&character3=' + document.getElementById('character3').value
								+ '&dataformat=' + document.getElementById('dataformat').value
								+ '&treeformat=' + document.getElementById('treeformat').value
								+ '&opensourcecheckbox=' + document.getElementById('opensourcecheckbox').value
								+ '&freeprogramcheckbox=' + document.getElementById('freeprogramcheckbox').value
								 + '&applicationkind=' + document.getElementById('applicationkind').value
								 + '&platform=' + document.getElementById('platform').value
								 , myProgramsTable.onDataReturnInitializeTable, myProgramsTable);
	
	myMethodsSource.sendRequest(
								'criterion=' + document.getElementById('criterion').value 
								+ '&generalquestion=' + document.getElementById('generalquestion').value
								+ '&posedquestion=' + document.getElementById('posedquestion').value
								+ '&branchlengthtype=' + document.getElementById('branchlengthtype').value
								+ '&treetype=' + document.getElementById('treetype').value
								+ '&character1=' + document.getElementById('character1').value
								+ '&character2=' + document.getElementById('character2').value
								+ '&character3=' + document.getElementById('character3').value
								, myMethodsTable.onDataReturnInitializeTable, myMethodsTable);
}

YAHOO.util.Event.addListener(window, "load", function() {
	YAHOO.util.Cookie.set("findtool", "yes"); 
							 YAHOO.example.XHR_Text = new function() {
							 var Dom = YAHOO.util.Dom;
							 Event = YAHOO.util.Event;
							 myMethodsSource = null;
							 myMethodsTable = null;
							 mySoftwareSource = null;
							 mySoftwareTable = null;
							 
							 formatCheckButtonMethod = function(elCell, oRecord, oColumn, oData) {
								 var truefalse=oRecord.getData('Select');
								 if(truefalse.match("true")) {				
									 elCell.innerHTML = '<input type=checkbox id= "checkbutton_method_0_opt_' + oRecord.getData('ID') + '"  name= "checkbuttonmethod_0[]"  value= ' + oRecord.getData('ID') + '  checked />';
									 YAHOO.util.Cookie.setSub("findmethod2", "lastmethod", 5);
									 YAHOO.util.Cookie.setSub("findmethod", oRecord.getData('ID'), 1, { domain: "treetapper.org" }); 
									 alert('method '+oRecord.getData('ID')+' selected');
								 }
								 else {
									 elCell.innerHTML = '<input type=checkbox id= "checkbutton_method_0_opt_' + oRecord.getData('ID') + '"  name= "checkbuttonmethod_0[]"  value= ' + oRecord.getData('ID') + '   />';
									 YAHOO.util.Cookie.removeSub("findmethod",  oRecord.getData('ID'), { domain: "treetapper.org" }); 
								 }
							 };
							 
							 formatAddTooltipMethod=function(elCell, oRecord, oColumn, oData)
							 {
							 elCell.title= oRecord.getData('Description');
								 
							 elCell.innerHTML = '<a href="http://www.treetapper.org/method/'+oRecord.getData('ID')+'" target="_blank">'+oData+'</a>';
							 };
							 
							 myMethodsSource = new YAHOO.util.DataSource("templates/findtool_methodtable.php?");
							 //myMethodsSource = new YAHOO.util.DataSource("templates/selectiontable_js.php?");
							 
							 
							 myMethodsSource.responseType = YAHOO.util.DataSource.TYPE_TEXT;
							 var myMethodsSourceColumnDefs = [
							 {label:"Select", formatter:formatCheckButtonMethod},
							 {key:"Name", formatter:formatAddTooltipMethod},
							 {key:"ID", sortable:true, visible:false},
							 {key:"PDF",  sortable:true, sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_DESC}},
							 {key:"HTML", sortable:true, sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_DESC}} 
							 ];
							 myMethodsSource.responseSchema = {
							 recordDelim: "\n",
							 fieldDelim: "\t",
							 fields: ["ID","Name","Description",{key: "PDF", parser: YAHOO.util.DataSource.parseNumber},{key: "HTML", parser: YAHOO.util.DataSource.parseNumber},"Select"]
							 };
							 myMethodsTable = new YAHOO.widget.DataTable("methodtable",myMethodsSourceColumnDefs,myMethodsSource,{initialRequest: 'criterion=0'});
							 
							 formatAddTooltipProgram=function(elCell, oRecord, oColumn, oData)
							 {
							 elCell.title= oRecord.getData('Description');
								  elCell.innerHTML = '<a href="http://www.treetapper.org/program/'+oRecord.getData('ID')+'" target="_blank">'+oData+'</a>';
							 };
							 
							 myProgramsSource = new YAHOO.util.DataSource("templates/findtool_programtable.php?");
							 //myProgramsSource = new YAHOO.util.DataSource("templates/selectiontable_js.php?");
							 
							 
							 myProgramsSource.responseType = YAHOO.util.DataSource.TYPE_TEXT;
							 var myProgramsSourceColumnDefs = [
							 {key:"Name", formatter:formatAddTooltipProgram},
							 {key:"ID", sortable:true, visible:false},
							 {key:"PDF",  sortable:true, sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_DESC}},
							 {key:"HTML", sortable:true, sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_DESC}} 
							 ];
							 myProgramsSource.responseSchema = {
							 recordDelim: "\n",
							 fieldDelim: "\t",
							 fields: ["ID","Name","Description",{key: "PDF", parser: YAHOO.util.DataSource.parseNumber},{key: "HTML", parser: YAHOO.util.DataSource.parseNumber},"Select"]
							 };
							 myProgramsTable = new YAHOO.widget.DataTable("programtable",myProgramsSourceColumnDefs,myProgramsSource,{initialRequest: 'applicationkind=0'});
							 
							 };
							 });
</script>


<?PHP
	echo ("<div class=\"yui-g\">
		  <div class=\"yui-u first\">
		  ");
	include 'templates/optionbox_general_to_posedquestion.php';
	
	$_GET['table'] = 'criterion';
	//echo "<br>Criterion: <select name=\"criterion\" id=\"criterion\" onchange=\"myMethodsSource.sendRequest('criterion=' + document.getElementById('criterion').value + '&character1=' + document.getElementById('character1').value, myMethodsTable.onDataReturnInitializeTable, myMethodsTable)\">";
	echo "<br>Criterion: <select name=\"criterion\" id=\"criterion\" onchange=\"updateAll()\">";
	
	include 'templates/optionbox_generic.php';
	echo "</select> ";
	include 'templates/helppanel_generic.php';
	
	echo "<br>Character type(s):";
	$_GET['table'] = 'charactertype';
	include 'templates/helppanel_generic.php';
	echo "\n<ol>\n<li style=\"margin-left: 40px;\"> <select name=\"character1\" id=\"character1\" onchange=\"updateAll()\">";
	include 'templates/optionbox_generic.php';
	echo "</select>";
	echo " (first/single character)</li><li style=\"margin-left: 40px;\"> <select name=\"character2\" id=\"character2\" onchange=\"updateAll()\">";
	include 'templates/optionbox_generic.php';
	echo "</select>";
	echo " (second of a pair of characters)</li><li style=\"margin-left: 40px;\"> <select name=\"character3\" id=\"character3\" onchange=\"updateAll()\">";
	include 'templates/optionbox_generic.php';
	echo "</select> ";
	echo "</li></ol>";
	echo "Branch length type: <select name=\"branchlengthtype\" id=\"branchlengthtype\" onchange=\"updateAll()\">";
	$_GET['table'] = 'branchlengthtype';
	include 'templates/optionbox_generic.php';
	echo "</select>";
	include 'templates/helppanel_generic.php';
	
	echo "<br>Tree type: <select name=\"treetype\" id=\"treetype\" onchange=\"updateAll()\">";
	$_GET['table'] = 'treetype';
	include 'templates/optionbox_generic.php';
	echo "</select>";
	include 'templates/helppanel_generic.php';
	
	echo "</div>\n<div class=\"yui-u\">";
	
	$_GET['table'] = 'applicationkind';
	echo "<br>Application kind: <select name=\"applicationkind\" id=\"applicationkind\" onchange=\"updateAll()\">";
	include 'templates/optionbox_generic.php';
	echo "</select> ";
	include 'templates/helppanel_generic.php';
	
	$_GET['table'] = 'platform';
	echo "<br>Platform: <select name=\"platform\" id=\"platform\" onchange=\"updateAll()\">";
	include 'templates/optionbox_generic.php';
	echo "</select> ";
	include 'templates/helppanel_generic.php';
	
	echo "<br><a  href=\"http://en.wikipedia.org/wiki/Open-source_software\" target=\"_blank\">Open source</a> programs only: <input type=\"checkbox\" name=\"opensourcecheckbox\" id=\"opensourcecheckbox\" onchange=\"updateAll()\">";
	
	echo "<br>Free programs only: <input type=\"checkbox\" name=\"freeprogramcheckbox\" id=\"freeprogramcheckbox\" onchange=\"updateAll()\">";
	
	
	$_GET['table'] = 'dataformat';
	echo "<br>Data file format: <select name=\"dataformat\" id=\"dataformat\" onchange=\"updateAll()\">";
	include 'templates/optionbox_generic.php';
	echo "</select> ";
	include 'templates/helppanel_generic.php';
	
	$_GET['table'] = 'treeformat';
	echo "<br>Tree file format: <select name=\"treeformat\" id=\"treeformat\" onchange=\"updateAll()\">";
	include 'templates/optionbox_generic.php';
	echo "</select> ";
	include 'templates/helppanel_generic.php';
	
	echo "</div></div>";
	echo "<br><hr><br>";
	/*echo "<br>Specific question: <select name=\"posedquestion\">";
	 $_GET['table'] = 'posedquestion';
	 include 'optionbox_generic.php';
	 echo "</select>";
	 include 'helppanel_generic.php';
	 */
	?>
<div class="yui-g">
<div class="yui-u first">
<p>Methods
<br>
<div id='methodtable' name='methodtable'></div>
</p>
</div>
<div class="yui-u">
<p>Software
<br>
<div id='programtable' name='programtable'></div>

</p>
</div>
</div>





<?PHP
	include('templates/template_pageend.php');
	?>
