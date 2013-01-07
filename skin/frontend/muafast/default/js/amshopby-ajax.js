var amshopby_working  = false;
var amshopby_blocks   = {};
 var request =false;
function amshopby_ajax_init(){
	
    $$('div.block-layered-nav a', amshopby_toolbar_selector + ' a').
        each(function(e){
            var p = e.up();
            if ( p.hasClassName('clearer')){
               return;
            }

            e.onclick = function(){
				jQuery('body,html').animate({
					scrollTop: 0
				}, 800);
                if (this.hasClassName('checked')) {
                    this.removeClassName('checked');
                } else {
                    this.addClassName('checked');
                }
		        
                var s = this.href;
                if (s.indexOf('#') > 0){
                	s = s.substring(0, s.indexOf('#'))
                }
                amshopby_ajax_request(s);
                return false;
            };
        });
    
    $$('div.block-layered-nav select.amshopby-ajax-select', amshopby_toolbar_selector + ' select').
        each(function(e){
            e.onchange = 'return false';
            Event.observe(e, 'change', function(e){
                amshopby_ajax_request(this.value);
                Event.stop(e);
            });
        });
        
    if (typeof(amshopby_external) != 'undefined'){
        amshopby_external();    
    }
}

function amshopby_get_created_container()
{
    var elements = document.getElementsByClassName('amshopby-page-container');
    return (elements.length > 0) ? elements[0] : null;
}

function amshopby_get_container()
{
	var createdElement = amshopby_get_created_container();
	if (!createdElement) {
		var container_element = null;
		
		var elements = $$('div.category-products');
		if (elements.length == 0) {
			container_element = amshopby_get_empty_container();
		} else {
			container_element = elements[0];
		}
		
		if (!container_element) {
            alert('Please add the <div class="amshopby-page-container"> to the list template as per installtion guide. Enable template hints to find the right file if needed.');
        }	
        
		container_element.wrap('div', { 'class': 'amshopby-page-container', 'id' : 'amshopby-page-container' });
		
		createdElement = amshopby_get_created_container();
		
		$(createdElement).insert({ bottom : '<div style="display:none" class="amshopby-overlay"><div></div></div>'});
	}
	return createdElement;
}

function amshopby_get_empty_container()
{
	var notes = document.getElementsByClassName('note-msg');
	if (notes.length == 1) {
		return notes[0];
	}
}


function amshopby_ajax_request(url){
    if(request){request.transport.abort();}
	
		var block = amshopby_get_container();
		
		if (block && amshopby_scroll_to_products) {
			block.scrollTo();
		}

		amshopby_working = true;
		
		$$('div.amshopby-overlay').each(function(e){
			e.show();
		});

		 request = new Ajax.Request(url,{
				method: 'get',
				parameters:{'is_ajax':1},
				onSuccess: function(response){
					
					data = response.responseText;
					if(!data.isJSON()){
						//setLocation(url);
					}
					
					data = data.evalJSON();
					if (!data.page || !data.blocks){
						//setLocation(url);
					}
					
					amshopby_ajax_update(data);
					amshopby_working = false;
					jQuery('dl.color dd.attr-attr ol').css('display','none');
					jQuery('dl.price dd.attr-price ol').css('display','none');
					
					// load facebook like button
					jQuery('.products-grid li.item').hover(function(){
						var x =jQuery(this).find('.overHidden');
						if(x.css("display") == "none"){
							 jQuery(this).find('.overHidden').show(); // toggle the display of the button
							 FB.XFBML.parse(this); //re-parse xfbml
						 }
					});
					
				},
				onFailure: function(){
					amshopby_working = false;
					//setLocation(url);
				}
			}
		);
}

function amshopby_ajax_update(data){

    //update category (we need all category as some filters changes description)
    var tmp = document.createElement('div');
    tmp.innerHTML = data.page;
    
    var block = amshopby_get_container();
    if (block) {
    	var targetElement = tmp.firstChild;
    	if(typeof tmp.firstDescendant != "undefined") {
    		targetElement = tmp.firstDescendant();
    	}
    	block.parentNode.replaceChild(targetElement, block);
    }

    var blocks = data.blocks;
    for (id in blocks){
        var html   = blocks[id];
        if (html){
            tmp.innerHTML = html;
        }
        
        block = $$('div.'+id)[0];
        if (html){
            if (!block){
                block = amshopby_blocks[id]; // the block WAS in the structure a few requests ago
                amshopby_blocks[id] = null;    
            }
            if (block){
            	var targetElement = tmp.firstChild;
            	if(typeof tmp.firstDescendant != "undefined") {
            		targetElement = tmp.firstDescendant();
            	}
            	block.parentNode.replaceChild(targetElement, block);
            }
        }
        else { // no filters returned, need to remove
            if (block){
                var empty = document.createTextNode('');
                amshopby_blocks[id] = empty; // remember the block in the DOM structure
                block.parentNode.replaceChild(empty, block);        
            }
        }  
    }
    amshopby_start(); 
    amshopby_ajax_init();
     
}


document.observe("dom:loaded", function(event) {
	amshopby_ajax_init();
});

var amshopby_toolbar_selector = 'div.toolbar';
var amshopby_scroll_to_products = false;
function amshopby_external(){
    //add here all external scripts for page reloading
    // like igImgPreviewInit(); 
}