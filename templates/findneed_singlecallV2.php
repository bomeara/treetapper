<?PHP
include ('dblogin.php'); //connection in $db
$circles="<circles>\n"; //global circles
$markers="<markers>\n"; //global markers
$polylines="<polylines>\n"; //global polylines
$polylinesall="";
$polylinessoftware="";
$polylinesmethods="";
$totaldepthcount=""; //global depth;
$fullstep=1.0;
$partstep=0.3;
$colorarray=array("#FF0000",  "#0000FF", "#000000", "#800080");
$colornames=array("red", "blue", "black",   "purple");

function plottree ($parentradius, $parentangle, $currentdepthcount, $parentcirclefraction, $parentfromstring, $parentwherestring, $parentlabel, $parentactualmethodstring, $parentactualsoftwarestring, $parentmethodcount, $parentsoftwarecount) {	
	global $totaldepthcount, $circles, $markers, $polylines, $polylinesall, $polylinessoftware, $polylinesmethods, $fullstep, $partstep, $colorarray, $colornames, $tablenamesarray, $tableoptionsarray, $db;
	$currentradius=$parentradius;
	$currentangle=$parentangle;
	$currentfraction=$parentfraction;
	$currentlabel=$parentlabel;
	$currentactualmethodstring=$parentactualmethodstring;
	$currentactualsoftwarestring=$parentactualsoftwarestring;
	$methodcount=$parentmethodcount;
	$softwarecount=$parentsoftwarecount;
	//echo "\n<br>____________________________________________<br>\nIn plottree function: ";
	//echo "$parentradius, $parentangle, $currentdepthcount, $parentcirclefraction, $parentfromstring, $parentwherestring \n<br>";
	if ($currentdepthcount<$totaldepthcount) { //not at a terminal yet
		$finalstep=FALSE;
		if ($currentdepthcount==-1+$totaldepthcount) {
			$finalstep=TRUE;
		}
		$currentfromstring=$parentfromstring;
		$currentwherestring=$parentwherestring;
		
		$tablename=$tablenamesarray[$currentdepthcount];
		$fulltablename=$tablename;
		if (stripos($tablename,"char")!==false) {
			$fulltablename=charactertype;
		}
		//echo "<br>\ntablename = ".$tablename;
		$currentselectstring="";
		$columnname="";
		$actualcolumnname="";
		if ((stripos($tablename,"general")!==false) || (stripos($tablename,"posed")!==false) || (stripos($tablename,"char")!==false)) { //use the generaltoposedtochartype view rather than primitive tables
			if ((stripos($tablename,"method")!==false) || (stripos($currentfromstring,"method")!==false)) { //have searched on method, perhaps with program
				if ((stripos($tablename,"program")!==false) || (stripos($currentfromstring,"program")!==false)) { //have searched on method and program
					$columnname="generaltoposedtochartype_".$tablename;
					if (0==preg_match("/generaltoposedtochartype/", $currentfromstring)) { //don't want to add this twice
						$currentfromstring.="generaltoposedtochartype, ";
					}
					if (0==preg_match("/$fulltablename/", $currentfromstring)) { //don't want to add this twice
						$currentfromstring.=$fulltablename.", ";
					}
					if (0==preg_match("/programtomethodtocharactercombination/", $currentfromstring)) { //don't want to add this twice
						$currentfromstring.="programtomethodtocharactercombination, ";
						$currentwherestring.=" AND programtomethodtocharactercombination_program=program_id AND programtomethodtocharactercombination_methodtocharactercombination=programtomethodtocharactercombination_methodtocharactercombination_id";
					}
					$currentwherestring.=" AND ".$fulltablename."_id = ".$columnname;
					if ($finalstep) {
						$currentselectstring="SELECT DISTINCT ".$columnname.", ".$fulltablename."_name ";
					}
					$actualcolumnname=$tablename."_id";
					
					
				}
				else { //have searched on method but not program
					//connect method with posed question
					$columnname="generaltoposedtochartype_".$tablename;
					if (0==preg_match("/generaltoposedtochartype/", $currentfromstring)) { //don't want to add this twice
						$currentfromstring.="generaltoposedtochartype, ";
					}
					if (0==preg_match("/$fulltablename/", $currentfromstring)) { //don't want to add this twice
						$currentfromstring.=$fulltablename.", ";
					}
					if (0==preg_match("/methodtoposedquestion/", $currentfromstring)) { //don't want to add this twice
						$currentfromstring.="methodtoposedquestion, ";
						$currentwherestring.=" AND methodtoposedquestion_method=method_id AND methodtoposedquestion_posedquestion=generaltoposedtochartype_posedquestion";
					}
					$currentwherestring.=" AND ".$fulltablename."_id = ".$columnname;
					if ($finalstep) {
						$currentselectstring="SELECT DISTINCT ".$columnname.", ".$fulltablename."_name ";
					}
					$actualcolumnname=$tablename."_id";
					
				}
				
			}
			else { //have not searched on method or program. Yet.
				$columnname="generaltoposedtochartype_".$tablename;
				if (0==preg_match("/generaltoposedtochartype/", $currentfromstring)) { //don't want to add this twice
					$currentfromstring.="generaltoposedtochartype, ";
				}
				if (0==preg_match("/$fulltablename/", $currentfromstring)) { //don't want to add this twice
					$currentfromstring.=$fulltablename.", ";
				}
				$currentwherestring.=" AND ".$fulltablename."_id = ".$columnname;
				if ($finalstep) {
					$currentselectstring="SELECT DISTINCT ".$columnname.", ".$fulltablename."_name ";
				}
				$actualcolumnname=$tablename."_id";
				
			}
		}
		else { //regular table
			if ($finalstep) {
				$currentselectstring="SELECT DISTINCT ".$tablename."_id, ".$tablename."_name ";
			}
			$currentfromstring.=$tablename.", ";
			$columnname=$tablename."_id";
			$actualcolumnname=$columnname;
		}
		
		
		
		
		if ($finalstep) {
			$startlat=$parentradius*sin(deg2rad($parentangle));
			$startlng=$parentradius*cos(deg2rad($parentangle));	
		}
		if ($tableoptionsarray[$currentdepthcount]==0) { //show all descendant nodes
			if ($finalstep) {
				$currentradius=$parentradius+$fullstep;
			}
			$finalfromstring=$currentfromstring;
			$finalwherestring=$currentwherestring;
			if (preg_match("/, $/", $currentfromstring)) {
				$finalfromstring=substr($currentfromstring,0,-2);
			}
			if (preg_match("/^ WHERE $/i", $currentwherestring)) {
				$finalwherestring=" ";
			}
			if (preg_match("/^ WHERE  AND/", $currentwherestring)) {
				$currentwherestring2=substr($currentwherestring,11);
				$finalwherestring=" WHERE ".$currentwherestring2;
				$currentwherestring=$finalwherestring;
			}		
			if ($finalstep) {
				$currenttotalsql=$currentselectstring.$finalfromstring.$finalwherestring; //Might need to chop of trailing/leading commas, ANDs, etc.
				//echo "<br>\n145: ".$currenttotalsql;
				$currentquery=pg_query($db, $currenttotalsql);
			//echo '<br>$tableoptionsarray[$currentdepthcount]='.$tableoptionsarray[$currentdepthcount]."<br>\ncurrenttotalsql=".$currenttotalsql."<br>\npgnumrows = ".pg_numrows($currentquery)."<br>\n";
				for ($option=0;$option<pg_numrows($currentquery);$option++) {
					$currentlabel=$parentlabel;
					$methodlabel="";
					$softwarelabel="";
					$optionid=pg_fetch_result($currentquery,$option,0);
					$optionname=pg_fetch_result($currentquery,$option,1);
				//$currentlabel.=$tablename.$optionname;
					$currentfraction=$parentcirclefraction/pg_numrows($currentquery); //would be more efficient to calculate this once above, but could have division by zero
				//this subtree must be contained from within +/- 0.5*(360*parentfraction) of the parent angle. With margins.
					$minimumangle=$parentangle-0.5*(360.0*$parentcirclefraction);
					$fullanglestep=360.0*$parentcirclefraction/pg_numrows($currentquery);				
					$currentangle=$minimumangle+$option*$fullanglestep+0.5*$fullanglestep; //The half step is to add a margin.
				//echo "<br>\ncurrentangle=".$currentangle." currentfraction=".$currentfraction." parentfraction=".$parentcirclefraction." minimumangle=".$minimumangle." fullanglestep=".$fullanglestep." pg_numrows(currentquery)=".pg_numrows($currentquery);
					
					
					$currentactualmethodstring="";
					$methodcount=0;
					if (strlen($parentactualmethodstring)>0) {
						$currentactualmethodstring.=$parentactualmethodstring;
						if ((0==preg_match("/applicationkind/",$tablename)) && (0==preg_match("/platform/",$tablename)) && (0==preg_match("/program/",$tablename)) && (0==preg_match("/treeformat/",$tablename)) && (0==preg_match("/dataformat/",$tablename))) { //because we don't care about applicationkind, etc. for just methods
							$currentactualmethodstring.=" AND actualmethods_".substr($actualcolumnname,0,-3)."=".$optionid;
						}
					//echo "<br>\n".$currentactualmethodstring;
						$methodquery=pg_query($db, $currentactualmethodstring);
						if (pg_numrows($methodquery)>=1) {
							$methodcount=pg_numrows($methodquery);
							if ($methodcount==0) {
								$currentactualmethodstring=""; //So we don't bother searching in the future
								$methodlabel.="none";
							}
							else {
								for ($i=0;$i<pg_numrows($methodquery);$i++) {
									$methodlabel.="AHREFmethod/".pg_fetch_result($methodquery,$i,0)."UNAHREF".pg_fetch_result($methodquery,$i,1)."ENDHREFLF";
								}
							}
						}
					}
					
					
					$currentactualsoftwarestring="";
					$softwarecount=0;
					if (strlen($parentactualsoftwarestring)>0) {
						$currentactualsoftwarestring.=$parentactualsoftwarestring;
						$currentactualsoftwarestring.=" AND actualsoftware_".substr($actualcolumnname,0,-3)."=".$optionid;
						$softwarequery=pg_query($db, $currentactualsoftwarestring);
						if (pg_numrows($softwarequery)>=1) {
							$softwarecount=pg_numrows($softwarequery);
							if ($softwarecount==0) {
								$currentactualsoftwarestring=""; //So we don't bother searching in the future
								$softwarelabel.="none";
							}
							else {
								for ($i=0;$i<pg_numrows($softwarequery);$i++) {
									$softwarelabel.="AHREFprogram/".pg_fetch_result($softwarequery,$i,0)."UNAHREF".pg_fetch_result($softwarequery,$i,1)."ENDHREFLF";
								}
							}
							
						}
					}
					
				//set the colortype (0=no methods or software, 1=methods only, 2=methods+software);				
					$colortype=0;
					if ($methodcount>0) {
						if ($softwarecount>0) {
							$colortype=2;
						}
						else {
							$colortype=1;
						}
					}
					if ($currentdepthcount>0) {
						$currentlabel.="+";
					}
					$currentlabel.="BOLD".$tablename."UNBOLD: ".$optionname." (".$methodcount." methods, ".$softwarecount." programs)LF";
					$tooltip="M: ".$methodcount.", S: ".$softwarecount.", ".$tablename.": ".$optionname;
					
				//Now, add the plotting info
					$endlat=$currentradius*sin(deg2rad($currentangle));
					$endlng=$currentradius*cos(deg2rad($currentangle));
					
					/*				if ($colortype>1) { //has software
						$polylinessoftware.="<polyline startlat=\"".$startlat."\" startlng=\"".$startlng."\" endlat=\"".$endlat."\" endlng=\"".$endlng."\" color=\"".$colorarray[3]."\" weight=\"8\" opacity=\"1\"/>\n";
					}
					if ($colortype>0) { //has methods
						$polylinesmethods.="<polyline startlat=\"".$startlat."\" startlng=\"".$startlng."\" endlat=\"".$endlat."\" endlng=\"".$endlng."\" color=\"".$colorarray[1]."\" weight=\"4\" opacity=\"0.5\"/>\n";
					}
					*/
					$polylinesall.="<polyline startlat=\"".$startlat."\" startlng=\"".$startlng."\" endlat=\"".$endlat."\" endlng=\"".$endlng."\" color=\"".$colorarray[$colortype]."\" weight=\"1\" opacity=\"1\"/>\n";
					
					$markers.="<marker lat=\"".$endlat."\" lng=\"".$endlng."\" colorname=\"".$colornames[$colortype]."\"  methodcount=\"".$methodcount."\" softwarecount=\"".$softwarecount."\" label=\"".$currentlabel."\" methodlabel=\"".$methodlabel."\" softwarelabel=\"".$softwarelabel."\" tooltip=\"".$tooltip."\"/>\n"; //also add element name and path name
					//echo "\n<br> Markers: $markers";
				}
			}
			//echo "Now at next plot tree";
			plottree($currentradius,$currentangle,1+$currentdepthcount, $currentfraction, $currentfromstring, $currentwherestring." AND ".$columnname." = ".$optionid, $currentlabel, $currentactualmethodstring, $currentactualsoftwarestring, $methodcount, $softwarecount); //recursive
			
			
		}
		else {
			//echo $tableoptionsarray[$currentdepthcount]."<br>\n";
			if ($finalstep) {
				$currentradius=$parentradius+$partstep;
			}
			$currentwherestring.=" AND ".$columnname."=".$tableoptionsarray[$currentdepthcount];
			if ($finalstep) {
				$currentangle=$parentangle;
				$currentfraction=$parentcirclefraction;
			}
			$finalfromstring=$currentfromstring;
			$finalwherestring=$currentwherestring;
			if (preg_match("/, $/", $currentfromstring)) {
				$finalfromstring=substr($currentfromstring,0,-2);
			}
			if (preg_match("/^ WHERE  AND/", $currentwherestring)) {
				$currentwherestring2=substr($currentwherestring,11);
				$finalwherestring=" WHERE ".$currentwherestring2;
				$currentwherestring=$finalwherestring;
			}
			if ($finalstep) {
				$currenttotalsql=$currentselectstring.$finalfromstring.$finalwherestring; //Might need to chop of trailing/leading commas, ANDs, etc.
				//echo "<br>\nELSE: ".$currenttotalsql;
				$currentquery=pg_query($db, $currenttotalsql);
			//echo "<br>\nCurrentangle=".$currentangle;
				//Now, add the plotting info
				if (pg_numrows($currentquery)==1) {
					$currentlabel=$parentlabel;
					$methodlabel="";
					$softwarelabel="";
					$optionid=pg_fetch_result($currentquery,$option,0);
					$optionname=pg_fetch_result($currentquery,$option,1);
					$currentactualmethodstring="";
					$methodcount=0;
					if (strlen($parentactualmethodstring)>0) {
						$currentactualmethodstring.=$parentactualmethodstring;
						if ((0==preg_match("/applicationkind/",$tablename)) && (0==preg_match("/platform/",$tablename)) && (0==preg_match("/program/",$tablename)) && (0==preg_match("/treeformat/",$tablename)) && (0==preg_match("/dataformat/",$tablename))) { //because we don't care about applicationkind, etc. for just methods
							$currentactualmethodstring.=" AND actualmethods_".substr($actualcolumnname,0,-3)."=".$optionid;
						}
					//echo "<br>\nELSE ".$currentactualmethodstring;
						$methodquery=pg_query($db, $currentactualmethodstring);
						if (pg_numrows($methodquery)>=1) {
							$methodcount=pg_numrows($methodquery);
							if ($methodcount==0) {
								$currentactualmethodstring=""; //So we don't bother searching in the future
								$methodlabel.="none";
							}
							else {
								
								for ($i=0;$i<pg_numrows($methodquery);$i++) {
									$methodlabel.="AHREFmethod/".pg_fetch_result($methodquery,$i,0)."UNAHREF".pg_fetch_result($methodquery,$i,1)."ENDHREFLF";
								}
							}
							
						}
					}
					
					
					$currentactualsoftwarestring="";
					$softwarecount=0;
					if (strlen($parentactualsoftwarestring)>0) {
						$currentactualsoftwarestring.=$parentactualsoftwarestring;
						$currentactualsoftwarestring.=" AND actualsoftware_".substr($actualcolumnname,0,-3)."=".$optionid;
						$softwarequery=pg_query($db, $currentactualsoftwarestring);
						if (pg_numrows($softwarequery)>=1) {
							$softwarecount=pg_numrows($softwarequery);
							if ($softwarecount==0) {
								$softwarelabel.="none";
							}
							else {
								for ($i=0;$i<pg_numrows($softwarequery);$i++) {
									$softwarelabel.="AHREFprogram/".pg_fetch_result($softwarequery,$i,0)."UNAHREF".pg_fetch_result($softwarequery,$i,1)."ENDHREFLF";							}
							}
							
						}
					}
					
				//set the colortype (0=no methods or software, 1=methods only, 2=methods+software);				
					$colortype=0;
					if ($methodcount>0) {
						if ($softwarecount>0) {
							$colortype=2;
						}
						else {
							$colortype=1;
						}
					}
				//Make the gray lines to show things we're omitting
					$missingquery=pg_query($db, "SELECT * FROM $fulltablename");
					for ($missing=0;$missing<pg_numrows($missingquery);$missing++) {
						$minimumangle=$parentangle-0.5*(360.0*$parentcirclefraction);
						$fullanglestep=360.0*$parentcirclefraction/pg_numrows($missingquery);				
						$missingangle=$minimumangle+$missing*$fullanglestep+0.5*$fullanglestep;
						$endlat=$currentradius*sin(deg2rad($missingangle));
						$endlng=$currentradius*cos(deg2rad($missingangle));
						$polylinesall.="<polyline startlat=\"".$startlat."\" startlng=\"".$startlng."\" endlat=\"".$endlat."\" endlng=\"".$endlng."\" color=\"#C0C0C0\" weight=\"1\"/>\n";
					}
					
					$endlat=$currentradius*sin(deg2rad($currentangle));
					$endlng=$currentradius*cos(deg2rad($currentangle));
					$currentlabel.="BOLD".$tablename."UNBOLD: ".$optionname." (".$methodcount." methods, ".$softwarecount." programs)LF";
					$tooltip="M: ".$methodcount.", S: ".$softwarecount.", ".$tablename.": ".$optionname;
					/*				if ($colortype>1) { //has software
						$polylinessoftware.="<polyline startlat=\"".$startlat."\" startlng=\"".$startlng."\" endlat=\"".$endlat."\" endlng=\"".$endlng."\" color=\"".$colorarray[3]."\" weight=\"8\" opacity=\"1\"/>\n";
					}
					if ($colortype>0) { //has methods
						$polylinesmethods.="<polyline startlat=\"".$startlat."\" startlng=\"".$startlng."\" endlat=\"".$endlat."\" endlng=\"".$endlng."\" color=\"".$colorarray[1]."\" weight=\"4\" opacity=\"0.5\"/>\n";
					}
					*/
					$polylinesall.="<polyline startlat=\"".$startlat."\" startlng=\"".$startlng."\" endlat=\"".$endlat."\" endlng=\"".$endlng."\" color=\"".$colorarray[$colortype]."\" weight=\"1\" opacity=\"1\"/>\n";
					
					$markers.="<marker lat=\"".$endlat."\" lng=\"".$endlng."\" colorname=\"".$colornames[$colortype]."\" methodcount=\"".$methodcount."\" softwarecount=\"".$softwarecount."\" label=\"".$currentlabel."\" methodlabel=\"".$methodlabel."\" softwarelabel=\"".$softwarelabel."\" tooltip=\"".$tooltip."\"/>\n"; //also add element name and path name
				}
			}
			//echo "now at next plot tree";
			plottree($currentradius,$currentangle,1+$currentdepthcount, $currentfraction, $currentfromstring, $currentwherestring, $currentlabel, $currentactualmethodstring, $currentactualsoftwarestring, $methodcount, $softwarecount); //recursive
			
			
		}
		
		
	}
	
}


