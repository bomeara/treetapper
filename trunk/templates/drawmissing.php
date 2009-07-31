<?PHP
include ('dblogin.php'); //connection in $db
//header("Content-type: image/png");
$imgWidth=800;
$imgHeight=800;
$margin=30;
$minDim=min($imgWidth,$imgHeight);
$image=imagecreatetruecolor($imgWidth, $imgHeight);
imageantialias($image, true);
$colorWhite=imagecolorallocate($image, 255, 255, 255);
$colorGrey=imagecolorallocate($image, 192, 192, 192);
$colorBlack=imagecolorallocate($image, 0, 0, 0);
$colorRed=imagecolorallocate($image, 255, 0, 0);
$colorBlue=imagecolorallocate($image, 0, 0, 192);
imagefill($image, 0, 0, $colorWhite);
$maxradius=0;
$markerradius=7;
$maxid=0;
$mapstring="";
$starttime=time();
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
$phpcolorarray=array($colorRed, $colorBlue, $colorBlack, $colorGrey);

function scaleX ($lat) {
	global $minDim, $maxradius, $margin;
	$finalX=$minDim/2.0 + (($minDim/2.0)-$margin)*$lat/$maxradius;
	return $finalX;
}

function scaleY ($lng) {
	global $minDim, $maxradius, $margin;
	$finalY=$minDim/2.0 + (($minDim/2.0)-$margin)*$lng/$maxradius;
	return $finalY;
}

