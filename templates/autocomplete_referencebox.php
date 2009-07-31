<?PHP
//include('template_pagestart.php');
include ('dblogin.php'); //connection in $db

?>

<style type="text/css">
/* custom styles for multiple stacked instances with custom formatting */
#example0 { z-index:9001; } /* z-index needed on top instances for ie & sf absolute inside relative issue */
#example1 { z-index:9000; } /* z-index needed on top instances for ie & sf absolute inside relative issue */
.autocomplete { padding-bottom:2em;width:40%; }/* set width of widget here*/
.autocomplete .yui-ac-highlight .sample-quantity,
.autocomplete .yui-ac-highlight .sample-result,
.autocomplete .yui-ac-highlight .sample-query { color:#FFF; }
.autocomplete .sample-quantity { float:right; } /* push right */
.autocomplete .sample-result { color:#A4A4A4; }
.autocomplete .sample-query { color:#000; }
</style>

<div id="autocomplete_examples">
<h2>Author search for references (enter last name first)</h2>
<div id="example0" class="autocomplete">
<input id="ysearchinput0" type="text">
<div id="ysearchcontainer0"></div>
</div>
</div>

<div class="yui-g"> 
	    <div class="yui-u first" id="referencewithauthorfilter"> 
	        <p>The table with references</p>
	    </div> 
	    <div class="yui-u"> 
	       
	    </div> 
	</div>



<script type="text/javascript">
YAHOO.example.ACFlatData = new function(){
    // Define a custom formatter function
    this.fnCustomFormatter = function(oResultItem, sQuery) {
//        var sKey = oResultItem[0];
		var sName = oResultItem[0];
//        var nQuantity = oResultItem[1];
		var nID = oResultItem[1];
//        var sKeyQuery = sKey.substr(0, sQuery.length);
//        var sKeyRemainder = sKey.substr(sQuery.length);
        var aMarkup = ["<div class='sample-result'><div class='sample-author'>",
            sName,
            "</div></div>"];
        return (aMarkup.join(""));
    };
	
    // Instantiate one XHR DataSource and define schema as an array:
    //     ["Record Delimiter",
    //     "Field Delimiter"]
    this.oACDS = new YAHOO.widget.DS_XHR("templates/list_authors.php", ["\n", "\t"]);
    this.oACDS.responseType = YAHOO.widget.DS_XHR.TYPE_FLAT;
    this.oACDS.maxCacheEntries = 60;
    this.oACDS.queryMatchSubset = true;
	
    // Instantiate first AutoComplete
    var myInput = document.getElementById('ysearchinput0');
    var myContainer = document.getElementById('ysearchcontainer0');
    this.oAutoComp0 = new YAHOO.widget.AutoComplete(myInput,myContainer,this.oACDS);
    this.oAutoComp0.queryDelay = 0.2;
	this.oAutoComp0.typeAhead = false; 
    this.oAutoComp0.formatResult = this.fnCustomFormatter;
	this.oAutoComp0.minQueryLength = 1;
	this.oAutoComp0.maxResultsDisplayed = 40; 
	
	//define your itemSelect handler function: 
	var itemSelectHandler = function(sType, aArgs) { 

//		YAHOO.log(sType); //this is a string representing the event; 
	                      //e.g., "itemSelectEvent" 
		var oMyAcInstance = aArgs[0]; // your AutoComplete instance 
		var elListItem = aArgs[1]; //the <li> element selected in the suggestion 
	                               //container 
		var aData = aArgs[2]; //array of the data for the item as returned by the DataSource 
		//Author id is aData[1]
		
			

			
			this.formatCheckButton = function(elCell, oRecord, oColumn, oData) {
				
				var truefalse=oRecord.getData('Select');
				if(truefalse.match("true")) {
					
					
					elCell.innerHTML = '<input type=checkbox id= "checkbutton_referencewithauthorfilter_0_opt_' + oRecord.getData('ID') + '"  name= "checkbuttonreferencewithauthorfilter_0[]"  value= ' + oRecord.getData('ID') + '  checked />';
					
				}
				else {
					
					elCell.innerHTML = '<input type=checkbox id= "checkbutton_referencewithauthorfilter_0_opt_' + oRecord.getData('ID') + '"  name= "checkbuttonreferencewithauthorfilter_0[]"  value= ' + oRecord.getData('ID') + '   />';
					
				}

			};

			this.formatWebHits=function(elCell, oRecord, oColumn, oData)
			{
				var hitcount=oData;
				if (hitcount != parseInt(hitcount)) {
					elCell.innerHTML='';
				}
				else {
					
					elCell.innerHTML= '<a href="http://search.yahoo.com/web/advanced?ei=UTF-8&p=' + oRecord.getData('WebQueryString') + '" target="_blank">'+ oData + '</a>';
				}
			};
			
			this.formatPDFHits=function(elCell, oRecord, oColumn, oData)
			{
				var hitcount=oData;
				if (hitcount != parseInt(hitcount)) {
					elCell.innerHTML='';
				}
				else {
					
					elCell.innerHTML= '<a href="http://search.yahoo.com/search?n=10&ei=UTF-8&va_vt=any&vo_vt=any&ve_vt=any&vp_vt=any&vd=all&vf=pdf&vm=p&p=' + oRecord.getData('WebQueryString') + '" target="_blank">'+ oData + '</a>';
				}
			};

			var myColumnDefs = [
				{label:"Select", formatter:this.formatCheckButton},
				{key:"Author", sortable:true},
				{key:"Year", formatter:YAHOO.widget.DataTable.formatNumber, sortable:true},
				{key:"Title", sortable:true},
				{key:"Journal", sortable:true},
				{key:"Volume", sortable:true},
				{key:"Issue", sortable:true},
				{key:"Pages", sortable:true},
				{key:"WebHits", sortable:true, formatter:this.formatWebHits},
				{key:"PDFHits", sortable:true, formatter:this.formatPDFHits},
				{key:"Link", sortable:true},
				{key:"ID", sortable:true},
				{key:"Accepted", sortable:true}
				];
			this.myDataSource = new YAHOO.util.DataSource('templates/selectiontable_js.php?');	
			this.myDataSource.responseType = YAHOO.util.DataSource.TYPE_TEXT;
			this.myDataSource.responseSchema = {
recordDelim: "\n",
fieldDelim: "\t",
fields: ["Select","Accepted","Author","Year","Title","Journal","Volume","Issue","Pages","Link","ID","WebHits","PDFHits","WebQueryString"]
			};
			this.myDataTable = new YAHOO.widget.DataTable("referencewithauthorfilter", myColumnDefs,
														  this.myDataSource, {initialRequest:'table=reference&includepending=0&authorid=' + aData[1],  scrollable:false});
			
		
		
		
	}; 
	
	//subscribe your handler to the event, assuming 
	//you have an AutoComplete instance myAC: 
	this.oAutoComp0.itemSelectEvent.subscribe(itemSelectHandler); 
};


</script>	

<script type="text/javascript">
/*(function() {
    var Dom = YAHOO.util.Dom,
    Event = YAHOO.util.Event,
    myDataSource = null,
    myDataTable = null;
	
	var getTerms = function(query) {
		alert('new query');
		alert('request is for ' + Dom.get('dt_input').value);
       // myDataSource.sendRequest('&authorid=' + Dom.get('dt_input').value,myDataTable.onDataReturnInitializeTable, myDataTable);
    };
	
	
    Event.onDOMReady(function() {
		var oACDS = new YAHOO.widget.DS_JSFunction(getTerms);
        oACDS.queryMatchContains = true;
        var oAutoComp = new YAHOO.widget.AutoComplete("dt_input","dt_ac_container", oACDS);
		
        
		formatCheckButton = function(elCell, oRecord, oColumn, oData) {
			var truefalse=oRecord.getData('Select');
			if(truefalse.match("true")) {
				elCell.innerHTML = '<input type=checkbox id= "checkbutton_".$tablename."_".$selectiontableid."_opt_' + oRecord.getData('ID') + '"  name= "checkbutton".$tablename."_".$selectiontableid."[]"  value= ' + oRecord.getData('ID') + '  checked />';
			}
			else {
				elCell.innerHTML = '<input type=checkbox id= "checkbutton_".$tablename."_".$selectiontableid."_opt_' + oRecord.getData('ID') + '"  name= "checkbutton".$tablename."_".$selectiontableid."[]"  value= ' + oRecord.getData('ID') + '   />'; 
			}
		};
		
		var myColumnDefs = [
			{label:"Select", formatter:formatCheckButton},
			{key:"Name", formatter:formatAddTooltip},
			{key:"ID", formatter:getMaxID, sortable:true},
			{key:"Accepted", sortable:true}
			];
		
		
		
 		myDataSource = new YAHOO.util.DataSource("templates/selectiontable_js.php?table=reference&includepending=1");
		myDataSource.responseType = YAHOO.util.DataSource.TYPE_TEXT;
		myDataSource.connXhrMode = "queueRequests";
		myDataSource.responseSchema = {
recordDelim: "\\n",
fieldDelim: "\\t",
fields: ["Select","Accepted","Author","Year","Title","Journal","Volume","Issue","Pages","Link","ID"]
		};
		
		
		myDataTable = new YAHOO.widget.DataTable("references", myColumnDefs,
												 myDataSource, {initialRequest: '&authorid=' + Dom.get('dt_input').value});
		
    });
})();*/
</script>

<?PHP
//	include('template_pageend.php');
?>
