<?PHP
require_once('templates/dblogin.php');
echo '<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
';
echo "\n<url>\n<loc>".$treetapperbaseurl."</loc>\n<priority>1</priority>\n</url>";
echo "\n<url>\n<loc>".$treetapperbaseurl."/vocabulary</loc>\n<priority>0.7</priority>\n</url>";
echo "\n<url>\n<loc>".$treetapperbaseurl."/person</loc>\n<priority>0.6</priority>\n</url>";
echo "\n<url>\n<loc>".$treetapperbaseurl."/reference</loc>\n<priority>0.6</priority>\n</url>";
echo "\n<url>\n<loc>".$treetapperbaseurl."/method</loc>\n<priority>0.6</priority>\n</url>";
echo "\n<url>\n<loc>".$treetapperbaseurl."/software</loc>\n<priority>0.6</priority>\n</url>";
$personquery=pg_query($db,"SELECT person_id FROM person");
for ($lp =0; $lp < pg_numrows($personquery); $lp++) {
	echo "\n<url>\n<loc>".$treetapperbaseurl."/person/".pg_fetch_result($personquery,$lp,0)."</loc>\n</url>";
}
$referencequery=pg_query($db,"SELECT reference_id FROM reference");
for ($lr =0; $lr < pg_numrows($referencequery); $lr++) {
        echo "\n<url>\n<loc>".$treetapperbaseurl."/reference/".pg_fetch_result($referencequery,$lr,0)."</loc>\n</url>";
}
$methodquery=pg_query($db,"SELECT method_id FROM method");
for ($lm =0; $lm < pg_numrows($methodquery); $lm++) {
        echo "\n<url>\n<loc>".$treetapperbaseurl."/method/".pg_fetch_result($methodquery,$lm,0)."</loc>\n</url>";
}
$programquery=pg_query($db,"SELECT program_id FROM program");
for ($ls =0; $ls < pg_numrows($programquery); $ls++) {
        echo "\n<url>\n<loc>".$treetapperbaseurl."/program/".pg_fetch_result($programquery,$ls,0)."</loc>\n</url>";
}

echo "\n</urlset>";
?>
