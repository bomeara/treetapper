<?PHP
include ("template_entrydialogs.js");
echo("
<script type=\"text/javascript\">


/*
 Initialize and render the MenuBar when its elements are ready 
 to be scripted.
 */

YAHOO.util.Event.onAvailable(\"treetappernavigation\", function () {
	
	/*
	 Instantiate a MenuBar:  The first argument passed to the 
	 constructor is the id of the element in the page 
	 representing the MenuBar; the second is an object literal 
	 of configuration properties.
	 */
	
	var oMenuBar = new YAHOO.widget.MenuBar(\"treetappernavigation\", { 
autosubmenudisplay: true, 
hidedelay: 750, 
lazyload: true });
	
	/*
	 Define an array of object literals, each containing 
	 the data necessary to create a submenu.
	 */
	
	var aSubmenuData = [
		
		{
id: \"findtool\"  ,
itemdata: [ 
	{ text: \"Find available software\", url: \"".$treetapperbaseurl."/findtool.php\" }
	]
			
		},
		

{
id: \"findneed\" ,
itemdata: [ 
	{ text: \"Find areas where methods or software are lacking\", url: \"".$treetapperbaseurl."/findneed.php\" }
	]
},

{
id: \"addmethodortool\" ,
itemdata: [ 
	//{ text: \"Add information to the database\", url: \"".$treetapperbaseurl."/addmethodortool.php\" },
	{ text: \"Upload file of references (login required)\", url: \"".$treetapperbaseurl."/uploadreferenceXML.php\" },
	//{ text: \"Upload RIS file of references using JS\", onclick: { fn: YAHOO.example.pagecontent.uploadrisdialog.show } },
	//{ text: \"Upload RIS file of references using JS\", onclick: { fn: activateuploadris }  },
	{ text: \"Add new method (login required)\", url: \"".$treetapperbaseurl."/add_method.php\" },
	{ text: \"Add new software (login required)\", url: \"".$treetapperbaseurl."/add_program.php\" }
	]
},

	 {
	 id: \"overview\" ,
	 itemdata: [ 
	 { text: \"People (treetapper.org/person)\", url: \"".$treetapperbaseurl."/person\" },
	 { text: \"References (treetapper.org/reference)\", url: \"".$treetapperbaseurl."/reference\" },
	 { text: \"Methods (treetapper.org/method)\", url: \"".$treetapperbaseurl."/method\" },
	{ text: \"Programs (treetapper.org/program)\", url: \"".$treetapperbaseurl."/program\" },
	{ text: \"Terms (treetapper.org/vocabulary)\", url: \"".$treetapperbaseurl."/vocabulary\" }
	 ]
	 },
	 
{
id: \"discuss\" ,
itemdata: [ 
	{ text: \"Discussion forum\", url: \"".$treetapperbaseurl."/discuss.php\" }
	]
},


{
id: \"registration\", 
itemdata: [ 
//	{ text: \"Log in\", url: \"".$treetapperbaseurl."/login.php\" },
	{ text: \"Log in\", onclick: { fn: activatecheckauth }  },
	{ text: \"Log out\", url: \"".$treetapperbaseurl."/logout.php\" },
	{ text: \"New user (not added yet)\", url: \"".$treetapperbaseurl."/newuser.php\" }
	]
},

{
id: \"tutorial\"  ,
itemdata: [ 
	{ text: \"Basic tutorials\", url: \"".$treetapperbaseurl."/tutorial.php\" }
	]
},

{
id: \"api\" ,
itemdata: [ 
	{ text: \"Interface for other sites and programs\", url: \"".$treetapperbaseurl."/api.php\" }
	]
},

{
id: \"faq\" ,
itemdata: [ 
	{ text: \"Frequently asked questions\", url: \"".$treetapperbaseurl."/faq.php\" }
	]
},

{
id: \"devblog\" ,
itemdata: [ 
	{ text: \"Blog with development history\", url: \"http://treetapper-dev.blogspot.com\" },
	{ text: \"Blog feed\", url:\"http://treetapper-dev.blogspot.com/feeds/posts/default\" }
	]
},

];


var Dom = YAHOO.util.Dom,
oAnim;  // Animation instance


/*
 \"beforeshow\" event handler for each submenu of the MenuBar
 instance, used to setup certain style properties before
 the menu is animated.
 */

function onSubmenuBeforeShow(p_sType, p_sArgs) {
	
	var oBody,
	oShadow,
	oUL;
	
	
	if (this.parent) {
		
		/*
		 Get a reference to the Menu's shadow element and 
		 set its \"height\" property to \"0px\" to syncronize 
		 it with the height of the Menu instance.
		 */
		
		oShadow = this.element.lastChild;
		oShadow.style.height = \"0px\";
		
		
		/*
		 Stop the Animation instance if it is currently 
		 animating a Menu.
		 */ 
		
		if (oAnim && oAnim.isAnimated()) {
			
			oAnim.stop();
			oAnim = null;
			
		}
		
		
		/*
		 Set the body element's \"overflow\" property to 
		 \"hidden\" to clip the display of its negatively 
		 positioned <ul> element.
		 */ 
		
		oBody = this.body;
		
		
		/*
		 There is a bug in gecko-based browsers where 
		 an element whose \"position\" property is set to 
		 \"absolute\" and \"overflow\" property is set to 
		 \"hidden\" will not render at the correct width when
		 its offsetParent's \"position\" property is also 
		 set to \"absolute.\"  It is possible to work around 
		 this bug by specifying a value for the width 
		 property in addition to overflow.
		 */
		
		if (this.parent && 
			!(this.parent instanceof YAHOO.widget.MenuBarItem) && 
			YAHOO.env.ua.gecko) {
			
			Dom.setStyle(oBody, \"width\", (oBody.clientWidth + \"px\"));
			
		}
		
		
		Dom.setStyle(oBody, \"overflow\", \"hidden\");
		
		
		/*
		 Set the <ul> element's \"marginTop\" property 
		 to a negative value so that the Menu's height
		 collapses.
		 */ 
		
		oUL = oBody.getElementsByTagName(\"ul\")[0];
		
		Dom.setStyle(oUL, \"marginTop\", (\"-\" + oUL.offsetHeight + \"px\"));
		
	}
	
}


/*
 \"tween\" event handler for the Anim instance, used to 
 syncronize the size and position of the Menu instance's 
 shadow and iframe shim (if it exists) with its 
 changing height.
 */

function onTween(p_sType, p_aArgs, p_oShadow) {
	
	if (this.cfg.getProperty(\"iframe\")) {
		
		this.syncIframe();
		
	}
	
	if (p_oShadow) {
		
		p_oShadow.style.height = this.element.offsetHeight + \"px\";
		
	}
	
}


/*
 \"complete\" event handler for the Anim instance, used to 
 remove style properties that were animated so that the 
 Menu instance can be displayed at its final height.
 */

function onAnimationComplete(p_sType, p_aArgs, p_oShadow) {
	
	var oBody = this.body,
	oUL = oBody.getElementsByTagName(\"ul\")[0];
	
	if (p_oShadow) {
		
		p_oShadow.style.height = this.element.offsetHeight + \"px\";
		
	}
	
	Dom.setStyle(oUL, \"marginTop\", \"\");
	Dom.setStyle(oBody, \"overflow\", \"\");
	
	
	if (this.parent && 
		!(this.parent instanceof YAHOO.widget.MenuBarItem) && 
		YAHOO.env.ua.gecko) {
		
		Dom.setStyle(oBody, \"width\", \"\");
		
	}
	
}


/*
 \"show\" event handler for each submenu of the MenuBar 
 instance - used to kick off the animation of the 
 <ul> element.
 */

function onSubmenuShow(p_sType, p_sArgs) {
	
	var oElement,
	oShadow,
	oUL;
	
	if (this.parent) {
		
		oElement = this.element;
		oShadow = oElement.lastChild;
		oUL = this.body.getElementsByTagName(\"ul\")[0];
		
		
		/*
		 Animate the <ul> element's \"marginTop\" style 
		 property to a value of 0.
		 */
		
		oAnim = new YAHOO.util.Anim(oUL, 
									{ marginTop: { to: 0 } },
									.5, YAHOO.util.Easing.easeOut);
		
		
		oAnim.onStart.subscribe(function () {
			
			oShadow.style.height = \"100%\";
			
		});
		
		
		oAnim.animate();
		
		
		/*
		 Subscribe to the Anim instance's \"tween\" event for 
		 IE to syncronize the size and position of a 
		 submenu's shadow and iframe shim (if it exists)  
		 with its changing height.
		 */
		
		if (YAHOO.env.ua.ie) {
			
			oShadow.style.height = oElement.offsetHeight + \"px\";
			
			
			/*
			 Subscribe to the Anim instance's \"tween\"
			 event, passing a reference Menu's shadow 
			 element and making the scope of the event 
			 listener the Menu instance.
			 */
			
			oAnim.onTween.subscribe(onTween, oShadow, this);
			
		}
		
		
		/*
		 Subscribe to the Anim instance's \"complete\" event,
		 passing a reference Menu's shadow element and making 
		 the scope of the event listener the Menu instance.
		 */
		
		oAnim.onComplete.subscribe(onAnimationComplete, oShadow, this);
		
	}
	
}


/*
 Subscribe to the \"beforerender\" event, adding a submenu 
 to each of the items in the MenuBar instance.
 */

oMenuBar.subscribe(\"beforeRender\", function () {
	
	if (this.getRoot() == this) {
		
		this.getItem(0).cfg.setProperty(\"submenu\", aSubmenuData[0]);
		this.getItem(1).cfg.setProperty(\"submenu\", aSubmenuData[1]);
		this.getItem(2).cfg.setProperty(\"submenu\", aSubmenuData[2]);
		this.getItem(3).cfg.setProperty(\"submenu\", aSubmenuData[3]);
		this.getItem(4).cfg.setProperty(\"submenu\", aSubmenuData[4]);
		this.getItem(5).cfg.setProperty(\"submenu\", aSubmenuData[5]);
		this.getItem(6).cfg.setProperty(\"submenu\", aSubmenuData[6]);
		this.getItem(7).cfg.setProperty(\"submenu\", aSubmenuData[7]);
		this.getItem(8).cfg.setProperty(\"submenu\", aSubmenuData[8]);
		
	}
	
});


/*
 Subscribe to the \"beforeShow\" and \"show\" events for 
 each submenu of the MenuBar instance.
 */

oMenuBar.subscribe(\"beforeShow\", onSubmenuBeforeShow);
oMenuBar.subscribe(\"show\", onSubmenuShow);


/*
 Call the \"render\" method with no arguments since the 
 markup for this MenuBar instance is already exists in 
 the page.
 */

oMenuBar.render();         

});

</script>
<div id=\"treetappernavigation\" class=\"yuimenubar yuimenubarnav\">
<div class=\"bd\">

<ul class=\"first-of-type\">

<li class=\"yuimenubaritem first-of-type\">
<a class=\"yuimenubaritemlabel\" href=\"".$treetapperbaseurl."/findtool.php\">Find method/tool</a>
</li>

<li class=\"yuimenubaritem\">
<a class=\"yuimenubaritemlabel\" href=\"".$treetapperbaseurl."/findneed.php\">Identify need</a>
</li>

<li class=\"yuimenubaritem\">
<a class=\"yuimenubaritemlabel\" href=\"".$treetapperbaseurl."/addmethodortool.php\">Add method/tool</a>
</li>

	 <li class=\"yuimenubaritem\">
	 <a class=\"yuimenubaritemlabel\" href=\"".$treetapperbaseurl."/index.php\">Overviews</a>
	 </li>
	 
	 
<li class=\"yuimenubaritem\">
<a class=\"yuimenubaritemlabel\" href=\"".$treetapperbaseurl."/discuss.php\">Discuss</a>
</li>

<li class=\"yuimenubaritem\">
<a class=\"yuimenubaritemlabel\" href=\"".$treetapperbaseurl."/login.php\">Registration</a>
</li>

<li class=\"yuimenubaritem\">
<a class=\"yuimenubaritemlabel\" href=\"".$treetapperbaseurl."/tutorial.php\">Tutorials</a>
</li>

<li class=\"yuimenubaritem\">
<a class=\"yuimenubaritemlabel\" href=\"".$treetapperbaseurl."/api.php\">API</a>
</li>

<li class=\"yuimenubaritem\">
<a class=\"yuimenubaritemlabel\" href=\"".$treetapperbaseurl."/faq.php\">FAQ</a>
</li>

<li class=\"yuimenubaritem\">
<a class=\"yuimenubaritemlabel\" href=\"http://treetapper-dev.blogspot.com\">Dev blog</a>
</li>

</ul>
</div>
</div>
");
?>
