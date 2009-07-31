<?PHP


echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01//EN\" \"http://www.w3.org/TR/html4/strict.dtd\">
<html>
<head>
    <meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\">
<title>Default TreeView</title>

<!--CSS source files for the entire YUI Library--> 
<!--CSS Foundation: (also partially aggegrated in reset-fonts-grids.css; does not include base.css)--> 

<!--CSS for Controls:--> 
<link rel=\"stylesheet\" type=\"text/css\" href=\"http://yui.yahooapis.com/2.4.1/build/container/assets/skins/sam/container.css\"> 
<link rel=\"stylesheet\" type=\"text/css\" href=\"http://yui.yahooapis.com/2.4.1/build/menu/assets/skins/sam/menu.css\"> 
<link rel=\"stylesheet\" type=\"text/css\" href=\"http://yui.yahooapis.com/2.4.1/build/autocomplete/assets/skins/sam/autocomplete.css\"> 
<link rel=\"stylesheet\" type=\"text/css\" href=\"http://yui.yahooapis.com/2.4.1/build/button/assets/skins/sam/button.css\"> 
<link rel=\"stylesheet\" type=\"text/css\" href=\"http://yui.yahooapis.com/2.4.1/build/calendar/assets/skins/sam/calendar.css\"> 
<link rel=\"stylesheet\" type=\"text/css\" href=\"http://yui.yahooapis.com/2.4.1/build/colorpicker/assets/skins/sam/colorpicker.css\"> 
<link rel=\"stylesheet\" type=\"text/css\" href=\"http://yui.yahooapis.com/2.4.1/build/datatable/assets/skins/sam/datatable.css\"> 
<link rel=\"stylesheet\" type=\"text/css\" href=\"http://yui.yahooapis.com/2.4.1/build/editor/assets/skins/sam/editor.css\"> 
<link rel=\"stylesheet\" type=\"text/css\" href=\"http://yui.yahooapis.com/2.4.1/build/logger/assets/skins/sam/logger.css\"> 
<link rel=\"stylesheet\" type=\"text/css\" href=\"http://yui.yahooapis.com/2.4.1/build/tabview/assets/skins/sam/tabview.css\"> 
<link rel=\"stylesheet\" type=\"text/css\" href=\"http://yui.yahooapis.com/2.4.1/build/treeview/assets/skins/sam/treeview.css\"> 


<!--JavaScript source files for the entire YUI Library:--> 

<!--Utilities (also aggregated in yahoo-dom-event.js and utilities.js; see readmes in the 
			   YUI download for details on each of the aggregate files and their contents):--> 
<script type=\"text/javascript\" src=\"http://yui.yahooapis.com/2.4.1/build/yahoo/yahoo-min.js\"></script> 
<script type=\"text/javascript\" src=\"http://yui.yahooapis.com/2.4.1/build/dom/dom-min.js\"></script> 
<script type=\"text/javascript\" src=\"http://yui.yahooapis.com/2.4.1/build/event/event-min.js\"></script> 
<script type=\"text/javascript\" src=\"http://yui.yahooapis.com/2.4.1/build/element/element-beta-min.js\"></script> 
<script type=\"text/javascript\" src=\"http://yui.yahooapis.com/2.4.1/build/animation/animation-min.js\"></script> 
<script type=\"text/javascript\" src=\"http://yui.yahooapis.com/2.4.1/build/connection/connection-min.js\"></script> 
<script type=\"text/javascript\" src=\"http://yui.yahooapis.com/2.4.1/build/datasource/datasource-beta-min.js\"></script> 
<script type=\"text/javascript\" src=\"http://yui.yahooapis.com/2.4.1/build/dragdrop/dragdrop-min.js\"></script> 
<script type=\"text/javascript\" src=\"http://yui.yahooapis.com/2.4.1/build/get/get-beta-min.js\"></script> 
<script type=\"text/javascript\" src=\"http://yui.yahooapis.com/2.4.1/build/history/history-min.js\"></script> 
<script type=\"text/javascript\" src=\"http://yui.yahooapis.com/2.4.1/build/imageloader/imageloader-beta-min.js\"></script> 
<script type=\"text/javascript\" src=\"http://yui.yahooapis.com/2.4.1/build/json/json-beta-min.js\"></script> 
<script type=\"text/javascript\" src=\"http://yui.yahooapis.com/2.4.1/build/profiler/profiler-beta-min.js\"></script> 
<script type=\"text/javascript\" src=\"http://yui.yahooapis.com/2.4.1/build/selector/selector-beta-min.js\"></script> 
<script type=\"text/javascript\" src=\"http://yui.yahooapis.com/2.4.1/build/yuiloader/yuiloader-beta-min.js\"></script> 

