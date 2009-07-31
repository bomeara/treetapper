<?PHP
$_GET['pagetitle']="TreeTapper: Find missing methods or software";
include('templates/template_pagestart.php');
include ('templates/dblogin.php'); //connection in $db

?>
<script type="text/javascript">


function updateTargetURL() {
	<?PHP
	echo "\nvar baseurl=\"".$treetapperbaseurl."\";\n";
	?>
	var treetablearray=new Array();
	var ul=YAHOO.util.Dom.get("ul1");
	var items = ul.getElementsByTagName("li");
	var tablenames="?tablenames=";
	var tableoptions="";
	for (i=0;i<items.length;i=i+1) {
		if((items[i].id).match("CUTOFF")) {
			break;
		}
		else {
			var elementname=(items[i].id).substring(0,(items[i].id).length-3);
			if (i!=0) {
				tablenames=tablenames+',';
				tableoptions=tableoptions+',';
			}
			var optionvalue=(document.getElementById(elementname)).value;
			tablenames = tablenames + elementname;
			tableoptions = tableoptions + optionvalue;
		}
	}
	globaltableoptions=tableoptions;
	tableoptions='&tableoptions='+tableoptions;
	//globalTargetURL = baseurl + '/templates/findneed_stream.php' + tablenames + tableoptions;
	globalTargetURL = baseurl + '/templates/drawmissingcontroller.php' + tablenames + tableoptions;
	
	var div = document.getElementById('findmissing_container');
	//div.innerHTML ='<!--#include file="'+ globalTargetURL +'" -->';
	div.innerHTML = '<iframe src = "'+globalTargetURL+'" frameborder=0 height=900 width=900 scrolling=no></iframe>';
	/*
	var handleSuccess = function(o){
		
		
		if(o.responseText !== undefined){
		//	div.innerHTML = "<li>Transaction id: " + o.tId + "</li>";
		//	div.innerHTML += "<li>HTTP status: " + o.status + "</li>";
		//	div.innerHTML += "<li>Status code message: " + o.statusText + "</li>";
		//	div.innerHTML += "<li>HTTP headers: <ul>" + o.getAllResponseHeaders + "</ul></li>";
		//	div.innerHTML += "<li>Server response: " + o.responseText + "</li>";
		//	div.innerHTML += "<li>Argument object: Object ( [foo] => " + o.argument.foo +
		//		" [bar] => " + o.argument.bar +" )</li>";
			div.innerHTML = o.responseText;
		}
	}
	
	var handleFailure = function(o){
		
		if(o.responseText !== undefined){
			div.innerHTML = "<ul><li>Transaction id: " + o.tId + "</li>";
			div.innerHTML += "<li>HTTP status: " + o.status + "</li>";
			div.innerHTML += "<li>Status code message: " + o.statusText + "</li></ul>";
		}
	}
	
	var callback =
	{
success:handleSuccess,
failure:handleFailure
	};
	if (numberofrequests>0) {
		if (YAHOO.util.Connect.isCallInProgress(globalrequest)) { //previous one is still running
			YAHOO.util.Connect.abort(globalrequest); //abort it so that it doesn't overwrite the current one when it finishes
		}
	}
	globalrequest = YAHOO.util.Connect.asyncRequest('GET', globalTargetURL, callback);
	numberofrequests++;*/
}

</script>

<style type="text/css">

div.workarea { padding:10px; float:left }

ul.draglist_alt { 
position: relative;
//width: 400px; 
height:40px;
background: #f7f7f7;
border: 1px solid gray;
    list-style: none;
margin:0;
padding:0;
}

ul.draglist li {
margin: 1px;
cursor: move; 
}

ul.draglist { 
position: relative;
//width: 400px; 
    list-style: none;
margin:0;
padding:0;
    /*
	 The bottom padding provides the cushion that makes the empty 
	 list targetable.  Alternatively, we could leave the padding 
	 off by default, adding it when we detect that the list is empty.
	 */
    padding-bottom:20px;
}

