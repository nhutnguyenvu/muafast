
    jQuery(document).ready(function(){
	
        var design = jQuery( ".result_product .products-grid" );
        var frame = jQuery( "#mm-design-frame" );
		var flag_empty_frame = true;
		var current_tab_index = 1;
		var tab_title_default = "Sản phẩm";
		var number_of_items = 0;
		var current_frame_bg = "";
		var grouped_product_url = "";
		var right_menu_tabs = new Array();
		
        jQuery(".mixnmatch_ajax").live("click",function(){
            var p = 1;
            if(jQuery(this).attr("p")){
                var p = jQuery(this).attr("p");
            }
			
            ajax_mixnmatch(jQuery(this).attr("href"),"#result_product_" + (current_tab_index),p);
			
			var tab_text = jQuery(this).children("span").text();
			if(tab_text)
				jQuery("#ui-id-"+current_tab_index).text(tab_text);
			
			jQuery(".default-tab").addClass("mm-jcaro-hidden");
            return false;
        });
        jQuery(".back").live("click",function (){
            jQuery("#result_product_"+ (current_tab_index)).html("");
			jQuery(".default-tab").removeClass("mm-jcaro-hidden");
			jQuery("#ui-id-"+current_tab_index).text(tab_title_default);
        });    
		
			
        //
        //// init all event
        //
        initDragDropItems();
        initJcarousel();
		jQuery("#mm-left-menu-share").addClass("disabled");
		// jQuery("#mm-left-menu-share").hide();
	
		jQuery('.mm-right-menu-tabs').tabs({
			select: function(event,ui) {
				current_tab_index = jQuery(ui.panel).attr("taborder");
				// alert(current_tab_index);
				if(jQuery("#result_product_" + (current_tab_index)).html().trim() != "") jQuery(".default-tab").addClass("mm-jcaro-hidden");
				else jQuery(".default-tab").removeClass("mm-jcaro-hidden");
				// console.log(ui);
				
				
			}
		});
		
        function deleteImage( item ) {
            var pro_id = jQuery(item).attr('id');
			number_of_items++;
			if(number_of_items>=2) {
				// jQuery("#mm-left-menu-share").show();
				jQuery("#mm-left-menu-share").removeClass("disabled");
			} else {
				// jQuery("#mm-left-menu-share").hide();
				jQuery("#mm-left-menu-share").addClass("disabled");
			}
            info_product(frame,pro_id,jQuery(item).offset(),number_of_items,item);
            //var item2 = jQuery(item).clone(true);
            // jQuery(frame).append(item2);
            item.fadeOut();
            // item2.hide();
            // item2.css("left",0);
            //item2.css("top",0);
            // item2.fadeIn();
            frameUnEmpty();
        }
    
        function frameUnEmpty(){
            jQuery("#mm-default-text").hide();
            flag_empty_frame = false;
        }
        function frameEmpty(){
			if(!jQuery("#mm-design-frame .mm-editing:visible").size()){
				jQuery("#mm-default-text").show();
				flag_empty_frame = true;
			}
        }
        function frameClear(){
			jQuery("#mm-design-frame .mm-editing:visible").remove();
			jQuery(".back").trigger("click");
			frameEmpty();
		}
		
		function info_product(frame,pro_id,item_position,z_index,last_item){
			if(jQuery("#mm-design-frame #mm-editing_"+pro_id).size()) {
				var item = jQuery("#mm-design-frame #mm-editing_"+pro_id);
				item.css("left", parseInt(item_position.left - frame.offset().left));
				item.css("top", parseInt(item_position.top - frame.offset().top));
				return;
			}
			try {
				jQuery(".loading_image").show();
				jQuery.ajax( {
					url : "http://"+location.host+"/muafast/mixmatch/index/infoproduct/",
					dataType : 'json',
					data: {id:pro_id, isAjax:1},
					success : function(data) {
						jQuery(".loading_image").hide();
						
						var item = 	jQuery(data).appendTo('#mm-design-frame');
						var img = 	jQuery(item).children("img");
						var edit_bar 	= 	jQuery(item).find(".mm-edit-bar");
						var bt_backward = 	jQuery(item).find(".mm-bt-fordward");
						var bt_forward 	= 	jQuery(item).find(".mm-bt-backward");
						var bt_remove 	= 	jQuery(item).find(".mm-bt-remove");
						
						item.resizable({
							// alsoResize: img,
							autoHide: true,
							handles: "ne, se, sw, nw",
							minHeight: 50,
							minWidth: 50,
							resize: function( event, ui ) {
								img.css("width", parseInt(item.width()));
								img.css("height", parseInt(item.height()));
							},
							containment: "#mm-design-frame",
							grid: [ 1, 1 ],
							stop: function(){
								var breakable = jQuery(this).collision( "#mm-design-frame .mm-editing" );
								// console.log(breakable);
							}
						});
						
						item.draggable({
							containment: "#mm-design-frame",
							grid: [ 1,1 ]
						});
						
						item.css("left", parseInt(item_position.left - frame.offset().left));
						item.css("top", parseInt(item_position.top - frame.offset().top));
						
						z_index = 449 + z_index;
						item.css("z-index", z_index);
						img.css("cursor", "move");
						
						bt_backward.click(function(){
							if(parseInt(item.css("z-index")) > 1 )
							item.css("z-index", parseInt(item.css("z-index"))-1);
						});
						
						bt_forward.click(function(){
							if(parseInt(item.css("z-index")) < 998 )
								item.css("z-index", parseInt(item.css("z-index"))+1);
						});
						
						bt_remove.click(function(){
							item.fadeOut();
							item.remove();
							last_item.css("display", "");
							last_item.css("position", "relative");
							last_item.css("left", "");
							last_item.css("top", "");
							frameEmpty();
							number_of_items--;
							if(number_of_items>=2) {
								jQuery("#mm-left-menu-share").removeClass("disabled");
								// jQuery("#mm-left-menu-share").show();
							} else {
								jQuery("#mm-left-menu-share").addClass("disabled");
								// jQuery("#mm-left-menu-share").hide();
							}
						});
						
						edit_bar.hide();
						item.mouseenter(function(){
							edit_bar.show();
						});
						item.mouseleave(function(){
							edit_bar.hide();
						});
						
					}
				});
			} catch (e) {
			}
		}
		
		function saveGroupedProduct(productid_position, sharename, sharedes){
			
			try {
				jQuery(".loading_image").show();
				
				jQuery.ajax( {
					url : "http://"+location.host+"/index.php/mixmatch/index/createimage/",
					dataType : 'json',
					data: {str_pid_ppos:productid_position,frame_bg:current_frame_bg,isAjax:1,sharename:sharename, sharedes:sharedes},
					success : function(data) {
						jQuery(".loading_image").hide();
						if(data.picture) {
							data.redirect_uri = data.redirect_uri.replace("\\","");
							data.link = data.link.replace("\\","");
							data.picture = data.picture.replace("\\","");
							grouped_product_url = data.redirect_uri;
							postToFeed( data.redirect_uri, data.link, data.picture, data.name, data.caption, data.description );
						}
						else alert(data);
					}
				});
			} catch (e) {
				alert(e);
			}
		}
		
        function search(url){
            url +='?name='+jQuery("#string").val();
            ajax_mixnmatch(url,".result_product");
        }
	
        function ajax_mixnmatch(url,id,p){
			jQuery(id).html("");
			jQuery(id).addClass("mm-loading");
            try {
                jQuery.ajax( {
                    url : url,
                    dataType : 'json',
                    data: { url: url,p:p ,isAjax:1},
                    success : function(data) {
                        jQuery(id).html(data);
						// right_menu_tabs.push(data);
						jQuery(id).show();
                        initDragDropItems();
						jQuery(id).removeClass("mm-loading");
                    }
                });
            } catch (e) {
            }
        }
		
        // var urlajaxiinfoproduct = "<?php echo $this->getUrl('abc') ?>";
        
        // var data = [
        // { label: "anders", category: "" },
        // { label: "andreas", category: "" },
        // { label: "antal", category: "" },
        // { label: "annhhx10", category: "Products" },
        // { label: "annk K12", category: "Products" },
        // { label: "annttop C13", category: "Products" },
        // { label: "anders andersson", category: "People" },
        // { label: "andreas andersson", category: "People" },
        // { label: "andreas johnson", category: "People" }
        // ];
 
        // jQuery( ".input-mm-search" ).catcomplete({
        // delay: 0,
        // source: data
        // });
        
        jQuery("#mm-left-menu-new").click(function(){
			jQuery( "#notify-dialog" ).attr("title","Tạo thiết kế mới");
			jQuery( "#notify-dialog #notify-dialog-content" ).html("Bạn muốn xóa hết tất cả hình ảnh trên khung thiết kế?");
			jQuery( "#notify-dialog" ).dialog({
				resizable: false,
				height:140,
				modal: true,
				buttons: {
					"Phải": function() {
						jQuery( this ).dialog( "close" );
						frameClear();
					},
					"Không": function() {
						jQuery( this ).dialog( "close" );
					}
				}
			});
		});
		jQuery(".mm-background-img").click(function(){
			current_frame_bg = jQuery(this).attr("src");
			if(current_frame_bg)
				jQuery("#mm-design-frame").css("background", "url('"+current_frame_bg+"') no-repeat center center transparent");
			else
				jQuery("#mm-design-frame").css("background", "none repeat scroll 0 0 #FAFAFA");				
		});
		jQuery("#mm-left-menu-share").click(function(){
			if(!jQuery(this).hasClass("disabled")){
				var str_pid_posxy = "";
				jQuery("#mm-design-frame .mm-editing:visible").each(function(){
					var item = jQuery(this);
					str_pid_posxy += item.attr("id").replace(/[^0-9]/g, '');
					str_pid_posxy += ",";
					str_pid_posxy += item.width();
					str_pid_posxy += ",";
					str_pid_posxy += item.height();
					str_pid_posxy += ",";
					str_pid_posxy += item.css("left").replace(/[^0-9]/g, '');
					str_pid_posxy += ",";
					str_pid_posxy += item.css("top").replace(/[^0-9]/g, '');
					str_pid_posxy += ",";
					str_pid_posxy += item.css("z-index");
					str_pid_posxy += ";";
				});
				// alert(str_pid_posxy);
				// id,	width	height	left	top		z-index
				// 3973,100,	150,	397,	340,	1;
				
				
				var formHTML = "<table> <tr><td>Tên </td></tr> <tr><td><input name='sharename' id='sharename'/></td></tr> <tr><td>Mô tả</td></tr> <tr><td><textarea name='sharedes' id='sharedes'></textarea></td></tr> </table>";
				jQuery("#notify-dialog-content").html(jQuery(formHTML));
				// jQuery(formHTML).appendTo;
				
				jQuery( "#notify-dialog" ).attr("title","Lưu ảnh và chia sẻ");
				
				jQuery( "#notify-dialog" ).dialog({
					resizable: false,
					height:140,
					modal: true,
					buttons: {
						"Chia sẻ": function() {
							jQuery( this ).dialog( "close" );
							saveGroupedProduct(str_pid_posxy, jQuery("#sharename").val(), jQuery("#sharedes").val());
						},
						"Quay lại": function() {
							jQuery( this ).dialog( "close" );
						}
					}
				});
			}
		});
		
		
		
        jQuery(".bt-search").button();
        var tabTitle = jQuery( "#tab_title" ),
        tabContent = jQuery( "#tab_content" ),
        tabTemplate = "<li><a href='#{href}'>#{label}</a> <span class='ui-icon ui-icon-close'>Remove Tab</span></li>",
        tabCounter = 2;
 
        var tabs = jQuery( ".mm-right-menu-tabs" ).tabs();
        var tab = jQuery( ".mm-right-menu-tabs" );
        
        jQuery(".ui-tabs-nav").append('<li class="ui-state-default ui-corner-top" tabindex="-1"><a class="no-icon" id="add_tab"></a><span class="ui-icon ui-icon-add">Remove Tab</span></li>');
        
        // modal dialog init: custom buttons and a "close" callback reseting the form inside
        /* var dialog = jQuery( "#dialog" ).dialog({
            autoOpen: false,
            modal: true,
            buttons: {
                Add: function() {
                    addTab();
                    jQuery( this ).dialog( "close" );
                },
                Cancel: function() {
                    jQuery( this ).dialog( "close" );
                }
            },
            close: function() {
                form[ 0 ].reset();
            }
        }); */
        
        // addTab form: calls addTab function on submit and closes the dialog
        /* var form = dialog.find( "form" ).submit(function( event ) {
            addTab();
            dialog.dialog( "close" );
            event.preventDefault();
        }); */
        
        // actual addTab function: adds new tab using the input from the form above
        
        function addTab() {
            var tabsAdd = jQuery( "#add_tab" ).parent().detach();
            // var label = tabTitle.val() || "Tab " + tabCounter;
            var label = "Sản phẩm";
            var id = "tabs-" + tabCounter;
            var li = jQuery( tabTemplate.replace( /#\{href\}/g, "#" + id ).replace( /#\{label\}/g, label ) );
            var tabContentHtml = tabContent.val() || "Tab " + tabCounter + " content.";
			
            tabs.find( ".ui-tabs-nav" ).append( li );
			
            tabs.append( "<div id='tabs-" + tabCounter + "' taborder='"+(tabCounter)+"'> <div id='result_product_"+tabCounter+"' class='result_product'> </div> </div>" );
            tabs.tabs( "refresh" );
            tabs.tabs({ active: tab.tabs("length")-1 });
            jQuery(tabsAdd).appendTo(".ui-tabs-nav");
            tabCounter++;
        }
        
        // addTab button: just opens the dialog
        jQuery( "#add_tab" ).next("span")
        .click(function() {
            // dialog.dialog( "open" );
            addTab();
        });
        // close icon: removing the tab on click
        jQuery( ".mm-right-menu-tabs span.ui-icon-close" ).live( "click", function() {
            var panelId = jQuery( this ).closest( "li" ).remove().attr( "aria-controls" );
            current_tab_index = jQuery( "#" + panelId ).attr("taborder") - 1;
            jQuery( "#" + panelId ).remove();
            tabs.tabs( "refresh" );
			tabs.tabs({ active: current_tab_index });
        });
		
        function initJcarousel(){
            try{
                jQuery(".mm-tree-table").jcarousel({
                    scroll: 5,
                    visible: 5,
                    itemFallbackDimension: 300,
                    buttonPrevHTML: '<div class="mm-cat-list-prev"></div>',
                    buttonNextHTML: '<div class="mm-cat-list-next"></div>',
                });
            }catch(e){}
        }
		
		
		function runTest(){
			jQuery("#test-offset").text("aaa");
		}
		
        function initDragDropItems(){
            jQuery( ".result_product .products-grid li" ).draggable({
                revert: "invalid", // when not dropped, the item will revert back to its initial position
                cursor: "move",
				grid: [ 1,1 ]
            });
            frame.droppable({
                accept: ".result_product .products-grid li",
				tolerance: "fit",
                activeClass: "ui-state-hover",
                hoverClass: "ui-state-active",
                drop: function( event, ui ) {
                    deleteImage( ui.draggable );
                },
				grid: [ 1,1 ]
            });	
        }
		
		function postToFeed( redirect_uri, link, picture, name, caption, description ) {

			// calling the API ...
			var obj = {
			  method: 'feed',
			  link: link,
			  picture: picture,
			  name: name,
			  caption: caption,
			  description: description
			};

			function callback(response) {
				window.close();
				window.location = grouped_product_url;
			}

			FB.ui(obj, callback);
		}



	///////////////////////////////////////////////////////////////////////
	// Caculating offset






    });
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	