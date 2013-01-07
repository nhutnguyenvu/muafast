/*global window */
window.onload = function(){
var i;
try 
{ 
if(document.getElementById('search_mini_form'))
{
var links = document.links;
    for (i = 0; i < links.length; i++) {
        if (links[i].href.search('/customer/account/login/') != -1) {
        links[i].href = 'javascript:apptha_header_logo_Div.open();';
        var objInput = document.getElementsByTagName("input");

        }
		 if (links[i].href.search('/wishlist/') != -1) {
        links[i].href = 'javascript:apptha_header_logo_Div.open();';
        var objInput = document.getElementsByTagName("input");

        }
		 if (links[i].href.search('/customer/account/') != -1) {
        links[i].href = 'javascript:apptha_header_logo_Div.open();';
        var objInput = document.getElementsByTagName("input");

        }
    }
}  
if(document.getElementById("checkout-step-login"))
{
$$('.col-2 .buttons-set').each(function(e) {
        e.insert({bottom: '<div id="multilogin"> <button type="button" class="button" style="" onclick="javascript:apptha_header_logo_Div.open();" title="Social Login" name="headerboxLink1" id="headerboxLink1"><span><span>Social Login</span></span></button></div>'});
    });

} 
}catch(exception)
{
    alert(exception);
}
}