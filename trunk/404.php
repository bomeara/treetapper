<?PHP
$_GET['pagetitle']="TreeTapper.org";
include('templates/template_pagestart.php');
include ('templates/dblogin.php'); //connection in $db
echo("<p><b>Sorry, the page you requested was not found.</b> You may want to try a list of <a href=\"".$treetapperbaseurl."/vocabulary\">controlled vocabularies</a>, a page of <a href=\"".$treetapperbaseurl."/person\">people</a>, <a href=\"".$treetapperbaseurl."/method\">methods</a>, <a href=\"".$treetapperbaseurl."/reference\">references</a>, or <a href=\"".$treetapperbaseurl."/program\">programs</a>.");
include ('templates/author_jump.php');
echo "<br />You can also see the <a href=\"".$treetapperbaseurl."/makesitemap.php\">XML sitemap</a>";
include('templates/template_pageend.php');
?>

