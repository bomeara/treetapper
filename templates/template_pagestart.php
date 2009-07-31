<?PHP
session_start();
//below variables moved to dblogin.php
//$treetapperbaseurl="http://treetapper.nescent.org";
//$pathtobibutils="/home2/www/treetapper.nescent.org/site/bin/bibutils";
$currentpagetitle="TreeTapper";
if (strlen($_GET['pagetitle']) > 0) {
	$currentpagetitle=$_GET['pagetitle'];
}
echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01//EN\" \"http://www.w3.org/TR/html4/strict.dtd\">
<html bgcolor=\"gray\" color=\"gray\">
<head>
";
include_once ('yuiheadersPart1.php'); //Has script sources
include_once ('yuiheadersPart2.php'); //Has style info, other script info

?>

<script type="text/javascript">
//This script is for dynamic resizing of iframe with dbgraphnav bar
/***********************************************
* IFrame SSI script II- © Dynamic Drive DHTML code library (http://www.dynamicdrive.com)
															* Visit DynamicDrive.com for hundreds of original DHTML scripts
															* This notice must stay intact for legal use
															***********************************************/

//Input the IDs of the IFRAMES you wish to dynamically resize to match its content height:
//Separate each ID with a comma. Examples: ["myframe1", "myframe2"] or ["myframe"] or [] for none:
var iframeids=["dbgraphnav"]

//Should script hide iframe from browsers that don't support this script (non IE5+/NS6+ browsers. Recommended):
var iframehide="yes"

var getFFVersion=navigator.userAgent.substring(navigator.userAgent.indexOf("Firefox")).split("/")[1]
var FFextraHeight=parseFloat(getFFVersion)>=0.1? 16 : 0 //extra height in px to add to iframe in FireFox 1.0+ browsers

function resizeCaller() {
	var dyniframe=new Array()
	for (i=0; i<iframeids.length; i++){
		if (document.getElementById)
			resizeIframe(iframeids[i])
//reveal iframe for lower end browsers? (see var above):
				if ((document.all || document.getElementById) && iframehide=="no"){
					var tempobj=document.all? document.all[iframeids[i]] : document.getElementById(iframeids[i])
					tempobj.style.display="block"
				}
	}
}

function resizeIframe(frameid){
	var currentfr=document.getElementById(frameid)
	if (currentfr && !window.opera){
		currentfr.style.display="block"
		if (currentfr.contentDocument && currentfr.contentDocument.body.offsetHeight) //ns6 syntax
			currentfr.height = currentfr.contentDocument.body.offsetHeight+FFextraHeight;
		else if (currentfr.Document && currentfr.Document.body.scrollHeight) //ie5+ syntax
			currentfr.height = currentfr.Document.body.scrollHeight;
		if (currentfr.addEventListener)
			currentfr.addEventListener("load", readjustIframe, false)
				else if (currentfr.attachEvent){
					currentfr.detachEvent("onload", readjustIframe) // Bug fix line
					currentfr.attachEvent("onload", readjustIframe)
				}
	}
}

function readjustIframe(loadevt) {
	var crossevt=(window.event)? event : loadevt
	var iframeroot=(crossevt.currentTarget)? crossevt.currentTarget : crossevt.srcElement
	if (iframeroot)
		resizeIframe(iframeroot.id);
}

function loadintoIframe(iframeid, url){
	if (document.getElementById)
		document.getElementById(iframeid).src=url
}

if (window.addEventListener)
window.addEventListener("load", resizeCaller, false)
else if (window.attachEvent)
window.attachEvent("onload", resizeCaller)
else
window.onload=resizeCaller

</script>
<?PHP
//echo "\n<script type=\"text/javascript\" src=\"http://static.robotreplay.com/nitobi.replay.js\"></script>\n";

echo "<title>$currentpagetitle</title>";
echo "</head>
<STYLE type=\"text/css\">
html, body {
height: 100%;
}
BODY {text-align: left}
A:link {text-decoration: none; color: navy;}
A:visited {text-decoration: none; color: silver;}
A:active {text-decoration: none; color: silver;}
A:hover {text-decoration: none; color: red;}
body {
margin:0;
padding:0;
color: rgb(1,1,1);
	background-color: gray;
	background:gray;
	//background-image: url(\"http://www.brianomeara.info/tile_whitecorners_gray-light.png\");
	font-family:Optima, Helvetica, Verdana, Arial, sans-serif;
}
.yui-content { 
background-color: white;
	padding:1.0em 1.0em; /* content padding */ 
} 

</STYLE>\n

<style>
#helpcontainer {height:15em;}
</style>

<body class=\"yui-skin-sam\" bgcolor=\"gray\"><div id=\"doc3\"><div id=\"pagecontent\" name=\"pagecontent\">
";
include_once ('headerbig.php'); //Has TreeTapper.org heading, including image

include_once ('template_menubar.php');
echo "<div class=\"yui-content\">";
$currentpage=$_SERVER['PHP_SELF'];
$currenturi=$_SERVER['REQUEST_URI'];
?>
