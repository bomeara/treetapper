<?PHP
$_GET['pagetitle']="TreeTapper: Find missing methods or software";
include('templates/template_pagestart.php');
include ('templates/dblogin.php'); //connection in $db

?>
<script type="text/javascript">


function drawCircle(center, radius, color, thickness, opacity, text) {
	//Function created by Chris Haas, found at http://www3.telus.net/DougHenderson/MapCircle_v2.html
	var circleQuality = 1;			//1 is best but more points, 5 looks pretty good, too
	var M = Math.PI / 180;			//Create Radian conversion constant
	var L = map.getBounds();		//Holds copy of map bounds for use below
	var sw = L.getSouthWest();
	var ne = L.getNorthEast();
	
	//The map is not completely square so this calculates the lat/lon ratio
	// this works because we create a square map
	var circleSquish = (ne.lng() - sw.lng()) / (ne.lat() - sw.lat());
	
	var points = [];							//Init Point Array
	//Loop through all degrees from 0 to 360
	for(var i=0; i<360; i+=circleQuality){
		var P = new GLatLng(
							center.lat() + (radius * Math.sin(i * M)),
									//center.lng() + (radius * Math.cos(i * M)) * circleSquish
							center.lng() + (radius * Math.cos(i * M))
							);
		points.push(P);
	}
	points.push(points[0]);	// close the circle
	var p = new GPolyline(points, color, thickness, opacity)
	map.addOverlay(p, {title:text});
}



