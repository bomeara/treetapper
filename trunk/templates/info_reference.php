<?PHP
	include ('dblogin.php'); //connection in $db
	$personid="";
	$referenceid="";
	$haspersonid=false;
	$hasreferenceid=false;
	if (strlen($_GET['personid'])>0) {
		if (is_numeric($_GET['personid'])) {
			$personid=$_GET['personid'];
			$haspersonid=true;
		}
	}
	if (strlen($_GET['referenceid'])>0) {
		if (is_numeric($_GET['referenceid'])) {
			$referenceid=$_GET['referenceid'];
			$hasreferenceid=true;
		}
	}
	if ($haspersonid) {
		if ($_GET['include']!=1) {
			$_GET['pagetitle']="TreeTapper: Reference";
			include('template_pagestart.php');
		}		
		$searchbypersonid= pg_query($db,"SELECT DISTINCT reference_id, reference_title, reference_publicationdate, reference_publicationname, reference_volume, reference_issue, reference_startpage, reference_endpage, reference_approved FROM reference, person, referencetoperson WHERE reference_id=referencetoperson_reference AND referencetoperson_person=".$personid." ORDER BY reference_publicationdate ASC") or die("Could not query the database.");
		if (pg_numrows($searchbypersonid)>0) {
			for ($lt = 0; $lt < pg_numrows($searchbypersonid); $lt++) {
				echo "<br>";
				$searchforauthors=pg_query($db,"SELECT person_id, person_first, person_middle, person_last, referencetoperson_authororder FROM person, referencetoperson WHERE referencetoperson_reference=".pg_fetch_result($searchbypersonid,$lt,0)." AND referencetoperson_person=person_id ORDER BY referencetoperson_authororder ASC");
				for ($la = 0; $la < pg_numrows($searchforauthors); $la++) {
					if(pg_fetch_result($searchforauthors,$la,0)==$personid) {
						echo "<b>";
					}
					echo "<a href='".$treetapperbaseurl."/person/".pg_fetch_result($searchforauthors,$la,0)."'>".pg_fetch_result($searchforauthors,$la,1)." ".pg_fetch_result($searchforauthors,$la,2)." ".pg_fetch_result($searchforauthors,$la,3)."</a>";
					if(pg_fetch_result($searchforauthors,$la,0)==$personid) {
						echo "</b>";
					}
					
					if (($la+1)<pg_numrows($searchforauthors)) {
						echo ", ";
					}
				}
				echo " ".pg_fetch_result($searchbypersonid,$lt,2)." \"<a href='".$treetapperbaseurl."/reference/".pg_fetch_result($searchbypersonid,$lt,0)."'>".pg_fetch_result($searchbypersonid,$lt,1)."</a>\" ".pg_fetch_result($searchbypersonid,$lt,3).": ".pg_fetch_result($searchbypersonid,$lt,4)."(".pg_fetch_result($searchbypersonid,$lt,5)."): ".pg_fetch_result($searchbypersonid,$lt,6)."-".pg_fetch_result($searchbypersonid,$lt,7);		
				
			}
		}
		else {
			echo "No references";
		}
	}
	else if ($hasreferenceid) {
		if ($_GET['include']!=1) {
			$refinfo=pg_query($db,"SELECT reference_id, reference_publicationdate FROM reference WHERE reference_id=$referenceid");
			if (pg_numrows($refinfo)==1) {
				$authorinfo=pg_query($db,"SELECT person_last, referencetoperson_authororder FROM person, referencetoperson WHERE referencetoperson_reference=$referenceid AND referencetoperson_person=person_id ORDER BY referencetoperson_authororder ASC");
				if (pg_numrows($authorinfo)==0) {
					$_GET['pagetitle']="TreeTapper: Reference $referenceid";
				}
				else if (pg_numrows($authorinfo)==1) {
					$_GET['pagetitle']="TreeTapper: ".pg_fetch_result($authorinfo,0,0)." ".pg_fetch_result($refinfo,0,1);
				}
				else if (pg_numrows($authorinfo)==2) {
					$_GET['pagetitle']="TreeTapper: ".pg_fetch_result($authorinfo,0,0)." & ".pg_fetch_result($authorinfo,1,0)." ".pg_fetch_result($refinfo,0,1);
				}
				else {
					$_GET['pagetitle']="TreeTapper: ".pg_fetch_result($authorinfo,0,0)." et al. ".pg_fetch_result($refinfo,0,1);
				}
			}
			else {
				$_GET['pagetitle']="TreeTapper: Reference unknown";
			}
			include('template_pagestart.php');
		}
		
		$searchbyreferenceid= pg_query($db,"SELECT DISTINCT reference_id, reference_title, reference_publicationdate, reference_publicationname, reference_volume, reference_issue, reference_startpage, reference_endpage, reference_approved FROM reference, person, referencetoperson WHERE reference_id=referencetoperson_reference AND reference_id=".$referenceid." ORDER BY reference_publicationdate ASC") or die("Could not query the database.");
		if (pg_numrows($searchbyreferenceid)>0) {
			for ($lt = 0; $lt < pg_numrows($searchbyreferenceid); $lt++) {
				echo "<p><br>Citations(s) (based on web hits only through time [<a href=\"http://treetapper-dev.blogspot.com/2008/01/updated-schema.html\" target=\"_blank\">why</a>]):</p>";
				$_GET['refid']=$referenceid;
				include('plot_citations.php');
				echo "<br />";
				$searchforauthors=pg_query($db,"SELECT person_id, person_first, person_middle, person_last, referencetoperson_authororder FROM person, referencetoperson WHERE referencetoperson_reference=".pg_fetch_result($searchbyreferenceid,$lt,0)." AND referencetoperson_person=person_id ORDER BY referencetoperson_authororder ASC");
				for ($la = 0; $la < pg_numrows($searchforauthors); $la++) {
					if(pg_fetch_result($searchforauthors,$la,0)==$personid) {
						echo "<b>";
					}
					echo "<a href='".$treetapperbaseurl."/person/".pg_fetch_result($searchforauthors,$la,0)."'>".pg_fetch_result($searchforauthors,$la,1)." ".pg_fetch_result($searchforauthors,$la,2)." ".pg_fetch_result($searchforauthors,$la,3)."</a>";
					if(pg_fetch_result($searchforauthors,$la,0)==$personid) {
						echo "</b>";
					}
					
					if (($la+1)<pg_numrows($searchforauthors)) {
						echo ", ";
					}
				}
				echo " ".pg_fetch_result($searchbyreferenceid,$lt,2)." \"<a href='".$treetapperbaseurl."/reference/".pg_fetch_result($searchbyreferenceid,$lt,0)."'>".pg_fetch_result($searchbyreferenceid,$lt,1)."</a>\" ".pg_fetch_result($searchbyreferenceid,$lt,3).": ".pg_fetch_result($searchbyreferenceid,$lt,4)."(".pg_fetch_result($searchbyreferenceid,$lt,5)."): ".pg_fetch_result($searchbyreferenceid,$lt,6)."-".pg_fetch_result($searchbyreferenceid,$lt,7);
				echo "<p /><br>Coauthorship network graph (click to navigate, mouseover for info)<br>";
				echo "<div id=\"networkgraph\">";
				echo "<iframe valign=center frameborder=no id=\"dbgraphnav\" name=\"dbgraphnav\" scrolling=no src=\"$treetapperbaseurl"."/dbgraphnav/ajax-loader.gif\" marginwidth=0 marginheight=0 vspace=0 hspace=0 style=\"overflow:visible; width:100%; display:none\">Navigation image from <a href=\"http://code.google.com/p/dbgraphnav/\">dbgraphnav</a> [a Google Summer of Code 2008 project by <a href=\"http://thefire.us/\">Paul McMillan</a>, mentored by <a href=\"http://www.brianomeara.info\">Brian O'Meara</a> and organized through <a href=\"http://www.nescent.org\">NESCent</a>].</iframe>\n</div>";
				echo "<script type=\"text/javascript\">";
				echo "YAHOO.util.Event.addListener(window, \"load\", function() {";
				echo "loadintoIframe('dbgraphnav', '".$treetapperbaseurl."/dbgraphnav/main.php?id=".$referenceid."&type=reference&depth=2');
				});";
				echo "</script>";
				
			}
		}
		else {
			echo "No references";
		}
	}
	else {
		if ($_GET['include']!=1) {
			$_GET['pagetitle']="TreeTapper: References";
			include('template_pagestart.php');
		}		
		$searchall= pg_query($db,"SELECT reference_id, reference_title, reference_publicationdate, reference_publicationname, reference_volume, reference_issue, reference_startpage, reference_endpage, reference_approved FROM reference ORDER BY reference_publicationdate ASC") or die("Could not query the database.");
		if (pg_numrows($searchall)>0) {
			for ($lt = 0; $lt < pg_numrows($searchall); $lt++) {
				echo "<br>";
				$searchforauthors=pg_query($db,"SELECT person_id, person_first, person_middle, person_last, referencetoperson_authororder FROM person, referencetoperson WHERE referencetoperson_reference=".pg_fetch_result($searchall,$lt,0)." AND referencetoperson_person=person_id ORDER BY referencetoperson_authororder ASC");
				for ($la = 0; $la < pg_numrows($searchforauthors); $la++) {
					echo "<a href='".$treetapperbaseurl."/person/".pg_fetch_result($searchforauthors,$la,0)."'>".pg_fetch_result($searchforauthors,$la,1)." ".pg_fetch_result($searchforauthors,$la,2)." ".pg_fetch_result($searchforauthors,$la,3)."</a>";
					
					if (($la+1)<pg_numrows($searchforauthors)) {
						echo ", ";
					}
				}
				echo " ".pg_fetch_result($searchall,$lt,2)." \"<a href='".$treetapperbaseurl."/reference/".pg_fetch_result($searchall,$lt,0)."'>".pg_fetch_result($searchall,$lt,1)."</a>\" ".pg_fetch_result($searchall,$lt,3).": ".pg_fetch_result($searchall,$lt,4)."(".pg_fetch_result($searchall,$lt,5)."): ".pg_fetch_result($searchall,$lt,6)."-".pg_fetch_result($searchall,$lt,7);			
			}
		}
	}
	
	if ($_GET['include']!=1) {
		include('template_pageend.php');
	}
	
	?>