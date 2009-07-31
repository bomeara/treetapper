<?php
$_GET['pagetitle']="TreeTapper: Add method";
include('templates/template_pagestart.php');
require ('templates/checkauth.php');
if (!$_POST['name'] || !$_POST['description']) {
	echo "<h3>Add method</h3><br>";
	echo ("<br><br><div id=\"form\" align=\"left\">\n<form action='$_SERVER[PHP_SELF]' method='post'>");
		  	echo ("Method name: <br><input type=\"text\" size=\"100\" name='name' id='name' >");
		  echo ("<br><br>
Brief description:<br><input type=\"text\" size=\"200\" name='description' id='description'><br>");
	
//		  echo "<br><p>";
//		  echo "</p><p>";
//		  include ('templates/general_toposed.php');
		  
//		  echo "<br>Category: <select name=\"generalquestion\">";
//		  $_GET['table'] = 'generalquestion';
//		  include ('templates/optionbox_generic.php');
//		  echo "</select>";
//		  echo "</p><p>";

		  echo "<br><p>";
		  $_GET['table'] = 'posedquestion';
		  include ('templates/selectiontable_generic.php');
		  echo "</p><p>";
		  
		  echo "<br><p>";
		  $_GET['table'] = 'criterion';
		  include ('templates/selectiontable_generic.php');
		  echo "</p><p>";
	  		  
		  echo "<br><p>";
		  $_GET['table'] = 'reference';
		  include ('templates/autocomplete_referencebox.php');
		  echo "</p><p>";
		  
		  echo "<br><p>";
		  echo "Select character combination(s)<br />";
		  $_GET['table'] = 'method';
		  $_GET['includeinform']='1';
		  include ('templates/selectXtocharactercombination.php');
		  echo "</p><p>";
		  
		  echo "<br><p>";
		  $_GET['table1']='treetype';
		  $_GET['table2']='branchlengthtype';
		  echo "Select one of these common sets of tree and branch length combinations or select individually below<br />";
		  echo "<input type='radio' name='treebrlenradio' value='0' checked='true'>Specified in table below<br />";
		  echo "<input type='radio' name='treebrlenradio' value='1'>Any tree (not network) with or without branch lengths<br />";
		  echo "<input type='radio' name='treebrlenradio' value='2'>Any tree (not network) with branch lengths<br />";
		  echo "<input type='radio' name='treebrlenradio' value='3'>Any fully-resolved tree (not network) with or without branch lengths<br />";
		  echo "<input type='radio' name='treebrlenradio' value='4'>Any fully-resolved tree (not network) with branch lengths<br />";
		  echo "<input type='radio' name='treebrlenradio' value='5'>Any rooted, fully-resolved tree (not network) with or without branch lengths<br />";
		  echo "<input type='radio' name='treebrlenradio' value='6'>Any rooted, fully-resolved tree (not network) with branch lengths<br />";
		  echo "<input type='radio' name='treebrlenradio' value='7'>Any rooted tree (not network) with or without branch lengths, with or without polytomies<br />";
		  echo "<input type='radio' name='treebrlenradio' value='8'>Any rooted tree (not network) with branch lengths, with or without polytomies<br />";
		  include ('templates/selectiontable_generic2D.php');
		  echo "</p><p>";
		  
		  echo ("<br>
<input type='submit' value='Submit'>
</form></div>");
}
else {
	echo "Now processing<br>";
	include ('templates/dblogin.php'); //connection in $db	
	$validinput=1;
	if (strlen($_POST['name']) < 5) {
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
		
		$res=pg_query($db,"SELECT nextval('method_method_id_seq') as key");
		$row=pg_fetch_array($res, 0);
		$newmethodid=$row['key'];
		//$newmethodid=0;
		$insertmethodsql="INSERT INTO method (method_id, method_name, method_description, method_addedby, method_approved) VALUES ('".$newmethodid."', '".$escname."', '".$escdescription."', '".$personid."', '".$approved."')";
		//echo "<br>$insertmethodsql";
		$insertmethodresult=pg_query($db,$insertmethodsql);
		foreach ($_POST['checkbuttonposedquestion_0'] as $checkbuttonposedquestionorder => $checkbuttonposedquestionid) {
			$insertionsql="INSERT INTO  methodtoposedquestion (methodtoposedquestion_method, methodtoposedquestion_posedquestion, methodtoposedquestion_addedby, methodtoposedquestion_approved) VALUES ('".$newmethodid."', '".$checkbuttonposedquestionid."', '".$personid."', '".$approved."')";
			//echo "<br>$insertionsql";
			$insertionresult = pg_query($db, $insertionsql);
		}
		foreach ($_POST['checkbuttoncriterion_0'] as $checkbuttoncriterionorder => $checkbuttoncriterionid) {
			$insertionsql="INSERT INTO  methodtocriterion (methodtocriterion_method, methodtocriterion_criterion, methodtocriterion_addedby, methodtocriterion_approved) VALUES ('".$newmethodid."', '".$checkbuttoncriterionid."', '".$personid."', '".$approved."')";
			//echo "<br>$insertionsql";
			$insertionresult = pg_query($db, $insertionsql);
		}
		foreach ($_POST['checkbuttonreferencewithauthorfilter_0'] as $checkbuttonreferenceorder => $checkbuttonreferenceid) {
			$insertionsql="INSERT INTO  methodtoreference (methodtoreference_method, methodtoreference_reference, methodtoreference_addedby, methodtoreference_approved) VALUES ('".$newmethodid."', '".$checkbuttonreferenceid."', '".$personid."', '".$approved."')";
			//echo "<br>$insertionsql";
			$insertionresult = pg_query($db, $insertionsql);
		}
		foreach ($_POST['checkbuttoncombid'] as $checkbuttonorder => $checkbuttonid) {
			$insertionsql="INSERT INTO  methodtocharactercombination (methodtocharactercombination_method, methodtocharactercombination_charactercombination, methodtocharactercombination_addedby, methodtocharactercombination_approved) VALUES ('".$newmethodid."', '".$checkbuttonid."', '".$personid."', '".$approved."')";
			//echo "<br>$insertionsql";
			$insertionresult = pg_query($db, $insertionsql);
		}
		if ($_POST['treebrlenradio']==0) {
			foreach ($_POST['checkbuttoncombid_treetype_branchlengthtype_0'] as $checkbuttonTBorder => $checkbuttonTBid) {
				$valuearray=explode("_",$checkbuttonTBid);
				$insertionsql="INSERT INTO  methodtotreetypetobranchlengthtype (methodtotreetypetobranchlengthtype_method, methodtotreetypetobranchlengthtype_treetype, methodtotreetypetobranchlengthtype_branchlengthtype, methodtotreetypetobranchlengthtype_addedby, methodtotreetypetobranchlengthtype_approved) VALUES ('".$newmethodid."', '".$valuearray[0]."', '".$valuearray[1]."', '".$personid."', '".$approved."')";
			//echo "<br>$insertionsql";
				$insertionresult = pg_query($db, $insertionsql);
			}
		}
		else if ($_POST['treebrlenradio']==1) {
			$brlenarray=array(1,2,3,4,5,6,7,8,9,10,11,12,13);
			$treetypearray=array(1,2,3,4,5,6,7,8);
			foreach ($brlenarray as $brlenid) {
				foreach ($treetypearray as $treetypeid) {
					$insertionsql="INSERT INTO  methodtotreetypetobranchlengthtype (methodtotreetypetobranchlengthtype_method, methodtotreetypetobranchlengthtype_treetype, methodtotreetypetobranchlengthtype_branchlengthtype, methodtotreetypetobranchlengthtype_addedby, methodtotreetypetobranchlengthtype_approved) VALUES ('".$newmethodid."', '".$treetypeid."', '".$brlenid."', '".$personid."', '".$approved."')";

				}
			}
		}
		else if ($_POST['treebrlenradio']==2) {
			$brlenarray=array(2,3,4,5,6,7,8,9,10,11,12,13);
			$treetypearray=array(1,2,3,4,5,6,7,8);
			foreach ($brlenarray as $brlenid) {
				foreach ($treetypearray as $treetypeid) {
					$insertionsql="INSERT INTO  methodtotreetypetobranchlengthtype (methodtotreetypetobranchlengthtype_method, methodtotreetypetobranchlengthtype_treetype, methodtotreetypetobranchlengthtype_branchlengthtype, methodtotreetypetobranchlengthtype_addedby, methodtotreetypetobranchlengthtype_approved) VALUES ('".$newmethodid."', '".$treetypeid."', '".$brlenid."', '".$personid."', '".$approved."')";
					
				}
			}
		}
		else if ($_POST['treebrlenradio']==3) {
			$brlenarray=array(1,2,3,4,5,6,7,8,9,10,11,12,13);
			$treetypearray=array(1,2,3,4);
			foreach ($brlenarray as $brlenid) {
				foreach ($treetypearray as $treetypeid) {
					$insertionsql="INSERT INTO  methodtotreetypetobranchlengthtype (methodtotreetypetobranchlengthtype_method, methodtotreetypetobranchlengthtype_treetype, methodtotreetypetobranchlengthtype_branchlengthtype, methodtotreetypetobranchlengthtype_addedby, methodtotreetypetobranchlengthtype_approved) VALUES ('".$newmethodid."', '".$treetypeid."', '".$brlenid."', '".$personid."', '".$approved."')";
					
				}
			}
		}
		else if ($_POST['treebrlenradio']==4) {
			$brlenarray=array(2,3,4,5,6,7,8,9,10,11,12,13);
			$treetypearray=array(1,2,3,4);
			foreach ($brlenarray as $brlenid) {
				foreach ($treetypearray as $treetypeid) {
					$insertionsql="INSERT INTO  methodtotreetypetobranchlengthtype (methodtotreetypetobranchlengthtype_method, methodtotreetypetobranchlengthtype_treetype, methodtotreetypetobranchlengthtype_branchlengthtype, methodtotreetypetobranchlengthtype_addedby, methodtotreetypetobranchlengthtype_approved) VALUES ('".$newmethodid."', '".$treetypeid."', '".$brlenid."', '".$personid."', '".$approved."')";
					
				}
			}
		}
		else if ($_POST['treebrlenradio']==5) {
			$brlenarray=array(1,2,3,4,5,6,7,8,9,10,11,12,13);
			$treetypearray=array(1,2);
			foreach ($brlenarray as $brlenid) {
				foreach ($treetypearray as $treetypeid) {
					$insertionsql="INSERT INTO  methodtotreetypetobranchlengthtype (methodtotreetypetobranchlengthtype_method, methodtotreetypetobranchlengthtype_treetype, methodtotreetypetobranchlengthtype_branchlengthtype, methodtotreetypetobranchlengthtype_addedby, methodtotreetypetobranchlengthtype_approved) VALUES ('".$newmethodid."', '".$treetypeid."', '".$brlenid."', '".$personid."', '".$approved."')";
					
				}
			}
		}
		else if ($_POST['treebrlenradio']==6) {
			$brlenarray=array(2,3,4,5,6,7,8,9,10,11,12,13);
			$treetypearray=array(1,2);
			foreach ($brlenarray as $brlenid) {
				foreach ($treetypearray as $treetypeid) {
					$insertionsql="INSERT INTO  methodtotreetypetobranchlengthtype (methodtotreetypetobranchlengthtype_method, methodtotreetypetobranchlengthtype_treetype, methodtotreetypetobranchlengthtype_branchlengthtype, methodtotreetypetobranchlengthtype_addedby, methodtotreetypetobranchlengthtype_approved) VALUES ('".$newmethodid."', '".$treetypeid."', '".$brlenid."', '".$personid."', '".$approved."')";
					
				}
			}
		}
		else if ($_POST['treebrlenradio']==7) {
			$brlenarray=array(1,2,3,4,5,6,7,8,9,10,11,12,13);
			$treetypearray=array(1,2,5,6);
			foreach ($brlenarray as $brlenid) {
				foreach ($treetypearray as $treetypeid) {
					$insertionsql="INSERT INTO  methodtotreetypetobranchlengthtype (methodtotreetypetobranchlengthtype_method, methodtotreetypetobranchlengthtype_treetype, methodtotreetypetobranchlengthtype_branchlengthtype, methodtotreetypetobranchlengthtype_addedby, methodtotreetypetobranchlengthtype_approved) VALUES ('".$newmethodid."', '".$treetypeid."', '".$brlenid."', '".$personid."', '".$approved."')";
					
				}
			}
		}
		else if ($_POST['treebrlenradio']==8) {
			$brlenarray=array(2,3,4,5,6,7,8,9,10,11,12,13);
			$treetypearray=array(1,2,5,6);
			foreach ($brlenarray as $brlenid) {
				foreach ($treetypearray as $treetypeid) {
					$insertionsql="INSERT INTO  methodtotreetypetobranchlengthtype (methodtotreetypetobranchlengthtype_method, methodtotreetypetobranchlengthtype_treetype, methodtotreetypetobranchlengthtype_branchlengthtype, methodtotreetypetobranchlengthtype_addedby, methodtotreetypetobranchlengthtype_approved) VALUES ('".$newmethodid."', '".$treetypeid."', '".$brlenid."', '".$personid."', '".$approved."')";
					
				}
			}
		}
		
		
		
		echo "Go to new method's page: <a href='http://www.treetapper.org/method/".$newmethodid."'>http://www.treetapper.org/method/".$newmethodid."</a><br /><a href='http://www.treetapper.org/add_method.php'>Add another method</a><br />";
		//undo code would be 
		/*
		 delete from methodtocriterion where methodtocriterion_method=$newmethodid;
		 delete from methodtocharactercombination where methodtocharactercombination_method=$newmethodid;
		 delete from methodtoposedquestion where methodtoposedquestion_method=$newmethodid;
		 delete from methodtoreference where methodtoreference_method=$newmethodid;
		 delete from methodtotreetypetobranchlengthtype where methodtotreetypetobranchlengthtype_method=$newmethodid;
		 delete from method where method_id=$newmethodid;
		 */
		
	}
}
include('templates/template_pageend.php');
?>
