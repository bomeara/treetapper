<?PHP
$_GET['pagetitle']="TreeTapper Tutorials";
include('templates/template_pagestart.php');
echo "Here will be basic tutorials: how to get a tree, why branch lengths matter, etc.";
?>
<br><br><b>How do I get a tree?</b>
<br>One relevant concern is what kind 
<?PHP $_GET['table'] = 'treetype';
include 'templates/helppanel_generic.php'; 
?>
of tree you need. Some methods (often parsimony-based) for looking at character evolution just need a <b>topology</b> -- a tree without branch lengths. For that, <a href="http://www.treebase.org">TreeBase</a> might suffice. TreeBase is a repository of topologies (branch lengths are not allowed in submitted trees), often with data included (so, it's possible to get, for example, parsimony-based branch lengths by bringing the tree and data into a program like PAUP). There are some alternate interfaces to TreeBase: <a href="http://pilin.cs.iastate.edu/phylofinder/">Phylofinder</a> by <a href="http://genome.cs.iastate.edu/CBL/people/duhong.shtml">Duhong Chen</a> allows more flexible searches, but doesn't yet allow downloads of the nexus files; <a href="http://linnaeus.zoology.gla.ac.uk/~rpage/tbmap/">TBMap</a> by <a href="http://taxonomy.zoology.gla.ac.uk/rod/rod.html">Rod Page</a> allows matching of taxon names in TreeBase to other names. San Diego Supercomputer Center had a <a href="http://www.phylo.org/treebase/index.php">mirror</a> of TreeBase with a different interface, but it is no longer working. <br><br>For most character methods (independent contrasts, various likelihood/Bayesian methods for looking at rates of character evolution, etc.), a <b>tree with branch lengths</b>

<?PHP $_GET['table'] = 'branchlengthtype';
include 'templates/helppanel_generic.php'; 
?>
, often proportional to time, is needed. There is currently no place to download such trees. A recent paper on a group of interest may have a calibrated tree to download as supplementary info. For plants, the <a href="http://www.phylodiversity.net/phylomatic/phylomatic.html">Phylomatic</a> site allows download of a tree with branch lengths. Making a tree oneself is also a good option. TreeBase allows data sets to be downloaded; raw sequence data may also be pulled from <a href="http://www.ncbi.nlm.nih.gov/Genbank/">GenBank</a>. The <a href="http://loco.biosci.arizona.edu/cgi-bin/pb.cgi">Phylota browser</a> has organized GenBank info by taxon, so one can find aligned gene datasets for a given named group of taxa to analyze. Tree building itself can be done with a wide variety of programs (see Felsenstein's list of <a href="http://evolution.genetics.washington.edu/phylip/software.html#Likelihood">likelihood/Bayesian tree inference programs</a>); there are also online <a href="http://evolution.genetics.washington.edu/phylip/software.serv.html#servers">servers</a> for tree search, including the <a href="http://www.phylo.org/sub_sections/portal/">CIPRES tree portal</a>. For calibrating a tree (using fossil or other information to make branches proportional to time), popular programs are <a href="http://beast.bio.ed.ac.uk/">BEAST</a>, <a href="http://loco.biosci.arizona.edu/r8s/">r8s</a>, and <a href="http://statgen.ncsu.edu/thorne/multidivtime.html">MultiDivTime</a>. Note that some methods need trees that are fully resolved (no polytomies: nodes with more than two descendants); others, especially those dealing with speciation and extinction, require trees with ALL extant species sampled.

<?PHP
include('templates/template_pageend.php');
?>
