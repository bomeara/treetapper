<?php
require ('templates/checkauth.php');
$_GET['pagetitle']="TreeTapper: Add reference";
include('templates/template_pagestart.php');
echo "<h3>Add reference</h3><a href=\"uploadris.php\">Upload RIS file</a> or add info  below:<br><br><form action='$_SERVER[PHP_SELF]' method='post'>
<script type=\"text/javascript\">
function checkDOI()
{
	var x=document.getElementById(\"doi\").value;
	var entryPoint = 'getreferencefromDB.php';
	var queryString = encodeURI('?inputformat=doi&inputtext=' + x);
	var sUrl = entryPoint + queryString;
	var request = YAHOO.util.Connect.asyncRequest('GET', sUrl, { success:successHandler, failure:failureHandler });
	//alert(\"now in checkDOI\");
}

function checkURL()
{
	var x=document.getElementById(\"url\").value;
	var entryPoint = 'getreferencefromDB.php';
	var queryString = encodeURI('?inputformat=url&inputtext=' + x);
	var sUrl = entryPoint + queryString;
	var request = YAHOO.util.Connect.asyncRequest('GET', sUrl, { success:successHandler, failure:failureHandler });
}

function checkPMID()
{
	var x=document.getElementById(\"pmid\").value;
	var entryPoint = 'getreferencefromDB.php';
	var queryString = encodeURI('?inputformat=pmid&inputtext=' + x);
	var sUrl = entryPoint + queryString;
	var request = YAHOO.util.Connect.asyncRequest('GET', sUrl, { success:successHandler, failure:failureHandler });
}


function successHandler(o){
	 if(o.responseText !== undefined){
		 var root = o.responseXML.documentElement;
		 var onumresults = root.getElementsByTagName('numresults')[0].firstChild.nodeValue;
		 if (onumresults>0) {
			 doi.value = root.getElementsByTagName('doi')[0].firstChild.nodeValue;
			 url.value = root.getElementsByTagName('url')[0].firstChild.nodeValue;
			 pmid.value = root.getElementsByTagName('pmid')[0].firstChild.nodeValue;
			 title.value = root.getElementsByTagName('title')[0].firstChild.nodeValue;
			 pubname.value = root.getElementsByTagName('pubname')[0].firstChild.nodeValue;
			 year.value = root.getElementsByTagName('pubdate')[0].firstChild.nodeValue;
			 vol.value = root.getElementsByTagName('volume')[0].firstChild.nodeValue;
			 issue.value = root.getElementsByTagName('issue')[0].firstChild.nodeValue;
			 pagestart.value = root.getElementsByTagName('startpage')[0].firstChild.nodeValue;
			 pageend.value = root.getElementsByTagName('endpage')[0].firstChild.nodeValue;
		 }
		 //alert(\"got \" + onumresults + \" results\");
		 //alert(\"Response = \" + o.responseText);
		 }
}


function checkConnotea(o){
	
	}

function failureHandler(o){
	//alert(\"The failure handler was called.  tId: \" + o.tId + \".\", \"info\", \"example\");
	}

function parseFeedback(results)
{
		//var numreturns=-173;
	var numreturns=results.getData('numresults');
	alert(\"now parsing feedback with \" + numreturns + \" responses\");
	


}
</script>
DOI (if known): <input type='text' name='doi' id='doi' onBlur=\"checkDOI()\"><br>
PubMed ID (if known):  <input type='text' name='pmid' id='pmid' onBlur=\"checkPMID()\"><br>
URL (if known): <input type='text' name='url' id='url' onBlur=\"checkURL()\"><br>
Article title: <input type='text' name='title' id='title'><br>
Journal title: <input type='text' name='pubname' id='pubname'><br>
Year: <input type='text' name='year' id='year'><br>
Vol: <input type='text' name='vol' id='vol'><br>
Issue: <input type='text' name='issue' id='issue'><br>
Year: <input type='text' name='year' id='year'><br>
First page: <input type='text' name='pagestart' id='pagestart'><br>
Last page: <input type='text' name='pageend' id='pageend'><br>
<br>
<input type='submit' value='Submit'>
</form>";
include('templates/template_pageend.php');
?>