if (strlen($_GET['tablenames'])>0 && strlen($_GET['tableoptions'])>0) {
	$tablenamesstring=str_replace("charactertype_","char",$_GET['tablenames']);
	$tablenamesarray = explode(',', $tablenamesstring);
	//echo "\n<br>count(tablenamesarray) after explode = ".count($tablenamesarray);
	for ($i=0;$i<count($tablenamesarray);$i++) {
		//echo "\n<br>    ".$i."=".$tablenamesarray[$i];
	}
	$tableoptionsarray = explode(',', $_GET['tableoptions']);
	$totaldepthcount=count($tablenamesarray);
	$runningradius=0;
	$numberofdivisions=1;
	if (count($tableoptionsarray)==$totaldepthcount) { //only continue if same number
		plottree (0, 0, 0, 1, " FROM ", " WHERE ", "", "SELECT DISTINCT method_id, method_name FROM actualmethods, method WHERE actualmethods_method=method_id ","SELECT DISTINCT program_id, program_name FROM actualsoftware, program WHERE actualsoftware_program=program_id ", 1, 1);
		/*
		 $matchtablequeryselectcolumn="SELECT DISTINCT "; //First, get the tree of all possible, based on creating a large table. Then find entries here that match the actualmethods and actualsoftware views
		 $matchtablequerywhere=" WHERE ";
		 for ($i=0;$i<$totaldepthcount;$i++) {
			 if ($tableoptionsarray[$i]==0) {
				 $matchtablequeryselectcolumn.="_____ADD HERE_____";
			 }
			 else {
				 
			 }
		 }
		 $matchtablequery=$matchtablequerywhere." FROM generaltoposedtochartype, crossjoinoptions, method, software ".$matchtablequeryqhere;
		 */
		$output='<?xml version="1.0"?><document>';
		
		for ($i=0;$i<$totaldepthcount;$i++) {
			if ($tableoptionsarray[$i]==0) { //we want to show all options
				$runningradius+=$fullstep;
			}
			else { //we only want one option
				$runningradius+=$partstep;
			}
			$circles.="<circle radius=\"".$runningradius."\" label=\"".$tablenamesarray[$i]."\"/>\n";
		}
		$circles.="</circles>\n";
		$markers.="</markers>\n";
		$polylines.=$polylinessoftware; //Print out software background lines first
		$polylines.=$polylinesmethods; //Print out method lines next
		$polylines.=$polylinesall; //print out the rest
		$polylines.="</polylines>\n";
		$output.=$circles;
		$output.=$markers;
		$output.=$polylines;
		$output.="</document>";
		
		echo $output;	
	}
	else {
		echo "error";
	}
}
?>