ul.draglist_alt li {
margin: 1px;
cursor: move; 
}


li.list1 {
    background-color: #D1E6EC;
border:1px solid #7EA6B2;
}

li.list2 {
    background-color: #D8D4E2;
	border:1px solid #6B4C86;
}


</style>
<h3>Drag elements above or below the purple line to dynamically add or remove them from the chart; you can also rearrange the order of elements on the tree by dragging the boxes below. Scroll down to see the chart.
<br>
<div id="sortinglist" style="height: 450px">
<div class="workarea">
<ul id="ul1" class="draglist">
<?PHP
$pendingtables=array(4,8,36,-1,12,22,2,13,24,1,2,4,4,9,21,35);
$elementids=array();
$charactercount=0;
foreach ($pendingtables as $tableid) {
	
	if ($tableid>0) {
		$sql= "SELECT tablelist_name, tablelist_description, tablelist_id FROM tablelist WHERE tablelist_id=$tableid";
		$result = pg_query($db, $sql);
		$tabledescription=pg_fetch_result($result,0,1);
		$tablename=pg_fetch_result($result,0,0);
		if ($tableid!=4) {
			array_push($elementids,$tablename.'_li');
			$_GET['table'] = pg_fetch_result($result,0,0);
			echo "<li class=\"list1\" id=\"".$tablename."_li\">".$tabledescription;
			echo ": <select name=\"".pg_fetch_result($result,0,0)."\" id=\"".pg_fetch_result($result,0,0)."\" onchange=\"updateTargetURL()\" onclick=\"updateTargetURL()\">";
			//echo ": <select name=\"".pg_fetch_result($result,0,0)."\" id=\"".pg_fetch_result($result,0,0)."\">";
			$_GET['table'] = pg_fetch_result($result,0,0);
			include 'templates/optionbox_generic.php';
			echo "</select>";
			include 'templates/helppanel_generic.php';
			echo "</li>\n";
			
		}
		else {
			$charactercount++;
			array_push($elementids,$tablename."_".$charactercount.'_li');
			$_GET['table'] = pg_fetch_result($result,0,0);
			echo "<li class=\"list1\" id=\"".$tablename."_".$charactercount."_li\">".$tabledescription." ".$charactercount;
			echo ": <select name=\"".pg_fetch_result($result,0,0)."_".$charactercount."\" id=\"".pg_fetch_result($result,0,0)."_".$charactercount."\" onchange=\"updateTargetURL()\" onclick=\"updateTargetURL()\">";
			//echo ": <select name=\"".pg_fetch_result($result,0,0)."_".$charactercount."\" id=\"".pg_fetch_result($result,0,0)."_".$charactercount."\">";
			$_GET['table'] = pg_fetch_result($result,0,0);
			include 'templates/optionbox_generic.php';
			echo "</select>";
			if ($charactercount==1) {
				$_GET['table'] = "charactertype";
				include 'templates/helppanel_generic.php';
			}
			echo "</li>\n";
		}
	}
	else {
		array_push($elementids,"CUTOFF");
		echo "<li class=\"list2\" id=\"CUTOFF\"><b>Elements above this line appear on tree (first is closest to root, and so on)</b></li>\n";
	}
}

?>
</ul>
</div>




<script type="text/javascript">

