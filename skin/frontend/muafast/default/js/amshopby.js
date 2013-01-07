function amshopby_start(){
    
    var btn = $('amshopby-price-btn');
    if (Object.isElement(btn)){
        btn.observe('mouseover', amshopby_price_click_callback);
		
    }

    $$('.block-layered-nav .input-text').each(function (e){
        e.observe('focus', amshopby_price_focus_callback);
        e.observe('keypress', amshopby_price_click_callback);
    });
    
    $$('a.amshopby-less', 'a.amshopby-more').each(function (a){
        a.observe('click', amshopby_toggle);
    });
    
    $$('span.amshopby-plusminus').each(function (span){
        span.observe('mouseover', amshopby_category_show);
    });
    
    /*$$('.block-layered-nav dl').each(function (dl){
        dl.observe('mouseover', amshopby_filter_show);
		//dl.observe('mouseout', amshopby_filter_hide);
    });
    
	$$('dl.drop ol').each(function (ol){
		ol.observe('mouseout', amshopby_filter_hide);
    });
	
    $$('.block-layered-nav dt img').each(function (img){
        img.observe('mouseover', amshopby_tooltip_show);
        img.observe('mouseout', amshopby_tooltip_hide);
    });*/
    
    var param = $('amshopby-slider-param');
    if (Object.isElement(param)){
        param = param.value.split(',');
        amshopby_slider(param[0], param[1], param[2], param[3], param[4], param[5], param[6]); 
    }  
  
} 

function amshopby_price_click_callback(evt){
    if (evt && evt.type == 'keypress' && 13 != evt.keyCode)
        return;
    
    var numFrom = amshopby_price_format($('amshopby-price-from').value);
    var numTo   = amshopby_price_format($('amshopby-price-to').value);
  
    if ((numFrom < 0.01 && numTo < 0.01) || numFrom < 0 || numTo < 0)   
        return;   
        
    var url =  $('amshopby-price-url').value.gsub('price-from', numFrom).gsub('price-to', numTo);
    amshopby_set_location(url);
}

function amshopby_price_focus_callback(evt){
    var el = Event.findElement(evt, 'input');
    if (isNaN(parseFloat(el.value))){
        el.value = '';
    } 
}


function amshopby_price_format(num){
    num = parseFloat(num);

    if (isNaN(num))
        num = 0;
        

    return Math.round(num);
}


function amshopby_slider(width, from, to, max_value, prefix, min_value, ratePP) {
	
	width = parseFloat(width);
	from = parseFloat(from);
	to = parseFloat(to);
	max_value = parseFloat(max_value);
	min_value = parseFloat(min_value);
	
	
    var slider = $('amshopby-slider');
    
//    var allowedVals = new Array(11);
//    for (var i=0; i<allowedVals.length; ++i){
//        allowedVals[i] = Math.round(i * to /10);
//    }
    return new Control.Slider(slider.select('.handle'), slider, {
      range: $R(0, width),
      sliderValue: [from, to],
      restricted: true,
      //values: allowedVals,
      
      onChange: function (values){
        this.onSlide(values);  
        amshopby_price_click_callback(null);  
      },
      onSlide: function(values) {
    	  
    	
    	var fromPrice = Math.round(min_value + ratePP * values[0]);
    	var toPrice = Math.round(min_value + ratePP * values[1]);
    	
    	if ($(prefix+'from')) {
	        $(prefix+'from').value = fromPrice;
	        $(prefix+'to').value   = toPrice;
    	}
    	
        if ($('amshopby-slider-price-from')) {
	        $('amshopby-slider-price-from').update(fromPrice);
	        $('amshopby-slider-price-to').update(toPrice);
        }
      }
    });
}

function amshopby_toggle(evt){
    var attr = Event.findElement(evt, 'a').id.substr(14);
    
    $$('.amshopby-attr-' + attr).invoke('toggle');       
        
    $('amshopby-less-' + attr, 'amshopby-more-' + attr).invoke('toggle');
    
    Event.stop(evt);
    return false;
}

function amshopby_category_show(evt){
    var span = Event.findElement(evt, 'span');
    var id = span.id.substr(16);
    
    $$('.amshopby-cat-parentid-' + id).invoke('toggle');

    span.toggleClassName('minus'); 
    Event.stop(evt);
          
    return false;
}

function amshopby_filter_show(evt){
    var dt = Event.findElement(evt, 'dt');
    
    dt.next('dd').down('ol').toggle();
    dt.toggleClassName('amshopby-collapsed'); 
  
    Event.stop(evt);
    return false;
}

function amshopby_filter_hide(evt){
    var dt = Event.findElement(evt, 'dt');
    
    dt.next('dd').down('ol').hide();
    dt.removeClassName('amshopby-collapsed'); 
  
    Event.stop(evt);
    return false;
}

function amshopby_tooltip_show(evt){
    var img = Event.findElement(evt, 'img');
    var txt = img.alt;
    
    var tooltip = $(img.id + '-tooltip');
    if (!tooltip) {
        tooltip           = document.createElement('div');
        tooltip.className = 'amshopby-tooltip';
        tooltip.id        = img.id + '-tooltip';
        tooltip.innerHTML = img.alt;
    
        document.body.appendChild(tooltip);
    }
    
    var offset = Element.cumulativeOffset(img);
    tooltip.style.top  = offset[1] + 'px';
    tooltip.style.left = (offset[0] + 30) + 'px';
    tooltip.show();
}

function amshopby_tooltip_hide(evt){
    var img = Event.findElement(evt, 'img');
    var tooltip = $(img.id + '-tooltip');
    if (tooltip) {
        tooltip.hide();
    }    
}

function amshopby_set_location(url){
    if (typeof amshopby_working != 'undefined'){
        return amshopby_ajax_request(url);    
    }
    else
        return setLocation(url);
}

document.observe("dom:loaded", function() { amshopby_start(); }); 