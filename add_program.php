<?PHP
	$_GET['pagetitle']="TreeTapper: Add program";
	include('templates/template_pagestart.php');
	require('templates/checkauth.php');
	if (!$_POST['name'] || !$_POST['description']) {
		echo ("\n\n<script src=\"templates/js_updateoptionbox.js\"></script>\n\n");
		echo ("<br><br><div id=\"form\" align=\"left\">
			  <form action='$_SERVER[PHP_SELF]' method='post'>");
		echo ("Program name: <input type=\"text\" size=\"100\" name='name' id='name'>*<br>
			  Brief description: <textarea cols=\"100\" rows=\"5\" name='description' id='description' ></textarea>*<br>
			  URL: <input type=\"text\" size=\"100\" name='url' id='url'><br>
			  Cost: <input type=\"text\" size=\"100\" name='cost' id='cost' default='0'><br>
			  Version: <input type=\"text\" size=\"100\" name='version' id='version'>*<br>
			  Open source: <input type=\"checkbox\" name='opensource' id='opensource'><br>
			  ");
		
		echo "<br><p>";
		$_GET['table'] = 'dataformat';
		include ('templates/selectiontable_generic.php');
		echo "</p><p>";
		
		echo "<br><p>";
		$_GET['table'] = 'treeformat';
		include ('templates/selectiontable_generic.php');
		echo "</p><p>";
		
		echo "<br><p>";
		$_GET['table'] = 'reference';
		include ('templates/autocomplete_referencebox.php');
		echo "</p><p>";
		
		echo "<br><p>";
		$_GET['table1']='applicationkind';
		$_GET['table2']='platform';
		include ('templates/selectiontable_generic2D.php');
		echo "</p><p>";
		
		
		//select methods and character combinations
		echo "<br><p>Select methods and character types implemented in the program<br>";
		$characternamearray=array();
		$charnamesql="SELECT charactertype_id, charactertype_name FROM charactertype";
		$charnameresult=pg_query($db, $charnamesql);
		for ($lchar=0; $lchar<pg_numrows($charnameresult); $lchar++) {
			$characternamearray[pg_fetch_result($charnameresult,$lchar,0)]=pg_fetch_result($charnameresult,$lchar,1);
		}
		$characternamearray[0]="No character required"; // override the actual name, "None"
		$methodsql = "SELECT method_id, method_name, method_description, method_approved FROM method ORDER BY method_id";
		$methodresult = pg_query($db, $methodsql);
		echo "<div id=\"treeMethodCharComb\"></div>\n";
		echo "<script type=\"text/javascript\">
		//an anonymous function wraps our code to keep our variables
		//in function scope rather than in the global namespace:
		
		var tree; //will hold our TreeView instance
		var tt, contextElements = [];
		function treeInit() {
		
		tt = new YAHOO.widget.Tooltip(\"tt\", { 
		context: contextElements 
		});
		
		buildNodes();
		
		}
		
		function buildNodes() {
		tree = new YAHOO.widget.TreeView(\"treeMethodCharComb\");
		";
		for ($lm = 0; $lm < pg_numrows($methodresult); $lm++) {
			//echo "<br>".pg_fetch_result($methodresult,$lm,1)." (".pg_fetch_result($methodresult,$lm,2).")\n";
			echo "var methNode = new YAHOO.widget.TextNode({label: '".pg_fetch_result($methodresult,$lm,1)."', title: '".pg_fetch_result($methodresult,$lm,2)."'}, tree.getRoot(), false);\ncontextElements.push(methNode.labelElId);\n";
			$combosql="SELECT methodtocharactercombination_id, charactercombination_char1, charactercombination_char2, charactercombination_char3 FROM methodtocharactercombination, charactercombination WHERE methodtocharactercombination_method=".pg_fetch_result($methodresult,$lm,0)." AND methodtocharactercombination_charactercombination=charactercombination_id";
			$comboresult=pg_query($db,$combosql);
			for ($lc =0; $lc < pg_numrows($comboresult); $lc++) {
				$namestring="";
				//echo "<br><input type=\"checkbox\" name=\"checkbuttoncombid_methodtocharactercombination[]\" id=\"checkbutton_methodtocharactercombination_".pg_fetch_result($comboresult,$lc,0)."\" value=\"".pg_fetch_result($comboresult,$lc,0)."\" >\n";
				$namestring=$characternamearray[pg_fetch_result($comboresult,$lc,1)];
				if (pg_fetch_result($comboresult,$lc,2)>0) {
					$namestring.="-".$characternamearray[pg_fetch_result($comboresult,$lc,2)];	
				}
				if (pg_fetch_result($comboresult,$lc,3)>0) {
					$namestring.="-".$characternamearray[pg_fetch_result($comboresult,$lc,3)];	
				}
				echo "var charNode = new YAHOO.widget.HTMLNode('<input type=\"checkbox\" name=\"checkbuttoncombid_methodtocharactercombination[]\" id=\"checkbutton_methodtocharactercombination_".pg_fetch_result($comboresult,$lc,0)."\" value=\"".pg_fetch_result($comboresult,$lc,0)."\" > ".$namestring."', methNode, false);\n";
			}
		}
		echo "
		tree.draw();
		}
		
		YAHOO.util.Event.addListener(window, \"load\", treeInit); 
		
		
		</script>";
		echo "</p><p>";
		
		
		//echo "<script>YAHOO.util.Event.onDOMReady(updateoptionbox('reference','reference'));</script>";
		echo "<br> <input type='submit' value='Submit'></form></div>";
	}
	else {
		echo "Now processing<br>";
		include ('templates/dblogin.php'); //connection in $db	
		$validinput=1;
		if (strlen($_POST['name']) < 1) {
			$validinput=0;
			echo "Input name: ".$_POST['name']." was too short<br>";
		}
		if (strlen($_POST['description']) < 10) {
			$validinput=0;
			echo "Input description: ".$_POST['description']." was too short<br>";
		}		
		if ($validinput==1) {
			echo "Valid input<br>";
			$escname = pg_escape_string($_POST[name]);
			$escdescription = pg_escape_string($_POST[description]);
			$escurl = pg_escape_string($_POST[url]);
			$esccost = pg_escape_string($_POST[cost]);
			$escversion = pg_escape_string($_POST[version]);
			echo "<br> escname= $escname <br> escdescription = $escdescription <br> escurl = $escurl <br> esccost= $esccost <br> escversion = $escversion <br>";
			$res=pg_query($db,"SELECT nextval('program_program_id_seq') as key");
			$row=pg_fetch_array($res, 0);
			$newprogramid=$row['key'];
			//$newprogramid=-3;
			$programopen=0;
			if (isset($_POST["opensource"])) {
				$programopen=1;
			}
			$insertprogramsql="INSERT INTO program (program_id, program_name, program_description, program_version, program_cost, program_open, program_url, program_addedby, program_approved) VALUES ('".$newprogramid."', '".$escname."', '".$escdescription."', '".$escversion."', '".$esccost."', '".$programopen."', '".$escurl."', '".$personid."', '".$approved."')";
			//echo "<br>$insertprogramsql";
			$insertionresult = pg_query($db, $insertprogramsql);
			//foreach ($_REQUEST as $key => $value)
			//echo $key.'=>'.$value.'<br />';
			foreach ($_POST['checkbuttondataformat_0'] as $checkbuttondataformatorder => $checkbuttondataformatid) {
				$dataformatsql="INSERT INTO programtodataformat (programtodataformat_program, programtodataformat_dataformat, programtodataformat_addedby, programtodataformat_approved) VALUES ('".$newprogramid."', '".$checkbuttondataformatid."', '".$personid."', '".$approved."')";
					//echo "<br>$dataformatsql";
				$insertionresult = pg_query($db, $dataformatsql);
			}
			foreach ($_POST['checkbuttontreeformat_0'] as $checkbuttontreeformatorder => $checkbuttontreeformatid) {
				$treeformatsql="INSERT INTO programtotreeformat (programtotreeformat_program, programtotreeformat_treeformat, programtotreeformat_addedby, programtotreeformat_approved) VALUES ('".$newprogramid."', '".$checkbuttontreeformatid."', '".$personid."', '".$approved."')";
				//echo "<br>$treeformatsql";
				$insertionresult = pg_query($db, $treeformatsql);
			}
			foreach ($_POST['checkbuttoncombid_methodtocharactercombination'] as $checkbuttoncombid_methodtocharactercombinationorder => $checkbuttoncombid_methodtocharactercombinationid) {
				$mcsql="INSERT INTO programtomethodtocharactercombination (programtomethodtocharactercombination_program, programtomethodtocharactercombination_methodtocharactercombination, programtomethodtocharactercombination_addedby, programtomethodtocharactercombination_approved) VALUES ('".$newprogramid."', '".$checkbuttoncombid_methodtocharactercombinationid."', '".$personid."', '".$approved."')";
				//echo "<br> $mcsql";
				$insertionresult = pg_query($db, $mcsql);
			}
			foreach ($_POST['checkbuttoncombid_applicationkind_platform_0'] as $checkbuttoncombid_applicationkind_platformorder => $checkbuttoncombid_applicationkind_platformid) {
				$valuearray=explode("_",$checkbuttoncombid_applicationkind_platformid);
				$apsql="INSERT INTO programtoplatformappkind (programtoplatformappkind_program, programtoplatformappkind_applicationkind, programtoplatformappkind_platform, programtoplatformappkind_addedby, programtoplatformappkind_approved) VALUES ('".$newprogramid."', '".$valuearray[0]."', '".$valuearray[1]."', '".$personid."', '".$approved."')";
				//echo "<br> $apsql";
				$insertionresult = pg_query($db, $apsql);
			}
			foreach ($_POST['checkbuttonreferencewithauthorfilter_0'] as $checkbuttonreferenceorder => $checkbuttonreferenceid) {
				$insertionsql="INSERT INTO  programtoreference (programtoreference_program, programtoreference_reference, programtoreference_addedby, programtoreference_approved) VALUES ('".$newprogramid."', '".$checkbuttonreferenceid."', '".$personid."', '".$approved."')";
				//echo "<br>$insertionsql";
				$insertionresult = pg_query($db, $insertionsql);
			}
		}
	}
	include('templates/template_pageend.php');
	?>