(function() {
	
	var Dom = YAHOO.util.Dom;
	var Event = YAHOO.util.Event;
	var DDM = YAHOO.util.DragDropMgr;
	
//////////////////////////////////////////////////////////////////////////////
// example app
//////////////////////////////////////////////////////////////////////////////
	YAHOO.example.DDApp = {
init: function() {
	
	var cols=1,i,j;
	for (i=1;i<cols+1;i=i+1) {
		new YAHOO.util.DDTarget("ul"+i);
	}
	 <?PHP
		foreach ($elementids as $elementid) {
			echo "new YAHOO.example.DDList(\"".$elementid."\");\n ";
		}
	?>
	
	Event.on("showButton", "click", this.showOrder);
	Event.on("switchButton", "click", this.switchStyles);
	numberofrequests=0;
	updateTargetURL();
},

showOrder: function() {
	var parseList = function(ul, title) {
		var items = ul.getElementsByTagName("li");
		var out = title + ": ";
		for (i=0;i<items.length;i=i+1) {
			out += items[i].id + " ";
		}
		return out;
	};
	
	var ul1=Dom.get("ul1");
	alert(parseList(ul1, "List 1"));
	
},

switchStyles: function() {
	Dom.get("ul1").className = "draglist_alt";
	Dom.get("ul2").className = "draglist_alt";
}

	};

//////////////////////////////////////////////////////////////////////////////
// custom drag and drop implementation
//////////////////////////////////////////////////////////////////////////////

YAHOO.example.DDList = function(id, sGroup, config) {
	
    YAHOO.example.DDList.superclass.constructor.call(this, id, sGroup, config);
	
    var el = this.getDragEl();
    Dom.setStyle(el, "opacity", 0.67); // The proxy is slightly transparent
	
    this.goingUp = false;
    this.lastY = 0;
	this.addInvalidHandleType('select');
};

YAHOO.extend(YAHOO.example.DDList, YAHOO.util.DDProxy, {
	
startDrag: function(x, y) {
	
        // make the proxy look like the source element
	var dragEl = this.getDragEl();
	var clickEl = this.getEl();
	Dom.setStyle(clickEl, "visibility", "hidden");
	
	dragEl.innerHTML = clickEl.innerHTML;
	
	Dom.setStyle(dragEl, "color", Dom.getStyle(clickEl, "color"));
	Dom.setStyle(dragEl, "backgroundColor", Dom.getStyle(clickEl, "backgroundColor"));
	Dom.setStyle(dragEl, "border", "2px solid gray");
},

endDrag: function(e) {
	
	var srcEl = this.getEl();
	var proxy = this.getDragEl();
	
        // Show the proxy element and animate it to the src element's location
	Dom.setStyle(proxy, "visibility", "");
	var a = new YAHOO.util.Motion( 
		proxy, { 
			points: { 
				to: Dom.getXY(srcEl)
			}
		}, 
		0.2, 
		YAHOO.util.Easing.easeOut
	)
	var proxyid = proxy.id;
	var thisid = this.getEl();

        // Hide the proxy and show the source element when finished with the animation
	
	a.onComplete.subscribe(function() {
		Dom.setStyle(proxyid, "visibility", "hidden");
		Dom.setStyle(thisid, "visibility", "");
	});
	a.animate();
	updateTargetURL();
},

onDragDrop: function(e, id) {
	
        // If there is one drop interaction, the li was dropped either on the list,
        // or it was dropped on the current location of the source element.
	if (DDM.interactionInfo.drop.length === 1) {
		
            // The position of the cursor at the time of the drop (YAHOO.util.Point)
		var pt = DDM.interactionInfo.point; 
		
            // The region occupied by the source element at the time of the drop
		var region = DDM.interactionInfo.sourceRegion; 
		
            // Check to see if we are over the source element's location.  We will
            // append to the bottom of the list once we are sure it was a drop in
            // the negative space (the area of the list without any list items)
		if (!region.intersect(pt)) {
			var destEl = Dom.get(id);
			var destDD = DDM.getDDById(id);
			destEl.appendChild(this.getEl());
			destDD.isEmpty = false;
			DDM.refreshCache();
		}
		
	}
},

onDrag: function(e) {
	
        // Keep track of the direction of the drag for use during onDragOver
	var y = Event.getPageY(e);
	
	if (y < this.lastY) {
		this.goingUp = true;
	} else if (y > this.lastY) {
		this.goingUp = false;
	}
	
	this.lastY = y;
},

onDragOver: function(e, id) {
    
	var srcEl = this.getEl();
	var destEl = Dom.get(id);
	
        // We are only concerned with list items, we ignore the dragover
        // notifications for the list.
	if (destEl.nodeName.toLowerCase() == "li") {
		var orig_p = srcEl.parentNode;
		var p = destEl.parentNode;
		
		if (this.goingUp) {
			p.insertBefore(srcEl, destEl); // insert above
		} else {
			p.insertBefore(srcEl, destEl.nextSibling); // insert below
		}
		
		DDM.refreshCache();
	}
}
});

Event.onDOMReady(YAHOO.example.DDApp.init, YAHOO.example.DDApp, true);

})();


