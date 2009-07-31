function updateoptionbox(boxid,table,foreignkey)
{
//	alert("Called update option box");
	var entryPoint = 'templates/optionbox_js.php';
	var tableuri = encodeURI(table);
	var queryString;
	foreignkey = typeof(foreignkey) != 'undefined' ? foreignkey : 0; //creates default argument
	if (foreignkey==0) {
		queryString = encodeURI('?table=' + tableuri);
	}
	if (foreignkey>0) {
		queryString = encodeURI('?table=' + tableuri + '&foreignkey=' + foreignkey);
	}
	var sUrl = entryPoint + queryString;
	var callback = {
success: function(oResponse) {
	//alert("Had success:\nBegin text ->" + oResponse.responseText + "<-End text");
	var x=document.getElementById(boxid,table);
	while (document.getElementById(boxid).length>0) {
		x.remove(0);
	}	
	var newoptions = new Array();
	newoptions = (oResponse.responseText).split("\n");
	for (i=0;i<newoptions.length;i++)
	{
		var currentoptionarray = new Array();
		currentoptionarray = (newoptions[i]).split("\t");
		//alert("option line = " + newoptions[i] + " of " + newoptions.length + "\n\"" + currentoptionarray[1] + "\"\n\"" + currentoptionarray[0] + "\"");
		if (currentoptionarray[0].length>0) {
			y=new Option(currentoptionarray[0], currentoptionarray[1]);
		//alert("option " + i + "text = " + currentoptionarray[1] + "value = " + currentoptionarray[0]);
			try
			{
				x.add(y,null); // standards compliant
			}
			catch(ex)
			{
				x.add(y); // IE only
			}
		}
	}
},
failure: function(oResponse) {
	//alert("Failed to process XHR transaction: " + oResponse.responseText);
}
	};
//alert("boxid = " + boxid + " tableid = " + table + "\nURL = \"" + sUrl + "\"");
var request = YAHOO.util.Connect.asyncRequest('GET', sUrl, callback);
//alert("Now have finished request");
}