drawChart = function() {
	<?PHP
	echo "\nvar baseurl=\"".$treetapperbaseurl."\";";
	?>
	map.clearOverlays();
	var treetablearray=new Array();
	var ul=YAHOO.util.Dom.get("ul1");
	var items = ul.getElementsByTagName("li");
	var tablenames="?tablenames=";
	var tableoptions="&tableoptions=";
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
	//alert(tablenames + tableoptions);
	
	GDownloadUrl('templates/findneed_js.php' + tablenames + tableoptions, function(data, responseCode) {
		var baseIcon = new GIcon();
		baseIcon.iconSize = new GSize(10, 10);
		baseIcon.iconAnchor = new GPoint(5, 5);
		baseIcon.infoWindowAnchor = new GPoint(5, 5);		
		function CreateNewMarker (myPoint,myIcon,myLabel,myMethodLabel,mySoftwareLabel,myTooltip, methodcount, softwarecount) {
			var nodemarker=new GMarker(myPoint, {icon:myIcon, title:myTooltip});
			map.addOverlay(nodemarker);
			GEvent.addListener(nodemarker, "click", function() {
				nodemarker.openInfoWindowTabsHtml([new GInfoWindowTab("Overview",myLabel), new GInfoWindowTab("Methods (" + methodcount + ")",myMethodLabel), new GInfoWindowTab("Software (" + softwarecount + ")",mySoftwareLabel)]);
			});
		}
		var xml = GXml.parse(data);
		var circles = xml.documentElement.getElementsByTagName("circle");
		var maxradius=0;
		for (var i = 0; i < circles.length; i++) {
			var radius = parseFloat(circles[i].getAttribute("radius"));
			if (radius>maxradius) {
				maxradius=radius;
			}
			var text=circles[i].getAttribute("label");
			drawCircle(new GLatLng(0, 0),radius,"#C0C0C0",1,1,text);
			//alert('Radius is ' + radius);
		}		
		var bounds=new GLatLngBounds(new GLatLng(-1.0*maxradius, -1.0*maxradius),new GLatLng(maxradius, maxradius));
		var markers = xml.documentElement.getElementsByTagName("marker");
		for (var i = 0; i < markers.length; i++) {
			var point = new GLatLng(parseFloat(markers[i].getAttribute("lat")),
									parseFloat(markers[i].getAttribute("lng")));
			var myIcon = new GIcon(baseIcon);
			myIcon.image = "/images/" + markers[i].getAttribute("colorname") + "node.png";
			var labelarray= new Array()
			labelarray[0] = markers[i].getAttribute("label");
			labelarray[1] = markers[i].getAttribute("methodlabel");
			labelarray[2] = markers[i].getAttribute("softwarelabel");
			var methodcount = markers[i].getAttribute("methodcount");
			var softwarecount = markers[i].getAttribute("softwarecount");
			for (var labelnum=0; labelnum<3; labelnum++) {
				labelarray[labelnum]=(labelarray[labelnum]).replace(/UNBOLD/g,"</b>");
				labelarray[labelnum]=(labelarray[labelnum]).replace(/BOLD/g,"<b>");
				labelarray[labelnum]=(labelarray[labelnum]).replace(/LF/g,"<br>");
				labelarray[labelnum]=(labelarray[labelnum]).replace(/UNAHREF/g,"'>");
				labelarray[labelnum]=(labelarray[labelnum]).replace(/AHREF/g,"<a href='"+baseurl+'/');
				labelarray[labelnum]=(labelarray[labelnum]).replace(/ENDHREF/g,"</a>");
			}
	/*		while (/METHODID/.test(markerlabel)) { //while there's a METHODID label
				var match = /METHODID(\d+)UNIDNAME([^(UNNAME)]*)/.exec(markerlabel);
				var replacestring = '<a href="' + baseurl + "/method/" + match[1] + '">' + match[2] + "</a>";
				markerlabel.replace(/match[0]/,replacestring);
			}
			while (/SOFTWAREID/.test(markerlabel)) { //while there's a SOFTWAREID label
				var match = /SOFTWAREID(\d+)UNIDNAME([^(UNNAME)]*)/.exec(markerlabel);
				var replacestring = '<a href="' + baseurl + "/program/" + match[1] + '">' + match[2] + "</a>";
				markerlabel.replace(/match[0]/,replacestring);
			}
			*/
			var tooltip = markers[i].getAttribute("tooltip");
			//var nodemarker=new GMarker(point, {icon:myIcon});
			//map.addOverlay(nodemarker);
			//GEvent.addListener(nodemarker, "mouseover", function() {
			//	nodemarker.openInfoWindow("Some stuff for " + i);
			//});
			//var markerlabel="cow" + i;
			CreateNewMarker(point,myIcon,labelarray[0],labelarray[1],labelarray[2],tooltip, methodcount, softwarecount);
			
		}	
		map.setMapType(G_NORMAL_MAP); //This is a hack to get zooming working properly
		map.setZoom(-1+map.getBoundsZoomLevel(bounds)); // zoom out a bit more than needed to speed putting up info windows later
		map.setCenter(bounds.getCenter());
		map.setMapType(custommap); // back to the original maptype
		var polylines = xml.documentElement.getElementsByTagName("polyline");
		for (var i = 0; i < polylines.length; i++) {
			var startpoint = new GLatLng(parseFloat(polylines[i].getAttribute("startlat")),parseFloat(polylines[i].getAttribute("startlng")));
			var endpoint = new GLatLng(parseFloat(polylines[i].getAttribute("endlat")),parseFloat(polylines[i].getAttribute("endlng")));
			map.addOverlay(new GPolyline([startpoint, endpoint], polylines[i].getAttribute("color"), polylines[i].getAttribute("weight"), polylines[i].getAttribute("opacity")));
		}		
		
		
		//alert('End GDownloadURL ' + responseCode + " " + circles.length + " " + markers.length);
	});
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
<h3>Drag elements above or below the purple line to add or remove them from the chart; you can also rearrange the order of elements on the tree by dragging the boxes below. Click the button to update the chart. <input type="button" id="drawChartButton" onclick="drawChart()" value="Update chart" />
<br>
<div id="sortinglist" style="height: 500px">
<div class="workarea">
<ul id="ul1" class="draglist">
<?PHP
$pendingtables=array(22,-1,12,13,24,1,2,4,4,4,8,9,21,35,36);
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
			//echo ": <select name=\"".pg_fetch_result($result,0,0)."\" id=\"".pg_fetch_result($result,0,0)."\" onchange=\"drawChart()\">";
			echo ": <select name=\"".pg_fetch_result($result,0,0)."\" id=\"".pg_fetch_result($result,0,0)."\">";
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
			//echo ": <select name=\"".pg_fetch_result($result,0,0)."_".$charactercount."\" id=\"".pg_fetch_result($result,0,0)."_".$charactercount."\" onchange=\"drawChart()\">";
			echo ": <select name=\"".pg_fetch_result($result,0,0)."_".$charactercount."\" id=\"".pg_fetch_result($result,0,0)."_".$charactercount."\">";
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
	//drawChart();
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
<br>Chart<br>
<image src="images/rednode.png">=No methods or software 
<br><image src="images/bluenode.png">=Methods, no software
<br><image src="images/blacknode.png">=Methods and software
<br>Mouseover a node for info; click on a node for more detailed information.
<br>

<center><script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=ABQIAAAAtMHDuQXJCnjYm2C1nuC0gxRrKXAGH9v7TawpEPrzRPn3mnOCyxSQcYQGZwfy9cip6X2Ie2X_ZrZSVA"
type="text/javascript"></script></center>
<script type="text/javascript">

    //<![CDATA[

function loadmap() {
	if (GBrowserIsCompatible()) {
		var copyCollection = new GCopyrightCollection('');
		var copyright = new GCopyright(1, new GLatLngBounds(new GLatLng(-90, -180), new GLatLng(90, 180)), 0, "TreeTapper.org");
		copyCollection.addCopyright(copyright);
		function CustomGetTileUrl(a,b) {
			var f = "/images/white.jpg";
			return f;
		}
		var tilelayers = [new GTileLayer(copyCollection, 1, 20)];
		tilelayers[0].getTileUrl = CustomGetTileUrl;
		
		var baseIcon = new GIcon();
		baseIcon.iconSize = new GSize(10, 10);
		baseIcon.iconAnchor = new GPoint(5, 5);
		baseIcon.infoWindowAnchor = new GPoint(5, 5);
		var redIcon = new GIcon(baseIcon);
		redIcon.image = "/images/rednode.png";
		var blackIcon = new GIcon(baseIcon);
		blackIcon.image = "/images/blacknode.png";
		var grayIcon = new GIcon(baseIcon);
		grayIcon.image = "/images/graynode.png";
		var purpleIcon = new GIcon(baseIcon);
		purpleIcon.image = "/images/purplenode.png";
		var blueIcon = new GIcon(baseIcon);
		blueIcon.image = "/images/bluenode.png"; 
		custommap = new GMapType(tilelayers, new GMercatorProjection(18), "Chart", {errorMessage:"No chart data available"}); //note that custommap is global (first usage is here, no "var" preceding it.
		 map = new GMap2(document.getElementById("map"), {mapTypes:[custommap]}); //note that map is global (first usage is here, no "var" preceding it.
		map.addControl(new GSmallZoomControl());
		map.setCenter(new GLatLng(0, 0), 6, custommap);
        //var polyline = new GPolyline([
		//	new GLatLng(0, -5),
		//	new GLatLng(0, 0)
		//	], "#ff0000", 10);
		//map.addOverlay(polyline);
		//map.addOverlay(new GPolyline([new GLatLng(0, 5), new GLatLng(0, 0)], "#0000ff", 2, 1));
		//map.addOverlay(new GPolyline([new GLatLng(5, 0), new GLatLng(0, 0)], "#0000ff", 2, 0.5));
		//map.addOverlay(new GMarker(new GLatLng(5, 0),{ icon:redIcon }));
		//drawCircle(new GLatLng(0, 0),5,"#00ff00",1,1);
		drawChart();
	}
}
YAHOO.util.Event.onDOMReady(loadmap);
    //]]>
</script>

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
<div id="map" style="width: 800px; height: 800px"></div>




<?PHP 
include('templates/template_pageend.php');
?>
