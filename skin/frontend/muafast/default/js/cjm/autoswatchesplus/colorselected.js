// Swatch Selector Javascript - Copyright 2011 CJM Creative Designs

function colorSelected(id, value, product_image_src, front_label) {

	var theswitchis = 'off',
		switchCounter = 0,
		k = 0,
		b = 0,
		l= 0,
		thelements = [],
		children = [],
		newchildren = [],
		thisdiv = '',
		thisopt = '',
		nextAttribute = '',
		nextAttrib = [],
		theoptions = [],
		selectedmoreview = [],
		moreviews = [],
		disableDiv = '',
		i,d,e,h,z,dropdown,textdiv,theattributeid,thedropdown,thetextdiv,selectedid,thedivid,thediv,dropdownval,base_image;
	
		// Set the base image to the selected swatch
		base_image = document.getElementById("image1");
		if (product_image_src !== '') {
			base_image.setAttribute("src", product_image_src);}
		
		// Load the more views for the selected swatch		
		selectedmoreview = document.getElementsByName('moreview' + value);
				
	// ------------------------------------------------------------------------------------------
	// --- RESET ALL SWATCH BORDERS, DROPDOWNS, MORE VIEWS AND TEXT BELOW THE SELECTED SWATCH ---
	// ------------------------------------------------------------------------------------------
				
	// Go through every swatch attribute on product
	
	for (i = 0; i < attoptions.length; i += 1)
	{
		theattributeid = attoptions[i]; 
		thedropdown = 'attribute' + theattributeid; 
		thetextdiv = 'divattribute' + theattributeid; 
		selectedid = id;
	
		// If we are on the selected swatch dropdown, turn the switch on
		if (selectedid === thedropdown) {
			theswitchis = 'on'; } 
				
		// If we are either on the dropdown we selected the swatch from or a dropdown below
		if (theswitchis === 'on')
		{
			// If we are on the dropdown after the selected swatch dropdown, get
			// the next attribute id
			if(switchCounter === 1) {
				nextAttribute = theattributeid; } 
			
			dropdown = document.getElementById(thedropdown);
			textdiv =  document.getElementById(thetextdiv);	
			textdiv.innerHTML = selecttitle;
			dropdown.selectedIndex = 0;
			
			// If we are not on the selected attribute, reset all swatches below so they are all displayed.
			if(switchCounter !== 0) {
				
				thelements = document.getElementById('ul-attribute' + theattributeid).getElementsByTagName('*');
				
				for(h = 0; h < thelements.length; h += 1) {
					if (thelements[h].nodeName.toLowerCase() === 'div' || thelements[h].nodeName.toLowerCase() === 'img') {
						thedivid = thelements[h].id;
						thisdiv = document.getElementById(thedivid);
						thisdiv.className = "swatch";
					}
				}
			}
			
			// Go through all the swatches of this attribute and reset
			for (z = 1; z < dropdown.options.length; z += 1)
			{
				dropdownval = dropdown.options[z].value;
				moreviews = document.getElementsByName('moreview' + dropdownval);
				thediv = document.getElementById(dropdownval);
				// Unselect the swatch
				if (thediv !== null ) {
					thediv.className = "swatch";}
				// Hide the more view images of the swatch
				if (moreviews !== null ) {
					for(d = 0; d < moreviews.length; d += 1) {
						moreviews[d].style.display = 'none'; }
				}
			}
			switchCounter += 1;
		}
	}
	
	// If there is only one attribute on this product, set the
	// next attribute to none
	if(nextAttribute === null || nextAttribute === '') {
		nextAttribute = 'none'; }
			
	// ------------------------------------------------------------------------
	// ------------------- SELECT THE CORRECT SWATCH --------------------------
	// ------------------------------------------------------------------------
			
	dropdown = document.getElementById(id);
		
	for (i = 0; i < dropdown.options.length; i += 1)
	{
		if ( dropdown.options[i].value === value )
		{
			document.getElementById('div' + id).innerHTML = front_label;
			document.getElementById(value).className = "swatchSelected";
			if ( selectedmoreview !== null ) {
				for(e = 0; e < selectedmoreview.length; e += 1) {
					selectedmoreview[e].style.display = 'block'; }
			}	
			dropdown.selectedIndex = i;
			break;
		}
	}
		
	spConfig.configureElement($(id));
	
	// -------------------------------------------------------------------------
	// -------------------- HIDE UNAVAILABLE SWATCHES --------------------------
	// -------------------------------------------------------------------------
	
	// If there is more then one swatch attribute on this product
	if(nextAttribute !== 'none') {
		
		
		
		// Get all the swatches of the next attribute
		children = document.getElementById('ul-attribute' + nextAttribute).getElementsByTagName('*');
		// Set the next attributes dropdown
		nextAttrib = document.getElementById('attribute' + nextAttribute);
		
		// Get all wanted option values
		for(h = 0; h < nextAttrib.options.length; h += 1) {
			if(nextAttrib.options[h].value !== '') {
				theoptions[b] = nextAttrib.options[h].value;
				b += 1;
			}
		}
		
		//Get all divs and imgs
		for(h = 0; h < children.length; h += 1) {
			if (children[h].nodeName.toLowerCase() === 'div' || children[h].nodeName.toLowerCase() === 'img') {
				newchildren[k] = children[h].id;
				k += 1;
			}
		}
		
		for(h = 0; h < newchildren.length; h += 1) {
			thisdiv = newchildren[h];
			if(theoptions[l]) {
				thisopt = theoptions[l];
				if(thisopt === thisdiv) {
					disableDiv = document.getElementById(thisdiv);
					disableDiv.className = 'swatch';
					l += 1;
				} else {
					disableDiv = document.getElementById(thisdiv);
					disableDiv.className = 'disabledSwatch';
				}
			} else {
				disableDiv = document.getElementById(thisdiv);
				disableDiv.className = 'disabledSwatch';
			}
		}
	}
	// Not sure if this is still needed
	//this.reloadPrice();
}