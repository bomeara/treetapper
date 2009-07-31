

<style>
#example {height:30em;}
label { display:block;float:left;width:45%;clear:left; }
.clear { clear:both; }
#resp { margin:10px;padding:5px;border:1px solid #ccc;background:#fff;}
#resp li { font-family:monospace }
</style>

<script type="text/javascript">
YAHOO.namespace("example.pagecontent");

function init() {
	loadingpanelinit();
	checkauthinit();
	uploadrisinit();
	simpletableinit();
}

function checkauthinit() {
	
	// Define various event handlers for Dialog
	var handleSubmit = function() {
		this.submit();
	};
	var handleCancel = function() {
		this.cancel();
	};
	var handleSuccess = function(o) {
	//	var response = o.responseText;
	//	response = response.split("<!")[0];
	//	document.getElementById("resp").innerHTML = response;
		document.location.reload();
	};
	var handleFailure = function(o) {
		alert("Submission failed: " + o.status);
	};
	
	// Instantiate the Dialog
	YAHOO.example.pagecontent.checkauthdialog = new YAHOO.widget.Dialog("checkauthdialog", 
																	  { width : "325px",
fixedcenter : true,
visible : false, 
modal : true, 
close : false,
constraintoviewport : true,
buttons : [ { text:"Submit", handler:handleSubmit, isDefault:true },
	{ text:"Cancel", handler:handleCancel } ]
																	  } );
	
	YAHOO.example.pagecontent.checkauthdialog.setHeader("Please enter your user information");
	YAHOO.example.pagecontent.checkauthdialog.setBody("<form method='POST' action='login.php'><label for='user'>E-mail:</label><input type='textbox' name='user' /><label for='pass'>Password:</label><input type='password' name='pass' /></form>");
	
	// Validate the entries in the form to require that both first and last name are entered
	YAHOO.example.pagecontent.checkauthdialog.validate = function() {
		var data = this.getData();
		if (data.user == "" || data.pass == "") {
			alert("Please enter your email and password.");
			return false;
		} else {
			return true;
		}
	};
	
	// Wire up the success and failure handlers
	YAHOO.example.pagecontent.checkauthdialog.callback = { success: handleSuccess,
failure: handleFailure };
	
	// Render the Dialog
	YAHOO.example.pagecontent.checkauthdialog.render("pagecontent");
}


function activateuploadris() {
	YAHOO.example.pagecontent.uploadrisdialog.show();
}

function activatecheckauth() {
	YAHOO.example.pagecontent.checkauthdialog.show();
}




function uploadrisinit() {
	
	// Define various event handlers for Dialog
	var handleSubmit = function() {
		this.submit();
	};
	var handleCancel = function() {
		this.cancel();
	};
	var handleSuccess = function(o) {
	//	var response = o.responseText;
	//	response = response.split("<!")[0];
	//	document.getElementById("resp").innerHTML = response;
		document.location.reload();
	};
	var handleFailure = function(o) {
		alert("Submission failed: " + o.status);
	};
	
	// Instantiate the Dialog
	YAHOO.example.pagecontent.uploadrisdialog = new YAHOO.widget.Dialog("uploadrisdialog", 
																	  { width : "325px",
fixedcenter : true,
visible : false, 
modal : true, 
close : false,
constraintoviewport : true,
buttons : [ { text:"Submit", handler:handleSubmit, isDefault:true },
	{ text:"Cancel", handler:handleCancel } ]
																	  } );
	
	// Validate the entries in the form to require that both first and last name are entered
	YAHOO.example.pagecontent.uploadrisdialog.validate = function() {
		/*	var data = this.getData();
		if (data.user == "" || data.pass == "") {
			alert("Please enter your email and password.");
			return false;
		} else {*/
		return true;
	//	}
	};
	
	// Wire up the success and failure handlers
	YAHOO.example.pagecontent.uploadrisdialog.callback = { success: handleSuccess,
failure: handleFailure };
	
	// Render the Dialog
	YAHOO.example.pagecontent.uploadrisdialog.render("pagecontent");
}

function loadingpanelinit() {
	//Straight from YUI official examples
	if (!YAHOO.example.pagecontent.wait) {
		
            // Initialize the temporary Panel to display while waiting for external content to load
		
		YAHOO.example.pagecontent.wait = 
		new YAHOO.widget.Panel("wait",  
							   { width: "240px", 
fixedcenter: true, 
close: false, 
draggable: false, 
zindex:4,
modal: true,
visible: false
							   } 
							   );
		
		YAHOO.example.pagecontent.wait.setHeader("Loading, please wait...");
		YAHOO.example.pagecontent.wait.setBody("<img src=\"templates/ajax-loader.gif\"/>");
		YAHOO.example.pagecontent.wait.render(document.body);
		
	}	
}

