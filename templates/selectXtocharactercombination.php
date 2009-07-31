<?PHP
if ($_GET['includeinform']!=1) {
	$_GET['pagetitle']="TreeTapper: select";
	include('template_pagestart.php');
	require ('checkauth.php');
}
include 'dblogin.php'; //connection in $db
$tablename=$_GET['table'];
$validtablename=0;
$sql = "select relname from pg_stat_user_tables order by relname;";
$result = pg_query($db, $sql);
for ($lt = 0; $lt < pg_numrows($result); $lt++) {
	if(strcmp(pg_fetch_result($result,$lt,0),$tablename)==0) {
		$validtablename=1;
	}
}
$overwrite=0;
if (isset($_GET['overwrite'])) {
	$overwrite=$_GET['overwrite'];
}
if (!isset($_POST['submit']) || $_GET['includeinform']==1) {
//if (!isset($_POST['submit'])) {
	
	if ($validtablename==1) {
		
		
		echo "<script type=\"text/javascript\">

YAHOO.example.init = function () {
//	var allselected = {};
//	var samepairsselected = new Array();
//	var differentpairsselected = new Array();
//	var upperdiagonalselected = new Array();
	";
		$zerocomb = "SELECT charactercombination_id FROM charactercombination WHERE charactercombination_char1=0 AND charactercombination_char2=0 AND charactercombination_char3=0";
		$zerocombresult = pg_query($db, $zerocomb) or die( "could not connect");
		$zerocombid=pg_fetch_result($zerocombresult,0,0);
		echo "\nvar oCheckButton".$zerocombid." = new YAHOO.widget.Button({ type: \"checkbox\", label: \"No characters\", id: \"checkbutton".$zerocombid."\", name: \"checkbuttoncombid[]\", value: \"".$zerocombid."\", container: \"zerocombacters\", checked: false });";
		$charactertypearray=array();
		$charactertypedescriptionarray=array();
		$chartypesql = "SELECT charactertype_id, charactertype_name, charactertype_description FROM charactertype WHERE charactertype_id>0 ORDER BY charactertype_id";
		$chartyperesult = pg_query($db, $chartypesql);
		for ($lt = 0; $lt < pg_numrows($chartyperesult); $lt++) {
			$charactertypearray[pg_fetch_result($chartyperesult,$lt,0)]=pg_fetch_result($chartyperesult,$lt,1);
			$charactertypedescriptionarray[pg_fetch_result($chartyperesult,$lt,0)]=pg_fetch_result($chartyperesult,$lt,2);
		}
		foreach ($charactertypearray as $chartypeid => $chartypename) {
			$onecomb = "SELECT charactercombination_id FROM charactercombination WHERE charactercombination_char1=".$chartypeid." AND charactercombination_char2=0 AND charactercombination_char3=0";
		//echo "\nNEW QUERY IS $onecomb\n";
			$onecombresult = pg_query($db, $onecomb);
			$onecombid=pg_fetch_result($onecombresult,0,0);
			echo "\nvar oCheckButton".$onecombid." = new YAHOO.widget.Button({ type: \"checkbox\", label: \"".$chartypeid.": ".$chartypename."\", id: \"checkbutton".$onecombid."\", name: \"checkbuttoncombid[]\", value: \"".$onecombid."\", container: \"onecharacter\", checked: false });";
			$tooltiptext=$charactertypedescriptionarray[$chartypeid];
			echo "\nvar tt".$onecombid." = new YAHOO.widget.Tooltip(\"tt".$onecombid."\", { context: oCheckButton".$onecombid.".get(\"element\").getElementsByTagName(\"button\")[0], text:\"".$tooltiptext."\" });";
			
		}
		echo "function manualtwobytwoempty () {\n";
		foreach ($charactertypearray as $chartypeid1 => $chartypename1) {
			foreach ($charactertypearray as $chartypeid2 => $chartypename2) {
				$tooltiptext="Character type '".$chartypename1."' (row)<br>with character type '".$chartypename2."' (col)";
				$twocomb = "SELECT charactercombination_id FROM charactercombination WHERE charactercombination_char1=".$chartypeid1." AND charactercombination_char2=".$chartypeid2." AND charactercombination_char3=0";
				$twocombresult = pg_query($db, $twocomb);
				$twocombid=pg_fetch_result($twocombresult,0,0);
				echo "\nvar oCheckButton".$twocombid." = new YAHOO.widget.Button({ type: \"checkbox\", label: \"".$chartypeid1."-".$chartypeid2."\", id: \"checkbutton".$twocombid."\", name: \"checkbuttoncombid[]\", value: \"".$twocombid."\", container: \"tablecell".$chartypeid1."-".$chartypeid2."\", checked: false });";
				
				echo "\nvar tt".$twocombid." = new YAHOO.widget.Tooltip(\"tt".$twocombid."\", { context: oCheckButton".$twocombid.".get(\"element\").getElementsByTagName(\"button\")[0], text:\"".$tooltiptext."\" });";
			}
		}
		echo "};\n";
		
		echo "function manualtwobytwoall () {\n";
		foreach ($charactertypearray as $chartypeid1 => $chartypename1) {
			foreach ($charactertypearray as $chartypeid2 => $chartypename2) {
				$tooltiptext="Character type '".$chartypename1."' (row)<br>with character type '".$chartypename2."' (col)";
				$twocomb = "SELECT charactercombination_id FROM charactercombination WHERE charactercombination_char1=".$chartypeid1." AND charactercombination_char2=".$chartypeid2." AND charactercombination_char3=0";
				$twocombresult = pg_query($db, $twocomb);
				$twocombid=pg_fetch_result($twocombresult,0,0);
				echo "\nvar oCheckButton".$twocombid." = new YAHOO.widget.Button({ type: \"checkbox\", label: \"".$chartypeid1."-".$chartypeid2."\", id: \"checkbutton".$twocombid."\", name: \"checkbuttoncombid[]\", value: \"".$twocombid."\", container: \"tablecell".$chartypeid1."-".$chartypeid2."\", checked: true });";
				
				echo "\nvar tt".$twocombid." = new YAHOO.widget.Tooltip(\"tt".$twocombid."\", { context: oCheckButton".$twocombid.".get(\"element\").getElementsByTagName(\"button\")[0], text:\"".$tooltiptext."\" });";
			}
		}
		echo "};\n";
		
		echo "function manualtwobytwosame () {\n";
		foreach ($charactertypearray as $chartypeid1 => $chartypename1) {
			foreach ($charactertypearray as $chartypeid2 => $chartypename2) {
				$checkstatus="false";
				if ($chartypeid1==$chartypeid2) {
					$checkstatus="true";
				}
				$tooltiptext="Character type '".$chartypename1."' (row)<br>with character type '".$chartypename2."' (col)";
				$twocomb = "SELECT charactercombination_id FROM charactercombination WHERE charactercombination_char1=".$chartypeid1." AND charactercombination_char2=".$chartypeid2." AND charactercombination_char3=0";
				$twocombresult = pg_query($db, $twocomb);
				$twocombid=pg_fetch_result($twocombresult,0,0);
				echo "\nvar oCheckButton".$twocombid." = new YAHOO.widget.Button({ type: \"checkbox\", label: \"".$chartypeid1."-".$chartypeid2."\", id: \"checkbutton".$twocombid."\", name: \"checkbuttoncombid[]\", value: \"".$twocombid."\", container: \"tablecell".$chartypeid1."-".$chartypeid2."\", checked: ".$checkstatus." });";
				
				echo "\nvar tt".$twocombid." = new YAHOO.widget.Tooltip(\"tt".$twocombid."\", { context: oCheckButton".$twocombid.".get(\"element\").getElementsByTagName(\"button\")[0], text:\"".$tooltiptext."\" });";
			}
		}
		echo "};\n";
		
		echo "function manualtwobytwoupper () {\n";
		foreach ($charactertypearray as $chartypeid1 => $chartypename1) {
			foreach ($charactertypearray as $chartypeid2 => $chartypename2) {
				$checkstatus="false";
				if ($chartypeid1<$chartypeid2) {
					$checkstatus="true";
				}
				$tooltiptext="Character type '".$chartypename1."' (row)<br>with character type '".$chartypename2."' (col)";
				$twocomb = "SELECT charactercombination_id FROM charactercombination WHERE charactercombination_char1=".$chartypeid1." AND charactercombination_char2=".$chartypeid2." AND charactercombination_char3=0";
				$twocombresult = pg_query($db, $twocomb);
				$twocombid=pg_fetch_result($twocombresult,0,0);
				echo "\nvar oCheckButton".$twocombid." = new YAHOO.widget.Button({ type: \"checkbox\", label: \"".$chartypeid1."-".$chartypeid2."\", id: \"checkbutton".$twocombid."\", name: \"checkbuttoncombid[]\", value: \"".$twocombid."\", container: \"tablecell".$chartypeid1."-".$chartypeid2."\", checked: ".$checkstatus." });";
				
				echo "\nvar tt".$twocombid." = new YAHOO.widget.Tooltip(\"tt".$twocombid."\", { context: oCheckButton".$twocombid.".get(\"element\").getElementsByTagName(\"button\")[0], text:\"".$tooltiptext."\" });";
			}
		}
		echo "};\n";
		
		echo "function manualtwobytwoupperlower () {\n";
		foreach ($charactertypearray as $chartypeid1 => $chartypename1) {
			foreach ($charactertypearray as $chartypeid2 => $chartypename2) {
				$checkstatus="false";
				if ($chartypeid1!=$chartypeid2) {
					$checkstatus="true";
				}
				$tooltiptext="Character type '".$chartypename1."' (row)<br>with character type '".$chartypename2."' (col)";
				$twocomb = "SELECT charactercombination_id FROM charactercombination WHERE charactercombination_char1=".$chartypeid1." AND charactercombination_char2=".$chartypeid2." AND charactercombination_char3=0";
				$twocombresult = pg_query($db, $twocomb);
				$twocombid=pg_fetch_result($twocombresult,0,0);
				echo "\nvar oCheckButton".$twocombid." = new YAHOO.widget.Button({ type: \"checkbox\", label: \"".$chartypeid1."-".$chartypeid2."\", id: \"checkbutton".$twocombid."\", name: \"checkbuttoncombid[]\", value: \"".$twocombid."\", container: \"tablecell".$chartypeid1."-".$chartypeid2."\", checked: ".$checkstatus." });";
				
				echo "\nvar tt".$twocombid." = new YAHOO.widget.Tooltip(\"tt".$twocombid."\", { context: oCheckButton".$twocombid.".get(\"element\").getElementsByTagName(\"button\")[0], text:\"".$tooltiptext."\" });";
			}
		}
		echo "};\n";
		
		echo "var oPushButtonManualAddEmpty = new YAHOO.widget.Button({ label:\"All unselected\", id:\"manualselectionempty\", container:\"twocharbuttons\" });\noPushButtonManualAddEmpty.on(\"click\", manualtwobytwoempty);\n ";
		echo "var oPushButtonManualAddAll = new YAHOO.widget.Button({ label:\"All selected\", id:\"manualselectionall\", container:\"twocharbuttons\" });\noPushButtonManualAddAll.on(\"click\", manualtwobytwoall);\n ";
		echo "var oPushButtonManualAddSame = new YAHOO.widget.Button({ label:\"Only identical pairs\", id:\"manualselectionsame\", container:\"twocharbuttons\" });\noPushButtonManualAddSame.on(\"click\", manualtwobytwosame);\n ";
		echo "var oPushButtonManualAddUpper = new YAHOO.widget.Button({ label:\"Non-identical pairs, order irrelevant\", id:\"manualselectionupper\", container:\"twocharbuttons\" });\noPushButtonManualAddUpper.on(\"click\", manualtwobytwoupper);\n ";
		echo "var oPushButtonManualAddUpperLower = new YAHOO.widget.Button({ label:\"All non-identical pairs\", id:\"manualselectionupperlower\", container:\"twocharbuttons\" });\noPushButtonManualAddUpperLower.on(\"click\", manualtwobytwoupperlower);\n ";
		
		echo "} ();</script>";
		
		echo "<div align=left>";
		if ($_GET['includeinform']!=1) {
			echo "<form action='$_SERVER[PHP_SELF]' method='post'>";
			echo "<input type='hidden' name='table' value='".$tablename."'>\n";
		}
		if ($_GET['includeinform']!=1) {
			echo "<br>Specific question: <select name=\"selection\">";
			include ('optionbox_generic.php');
			echo "</select>";
			include ('helppanel_generic.php');	
		}
		echo "<br><fieldset id=\"zerocombacters\"><legend>Zero Characters</legend>";
		echo "</fieldset>";
		echo "<br><fieldset id=\"onecharacter\"><legend>One Character <input type='button' value='+' onclick='activatesimpletable(\"Make new character type (then reload this page)\")'></legend>";
		
		echo "</fieldset>";
		
		echo "<br><fieldset id=\"twocharacters\" ><legend>Two Characters (select a box to pre-fill entries, then click on the generated buttons to change these assignments)</legend>";

		echo "<div id=\"twocharbuttons\"></div>";
		echo "<table style=\"text-align: center; width: 100%;\" border=\"0\" cellpadding=\"0\"
cellspacing=\"0\" id='manualtable'><br>";
		foreach ($charactertypearray as $chartypeid1 => $chartypename1) {
			echo "\n<tr>";
			foreach ($charactertypearray as $chartypeid2 => $chartypename2) {
				echo "<td id=\"tablecell".$chartypeid1."-".$chartypeid2."\"></td>";
			}
			echo "</tr>";
		}
		echo "</table>";
		echo "</fieldset>";
		
		if ($_GET['includeinform']!=1) {
			echo "<br><input type='submit' value='Submit'>";
			echo "</form>";
		}
		echo "</div>";
	}
	else { //process form
		echo "Go to <a href=\"".$treetapperbaseurl."/add_".$_POST['table'].".php\">Add ".$_POST['table']." form</a><br>";
		echo "Entered results<br>";
//		if ( isset($_POST['checkbuttoncombid']) ) { 
//			$_POST['checkbuttoncombid'] = implode(', ', $_POST['checkbuttoncombid']); //Converts an array into a single string
//		}
		foreach ($_POST['checkbuttoncombid'] as $checkbuttonorder => $checkbuttonid) {
			echo "Combos you chose: $checkbuttonorder: $checkbuttonid<br />";
		}
		$tablename=$_POST['table'];
		$selection=$_POST['selection'];
		if ($selection>0) {
			$existingsql = "SELECT ".$tablename."tocharactercombination_id FROM ".$tablename."tocharactercombination WHERE ".$tablename."tocharactercombination_".$tablename."=".$selection;
			$existingresult = pg_query($db, $existingsql);
			if (pg_numrows($existingresult)==0 || $overwrite==1) {
				foreach ($_POST['checkbuttoncombid'] as $checkbuttonorder => $checkbuttonid) {
					$idsequence=$tablename."tocharactercombination_".$tablename."tocharactercombination_id_seq";
					if (preg_match('/posedquestion/',$tablename)) {
						$idsequence='posedquestiontocharactercombi_posedquestiontocharactercombi_seq';
					}	
					$insertionsql="INSERT INTO  ".$tablename."tocharactercombination (".$tablename."tocharactercombination_".$tablename.", ".$tablename."tocharactercombination_charactercombination, ".$tablename."tocharactercombination_addedby, ".$tablename."tocharactercombination_approved,".$tablename."tocharactercombination_id) VALUES (".$selection.", ".$checkbuttonid.", ".$personid.", ".$approved.",nextval('".$idsequence."'))";
					//$insertionsql="INSERT INTO  ".$tablename."tocharactercombination (".$tablename."tocharactercombination_".$tablename.", ".$tablename."tocharactercombination_charactercombination, ".$tablename."tocharactercombination_addedby, ".$tablename."tocharactercombination_approved) VALUES (".$selection.", ".$checkbuttonid.", ".$personid.", ".$approved.")";

					echo "<br>$insertionsql";
					$insertionresult = pg_query($db, $insertionsql);
				}
			}
			else {
				echo "Entries already exist for this item";
			}
		}
	}
}
if ($_GET['includeinform']!=1) {
	
	include('template_pageend.php');
}

?>