<!--YUI's UI Controls:--> 
<script type=\"text/javascript\" src=\"http://yui.yahooapis.com/2.4.1/build/container/container-min.js\"></script> 
<script type=\"text/javascript\" src=\"http://yui.yahooapis.com/2.4.1/build/menu/menu-min.js\"></script> 
<script type=\"text/javascript\" src=\"http://yui.yahooapis.com/2.4.1/build/autocomplete/autocomplete-min.js\"></script> 
<script type=\"text/javascript\" src=\"http://yui.yahooapis.com/2.4.1/build/button/button-min.js\"></script> 
<script type=\"text/javascript\" src=\"http://yui.yahooapis.com/2.4.1/build/calendar/calendar-min.js\"></script> 
<script type=\"text/javascript\" src=\"http://yui.yahooapis.com/2.4.1/build/charts/charts-experimental-min.js\"></script> 
<script type=\"text/javascript\" src=\"http://yui.yahooapis.com/2.4.1/build/colorpicker/colorpicker-beta-min.js\"></script> 
<script type=\"text/javascript\" src=\"http://yui.yahooapis.com/2.4.1/build/datatable/datatable-beta-min.js\"></script> 
<script type=\"text/javascript\" src=\"http://yui.yahooapis.com/2.4.1/build/editor/editor-beta-min.js\"></script> 
<script type=\"text/javascript\" src=\"http://yui.yahooapis.com/2.4.1/build/logger/logger-min.js\"></script> 
<script type=\"text/javascript\" src=\"http://yui.yahooapis.com/2.4.1/build/slider/slider-min.js\"></script> 
<script type=\"text/javascript\" src=\"http://yui.yahooapis.com/2.4.1/build/tabview/tabview-min.js\"></script> 
<script type=\"text/javascript\" src=\"http://yui.yahooapis.com/2.4.1/build/treeview/treeview-min.js\"></script> 

<style type=\"text/css\">
/*margin and padding on body element
can introduce errors in determining
element position and are not recommended;
we turn them off as a foundation for YUI
CSS treatments. */
body {
margin:0;
padding:0;
}
</style>


<!--begin custom header content for this example-->
<style>
    #treeDiv1 {background: #fff; padding:1em;}
</style>
<!--end custom header content for this example-->

</head>

<body class=\" yui-skin-sam\">

<h1>Default TreeView</h1>

<div class=\"exampleIntro\">
	<p>In this simple example you see the default presentation for the <a href=\"http://developer.yahoo.com/yui/treeview/\">TreeView Control</a>.  Click on labels or on the expand/collapse icons for each node to interact with the TreeView Control.</p>
			
</div>

<!--BEGIN SOURCE CODE FOR EXAMPLE =============================== -->

<div id=\"treeDiv1\"></div>

<script type=\"text/javascript\">

//global variable to allow console inspection of tree:
var tree;

//anonymous function wraps the remainder of the logic:
(function() {

	//function to initialize the tree:
    function treeInit() {
        buildRandomTextNodeTree();
    }
    
	//Function  creates the tree and 
	//builds between 3 and 7 children of the root node:
    function buildRandomTextNodeTree() {
	
		//instantiate the tree:
        tree = new YAHOO.widget.TreeView(\"treeDiv1\");

        for (var i = 0; i < Math.floor((Math.random()*4) + 3); i++) {
            var tmpNode = new YAHOO.widget.TextNode(\"label-\" + i, tree.getRoot(), false);
            // tmpNode.collapse();
            // tmpNode.expand();
            // buildRandomTextBranch(tmpNode);
            buildLargeBranch(tmpNode);
        }

       // Expand and collapse happen prior to the actual expand/collapse,
       // and can be used to cancel the operation
       tree.subscribe(\"expand\", function(node) {
                // return false; // return false to cancel the expand
           });

       tree.subscribe(\"collapse\", function(node) {
           });

       // Trees with TextNodes will fire an event for when the label is clicked:
       tree.subscribe(\"labelClick\", function(node) {
           });

		//The tree is not created in the DOM until this method is called:
        tree.draw();
    }

	//function builds 10 children for the node you pass in:
    function buildLargeBranch(node) {
        if (node.depth < 10) {
            for ( var i = 0; i < 10; i++ ) {
                new YAHOO.widget.TextNode(node.label + \"-\" + i, node, false);
            }
        }
    }

	//Add an onDOMReady handler to build the tree when the document is ready
    YAHOO.util.Event.onDOMReady(treeInit);

})();

</script>

<!--END SOURCE CODE FOR EXAMPLE =============================== -->



</body>
</html>";
?>