function simpletableinit () {
		// Define various event handlers for Dialog
	var handleSubmit = function() {
		this.submit();
	};
	var handleCancel = function() {
		this.cancel();
	};
	var handleSuccess = function(o) {
	//	var response = o.responseText;
	//	response = response.split("<!")[0];
	//	document.getElementById("resp").innerHTML = response;
		document.location.reload();
	};
	var handleFailure = function(o) {
		alert("Submission failed: " + o.status);
	};
	
	// Instantiate the Dialog
	YAHOO.example.pagecontent.simpletabledialog = new YAHOO.widget.Dialog("simpletabledialog", 
																		{ width : "325px",
fixedcenter : true,
visible : false, 
modal : true, 
close : false,
iframe : true,
constraintoviewport : true,
buttons : [ { text:"Submit", handler:handleSubmit, isDefault:true },
	{ text:"Cancel", handler:handleCancel } ]
																		} );
	
	// Validate the entries in the form to require that both first and last name are entered
	YAHOO.example.pagecontent.simpletabledialog.validate = function() {
		var data = this.getData();
		if (data.name == "" || data.description == "") {
			alert("Please enter both a name and description.");
			return false;
		} else {
			return true;
		}
	};
	
	// Wire up the success and failure handlers
	YAHOO.example.pagecontent.simpletabledialog.callback = { success: handleSuccess,
failure: handleFailure };
	
	// Render the Dialog
	YAHOO.example.pagecontent.simpletabledialog.render("pagecontent");
}

function hideloading() {
	YAHOO.example.pagecontent.wait.hide();
}
YAHOO.util.Event.onDOMReady(init);
</script>

<script>
YAHOO.namespace("example.pagecontent");

function activatesimpletable(newheader) {
	
	// Define various event handlers for Dialog
	var handleSubmit = function() {
		this.submit();
	};
	var handleCancel = function() {
		this.cancel();
	};
	var handleSuccess = function(o) {
		var response = o.responseText;
	//	response = response.split("<!")[0];
		document.getElementById("resp").innerHTML = response;
		//document.location.reload();
	};
	var handleFailure = function(o) {
		alert("Submission failed: " + o.status);
	};
	
	// Instantiate the Dialog
	YAHOO.example.pagecontent.simpletable = new YAHOO.widget.Dialog("simpletable", 
																	{ width : "750px",
fixedcenter : true,
iframe : true,
visible : true, 
modal : true, 
close : false,
constraintoviewport : true,
buttons : [ { text:"Submit", handler:handleSubmit, isDefault:true },
	{ text:"Cancel", handler:handleCancel } ]
																	} );
	YAHOO.example.pagecontent.simpletable.setHeader(newheader);
	YAHOO.example.pagecontent.simpletable.setBody("<form method='POST' action='templates/add_generic.php'><label for='genericname'>Name:</label><input type='textbox' size='100' name='genericname' /><label for='genericdescription'>Description:</label><input type='textbox'  size='100' name='genericdescription' /></form>");
	
	// Validate the entries in the form to require that both first and last name are entered
	YAHOO.example.pagecontent.simpletable.validate = function() {
		var data = this.getData();
		if (data.genericname.length<5) {
			alert("Please enter a longer name");
			return false;
		}
		else if (data.genericdescription.length<10) {
			alert("Please enter a longer description");
			return false;
			
		}
		else {
			return true;
		}
	};
	
	// Wire up the success and failure handlers
	YAHOO.example.pagecontent.simpletable.callback = { success: handleSuccess,
failure: handleFailure };
	
	// Render the Dialog
	YAHOO.example.pagecontent.simpletable.render("pagecontent");
	
	
}
</script>


<?PHP
/*<div id="checkauthdialog">
<div class="hd">Please enter your user login information</div>
<div class="bd">
<form method="POST" action="login.php">
<label for="user">E-mail:</label><input type="textbox" name="user" /> 
<label for="pass">Password:</label><input type="password" name="pass" /> 
<div class="clear"></div>
</form>
</div>
</div>

<div id="uploadrisdialog">
<div class="hd">Select a RIS-formatted file of references to upload</div>
<div class="bd">
<form action="parseuploadedris.php" method="post" enctype="multipart/form-data">
<label for="file">Filename:</label>
<input type="file" name="file" id="file" /> 
<br />
<div class="clear"></div>
</form>
</div>
</div>

<div id="simpletabledialog">
<div class="bd">
<form action="add_generic.php" method="post">
<label for="name">Name</label><input type="textbox" name="name" />
<label for="description">Description</label><input type="textbox" name="description" />
<div class="clear"></div>
</form>
</div>
</div>*/
?>

