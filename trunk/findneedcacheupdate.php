<?PHP
$tables=array('criterion', 'applicationkind', 'branchlengthtype', 'charactertype_1', 'charactertype_2', 'charactertype_3',  'dataformat', 'generalquestion', 'method', 'platform', 'posedquestion', 'program','treeformat', 'treetype');
$starttime=time();
system("curl -s -m90000 'http://www.treetapper.org/templates/drawmissing.php?tablenames=charactertype_1,criterion,treetype&tableoptions=0,0,0' > /dev/null");
$elapsedtime=time()-$starttime;
echo "charactertype_1,criterion,treetype\t".$elapsedtime."\n";

for ($i=0; $i<sizeof($tables); $i++) {
	
	$starttime=time();
	system("curl -s  -m90000 'http://www.treetapper.org/templates/drawmissing.php?tablenames=".$tables[$i]."&tableoptions=0' > /dev/null");
	$elapsedtime=time()-$starttime;
	echo $tables[$i]."\t".$elapsedtime."\n";
	for ($j=0; $j<sizeof($tables); $j++) {
		if ($i!=$j) {
		$starttime=time();
			system("curl -s -m90000 'http://www.treetapper.org/templates/drawmissing.php?tablenames=".$tables[$i].",".$tables[$j]."&tableoptions=0,0' > /dev/null");
			$elapsedtime=time()-$starttime;
			echo $tables[$i].",".$tables[$j]."\t".$elapsedtime."\n";
			for ($k=0; $k<sizeof($tables); $k++) {
				if ($i!=$k) {
					if ($j!=$k) {
						$starttime=time();
						system("curl -s -m90000 'http://www.treetapper.org/templates/drawmissing.php?tablenames=".$tables[$i].",".$tables[$j].",".$tables[$k]."&tableoptions=0,0,0' > /dev/null");
						$elapsedtime=time()-$starttime;
						echo $tables[$i].",".$tables[$j].",".$tables[$k]."\t".$elapsedtime."\n";
					}
				}
			}
			
		}
	}
}