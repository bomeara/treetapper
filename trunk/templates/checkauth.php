<?PHP

// Start the login session
session_start();
$personid="";
$persontrust="";
$approved="";
$approvedtodelete=0;
$currentpage=$_SERVER['PHP_SELF'];
$_SESSION['targetpage']=$treetapperbaseurl.$currentpage;

if (!$_SESSION['user'] || !$_SESSION['pass']) {
	
// What to do if the user hasn't logged in
// We'll just redirect them to the login page.
	header('Location: http://www.treetapper.org/login.php');
	die();
	echo "<style>
#example {height:30em;}
label { display:block;float:left;width:45%;clear:left; }
.clear { clear:both; }
#resp { margin:10px;padding:5px;border:1px solid #ccc;background:#fff;}
#resp li { font-family:monospace }
</style>

<script>
YAHOO.namespace(\"example.pagecontent\");

function init() {
	
	// Define various event handlers for Dialog
	var handleSubmit = function() {
		this.submit();
	};
	var handleCancel = function() {
		this.cancel();
	};
	var handleSuccess = function(o) {
	//	var response = o.responseText;
	//	response = response.split(\"<!\")[0];
	//	document.getElementById(\"resp\").innerHTML = response;
		document.location.reload();
	};
	var handleFailure = function(o) {
		alert(\"Submission failed: \" + o.status);
	};
	
	// Instantiate the Dialog
	YAHOO.example.pagecontent.dialog1 = new YAHOO.widget.Dialog(\"dialog1\", 
															  { width : \"325px\",
fixedcenter : true,
visible : true, 
modal : true, 
close : false,
constraintoviewport : true,
buttons : [ { text:\"Submit\", handler:handleSubmit, isDefault:true },
	{ text:\"Cancel\", handler:handleCancel } ]
															  } );
	YAHOO.example.pagecontent.dialog1.setHeader(\"Please enter your login information\");
	YAHOO.example.pagecontent.dialog1.setBody(\"<form method='POST' action='login.php'><label for='user'>E-mail:</label><input type='textbox' name='user' /><label for='pass'>Password:</label><input type='password' name='pass' /></form>\");
	
	// Validate the entries in the form to require that both first and last name are entered
	YAHOO.example.pagecontent.dialog1.validate = function() {
		var data = this.getData();
		if (data.user == \"\" || data.pass == \"\") {
			alert(\"Please enter your email and password.\");
			return false;
		} else {
			return true;
		}
	};
	
	// Wire up the success and failure handlers
	YAHOO.example.pagecontent.dialog1.callback = { success: handleSuccess,
failure: handleFailure };
	
	// Render the Dialog
	YAHOO.example.pagecontent.dialog1.render(\"pagecontent\");
	
	
}

YAHOO.util.Event.onDOMReady(init);
</script>


";
/*
<div id=\"dialog1\">
<div class=\"hd\">Please enter your login information</div>
<div class=\"bd\">
<form method=\"POST\" action=\"login.php\">
<label for=\"user\">E-mail:</label><input type=\"textbox\" name=\"user\" /> 
<label for=\"pass\">Password:</label><input type=\"password\" name=\"pass\" /> 

<div class=\"clear\"></div>

</form>
</div>
</div>

";
*/
die();
} else {
	
// If the session variables exist, check to see
// if the user has access.
	
	include ('dblogin.php'); //connection in $db
	$result = pg_query($db,"SELECT personhiddeninfo_personid, personhiddeninfo_trustlevel FROM personhiddeninfo WHERE personhiddeninfo_password='$_SESSION[pass]' AND personhiddeninfo_email='$_SESSION[user]'") or die("Couldn't query the user-database.");
	if (pg_numrows($result)!=1) {
		header('Location: login.php');
		die();
	}
	else {
		$personid=pg_fetch_result($result,0,0);
		$persontrust=pg_fetch_result($result,0,1);
		$approved=0;
		if ($persontrust>=4) { //admin
			$approved=1;
			$approvedtodelete=1;
		}			
		else if ($persontrust>=3) { //trusted user
			$approved=1;
			$approvedtodelete=1;
		}
		else if ($persontrust>=2) { //can add but not delete
			$approved=1;
			$approvedtodelete=0;
		}
		
	}
}
//Otherwise, continue with whatever is on page which calls this script
?>
