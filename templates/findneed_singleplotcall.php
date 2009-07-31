<?PHP
include ('dblogin.php'); //connection in $db

$xmlreturn='<?xml version="1.0"?><document>';
$xmlreturn.="\n<polylines>\n";
$fromtablearray = explode(',', $_GET['fromtable']);
$wheretablearray = explode(',', $_GET['wheretable']);
$whereidarray = explode(',', $_GET['whereid']);
$validinput=1;
//do this for security
for ($i=0; $i<count($fromtablearray); $i++) {
	if(!is_numeric($fromtablearray[$i])) {
		$validinput=0;
		echo "invalid input: fromtablearray[i]=".$fromtablearray[$i];
	}
}
for ($i=0; $i<count($wheretablearray); $i++) {
	if(!is_numeric($wheretablearray[$i])) {
		$validinput=0;
		echo "invalid input: wheretablearray[i]=".$wheretablearray[$i];
	}
}
for ($i=0; $i<count($whereidarray); $i++) {
	if(!is_numeric($whereidarray[$i])) {
		$validinput=0;
		echo "invalid input";
	}
}
if ($validinput==1) {  //it's important here to have ways to prevent hacking (i.e., don't pass sql statements back and forth)
	//idea is to get what would be the parentwhere and from strings, then use the plot function below
	$parentradius=$_GET['parentradius'];
	$parentangle=$_GET['parentangle'];
	$currentdepthcount=$_GET['currentdepthcount'];
	$parentcirclefraction=$_GET['parentcirclefraction'];
	$parentmethodcount=$_GET['parentmethodcount'];
	$parentsoftwarecount=$_GET['parentsoftwarecount'];
	$parentHtmlCitationcount=$_GET['parentHtmlCitationcount'];
	$parentPdfCitationcount=$_GET['parentPdfCitationcount'];
	$parentlabel=$_GET['parentlabel'];
	$fullstep=1.0;
	$partstep=0.3;
	$currentdepthcount=-1+count($fromtablearray);
	$fromstring="FROM generaltoposedtochartype, methodtoposedquestion, generalquestion, posedquestion, method";
	$wherestring=" WHERE generaltoposedtochartype_generalquestion=generalquestion_id AND generaltoposedtochartype_posedquestion=posedquestion_id AND methodtoposedquestion_method=method_id AND methodtoposedquestion_posedquestion=posedquestion_id";
	$notincluedtableids=array(1,2,8,9,13,21,24,35,36);
	$charactertypeids=array(4.1,4.2,4.3);
	$allowablefromids=array(1,2,4,1,4,2,4.3,8,9,12,13,19,21,22,24,32,35,36);
	for ($i=0; $i<count($fromtablearray); $i++) {
		if (in_array($fromtablearray[$i],$notincluedtableids)) {
			$tablenamequery=pg_query($db,"SELECT tablelist_name FROM tablelist WHERE tablelist_id=".$fromtablearray[$i]);
			$fromstring.=pg_fetch_result($tablenamequery,0,0).", ";
			if ($whereidarray[$i]>0) {
				$wherestring.=" AND ".pg_fetch_result($tablenamequery,0,0)."_id = ".$whereidarray[$i];
			}
		}
		else if (in_array($fromtablearray[$i],$charactertypeids)) {
			$charnumber=sprintf("%d",10*($fromtablearray[$i]-4.0));
			if ($whereidarray[$i]>0) {
				$wherestring.=" AND generaltoposedtochartype_char".$charnumber."=".$whereidarray[$i];
			}
		}
	}
	$fulltablename="";
	$tablename="";
	if ($fromtablearray[-1+count($fromtablearray)]>4 && $fromtablearray[-1+count($fromtablearray)]<4.4) {
		$fulltablename='charactertype';
		$charnumber=sprintf("%d",10*($fromtablearray[$i]-4.0));
		$tablename="char".$charnumber;
	}
	else {
		$tablenamequery=pg_query($db,"SELECT tablelist_name FROM tablelist WHERE tablelist_id=".$fromtablearray[-1+count($fromtablearray)]);
		$tablename=pg_fetch_result($tablenamequery,0,0);
		$fulltablename=$tablename;
	}
	$currentselectstring="";
	$columnname="";
	$actualcolumnname="";
	if ((stripos($tablename,"general")!==false) || (stripos($tablename,"posed")!==false) || (stripos($tablename,"char")!==false)) { 
		if ((stripos($tablename,"method")!==false) || (stripos($currentfromstring,"method")!==false)) { 
			if ((stripos($tablename,"program")!==false) || (stripos($currentfromstring,"program")!==false)) { 
				$columnname="generaltoposedtochartype_".$tablename;
				$currentselectstring="SELECT DISTINCT ".$columnname.", ".$fulltablename."_name ";
				$actualcolumnname=$tablename."_id";
			}
			else { 
				$columnname="generaltoposedtochartype_".$tablename;
				$currentselectstring="SELECT DISTINCT ".$columnname.", ".$fulltablename."_name ";
				$actualcolumnname=$tablename."_id";
			}
		}
		else {
			$columnname="generaltoposedtochartype_".$tablename;
			$currentselectstring="SELECT DISTINCT ".$columnname.", ".$fulltablename."_name ";
			$actualcolumnname=$tablename."_id";
		}
	}
	else { //regular table
		$currentselectstring="SELECT DISTINCT ".$tablename."_id, ".$tablename."_name ";
		$columnname=$tablename."_id";
		$actualcolumnname=$columnname;
	}
	
	$startlat=$parentradius*sin(deg2rad($parentangle));
	$startlng=$parentradius*cos(deg2rad($parentangle));		
	
	
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	
	// Do work below here, including updating method/software/ref count
	
	
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	
	
	if ($whereidarray[$currentdepthcount]==0) { //show all descendant nodes
		$currentradius=$parentradius+$fullstep;
		$currenttotalsql=$currentselectstring.$finalfromstring.$finalwherestring; //Might need to chop of trailing/leading commas, ANDs, etc.
		echo "<br>\n".$currenttotalsql;
		$currentquery=pg_query($db, $currenttotalsql);
		for ($option=0;$option<pg_numrows($currentquery);$option++) {
			$currentlabel=$parentlabel;
			$methodlabel="";
			$softwarelabel="";
			$optionid=pg_fetch_result($currentquery,$option,0);
			$optionname=pg_fetch_result($currentquery,$option,1);
			$currentfraction=$parentcirclefraction/pg_numrows($currentquery); //would be more efficient to calculate this once above, but could have division by zero
				//this subtree must be contained from within +/- 0.5*(360*parentfraction) of the parent angle. With margins.
			$minimumangle=$parentangle-0.5*(360.0*$parentcirclefraction);
			$fullanglestep=360.0*$parentcirclefraction/pg_numrows($currentquery);				
			$currentangle=$minimumangle+$option*$fullanglestep+0.5*$fullanglestep; //The half step is to add a margin.
			$methodcount=0;
			$softwarecount=0;
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
	//		$currentlabel.="BOLD".$tablename."UNBOLD: ".$optionname." (".$methodcount." methods, ".$softwarecount." programs)LF";
	//		$tooltip="M: ".$methodcount.", S: ".$softwarecount.", ".$tablename.": ".$optionname;
			
				//Now, add the plotting info
			$endlat=$currentradius*sin(deg2rad($currentangle));
			$endlng=$currentradius*cos(deg2rad($currentangle));
			
			
			$polylinesall.="<polyline startlat=\"".$startlat."\" startlng=\"".$startlng."\" endlat=\"".$endlat."\" endlng=\"".$endlng."\" colortype=\"".$colortype."\" color=\"".$colorarray[$colortype]."\" weight=\"1\" opacity=\"1\"/>\n";
			
			$markers.="<marker lat=\"".$endlat."\" lng=\"".$endlng."\" colortype=\"".$colortype."\" colorname=\"".$colornames[$colortype]."\"  methodcount=\"".$methodcount."\" softwarecount=\"".$softwarecount."\" label=\"".$currentlabel."\" methodlabel=\"".$methodlabel."\" softwarelabel=\"".$softwarelabel."\" tooltip=\"".$tooltip."\"/>\n"; //also add element name and path name

		}
		
	}
	else {
			//echo $tableoptionsarray[$currentdepthcount]."<br>\n";
		$currentradius=$parentradius+$partstep;
		$currentwherestring.=" AND ".$columnname."=".$tableoptionsarray[$currentdepthcount];
		$currentangle=$parentangle;
		$currentfraction=$parentcirclefraction;
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
				$polylinesall.="<polyline startlat=\"".$startlat."\" startlng=\"".$startlng."\" endlat=\"".$endlat."\" endlng=\"".$endlng."\" colortype=\"3\" color=\"#C0C0C0\" weight=\"1\"/>\n";
			}
			
			$endlat=$currentradius*sin(deg2rad($currentangle));
			$endlng=$currentradius*cos(deg2rad($currentangle));
			$currentlabel.="BOLD".$tablename."UNBOLD: ".$optionname." (".$methodcount." methods, ".$softwarecount." programs)LF";
			$tooltip="M: ".$methodcount.", S: ".$softwarecount.", ".$tablename.": ".$optionname;
			
			$polylinesall.="<polyline startlat=\"".$startlat."\" startlng=\"".$startlng."\" endlat=\"".$endlat."\" endlng=\"".$endlng."\" colortype=\"".$colortype."\" color=\"".$colorarray[$colortype]."\" weight=\"1\" opacity=\"1\"/>\n";
			
			$markers.="<marker lat=\"".$endlat."\" lng=\"".$endlng."\" colortype=\"".$colortype."\" colorname=\"".$colornames[$colortype]."\" methodcount=\"".$methodcount."\" softwarecount=\"".$softwarecount."\" label=\"".$currentlabel."\" methodlabel=\"".$methodlabel."\" softwarelabel=\"".$softwarelabel."\" tooltip=\"".$tooltip."\"/>\n"; //also add element name and path name
		}
		
	}
	$circles.="</circles>\n";
	$markers.="</markers>\n";
	$polylines.=$polylinessoftware; //Print out software background lines first
	$polylines.=$polylinesmethods; //Print out method lines next
	$polylines.=$polylinesall; //print out the rest
	$polylines.="</polylines>\n";
	$output.="<dimensions maxradius=\"".$maximumradius."\" />\n";
	$output.=$circles;
	$output.=$markers;
	$output.=$polylines;
	$output.="</document>";
	
	echo $output;	
}
/*	
	
	
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	
	///stuff below this line is old//////////////
	
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	
	global $totaldepthcount, $circles, $markers, $polylines, $polylinesall, $polylinessoftware, $polylinesmethods, $fullstep, $partstep, $colorarray, $colornames, $tablenamesarray, $tableoptionsarray, $db;
	//echo "\n<br>____________________________________________<br>\nIn plottree function: ";
	//echo "$parentradius, $parentangle, $currentdepthcount, $parentcirclefraction, $parentfromstring, $parentwherestring \n<br>";
	if ($currentdepthcount<$totaldepthcount) { //not at a terminal yet
		
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
					
					$currentselectstring="SELECT DISTINCT ".$columnname.", ".$fulltablename."_name ";
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
					
					$currentselectstring="SELECT DISTINCT ".$columnname.", ".$fulltablename."_name ";
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
				
				$currentselectstring="SELECT DISTINCT ".$columnname.", ".$fulltablename."_name ";
				$actualcolumnname=$tablename."_id";
				
			}
		}
		else { //regular table
			$currentselectstring="SELECT DISTINCT ".$tablename."_id, ".$tablename."_name ";
			$currentfromstring.=$tablename.", ";
			$columnname=$tablename."_id";
			$actualcolumnname=$columnname;
		}
		
		
		
		
		
		$startlat=$parentradius*sin(deg2rad($parentangle));
		$startlng=$parentradius*cos(deg2rad($parentangle));		
		if ($tableoptionsarray[$currentdepthcount]==0) { //show all descendant nodes
			$currentradius=$parentradius+$fullstep;
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
			$currenttotalsql=$currentselectstring.$finalfromstring.$finalwherestring; //Might need to chop of trailing/leading commas, ANDs, etc.
			//echo "<br>\n".$currenttotalsql;
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


				$polylinesall.="<polyline startlat=\"".$startlat."\" startlng=\"".$startlng."\" endlat=\"".$endlat."\" endlng=\"".$endlng."\" colortype=\"".$colortype."\" color=\"".$colorarray[$colortype]."\" weight=\"1\" opacity=\"1\"/>\n";

				$markers.="<marker lat=\"".$endlat."\" lng=\"".$endlng."\" colortype=\"".$colortype."\" colorname=\"".$colornames[$colortype]."\"  methodcount=\"".$methodcount."\" softwarecount=\"".$softwarecount."\" label=\"".$currentlabel."\" methodlabel=\"".$methodlabel."\" softwarelabel=\"".$softwarelabel."\" tooltip=\"".$tooltip."\"/>\n"; //also add element name and path name
				
				plottree($currentradius,$currentangle,1+$currentdepthcount, $currentfraction, $currentfromstring, $currentwherestring." AND ".$columnname." = ".$optionid, $currentlabel, $currentactualmethodstring, $currentactualsoftwarestring, $methodcount, $softwarecount); //recursive
			}
			
		}
		else {
			//echo $tableoptionsarray[$currentdepthcount]."<br>\n";
			$currentradius=$parentradius+$partstep;
			$currentwherestring.=" AND ".$columnname."=".$tableoptionsarray[$currentdepthcount];
			$currentangle=$parentangle;
			$currentfraction=$parentcirclefraction;
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
					$polylinesall.="<polyline startlat=\"".$startlat."\" startlng=\"".$startlng."\" endlat=\"".$endlat."\" endlng=\"".$endlng."\" colortype=\"3\" color=\"#C0C0C0\" weight=\"1\"/>\n";
				}
				
				$endlat=$currentradius*sin(deg2rad($currentangle));
				$endlng=$currentradius*cos(deg2rad($currentangle));
				$currentlabel.="BOLD".$tablename."UNBOLD: ".$optionname." (".$methodcount." methods, ".$softwarecount." programs)LF";
				$tooltip="M: ".$methodcount.", S: ".$softwarecount.", ".$tablename.": ".$optionname;

				$polylinesall.="<polyline startlat=\"".$startlat."\" startlng=\"".$startlng."\" endlat=\"".$endlat."\" endlng=\"".$endlng."\" colortype=\"".$colortype."\" color=\"".$colorarray[$colortype]."\" weight=\"1\" opacity=\"1\"/>\n";
				
				$markers.="<marker lat=\"".$endlat."\" lng=\"".$endlng."\" colortype=\"".$colortype."\" colorname=\"".$colornames[$colortype]."\" methodcount=\"".$methodcount."\" softwarecount=\"".$softwarecount."\" label=\"".$currentlabel."\" methodlabel=\"".$methodlabel."\" softwarelabel=\"".$softwarelabel."\" tooltip=\"".$tooltip."\"/>\n"; //also add element name and path name
				plottree($currentradius,$currentangle,1+$currentdepthcount, $currentfraction, $currentfromstring, $currentwherestring, $currentlabel, $currentactualmethodstring, $currentactualsoftwarestring, $methodcount, $softwarecount); //recursive
			}
			
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

		$output='<?xml version="1.0"?><document>';
		
		for ($i=0;$i<$totaldepthcount;$i++) {
			if ($tableoptionsarray[$i]==0) { //we want to show all options
				$runningradius+=$fullstep;
			}
			else { //we only want one option
				$runningradius+=$partstep;
			}
			$circles.="<circle radius=\"".$runningradius."\" label=\"".$tablenamesarray[$i]."\"/>\n";
			$maximumradius=$runningradius;
		}
		$circles.="</circles>\n";
		$markers.="</markers>\n";
		$polylines.=$polylinessoftware; //Print out software background lines first
		$polylines.=$polylinesmethods; //Print out method lines next
		$polylines.=$polylinesall; //print out the rest
		$polylines.="</polylines>\n";
		$output.="<dimensions maxradius=\"".$maximumradius."\" />\n";
		$output.=$circles;
		$output.=$markers;
		$output.=$polylines;
		$output.="</document>";
		
		echo $output;	
	}
	else {
		echo "error";
	}
}*/
?>
