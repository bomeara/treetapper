<?PHP
$_GET['pagetitle']="TreeTapper: Upload references";
include('templates/template_pagestart.php');
require ('templates/checkauth.php');

echo "<form action=\"parseuploadedreferenceXML.php\" method=\"post\"
enctype=\"multipart/form-data\">
<label for=\"file\">Filename:</label>
<input type=\"file\" name=\"file\" id=\"file\" /> 
<br />
Format:<br />
<input type=\"radio\" name=\"format\" value=\"1\" checked> RIS<br />
<input type=\"radio\" name=\"format\" value=\"2\"> bibtex<br />
<input type=\"radio\" name=\"format\" value=\"3\"> EndNote (Refer format)<br />
<input type=\"radio\" name=\"format\" value=\"4\"> EndNote (XML format)<br />
<input type=\"radio\" name=\"format\" value=\"5\"> ISI Web of Science<br />
<input type=\"radio\" name=\"format\" value=\"6\"> Pubmed XML<br />
<input type=\"radio\" name=\"format\" value=\"7\"> COPAC<br />
<input type=\"radio\" name=\"format\" value=\"8\"> MODS XML<br />
<input type=\"submit\" name=\"submit\" value=\"Submit\" />
</form>";

include('templates/template_pageend.php');
?>
