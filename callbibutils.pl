#!/usr/bin/perl -w
use diagnostics;
#make darn sure that this can only be called by legit commands
$numArgs = $#ARGV + 1;
if ($numArgs==3) {
	print "Now doing $ARGV[0] $ARGV[1] > $ARGV[2] in PERL";
	system("$ARGV[0] $ARGV[1] > $ARGV[2]");
	open(IN,"$ARGV[2]") or die ("<br>cannot open $ARGV[2] <br>");
	print "<br><br>BEGIN OUTPUT FROM PERL<br><br>";
	while (<IN>) {
		print "$_ <br>";
	}
	print "<br><br>END OUTPUT FROM PERL<br><br>";
}
else {
print "Error: not enough arguments";
}
