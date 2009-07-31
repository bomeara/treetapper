<?php
include 'templates/checkauth.php';
$_GET['pagetitle']="TreeTapper: Add general question";
include('templates/template_pagestart.php');
if (!$_POST['question'] || !$_POST['description']) {
	echo "<h3>Add general question</h3><br>";
	include ('templates/dblogin.php'); //connection in $db
	$result = pg_query($db,"SELECT generalquestion_id, generalquestion_name,generalquestion_description, generalquestion_approved FROM generalquestion ") or die("Couldn't query the database.");
	echo("<div id=\"markup\">
<table id=\"existingquestions\">
<thead>
<tr>
<th>Approved</th>
<th>Question</th>
<th>Description</th>
</tr>
</thead>
<tbody>");
	for ($lt = 0; $lt < pg_numrows($result); $lt++) {
		$approvestatus="Pending";
		if (pg_fetch_result($result,$lt,3)==1) {
			$approvestatus="Yes";
		}
		if (pg_fetch_result($result,$lt,3)==-1) {
			$approvestatus="No";
		}
		echo("<tr><td>$approvestatus</td><td>".pg_fetch_result($result,$lt,1)."</td><td>".pg_fetch_result($result,$lt,2)."</td></tr>\n");
	}
	echo("</tbody>
</table>
</div>\n\n");
	echo("<script type=\"text/javascript\">
YAHOO.util.Event.addListener(window, \"load\", function() {
    YAHOO.example.EnhanceFromMarkup = new function() {
        var myColumnDefs = [
			{key:\"approved\",label:\"Approved\",sortable:true},
            {key:\"question\",label:\"Question\",sortable:true},
            {key:\"description\",label:\"Description\", sortable:true}
			];
        this.myDataSource = new YAHOO.util.DataSource(YAHOO.util.Dom.get(\"existingquestions\"));
        this.myDataSource.responseType = YAHOO.util.DataSource.TYPE_HTMLTABLE;
        this.myDataSource.responseSchema = {
fields: [{key:\"approved\"}, {key:\"question\"},
	{key:\"description\"}
	]
        };
		
        this.myDataTable = new YAHOO.widget.DataTable(\"markup\", myColumnDefs, this.myDataSource,{sortedBy:{key:\"approved\",dir:\"desc\"}});
	};
});
</script>");

echo ("<br><br><div id=\"form\" align=\"left\">
<form action='$_SERVER[PHP_SELF]' method='post'>
New question:<br><input type=\"text\" style=\"width: 40em\" name='question' id='question'><br><br>
Brief description:<br><input type=\"text\" style=\"width: 90%\" name='description' id='description'><br>
<br>
<input type='submit' value='Submit'>
</form></div>");
}
else {
	include ('templates/dblogin.php'); //connection in $db
/*	$result = pg_query($db,"SELECT personhiddeninfo_personid, personhiddeninfo_trustlevel FROM personhiddeninfo WHERE personhiddeninfo_password='$_SESSION[pass]' AND personhiddeninfo_email='$_SESSION[user]'") or die("Couldn't query the user-database.");
	$personid=pg_fetch_result($result,0,0);
	$persontrust=pg_fetch_result($result,0,1);
	$approved=0;
	if ($persontrust>=4) {
		$approved=1;
	}*/
	pg_query($db,"INSERT INTO generalquestion (generalquestion_name, generalquestion_description, generalquestion_addedby, generalquestion_approved) VALUES ('$_POST[question]', '$_POST[description]', '$personid', '$approved')");
}
include('templates/template_pageend.php');
?>
