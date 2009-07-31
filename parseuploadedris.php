<?php
$_GET['pagetitle']="TreeTapper: Uploaded RIS file";
include('templates/template_pagestart.php');
require ('templates/checkauth.php');

include ('templates/dblogin.php'); //connection in $db
if (($_FILES["file"]["type"] != "image/gif")
	|| ($_FILES["file"]["type"] != "image/jpeg")
	|| ($_FILES["file"]["type"] != "image/pjpeg"))
{
	if ($_FILES["file"]["error"] > 0)
    {
		echo "Return Code: " . $_FILES["file"]["error"] . "<br />";
    }
	else
    {
		echo "Upload: " . $_FILES["file"]["name"] . "<br />";
		echo "Type: " . $_FILES["file"]["type"] . "<br />";
		echo "Size: " . ($_FILES["file"]["size"] / 1024) . " Kb<br />";
		//echo "Temp file: " . $_FILES["file"]["tmp_name"] . "<br />";
		$newreferences="";
		$oldreferences="";
		if (file_exists("upload/" . $_FILES["file"]["name"]))
		{
			echo $_FILES["file"]["name"] . " already exists. ";
		}
		else
		{
			$filestring=$_FILES["file"]["tmp_name"];
			$file = fopen($filestring, "r") or exit("Unable to open file!");
//Output a line of the file until the end is reached
			$doi="NULL";
			$pmid="NULL";
			$url="NULL";
			$articletitle="NULL";
			$journaltitle="NULL";
			$year="NULL";
			$vol="NULL";
			$issue="NULL";
			$firstpage="NULL";
			$lastpage="NULL";	
			$numref=0;
			$authornumber=0;
			$authorarray[1]=0;
			$risstring="";
			while(!feof($file))
			{
				$newline=trim(fgets($file));
				$risstring="$risstring\n$newline";
				if (preg_match('@(http[^\s]+)\s*@i',$newline,$matches)) {
					$url="'".addslashes($matches[1])."'";
				}
				if (preg_match('@(doi:[^\s]+)\s*@i',$newline,$matches)) {
					$doi="'".addslashes($matches[1])."'";
				}				
				if (preg_match('@^TI\s*-\s*(.*[^\s])\s*@i',$newline, $matches)) {
					$articletitle="'".addslashes($matches[1])."'";
				}
				else if (preg_match('@^PY\s*-\s*(.*[^\s])\s*@i',$newline, $matches)) {
					$year="'".addslashes($matches[1])."'";
				}
				else if (preg_match('@^SP\s*-\s*(\d+)-?(\d*)\s*@i',$newline, $matches)) {
					$firstpage="'".addslashes($matches[1])."'";
					//echo "<br>firstpage = $matches[1] ($firstpage) [".addslashes($matches[1])."]<br>";
					if (strlen($matches[2])>0) {
						$lastpage="'".addslashes($matches[2])."'";
					}
					else {
						$lastpage="'".addslashes($matches[1])."'";
					}
				}
				else if (preg_match('@^VL\s*-\s*([^\s]+)\s*@i',$newline, $matches)) {
					$vol="'".addslashes($matches[1])."'";
				}
				else if (preg_match('@^AU\s*-\s*(.*[^\s])\s*@i',$newline, $matches)) {
					$authornumber++;
					$lastname="";
					$firstname="";
					$middlename="";
					$authorstring="{$matches[1]}";
					preg_match('@([^\,\.\s]+)\s*\.?\,?\s*([^\,\.\s]*)\s*\.?\,?\s*([^\,\.\s]*)\s*@i',$authorstring, $matches);
					$lastname=$matches[1];
					if (strlen($matches[2])>0) {
						$firstname=$matches[2];
					}
					if (strlen($matches[3])>0) {
						$middlename=$matches[3];
					}
					$slashedname="'".addslashes($lastname)."'"; //In case there's an apostrophe in the name.
					$auquery="SELECT person_id, person_first, person_middle, person_last FROM person WHERE person_last ILIKE $slashedname";
					$ausearch = pg_query($db,$auquery) or die("Couldn't query the database.");
					//echo "Author $authornumber: ";
					$numberofmatches=0;
					$matchingarray[]="";
					for ($lt = 0; $lt < pg_numrows($ausearch); $lt++) {
						$nameupdate=0;
						$validmatch=True;
						$longerfirst=pg_fetch_result($ausearch,$lt,1);
						$longermiddle=pg_fetch_result($ausearch,$lt,2);
						if (min(strlen($firstname),strlen(pg_fetch_result($ausearch,$lt,1)))>0) {
							$pattern='@'.$firstname.'@i';
							$target=pg_fetch_result($ausearch,$lt,1);
							if (strlen($firstname)>strlen(pg_fetch_result($ausearch,$lt,1))) {
								$pattern='@'.pg_fetch_result($ausearch,$lt,1).'@i';
								$target=$firstname;
								$longerfirst=$firstname;
								$nameupdate=1;
							}
							if(!preg_match($pattern,$target,$matches)) {
								$validmatch=False;
							}
						}
						if (min(strlen($middlename),strlen(pg_fetch_result($ausearch,$lt,2)))>0) {
							$pattern='@'.$middlename.'@i';
							$target=pg_fetch_result($ausearch,$lt,2);
							if (strlen($middlename)>strlen(pg_fetch_result($ausearch,$lt,2))) {
								$pattern='@'.pg_fetch_result($ausearch,$lt,2).'@i';
								$target=$middlename;
								$longermiddle=$middlename;
								$nameupdate=1;
							}
							if(!preg_match($pattern,$target,$matches)) {
								$validmatch=False;
							}
						}
						if ($validmatch) {
							$numberofmatches++;
							$matchingarray[$numberofmatches]=array(pg_fetch_result($ausearch,$lt,0),$longerfirst,$longermiddle,pg_fetch_result($ausearch,$lt,3),$nameupdate);
						}
					}
					if ($numberofmatches>1) {
						die("Multiple matches to person $firstname $middlename $lastname, email bcomeara@nescent.org for help");
					}
					else if ($numberofmatches==1) {
					//	echo "ID".$matchingarray[1][0];
					//	echo " ".$matchingarray[1][1];
					//	echo " ($firstname) ".$matchingarray[1][2];
					//	echo " ($middlename) ".$matchingarray[1][3]." ($lastname)<br>";
						if ($nameupdate==1) {
							if (strlen($longerfirst)>0) {
								$slongerfirst="'".addslashes($longerfirst)."'";
							}
							else {
								$slongerfirst="NULL";
							}
							if (strlen($longermiddle)>0) {
								$slongermiddle="'".addslashes($longermiddle)."'";
							}
							else {
								$slongermiddle="NULL";
							}
							
							$updatequery="UPDATE person SET person_first=$slongerfirst , person_middle=$slongermiddle, person_moddate=CURRENT_DATE WHERE (person_id={$matchingarray[1][0]})";
						//	echo "<p>$updatequery<br>";
							pg_query($db,$updatequery);
						}
						$authorarray[$authornumber]=$matchingarray[1][0];
					}
					else {
						
						 $res=pg_query($db,"SELECT nextval('person_person_id_seq') as key");
						 $row=pg_fetch_array($res, 0);
						 $key=$row['key'];
						//now we have the serial value in $key, let's do the insert
						$sfirstname="'".addslashes($firstname)."'";
						 $smiddlename="'".addslashes($middlename)."'";
						 $slastname="'".addslashes($lastname)."'";

						 pg_query($db,"INSERT INTO person (person_id, person_first, person_middle, person_last) VALUES ($key, $sfirstname, $smiddlename, $slastname)");						 
						 
						//echo "   $firstname $middlename $lastname<br>";
						$authorarray[$authornumber]=$key; //New key for added name
					}	
				}				
				else if (preg_match('@^IS\s*-\s*([^\s]+)\s*@i',$newline, $matches)) {
					$issue="'".addslashes($matches[1])."'";
				}
				else if (preg_match('@^JF\s*-\s*(.*[^\s])\s*@i',$newline, $matches)) {
					$journaltitle="'".addslashes($matches[1])."'";
				}
				else if (!preg_match('@\w+\s*@i',$newline, $matches) && preg_match('@[\dabcdefghijkmopqrstvwxyz]@i',"$title $year $articletitle $journaltitle",$matches2)) {
					$numref++;
					$existingrefcount=0;
					//echo "Ref $numref: $year \"$articletitle\". $journaltitle $vol($issue): $firstpage-$lastpage<br>URL: $url<br>DOI: $doi<br>PMID: $pmid<br>$risstring<br><p>Author id = $authorarray[1]<p>";
					if ($authorarray[1]>0) {
						$existingrefquery="SELECT count(reference_id) FROM reference, referencetoperson, person WHERE (reference_title ILIKE $articletitle) AND (referencetoperson_person = person_id) AND (referencetoperson_reference = reference_id) AND (person_id={$authorarray[1]})";
						$existingrefcheck=pg_query($db,$existingrefquery);
						$existingrefcount=pg_fetch_result($existingrefcheck,0,0);
						//echo "<p>$existingrefquery</p><br>Count: $existingrefcount<br>";

					}
					if ($existingrefcount==0) {
						//echo "<br><b>Above is new</b><p>";
						$res=pg_query($db,"SELECT nextval('reference_reference_id_seq') as key");
						$row=pg_fetch_array($res, 0);
						$key=$row['key'];
						// now we have the serial value in $key, let's do the insert
						if (strlen($risstring)==0) {
							$risstring="NULL";
						}
						else {
							$risstring="'".addslashes($risstring)."'";
						}
						pg_query($db,"INSERT INTO reference (reference_id, reference_title, reference_publicationdate, reference_publicationname, reference_volume, reference_issue, reference_startpage, reference_endpage, reference_ris, reference_url, reference_doi, reference_pmid, reference_addedby, reference_approved) VALUES ('$key', $articletitle, $year, $journaltitle, $vol, $issue, $firstpage, $lastpage, $risstring, $url, $doi, $pmid, $personid, $approved)");						$newreferences="$newreferences"."<p>Ref $numref: $year \"$articletitle\". $journaltitle $vol($issue): $firstpage-$lastpage</p>";

						
						
						for ($i=1; $i<=$authornumber; $i++) {
							$referencetopersoninsert="INSERT INTO referencetoperson (referencetoperson_id, referencetoperson_reference, referencetoperson_person, referencetoperson_authororder, referencetoperson_addedby, referencetoperson_approved) VALUES (nextval('referencetoperson_referencetoperson_id_seq'), '{$key}', '{$authorarray[$i]}', '{$i}', '$personid', '$approved')";
							//echo "<p>$referencetopersoninsert</p><br>";
							pg_query($db,$referencetopersoninsert); 
						}
						
					}
					else {
						$oldreferences="$oldreferences"."<p>Ref $numref: $year \"$articletitle\". $journaltitle $vol($issue): $firstpage-$lastpage</p>";

					}
					
					$authornumber=0;
					$doi="NULL";
					$pmid="NULL";
					$url="NULL";
					$articletitle="NULL";
					$journaltitle="NULL";
					$year="NULL";
					$vol="NULL";
					$issue="NULL";
					$firstpage="NULL";
					$lastpage="NULL";	
					$authorarray[1]=0;
					$risstring="";
				}
			}
			fclose($file);
			
  //    move_uploaded_file($_FILES["file"]["tmp_name"],
   //   "upload/" . $_FILES["file"]["name"]);
    //  echo "Stored in: " . "upload/" . $_FILES["file"]["name"];
		}
		echo "<div align=left><h2>References added to the database</h2>";
		if (strlen($newreferences)==0) {
			echo "<p>Nothing added</p>";
		}
		else {
			echo ereg_replace("'","",$newreferences);
		}
		echo "<h2>References submitted but already in the database</h2>";
		if (strlen($oldreferences)==0) {
			echo "<p>No overlap</p>";
		}
		else {
			echo ereg_replace("'","",$oldreferences);
		}
		echo "</div>";

    }
}
else
{
	echo "Invalid file";
}
include('templates/template_pageend.php');
?>