function plottree ($parentradius, $parentangle, $currentdepthcount, $parentcirclefraction, $parentfromstring, $parentwherestring, $parentlabel, $parentactualmethodstring, $parentactualsoftwarestring, $parentmethodcount, $parentsoftwarecount, $parenturlstring) {	
	global $totaldepthcount, $circles, $markers, $polylines, $polylinesall, $polylinessoftware, $polylinesmethods, $fullstep, $partstep, $colorarray, $colornames, $tablenamesarray, $tableoptionsarray, $db, $image, $imgWidth, $imgHeight, $maxradius, $colorWhite, $colorRed, $colorGrey, $colorBlack, $colorBlue, $phpcolorarray, $margin, $markerradius, $maxid, $mapstring;
	////echo "\n<br>____________________________________________<br>\nIn plottree function: ";
	////echo "$parentradius, $parentangle, $currentdepthcount, $parentcirclefraction, $parentfromstring, $parentwherestring \n<br>";
	if ($currentdepthcount<$totaldepthcount) { //not at a terminal yet
		
		$currentfromstring=$parentfromstring;
		$currentwherestring=$parentwherestring;
		$urlstring=$parenturlstring;
		
		$tablename=$tablenamesarray[$currentdepthcount];
		$fulltablename=$tablename;
		if (stripos($tablename,"char")!==false) {
			$fulltablename=charactertype;
		}
		////echo "<br>\ntablename = ".$tablename;
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
			////echo "<br>\n".$currenttotalsql;
			$currentquery=pg_query($db, $currenttotalsql) or die ("query $currenttotalsql failed");
			////echo '<br>$tableoptionsarray[$currentdepthcount]='.$tableoptionsarray[$currentdepthcount]."<br>\ncurrenttotalsql=".$currenttotalsql."<br>\npgnumrows = ".pg_numrows($currentquery)."<br>\n";
			for ($option=0;$option<pg_numrows($currentquery);$option++) {
				$currentlabel=$parentlabel;
				$methodlabel="";
				$methodfulllabel="[ul][b]Methods[/b]";
				$softwarelabel="";
				$softwarefulllabel="[br][/br][ul][b]Software[/b]";
				$optionid=pg_fetch_result($currentquery,$option,0);
				$optionname=pg_fetch_result($currentquery,$option,1);
				$urlstring=$parenturlstring;
				if ($currentdepthcount>0) {
					$urlstring.='&';
				}
				$urlstring.=$tablename.'='.$optionid;
				//$currentlabel.=$tablename.$optionname;
				$currentfraction=$parentcirclefraction/pg_numrows($currentquery); //would be more efficient to calculate this once above, but could have division by zero
				//this subtree must be contained from within +/- 0.5*(360*parentfraction) of the parent angle. With margins.
				$minimumangle=$parentangle-0.5*(360.0*$parentcirclefraction);
				$fullanglestep=360.0*$parentcirclefraction/pg_numrows($currentquery);				
				$currentangle=$minimumangle+$option*$fullanglestep+0.5*$fullanglestep; //The half step is to add a margin.
				////echo "<br>\ncurrentangle=".$currentangle." currentfraction=".$currentfraction." parentfraction=".$parentcirclefraction." minimumangle=".$minimumangle." fullanglestep=".$fullanglestep." pg_numrows(currentquery)=".pg_numrows($currentquery);
				
				
				$currentactualmethodstring="";
				$methodcount=0;
				if (strlen($parentactualmethodstring)>0) {
					$currentactualmethodstring.=$parentactualmethodstring;
					if ((0==preg_match("/applicationkind/",$tablename)) && (0==preg_match("/platform/",$tablename)) && (0==preg_match("/program/",$tablename)) && (0==preg_match("/treeformat/",$tablename)) && (0==preg_match("/dataformat/",$tablename))) { //because we don't care about applicationkind, etc. for just methods
						$currentactualmethodstring.=" AND actualmethods_".substr($actualcolumnname,0,-3)."=".$optionid;
					}
					////echo "<br>\n".$currentactualmethodstring;
					$methodquery=pg_query($db, $currentactualmethodstring) or die ("query $currentactualmethodstring failed");
					if (pg_numrows($methodquery)>=1) {
						$methodcount=pg_numrows($methodquery);
						if ($methodcount==0) {
							$currentactualmethodstring=""; //So we don't bother searching in the future
							$methodlabel.="none";
							$methodfulllabel.="[li]none[/li]";
						}
						else {
							for ($i=0;$i<pg_numrows($methodquery);$i++) {
								$methodlabel.=','.pg_fetch_result($methodquery,$i,0);
								$methodfulllabel.="[li][a href=QUOTE".$treetapperbaseurl."/method/".pg_fetch_result($methodquery,$i,0)."QUOTE]".pg_fetch_result($methodquery,$i,1)."[/a][/li]";
							}
						}
					}
				}
				
				
				$currentactualsoftwarestring="";
				$softwarecount=0;
				if (strlen($parentactualsoftwarestring)>0) {
					$currentactualsoftwarestring.=$parentactualsoftwarestring;
					$currentactualsoftwarestring.=" AND actualsoftware_".substr($actualcolumnname,0,-3)."=".$optionid;
					$softwarequery=pg_query($db, $currentactualsoftwarestring) or die ("query $currentactualsoftwarestring failed");
					if (pg_numrows($softwarequery)>=1) {
						$softwarecount=pg_numrows($softwarequery);
						if ($softwarecount==0) {
							$currentactualsoftwarestring=""; //So we don't bother searching in the future
							$softwarelabel.="none";
							$softwarefulllabel.="[li]none[/li]";
						}
						else {
							for ($i=0;$i<pg_numrows($softwarequery);$i++) {
								$softwarelabel.=','.pg_fetch_result($softwarequery,$i,0);
								$softwarefulllabel.="[li][a href=QUOTE".$treetapperbaseurl."/method/".pg_fetch_result($softwarequery,$i,0)."QUOTE]".pg_fetch_result($softwarequery,$i,1)."[/a][/li]";
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
					$currentlabel.="[br /]";
				}
				$currentlabel.=$tablename.": ".$optionname." (".$methodcount." methods, ".$softwarecount." programs)";
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
				////echo "<polyline startlat=\"".$startlat."\" startlng=\"".$startlng."\" endlat=\"".$endlat."\" endlng=\"".$endlng."\" color=\"".$colorarray[$colortype]."\" weight=\"1\" opacity=\"1\"/>\n";
				
				if (preg_match('/^\,/',$methodlabel)==1) {
					$methodlabel=substr($methodlabel,1); //delete leading comma
					//$methodfulllabel=substr($methodfulllabel,1); //delete leading comma
				}
				if (preg_match('/^\,/',$softwarelabel)==1) {
					$softwarelabel=substr($softwarelabel,1); //delete leading comma
					//$softwarefulllabel=substr($softwarefulllabel,1); //delete leading comma					
				}
				$methodfulllabel.="[/ul]";
				$softwarefulllabel.="[/ul]";
				//echo "<0|".$startlat."|".$startlng."|".$endlat."|".$endlng."|".$colortype."|".$methodcount."|".$softwarecount."|".$currentlabel."|".$methodlabel."|".$softwarelabel."|".$tooltip."|".$urlstring;
				if ($methodcount>0) {
					//echo "&method=".$methodlabel;
				}
				if ($softwarecount>0) {
					//echo "&program=".$softwarelabel;
				}
				imageline($image, scaleX($startlat), scaleY($startlng), scaleX($endlat), scaleY($endlng), $phpcolorarray[$colortype]);
				imagefilledellipse($image, scaleX($endlat), scaleY($endlng), $markerradius, $markerradius, $phpcolorarray[$colortype]);
				//echo "<area shape='circle' coords='".scaleX($endlat).", ".scaleY($endlng).", ".$markerradius."' href='".$urlstring."' onMouseOver=\"alert('".$tooltip."')\" />\n";
				$maxid++;
				$mapstring.="\n<area shape='circle' id='marker".$maxid."' coords='".scaleX($endlat).", ".scaleY($endlng).", ".$markerradius."' href='".$urlstring."' target=\"_blank\" title=\"".$tablename.": ".$optionname." (".$methodcount." methods, ".$softwarecount." programs)\" onMouseOver=\"parent.updatepanel('".$methodcount." methods and ".$softwarecount." programs', '".$currentlabel."[br /][br /]".$methodfulllabel."".$softwarefulllabel."', 'Click to open a summary page')\" />";
			//echo ">";
				flush();
				////echo "<marker lat=\"".$endlat."\" lng=\"".$endlng."\" colorname=\"".$colornames[$colortype]."\"  methodcount=\"".$methodcount."\" softwarecount=\"".$softwarecount."\" label=\"".$currentlabel."\" methodlabel=\"".$methodlabel."\" softwarelabel=\"".$softwarelabel."\" tooltip=\"".$tooltip."\"/>\n"; //also add element name and path name
				
				plottree($currentradius,$currentangle,1+$currentdepthcount, $currentfraction, $currentfromstring, $currentwherestring." AND ".$columnname." = ".$optionid, $currentlabel, $currentactualmethodstring, $currentactualsoftwarestring, $methodcount, $softwarecount, $urlstring); //recursive
			}
			
		}
		else {
			////echo $tableoptionsarray[$currentdepthcount]."<br>\n";
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
			////echo "<br>\nELSE: ".$currenttotalsql;
			$currentquery=pg_query($db, $currenttotalsql) or die("query $currenttotalsql failed");
			////echo "<br>\nCurrentangle=".$currentangle;
				//Now, add the plotting info
			if (pg_numrows($currentquery)==1) {
				$currentlabel=$parentlabel;
				$methodlabel="";
				$methodfulllabel="[ul][b]Methods[/b]";
				$softwarelabel="";
				$softwarefulllabel="[br][/br][ul][b]Software[/b]";
				$optionid=pg_fetch_result($currentquery,$option,0);
				$optionname=pg_fetch_result($currentquery,$option,1);
				if ($currentdepthcount>0) {
					$urlstring.='&';
				}
				$urlstring.=$tablename.'='.$optionid;
				
				$currentactualmethodstring="";
				$methodcount=0;
				if (strlen($parentactualmethodstring)>0) {
					$currentactualmethodstring.=$parentactualmethodstring;
					if ((0==preg_match("/applicationkind/",$tablename)) && (0==preg_match("/platform/",$tablename)) && (0==preg_match("/program/",$tablename)) && (0==preg_match("/treeformat/",$tablename)) && (0==preg_match("/dataformat/",$tablename))) { //because we don't care about applicationkind, etc. for just methods
						$currentactualmethodstring.=" AND actualmethods_".substr($actualcolumnname,0,-3)."=".$optionid;
					}
					////echo "<br>\nELSE ".$currentactualmethodstring;
					$methodquery=pg_query($db, $currentactualmethodstring) or die("query $currentactualmethodstring failed");
					if (pg_numrows($methodquery)>=1) {
						$methodcount=pg_numrows($methodquery);
						if ($methodcount==0) {
							$currentactualmethodstring=""; //So we don't bother searching in the future
							$methodlabel.="none";
							$methodfulllabel="[li]none[/li]";
						}
						else {
							
							for ($i=0;$i<pg_numrows($methodquery);$i++) {
								$methodlabel.=','.pg_fetch_result($methodquery,$i,0);
								$methodfulllabel.="[li][a href=QUOTE".$treetapperbaseurl."/method/".pg_fetch_result($methodquery,$i,0)."QUOTE]".pg_fetch_result($methodquery,$i,1)."[/a][/li]";

							}
						}
						
					}
				}
				
				
				$currentactualsoftwarestring="";
				$softwarecount=0;
				if (strlen($parentactualsoftwarestring)>0) {
					$currentactualsoftwarestring.=$parentactualsoftwarestring;
					$currentactualsoftwarestring.=" AND actualsoftware_".substr($actualcolumnname,0,-3)."=".$optionid;
					$softwarequery=pg_query($db, $currentactualsoftwarestring) or die ("query $currentactualsoftwarestring failed");
					if (pg_numrows($softwarequery)>=1) {
						$softwarecount=pg_numrows($softwarequery);
						if ($softwarecount==0) {
							$softwarelabel.="none";
							$softwarefulllabel.="[li]none[/li]";
						
						}
						else {
							for ($i=0;$i<pg_numrows($softwarequery);$i++) {
								$softwarelabel.=','.pg_fetch_result($softwarequery,$i,0);						
								$softwarefulllabel.="[li][a href=QUOTE".$treetapperbaseurl."/method/".pg_fetch_result($softwarequery,$i,0)."QUOTE]".pg_fetch_result($softwarequery,$i,1)."[/a][/li]";
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
				//Make the gray lines to show things we're omitting
				$missingquery=pg_query($db, "SELECT * FROM $fulltablename")  or die ("query $missingquery failed");
				$graystring="";
				for ($missing=0;$missing<pg_numrows($missingquery);$missing++) {
					$minimumangle=$parentangle-0.5*(360.0*$parentcirclefraction);
					$fullanglestep=360.0*$parentcirclefraction/pg_numrows($missingquery);				
					$missingangle=$minimumangle+$missing*$fullanglestep+0.5*$fullanglestep;
					$endlat=$currentradius*sin(deg2rad($missingangle));
					$endlng=$currentradius*cos(deg2rad($missingangle));
					////echo "<polyline startlat=\"".$startlat."\" startlng=\"".$startlng."\" endlat=\"".$endlat."\" endlng=\"".$endlng."\" color=\"#C0C0C0\" weight=\"1\"/>\n";
					imageline($image, scaleX($startlat), scaleY($startlng), scaleX($endlat), scaleY($endlng), $phpcolorarray[3]);
				

					$graystring.="<1|".$startlat."|".$startlng."|".$endlat."|".$endlng.">";
				}
				
				$endlat=$currentradius*sin(deg2rad($currentangle));
				$endlng=$currentradius*cos(deg2rad($currentangle));
				//$currentlabel.="BOLD".$tablename."UNBOLD: ".$optionname." (".$methodcount." methods, ".$softwarecount." programs)LF";
				$currentlabel.="\n".$tablename.": ".$optionname." (".$methodcount." methods, ".$softwarecount." programs)";
				$tooltip="M: ".$methodcount.", S: ".$softwarecount.", ".$tablename.": ".$optionname;
				/*				if ($colortype>1) { //has software
					$polylinessoftware.="<polyline startlat=\"".$startlat."\" startlng=\"".$startlng."\" endlat=\"".$endlat."\" endlng=\"".$endlng."\" color=\"".$colorarray[3]."\" weight=\"8\" opacity=\"1\"/>\n";
				}
				if ($colortype>0) { //has methods
					$polylinesmethods.="<polyline startlat=\"".$startlat."\" startlng=\"".$startlng."\" endlat=\"".$endlat."\" endlng=\"".$endlng."\" color=\"".$colorarray[1]."\" weight=\"4\" opacity=\"0.5\"/>\n";
				}
				*/
				 ////echo "<polyline startlat=\"".$startlat."\" startlng=\"".$startlng."\" endlat=\"".$endlat."\" endlng=\"".$endlng."\" color=\"".$colorarray[$colortype]."\" weight=\"1\" opacity=\"1\"/>\n";

				if (preg_match('/^\,/',$methodlabel)==1) {
					$methodlabel=substr($methodlabel,1); //delete leading comma
				}
				if (preg_match('/^\,/',$softwarelabel)==1) {
					$softwarelabel=substr($softwarelabel,1); //delete leading comma				
				}
				$methodfulllabel.="[/ul]";
				$softwarefulllabel.="[/ul]";
				imageline($image, scaleX($startlat), scaleY($startlng), scaleX($endlat), scaleY($endlng), $phpcolorarray[$colortype]);
				imagefilledellipse($image, scaleX($endlat), scaleY($endlng), $markerradius, $markerradius, $phpcolorarray[$colortype]);
				$mapstring.="<area shape='circle' id='marker".$maxid."' coords='".scaleX($endlat).", ".scaleY($endlng).", ".$markerradius."' href='".$urlstring."' title=\"".$tablename.": ".$optionname." (".$methodcount." methods, ".$softwarecount." programs)\" onMouseOver=\"parent.updatepanel('".$methodcount." methods and ".$softwarecount." programs', '".$currentlabel."[br /][br /]".$methodfulllabel."".$softwarefulllabel."', 'Click to open a summary page')\" />";
				
				//echo ">";
				//echo "$graystring"; //so the polylines are preceded by the relevant marker


				flush();
				////echo "<marker lat=\"".$endlat."\" lng=\"".$endlng."\" colorname=\"".$colornames[$colortype]."\" methodcount=\"".$methodcount."\" softwarecount=\"".$softwarecount."\" label=\"".$currentlabel."\" methodlabel=\"".$methodlabel."\" softwarelabel=\"".$softwarelabel."\" tooltip=\"".$tooltip."\"/>\n"; //also add element name and path name
				plottree($currentradius,$currentangle,1+$currentdepthcount, $currentfraction, $currentfromstring, $currentwherestring, $currentlabel, $currentactualmethodstring, $currentactualsoftwarestring, $methodcount, $softwarecount, $urlstring); //recursive
			}
			
		}
		
		
	}
	
}



if (strlen($_GET['tablenames'])>0 && strlen($_GET['tableoptions'])>0) {
/*	echo "<script type=\"text/javascript\">
function showpanel(body) {
	alert(body);
			//	YAHOO.example.container.tooltippanel = new YAHOO.widget.Panel(\"tooltippanel\", { width:\"320px\", visible:true, draggable:true, close:false } );
			//	YAHOO.example.container.tooltippanel.setHeader(\"Panel #2 from Script &mdash; This Panel Isn't Draggable\");
			//	YAHOO.example.container.tooltippanel.setBody(\"This is a dynamically generated Panel.\");
			//	YAHOO.example.container.tooltippanel.setFooter(\"End of Panel #2\");
			//	YAHOO.example.container.tooltippanel.render(\"container\");
};
</script>\n";*/
	
	$mapstring.="<map id ='missingmap' name='missingmap'>\n";
	$querystring=$_GET['tablenames'].$_GET['tableoptions'];
	$cacheroot=sha1($querystring);
	$tablenamesstring=str_replace("charactertype_","char",$_GET['tablenames']);
	$tablenamesarray = explode(',', $tablenamesstring);
	////echo "\n<br>count(tablenamesarray) after explode = ".count($tablenamesarray);
	for ($i=0;$i<count($tablenamesarray);$i++) {
		////echo "\n<br>    ".$i."=".$tablenamesarray[$i];
	}
	$tableoptionsarray = explode(',', $_GET['tableoptions']);
	$totaldepthcount=count($tablenamesarray);
	$runningradius=0;
	$numberofdivisions=1;
	for ($i=0;$i<$totaldepthcount;$i++) {
		if ($tableoptionsarray[$i]==0) { //we want to show all options
			$maxradius+=$fullstep;
		}
		else { //we only want one option
			$maxradius+=$partstep;
		}
	}
	if ($maxradius>2) {
		$markerradius=4;
	}
	for ($i=0;$i<$totaldepthcount;$i++) {
		if ($tableoptionsarray[$i]==0) { //we want to show all options
			$runningradius+=$fullstep;
		}
		else { //we only want one option
			$runningradius+=$partstep;
		}
		imageellipse($image, $imgWidth/2, $imgHeight/2, 2*(scaleX($runningradius)-$minDim/2), 2*(scaleY($runningradius)-$minDim/2), $phpcolorarray[3]);
		//imageellipse($image, $imgWidth/2, $imgHeight/2, scaleX($runningradius), 60, $colorGrey);
		
			//$circles.="<circle radius=\"".$runningradius."\" label=\"".$tablenamesarray[$i]."\"/>\n";
	}	
	if (count($tableoptionsarray)==$totaldepthcount) { //only continue if same number
		$starturl=$treetapperbaseurl."/combo.php?";
		plottree (0, 0, 0, 1, " FROM ", " WHERE ", "", "SELECT DISTINCT method_id, method_name FROM actualmethods, method WHERE actualmethods_method=method_id ","SELECT DISTINCT program_id, program_name FROM actualsoftware, program WHERE actualsoftware_program=program_id ", 1, 1, $starturl);
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
		
		//$circles.="</circles>\n";
		//$markers.="</markers>\n";
		//$polylines.=$polylinessoftware; //Print out software background lines first
		//$polylines.=$polylinesmethods; //Print out method lines next
		//$polylines.=$polylinesall; //print out the rest
		//$polylines.="</polylines>\n";
		//$output.=$circles;
		//$output.=$markers;
		//$output.=$polylines;
		//$output.="</document>";
		
		////echo $output;	
		imagepng($image,"cache_missingmethods/".$cacheroot.".png");
		
		imagedestroy($image);
		$mapstring.="\n</map>\n<img src ='".$treetapperbaseurl."/templates/cache_missingmethods/".$cacheroot.".png' width ='".$imgWidth."' height ='".$imgHeight."' alt='Visualization of missing methods and software' usemap='#missingmap' border=0
 onLoad=\"parent.updatepanel('Node info box', 'Move your mouse over a node for info on that node. Click on a node to go to a summary page. You can drag this box around to get it out of the way.', '')\"/>";
		$mapfile=fopen("cache_missingmethods/".$cacheroot.".html",'wt+');
		fwrite($mapfile,$mapstring);
		fclose($mapfile);
		//echo "$mapstring";
		$elapsedtime=time()-$starttime;
		$existingfilequerystring="SELECT findneedquery_id, findneedquery_count FROM findneedquery WHERE findneedquery_hash='".$cacheroot."'";
		$existingfilequery=pg_query($db, $existingfilequerystring) or die ("query $existingfilequery failed");
		if (pg_numrows($existingfilequery)==1) {
			$querycount=pg_fetch_result($existingfilequery,0,1);
			$querycount++;
			$updatestring="UPDATE findneedquery SET findneedquery_count=".$querycount.", findneedquery_elapsedtime=".$elapsedtime.", findneedquery_moddate=CURRENT_TIMESTAMP WHERE findneedquery_hash='".$cacheroot."'";
			$updatequery=pg_query($db, $updatestring) or die ("query $updatestring failed");			
		}
		else {
			$res=pg_query($db,"SELECT nextval('findneedquery_findneedquery_id_seq') as key") or die ("query findneedquery_findneedquery_id_seq failed");
			$row=pg_fetch_array($res, 0);
			$newqueryid=$row['key'];			
			$addstring="INSERT INTO findneedquery (findneedquery_id, findneedquery_tablenames, findneedquery_tableoptions, findneedquery_hash, findneedquery_elapsedtime) VALUES ('".$newqueryid."', '".$_GET['tablenames']."', '".$_GET['tableoptions']."', '".$cacheroot."', '".$elapsedtime."')";
			$addquery=pg_query($db, $addstring) or die ("query $addstring failed");	
		}
		
		
		//echo "done";
	}
	else {
		//echo "error";
	}
}
?>
