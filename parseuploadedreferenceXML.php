<?php
$_GET['pagetitle']="TreeTapper: Uploaded RIS file";
include('templates/template_pagestart.php');
require ('templates/checkauth.php');
$formatid=$_POST['format'];
$format="";
if ($formatid==1) {
	$format="ris";
}
else if ($formatid==2) {
	$format="bib";
}
else if ($formatid==3) {
	$format="end";
}
else if ($formatid==4) {
	$format="endx";
}
else if ($formatid==5) {
	$format="isi";
}
else if ($formatid==6) {
	$format="med";
}
else if ($formatid==7) {
	$format="copac";
}
else if ($formatid==8) {
	$format="xml2ris | ris"; //to check formatting, convert from xml to ris, then back to xml
}

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
		
		//This section for conversion comes from refbase0.9.0 code
		$tempDirPath = session_save_path();
		$sourceText = file_get_contents($_FILES["file"]["tmp_name"]);
		//echo "$sourceText";
		$tempFile = tempnam($tempDirPath, "treetapperinput-");
		$tempFileHandle = fopen($tempFile, "w") or die ("Cannot open $tempFile"); // open temp file with write permission
		fwrite($tempFileHandle, $sourceText) or die ("Cannot write to $tempFileHandle"); // save data to temp file
		fclose($tempFileHandle); // close temp file
		$outputFile = tempnam($tempDirPath, "treetapperoutput-");
		$outputFile=$outputFile.".xml";
		$resultsVersion=exec($pathtobibutils."/".$format."2xml -v", $outputVersion, $errorVersion);
		echo "Using ";
		print_r ($outputVersion);
		//echo " $outputVersion with output code $errorVersion <br/>";
		//$resultsVersion=exec("ls -ulth $pathtobibutils", $outputVersion, $errorVersion);
		//echo "<br />ls ";
		//print_r ($outputVersion);
		//echo " $outputVersion with output code $errorVersion <br/>";
		$cmd=$pathtobibutils."/".$format."2xml ".$tempFile." > ".$outputFile;
		//echo "<br><br><hr> $cmd <hr><br><br>";
		$results0=exec($cmd, $output0, $err0);
		//echo "OUTPUT (result=".$results0." and err = ".$err0.")<hr>".implode("<br>",$output0)."<br><hr>"; //debugging code
		unlink($tempFile);
		
		$newreferences="";
		$oldreferences="";
		$problemreferences="";
		$numref=0;
		
		$xml = simplexml_load_file($outputFile);
		foreach ($xml->mods as $record) {
			//initialize storage variables
			$doi="NULL";
			$pmid="NULL";
			$url="NULL";
			$articletitle="NULL";
			$vol="NULL";
			$year="NULL";
			$issue="NULL";
			$referencetype="NULL";
			$journaltitle="NULL";
			$modsstring="NULL";
			$firstpage="NULL";
			$lastpage="NULL";	
			$authornumber=0;
			$authorarray[1]=0;
			$problemauthors="";
			
			//now fill them
			if (isset($record->titleInfo->title)) {
				$articletitle=(string) $record->titleInfo->title;
				if (strlen((string) $record->titleInfo->subTitle)>0) {
					$articletitle="$articletitle".": ".(string) $record->titleInfo->subTitle;
				}
				$articletitle="'".addslashes($articletitle)."'";
			}
			if (isset($record->part->date)) {
				$year="'".addslashes($record->part->date)."'";
			}
			if (isset($record->part->detail)) { //because not all refs have this
				foreach ($record->part->detail as $detail) {
					switch((string) $detail['type']) { // Get attributes as element indices
						case 'volume':
							$vol="'".addslashes($detail->number)."'";
							break;
						case 'issue':
							$issue="'".addslashes($detail->number)."'";
							break;
						case 'page':
							if (preg_match('@([^\-]+)-?([^\-]*)@i', (string) $detail->number, $matches)) {
								$firstpage="'".addslashes($matches[1])."'";
								if (strlen($matches[2])>0) {
									$lastpage="'".addslashes($matches[2])."'";
								}
								else {
									$lastpage="'".addslashes($matches[1])."'";
								}								
							}
					}
				}
			}
			if (isset($record->location->url)) {
				foreach ($record->location->url as $urllocation) {
					if (preg_match('@^(http.*)@i',(string) $urllocation,$matches)) {
						$url="'".addslashes($matches[1])."'";
						//echo "URL MATCH: ".(string) $record->location->url." $url <br>";
					}
				}
			}
			if (isset($record->note)) {
				foreach ($record->note as $note) {
					if (preg_match('@^(doi\:.*)@i',(string) $note,$matches)) {
						$doi="'".addslashes($matches[1])."'";
					}
				}
			}
			
			if (isset($record->relatedItem)) { //because not all refs have this
				foreach ($record->relatedItem as $relatedItem) {
					switch((string) $relatedItem['type']) { // Get attributes as element indices
						case 'host':
							$journaltitle="'".addslashes($relatedItem->titleInfo->title)."'";
							if (isset($relatedItem->genre)) {
								foreach ($relatedItem->genre as $genrefield) {
									switch((string) $genrefield['authority']) {
										case 'marcgt':
											$referencetype="'".addslashes($genrefield)."'";
									}
								}
							}
								break;
					}
				}
			}
			foreach ($record->name as $name) {
				switch((string) $name['type']) {
					case 'personal':
						$authornumber++;
						$first="";
						$middle="";
						$last="";
						foreach ($name->namePart as $namePart) {
							switch((string) $namePart['type']) {
								case 'given':
									if (strlen($first)==0) {
										$first=(string) $namePart;
									}
									else if (strlen($middle)==0) {
										$middle=(string) $namePart;
									}
									break;
								case 'family':
									$last=(string) $namePart;
									break;
							}
						}
							$slashedname="'".addslashes($last)."'"; //In case there's an apostrophe in the name.
						$auquery="SELECT person_id, person_first, person_middle, person_last FROM person WHERE person_last ILIKE $slashedname";
						$ausearch = pg_query($db,$auquery) or die("Couldn't query the database.");
						$numberofmatches=0;
						$matchingarray[]="";
						for ($lt = 0; $lt < pg_numrows($ausearch); $lt++) {
							$nameupdate=0;
							$validmatch=True;
							$longerfirst=pg_fetch_result($ausearch,$lt,1);
							$longermiddle=pg_fetch_result($ausearch,$lt,2);
							if (min(strlen($first),strlen(pg_fetch_result($ausearch,$lt,1)))>0) { //There is a first name to match
								$pattern='@'.$first[0].'@i'; //just match first letter
								$target=pg_fetch_result($ausearch,$lt,1); 
								if(!preg_match($pattern,$target[0],$matches)) {//just match first letter
									$validmatch=False;
								}
								else if (strlen($first)>strlen(pg_fetch_result($ausearch,$lt,1))) {
									$longerfirst=$first;
									$nameupdate=1;
								}
							}
							if (min(strlen($middle),strlen(pg_fetch_result($ausearch,$lt,2)))>0) { //There is a middle name to match
								$pattern='@'.$middle[0].'@i'; //just match first letter
								$target=pg_fetch_result($ausearch,$lt,2); 
								if(!preg_match($pattern,$target[0],$matches)) { //just match first letter
									$validmatch=False;
								}
								else if (strlen($first)>strlen(pg_fetch_result($ausearch,$lt,1))) {
									$longermiddle=$middle;
									$nameupdate=1;
								}
							}
							if ($validmatch) {
								$numberofmatches++;
								$matchingarray[$numberofmatches]=array(pg_fetch_result($ausearch,$lt,0),$longerfirst,$longermiddle,pg_fetch_result($ausearch,$lt,3),$nameupdate);
							}
						}
							if ($numberofmatches>1) {
								$problemauthors="$problemauthors"."<br>Multiple matches to person $first $middle $last: ";
								for ($i=1;$i<=$numberofmatches;$i++) {
									$problemauthors="$problemauthors"."<br>\"".implode(" ",$matchingarray[$i])."\"";
//									echo "$problemauthors";
								}
						//die("Multiple matches to person $first $middle $last, email bcomeara@nescent.org for help");
							}
							else if ($numberofmatches==1) {
//								echo "ID".$matchingarray[1][0];
//								echo " ".$matchingarray[1][1];
//								echo " ($first) ".$matchingarray[1][2];
//								echo " ($middle) ".$matchingarray[1][3]." ($last)<br>";
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
									//echo "<p>$updatequery<br>";
									pg_query($db,$updatequery);
								}
								$authorarray[$authornumber]=$matchingarray[1][0];
							}
							else {
								
								$res=pg_query($db,"SELECT nextval('person_person_id_seq') as key");
								$row=pg_fetch_array($res, 0);
								$key=$row['key'];
						//now we have the serial value in $key, let's do the insert
								$sfirstname="'".addslashes($first)."'";
								$smiddlename="'".addslashes($middle)."'";
								$slastname="'".addslashes($last)."'";
								//echo "INSERT INTO person (person_id, person_first, person_middle, person_last) VALUES ($key, $sfirstname, $smiddlename, $slastname)<br>";
								pg_query($db,"INSERT INTO person (person_id, person_first, person_middle, person_last) VALUES ($key, $sfirstname, $smiddlename, $slastname)");						 
								
						//echo "   $first $middle $last<br>";
								$authorarray[$authornumber]=$key; //New key for added name
							}	
							break;
				}
			}
			$modsstring=$record->asXML();
			//echo "$articletitle <br>";
			//echo "$vol ( $issue )<br>";
			//echo "$modsstring <br>";
			$numref++;
			$existingrefcount=0;
			//echo "Ref $numref: $year \"$articletitle\". $journaltitle $vol($issue): $firstpage-$lastpage<br>URL: $url<br>DOI: $doi<br>PMID: $pmid<br>$modsstring<br><p>Author id = $authorarray[1]<p>";
			if ($authorarray[1]>0) {
				$existingrefquery="SELECT count(reference_id) FROM reference, referencetoperson, person WHERE (reference_title ILIKE $articletitle) AND (referencetoperson_person = person_id) AND (referencetoperson_reference = reference_id) AND (person_id={$authorarray[1]})";
				//echo "$existingrefquery <br>";
				$existingrefcheck=pg_query($db,$existingrefquery);
				$existingrefcount=pg_fetch_result($existingrefcheck,0,0);
				//echo "<p>$existingrefquery</p><br>Count: $existingrefcount<br>";
				
			}
			//echo "<br>existingrefcount = $existingrefcount and strlen(problemauthors) = ".strlen($problemauthors)."<br>";
			if ($existingrefcount==0 && strlen($problemauthors)==0) {
				//echo "<br><b>Above is new</b><p>";
				$res=pg_query($db,"SELECT nextval('reference_reference_id_seq') as key");
				$row=pg_fetch_array($res, 0);
				$key=$row['key'];
						// now we have the serial value in $key, let's do the insert
				if (strlen($modsstring)==0) {
					$modsstring="NULL";
				}
				else {
					$modsstring="'".addslashes($modsstring)."'";
				}
				$newquery="INSERT INTO reference (reference_id, reference_title, reference_type, reference_publicationdate, reference_publicationname, reference_volume, reference_issue, reference_startpage, reference_endpage, reference_xml, reference_url, reference_doi, reference_pmid, reference_addedby, reference_approved) VALUES ('$key', $articletitle, $referencetype, $year, $journaltitle, $vol, $issue, $firstpage, $lastpage, $modsstring, $url, $doi, $pmid, $personid, $approved)";
				//echo "$newquery<br>";
				pg_query($db,$newquery) or die ("Problem connecting");						
				$newreferences="$newreferences"."<p>Ref $numref: $year \"$articletitle\". $journaltitle $vol($issue): $firstpage-$lastpage</p>";
				//echo "NEW REF <p>Ref $numref: $year \"$articletitle\". $journaltitle $vol($issue): $firstpage-$lastpage</p>";
				
				
				for ($i=1; $i<=$authornumber; $i++) {
					$referencetopersoninsert="INSERT INTO referencetoperson (referencetoperson_id, referencetoperson_reference, referencetoperson_person, referencetoperson_authororder, referencetoperson_addedby, referencetoperson_approved) VALUES (nextval('referencetoperson_referencetoperson_id_seq'), '{$key}', '{$authorarray[$i]}', '{$i}', '$personid', '$approved')" or die ("Problem connecting");
					//echo "<p>$referencetopersoninsert</p><br>";
					pg_query($db,$referencetopersoninsert); 
				}
				
				
			}
			else if (strlen($problemauthors)==0) {
				$oldreferences="$oldreferences"."<p>Ref $numref: $year \"$articletitle\". $journaltitle $vol($issue): $firstpage-$lastpage</p>";
				
			}
			else {
				$problemreferences="$problemreferences"."<p><hr> $problemauthors <br> $year \"$articletitle\". $journaltitle $vol($issue): $firstpage-$lastpage</p>";
				
				
			}
			
		}
		unlink($outputFile);
		if (strlen($problemreferences)>0) {
			echo "<h2><hr>References with some problems and so not entered</h2>";
			echo ereg_replace("'","",$problemreferences);
		}
		echo "<div align=left><h2><hr>References added to the database</h2>";
		if (strlen($newreferences)==0) {
			echo "<p>Nothing added</p>";
		}
		else {
			echo ereg_replace("'","",$newreferences);
		}
		echo "<h2><hr>References submitted but already in the database</h2>";
		if (strlen($oldreferences)==0) {
			echo "<p>No overlap</p>";
		}
		else {
			echo ereg_replace("'","",$oldreferences);
		}
		
    }
}
else
{
	echo "Invalid file";
}
include('templates/template_pageend.php');
?>
