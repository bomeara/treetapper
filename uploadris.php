<?PHP
$_GET['pagetitle']="TreeTapper: Upload RIS file";
include('templates/template_pagestart.php');
require ('templates/checkauth.php');

echo "<form action=\"parseuploadedris.php\" method=\"post\"
enctype=\"multipart/form-data\">
<label for=\"file\">Filename:</label>
<input type=\"file\" name=\"file\" id=\"file\" /> 
<br />
<input type=\"submit\" name=\"submit\" value=\"Submit\" />
</form>";

include('templates/template_pageend.php');
?>
