<?PHP
include ('templates/dblogin.php');
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
<?PHP
echo "<div id=\"autocomplete_examples\">
<br>Jump to author page (enter last name first, then hit return):<br>
<div id=\"example0\" class=\"autocomplete\">
<input id=\"ysearchinput0\" type=\"text\">
<div id=\"ysearchcontainer0\"></div>
</div>
</div>

<div id=\"authorjump\"></div>


<script type=\"text/javascript\">
YAHOO.example.ACFlatData = new function(){
    // Define a custom formatter function
    this.fnCustomFormatter = function(oResultItem, sQuery) {
//        var sKey = oResultItem[0];
		var sName = oResultItem[0];
//        var nQuantity = oResultItem[1];
		var nID = oResultItem[1];
//        var sKeyQuery = sKey.substr(0, sQuery.length);
//        var sKeyRemainder = sKey.substr(sQuery.length);
        var aMarkup = [\"<div class='sample-result'><div class='sample-author'>\",
            sName,
            \"</div></div>\"];
        return (aMarkup.join(\"\"));
    };
	
    // Instantiate one XHR DataSource and define schema as an array:
    //     [\"Record Delimiter\",
    //     \"Field Delimiter\"]
    this.oACDS = new YAHOO.widget.DS_XHR(\"templates/list_authors.php\", [\"\\n\", \"\\t\"]);
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
		var aData = aArgs[2]; //array of the data for the item as returned by the DataSource 
		//Author id is aData[1]
		<!--
			window.location = '".$treetapperbaseurl."/person/' + aData[1];
//-->
	}; 
	
	//subscribe your handler to the event, assuming 
	//you have an AutoComplete instance myAC: 
	this.oAutoComp0.itemSelectEvent.subscribe(itemSelectHandler); 
};


</script>	
";
?>