</script>
</div>

<br /><br /><div id="infobox"></div><br /><div id="findmissing_container" align="left" height="800px" width="800px"></div>

<script type="text/javascript">

	YAHOO.namespace("example.container");
	YAHOO.example.container.tooltippanel = new YAHOO.widget.Panel("tooltippanel", { context:["infobox", "tl", "bl"], width:"400px", visible:true, draggable:true, close:true } );
	//YAHOO.example.container.tooltippanel = new YAHOO.widget.Panel("tooltippanel", { width:"320px", visible:true, draggable:true, close:true } );
	YAHOO.example.container.tooltippanel.setHeader("Node info box");
	<?PHP
		echo "\nvar waitbar=\"".$treetapperbaseurl."/templates/ajax-loader-bar.gif\";";
	?>

	YAHOO.example.container.tooltippanel.setBody("<center>Now building diagram<br /><img src=" + waitbar + "></center>");
	
	YAHOO.example.container.tooltippanel.setFooter('Loading...');
	YAHOO.example.container.tooltippanel.render(document.body);


function updatepanel(header,body,footer) {
	YAHOO.namespace("example.container");
	body=body.replace(/QUOTE/g,"\"");
	body=body.replace(/\]/g,">");
	body=body.replace(/\[/g,"<");

	//YAHOO.example.container.tooltippanel = new YAHOO.widget.Panel("tooltippanel", { width:"320px", visible:true, draggable:true, close:true } );
	YAHOO.example.container.tooltippanel.setHeader(header);
	YAHOO.example.container.tooltippanel.setBody(body);
	YAHOO.example.container.tooltippanel.setFooter(footer);
	//YAHOO.example.container.tooltippanel.render(document.body);
};

function hidepanel() {
	
}
</script>

<?PHP
/*
<div id="processing_findmissingdynamic_container">

<!--[if !IE]> -->
<object classid="java:processing_findmissingdynamic.class" 
type="application/x-java-applet"
archive="processing_findmissingdynamic.jar"
width="800" height="800"
standby="Loading Processing software..." >

<param name="archive" value="processing_findmissingdynamic.jar" />

<param name="mayscript" value="true" />
<param name="scriptable" value="true" />

<param name="image" value="loading.gif" />
<param name="boxmessage" value="Loading Processing software..." />
<param name="boxbgcolor" value="#FFFFFF" />

<param name="test_string" value="outer" />
<!--<![endif]-->

<object classid="clsid:8AD9C840-044E-11D1-B3E9-00805F499D93" 
codebase="http://java.sun.com/update/1.4.2/jinstall-1_4_2_12-windows-i586.cab"
width="800" height="800"
standby="Loading Processing software..."  >

<param name="code" value="processing_findmissingdynamic" />
<param name="archive" value="processing_findmissingdynamic.jar" />

<param name="mayscript" value="true" />
<param name="scriptable" value="true" />

<param name="image" value="loading.gif" />
<param name="boxmessage" value="Loading Processing software..." />
<param name="boxbgcolor" value="#FFFFFF" />

<param name="test_string" value="inner" />

<p>
<strong>
This browser does not have a Java Plug-in.
<br />
<a href="http://java.sun.com/products/plugin/downloads/index.html" title="Download Java Plug-in">
Get the latest Java Plug-in here.
</a>
</strong>
</p>

</object>

<!--[if !IE]> -->
</object>
<!--<![endif]-->

</div>
*/
?>



<?PHP 
include('templates/template_pageend.php');
?>
