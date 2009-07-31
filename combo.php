<?PHP

include ('templates/dblogin.php'); //connection in $db
$include=$_GET['include'];
if ($include!=1) {
	$_GET['pagetitle']="TreeTapper: Combination";
	include('templates/template_pagestart.php');
}
?>
<style type="text/css">
div.innercontainer {
border: 0px solid #000000;
	background-color: #FFFFFF;
	padding-top: 10px;
	padding-bottom: 10px;
	padding-left: 10px;
	padding-right: 10px;
}
div.outercontainer {
border: 0px solid #000000;
	background-color: #CCCC99;
	padding-top: 10px;
	padding-bottom: 10px;
	padding-left: 10px;
	padding-right: 10px;
}
h3 {
color: #000000;
background: transparent;
	font-weight: bold;
	font-size: 1.1em;
}
</style>
<?PHP
foreach ($_GET as $key => $value) {
	$validtablename=0;
	$tablename=$key;
	$tabledescription="";
	$tableid="";
	$allowablefromids=array(1,2,4,8,9,12,13,19,21,22,24,32,35,36);
	$fulltablename=$key;
	if (stripos($key,"char")!==false) {
		$fulltablename='charactertype';
	}
	$escapedname=pg_escape_string($fulltablename);
	$sql = "SELECT tablelist_description, tablelist_id FROM tablelist WHERE tablelist_name ILIKE '$escapedname'";
	//echo "$sql\n";
	$result = pg_query($db, $sql);
	if (pg_numrows($result)==1) {
		$tabledescription=pg_fetch_result($result,0,0);
		$tableid=pg_fetch_result($result,0,1);
		if (in_array($tableid,$allowablefromids)) {
			$validtablename=1;
		}
	}
	if ($validtablename==1) {
		echo "<div class=\"outercontainer\">";
		echo "<h3>$tabledescription </h3>";
		$valuearray=explode(',', $value);
		if ($tableid==13) { //is method
			$methodcount=0;
			foreach ($valuearray as $methodid) {
				$_GET['include']=1;
				$_GET['id']=$methodid;
				if ($methodcount>0) {
					echo "<br />";
				}
				echo "<div class=\"innercontainer\">";
				include ('templates/info_method.php');
				echo "</div>";
				$methodcount++;
			}
		}
		else if ($tableid==24) { //is program
			$programcount=0;
			foreach ($valuearray as $programid) {
				$_GET['include']=1;
				$_GET['id']=$programid;
				if ($programcount>0) {
					echo "<br />";
				}
				echo "<div class=\"innercontainer\">";
				echo "<b>Need to create program display module</b>";
				//include ('templates/info_program.php'); //NEED TO MAKE THIS
				echo "</div>";
				$programcount++;
			}
			
		}
		else if ($tableid==19) { // is person
			$personcount=0;
			foreach ($valuearray as $personid) {
				$_GET['include']=1;
				$_GET['id']=$personid;
				if ($personcount>0) {
					echo "<br />";
				}
				echo "<div class=\"innercontainer\">";
				include ('templates/info_person.php'); 
				echo "</div>";
				$personcount++;
			}
		}
		else {
			$itemcount=0;
			echo "<div class=\"innercontainer\"><ul>";
			foreach ($valuearray as $id) {
				if (is_numeric($id)) {
					$newsql="SELECT ".$fulltablename."_name, ".$fulltablename."_description FROM $fulltablename WHERE $fulltablename"."_id = $id";
					$newresult=pg_query($db,$newsql);
					if (pg_numrows($newresult)==1) {
						echo "<li>".pg_fetch_result($newresult,0,0).": ".pg_fetch_result($newresult,0,1)."</li>";
					}
				}
			}
			echo "</ul></div";
		}
		echo "</div><br />";
		//echo "Key is $key, value is $value\n";
	}	
}

if ($include!=1) {
	include('templates/template_pageend.php');
}

?>