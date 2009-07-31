<?php
// hello.php
// Requirements:
// allow_url_open must be set to true (for file_get_contents)
//

// The Yahoo! web services request
//$request = 'http://search.yahooapis.com/WebSearchService/V1/webSearch?appid=i92UbRLV34HgDOEL_OJ3rqEdKnTr4DD12hriXbN84O2nvT6edKkMTqm4jApEDpc-&similar_ok=1&query=felsenstein+1985+%22phylogenies+and+the+comparative+method%22&results=2&output=xml';
//$request= 'http://search.yahooapis.com/WebSearchService/V1/webSearch?appid=i92UbRLV34HgDOEL_OJ3rqEdKnTr4DD12hriXbN84O2nvT6edKkMTqm4jApEDpc-&similar_ok=1&query=o\'meara+2006+testing+for+different+rates+of+continuous+trait+evolution+using+likelihood&type=all&results=1';
$request= 'http://search.yahooapis.com/WebSearchService/V1/webSearch?appid=i92UbRLV34HgDOEL_OJ3rqEdKnTr4DD12hriXbN84O2nvT6edKkMTqm4jApEDpc-&similar_ok=1&query=o\'meara+2006+testing+for+diffrent+rates+of+continuous+trait+evolution+using+likelihood&type=all&format=pdf&results=1&output=xml';

// Make the request

if (isset ($res['totalResultsAvailable'])) {
	echo "res['totalResultsAvailable'] is set to ".$res['totalResultsAvailable']."<br>";
}
else {
	echo "res['totalResultsAvailable'] is NOT set to ".$res['totalResultsAvailable']."<br>";
	
}

$results = file_get_contents($request);

if ($results == false) {
    die("Web services request failed");
}
if ($results != false) {
	echo "results != false<br>";
}

// Output the XML
echo "$results\n";
echo htmlspecialchars($results, ENT_QUOTES);

$response = file_get_contents($request);

if ($response === false) {
	die('Request failed');
}
if ($response != false) {
	echo "response != false<br>";
}
else {
	echo "$response<br> is false <br>";
}

$phpobject = simplexml_load_string($response);
if ($phpobject === false) {
	die('Parsing failed');
}

if ($phpobject != false) {
	echo "phpobject != false<br>";
}
else {
	echo "PHP OBJECT: <br> $phpobject <br>";
}

// Output the data
// SimpleXML returns the data as a SimpleXML object

// Collect the attributes
foreach($phpobject->attributes() as $name=>$attr) {
	$res[$name]=$attr;
}
if (isset ($res['totalResultsAvailable'])) {
	echo "res['totalResultsAvailable'] is set to ".$res['totalResultsAvailable']."<br>";
}
else {
	echo "res['totalResultsAvailable'] is NOT set to ".$res['totalResultsAvailable']."<br>";
	
}
echo "\n\n<p>Matched ${res['totalResultsAvailable']}\n";


?>
