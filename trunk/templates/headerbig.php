<?PHP
require_once('dblogin.php');
echo "<table style=\"text-align: left; width: 100%; background-color: white;\" border=\"0\" cellpadding=\"5\"
cellspacing=\"5\">
<tbody>
<tr>
<td style=\"vertical-align: top; width: 260px;\"><a href=\"".$treetapperbaseurl."\"><img style=\"align: left\" alt=\"header\" src=\"".$treetapperbaseurl."/images/headertrees.jpg\"></a></td>";
?>
<td style="vertical-align: top;">
<span style="font-style: italic; font-size:4em">TreeTapper</span><br />
<span style="font-size:1.5em">Tools to better understand biology by tapping information in phylogenies</span>
</td>
<td style="vertical-align: top; align: right">
<?PHP 
#$svnversion = system('svnversion', $retval);
#echo "TreeTapper version $svnversion\n<br>";
if ($_SESSION['user'] && $_SESSION['pass']) {
	$result = pg_query($db,"SELECT person_id, person_first, person_last FROM personhiddeninfo, person WHERE personhiddeninfo_password='$_SESSION[pass]' AND personhiddeninfo_email='$_SESSION[user]' AND personhiddeninfo_personid=person_id") or die("Couldn't query the user-database.");
	if (pg_numrows($result)==1) {
		echo "Logged in as <br /><a href=\"".$treetapperbaseurl."/person/".pg_fetch_result($result,0,0)."\">".pg_fetch_result($result,0,1)." ".pg_fetch_result($result,0,2)."</a>";
	}
	else {
		echo "Login failed (returned ".pg_numrows($result)." people in the database with login ".$_SESSION[user].")";
	}
	
}
else if ($_SESSION['user']) {
	echo "User name ".$_SESSION['user']." entered but missing password";
}
else {
	echo "Not logged in";
}
?>
</td>
</tr>
</tbody>
</table>
