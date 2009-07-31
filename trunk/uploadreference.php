<?PHP
$_GET['pagetitle']="TreeTapper: Upload references";
include('templates/template_pagestart.php');
require ('templates/checkauth.php');

echo "<form action=\"parseuploadedreference.php\" method=\"post\"
enctype=\"multipart/form-data\">
<label for=\"file\">Filename:</label>
<input type=\"file\" name=\"file\" id=\"file\" /> 
<br />
Format:<br />
<input type=\"radio\" name=\"format\" value=\"ris\" checked> RIS<br />
<input type=\"radio\" name=\"format\" value=\"bib\"> bibtex<br />
<input type=\"radio\" name=\"format\" value=\"end\"> EndNote (Refer format)<br />
<input type=\"radio\" name=\"format\" value=\"endx\"> EndNote (XML format)<br />
<input type=\"radio\" name=\"format\" value=\"isi\"> ISI Web of Science<br />
<input type=\"radio\" name=\"format\" value=\"med\"> Pubmed XML<br />
<input type=\"radio\" name=\"format\" value=\"copac\"> COPAC<br />


<input type=\"submit\" name=\"submit\" value=\"Submit\" />
</form>";

include('templates/template_pageend.php');
?>
