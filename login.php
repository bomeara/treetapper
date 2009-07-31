<?PHP

include ('templates/dblogin.php'); //connection in $db

// Add slashes to the username, and make a md5 checksum of the password.
$_POST['user'] = addslashes($_POST['user']);
$_POST['pass'] = md5($_POST['pass']);

$result = pg_query($db,"SELECT count(personhiddeninfo_id) FROM personhiddeninfo WHERE personhiddeninfo_password='$_POST[pass]' AND personhiddeninfo_email='$_POST[user]'") or die("Couldn't query the user-database.");
$num = pg_num_rows($result);

if ($num!=1) {
	
// When the query didn't return anything,
// display the login form.
	
	echo "<h3>User Login</h3>
<form action='$_SERVER[PHP_SELF]' method='post'>
Email: <input type='text' name='user'><br>
Password: <input type='password' name='pass'><br><br>
<input type='submit' value='Login'>
</form>";
	$userpost=$_POST['user'];
	$userpass=$_POST['pass'];
		echo "the user name is ".$userpost." and password is ".$userpass." and query was SELECT count(personhiddeninfo_id) FROM personhiddeninfo WHERE personhiddeninfo_password='$_POST[pass]' AND personhiddeninfo_email='$_POST[user]' and num = $num and result = $result";
		$userlistquery = pg_query($db,"SELECT personhiddeninfo_email FROM personhiddeninfo") or die("Couldn't query the user-database.");
		$userlist=pg_fetch_result($userlistquery,0,0);
		echo " user list is ".$userlist;

		
		
} else {
	
// Start the login session
	session_start();
	
// We've already added slashes and MD5'd the password
	$_SESSION['user'] = $_POST['user'];
	$_SESSION['pass'] = $_POST['pass'];
	
// All output text below this line will be displayed
// to the users that are authenticated. Since no text
// has been output yet, you could also use redirect
// the user to the next page using the header() function.
// header('Location: page2.php');
	if ($_SESSION['targetpage']) {
		header('Location: '.$treetapperbaseurl.$_SESSION['targetpage']);
	}
	else {
		header('Location: index.php');
	}
//echo "<h1>Congratulations</h1>";
//echo "You're now logged in. Try visiting <a href='page2.php'>Page 2</a>.";
	
}

?> 
