jQuery.noConflict();

jQuery(document).ready(function(){
	// christmas theme
	/* jQuery.fn.snow();
	// jQuery("body").addClass("christmas");
	// jQuery(".header-container").before("<div id='christmas-footer'></div>");

	jQuery(".home-spot .box:first").after("<div id='christmas-banner'><a href='javascript:void(0)'><img src='/skin/frontend/muafast/default/images/christmas/banner-christmas-sale.jpg'/></a></div>");
     */
	// end christmas theme
	
    jQuery("body").addClass("newyear");
    
    var _flag_browser_mozilla = jQuery.browser.mozilla;
    if(_flag_browser_mozilla != true){
        jQuery( 'body' ).addClass('chrome');
    }

    var nav_h = 51;
    var saiso = 0;
    function resizeNavigation(){
        var nav_w = jQuery("ul#nav").width();
        var current_nav_items_w = 0;
        jQuery("ul#nav li").each(function(){
            current_nav_items_w += jQuery(this).width();
        });
        if(nav_h*1.5 < jQuery("ul#nav").height() || nav_w > current_nav_items_w + saiso){
            //alert(nav_h*1.5 + " " + jQuery("ul#nav").height() + " " + nav_w + " " + current_nav_items_w );
            var number_of_nav_item = jQuery("ul#nav li").children().size();
            var nav_items_w = 0;
            jQuery("ul#nav li a").each(function(){
                nav_items_w += jQuery(this).width();
            });
            nav_items_w = Math.floor(nav_items_w);
            var padding_w = Math.floor((nav_w - nav_items_w)/(number_of_nav_item*2));
            var add_last_w = Math.floor(nav_w - nav_items_w - padding_w*number_of_nav_item*2);
            
            //alert(number_of_nav_item + " " + nav_w + " " + nav_items_w + " " + padding_w + " " + add_last_w);
            
            jQuery("ul#nav li a").css("padding", "0 " + padding_w + "px");
            jQuery("ul#nav li.last a").css("padding-right", padding_w + add_last_w +"px");
            var i=1;
            for(i=1; i<10; i++){
                if(jQuery("ul#nav").height() > nav_h*1.5){
                    jQuery("ul#nav li.last a").css("padding-right", padding_w + add_last_w - i +"px");
                }else{
                    break;
                }
            }
            saiso = i;
        }
    }

    resizeNavigation();
    window.setInterval(resizeNavigation, 500);
    
    // jQuery(".quick-access .links").hide();
    jQuery("div.rich-text").each(function(){
        var small_height = "65";
        var current_height = jQuery(this).height();
        if(current_height > small_height){
            
            var current_width = jQuery(this).width();

            var bt_more = document.createElement('span');
            jQuery(bt_more).addClass("bt-read-more");
            jQuery(bt_more).addClass("bt-read-more-expand");

            // bt_more_h = jQuery(bt_more).height();
            // bt_more_w = jQuery(bt_more).width();

            var bt_more_h = 20;
            var bt_more_w = 64;

            var bt_more_exp_t = small_height - current_height - bt_more_h ;
            var bt_more_col_t = - bt_more_h - 3;

            jQuery(bt_more).css("margin-top", bt_more_exp_t + "px");
            jQuery(bt_more).css("margin-left", current_width - bt_more_w + "px" );

            jQuery(this).append(bt_more);
            jQuery(this).css("height", small_height+"px")
            jQuery(this).css("cursor", "pointer")
            jQuery(this).click(function(){
                if(jQuery(this).height() == small_height){
                    jQuery(bt_more).hide();
                    jQuery(this).animate({
                        height: current_height
                    },"normal",function(){
                        jQuery(bt_more).removeClass("bt-read-more-expand");
                        jQuery(bt_more).addClass("bt-read-more-collapse");
                        jQuery(bt_more).css("margin-top", bt_more_col_t + "px");
                        jQuery(bt_more).show();
                    });

                }else{
                    jQuery(bt_more).hide();
                    jQuery(this).animate({
                        height: small_height
                    },"normal", function(){
                        jQuery(bt_more).removeClass("bt-read-more-collapse");
                        jQuery(bt_more).addClass("bt-read-more-expand");
                        jQuery(bt_more).css("margin-top", bt_more_exp_t + "px");
                        jQuery(bt_more).show();
                    });
                }
            });
        }
    });
});
// **************** add to cart by ajax*by khanh.phan************************
jQuery.noConflict();
jQuery(document).ready(function(){
    /*
    jQuery('.fancybox').fancybox(
    {
        hideOnContentClick : true,
        width: 382,
        autoDimensions: true,
        type : 'iframe',
        showTitle: false,
        scrolling: 'no',
        onComplete: function(){
            jQuery('#fancybox-frame').load(function() { // wait for frame to load and then gets it's height
                jQuery('#fancybox-content').height(jQuery(this).contents().find('body').height()+30);
                jQuery.fancybox.resize();
            });

        }
    }
    );
    jQuery("#msg").fancybox({
        'modal' : true
    });	
		*/
});
function showOptions(id){
	   jQuery('.fancybox').fancybox(
    {
        hideOnContentClick : true,
        width: 382,
        autoDimensions: true,
        type : 'iframe',
        showTitle: false,
        scrolling: 'no',
        onComplete: function(){
            jQuery('#fancybox-frame').load(function() { // wait for frame to load and then gets it's height
                jQuery('#fancybox-content').height(jQuery(this).contents().find('body').height()+30);
                jQuery.fancybox.resize();
            });

        }
    }
    );
    jQuery("#msg").fancybox({
        'modal' : true
    });	
    jQuery('#fancybox'+id).trigger('click');
}
function setAjaxData(data,iframe){
    if(data.status == 'ERROR'){
        //alert(data.message);
        jQuery('.msg-text').html(data.message);
        jQuery('#msg').trigger('click');
    }else{
        jQuery('html, body').animate({
            scrollTop:0
        },500);
		
        if(jQuery('.cart-inner')){
            jQuery('.cart-inner').replaceWith(data.sidebar);
        }
			
        if(jQuery('.header .links .cart-mini .num-cart')){
            jQuery('.header .links .cart-mini .num-cart').html(data.toplink);
        }
				
        
        //jQuery('.cart-inner .cart-content').stop(true, true).delay(0).slideDown(0);
        jQuery('.cart-inner .cart-content').slideDown(0);
        jQuery.fancybox.close();
    //setTimeout(function(){
    //jQuery('.cart-inner .cart-content').stop(true, true).delay(500).slideUp(400);
    //},2000);
    }
}
function setLocationAjax(url,id){
    url += 'isAjax/1';
    url = url.replace("checkout/cart","ajax/index");
    //jQuery('#ajax_loader'+id).show();
    jQuery('.amshopby-overlay').show();
    try {
        jQuery.ajax( {
            url : url,
            dataType : 'json',
            success : function(data) {
                jQuery('html, body').animate({
                    scrollTop:0
                },500);
                jQuery('.amshopby-overlay').hide();
                setAjaxData(data,false);  
            }
        });
    } catch (e) {
    }
}

