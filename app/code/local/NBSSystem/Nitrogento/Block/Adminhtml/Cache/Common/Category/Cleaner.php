<?php

class NBSSystem_Nitrogento_Block_Adminhtml_Cache_Common_Category_Cleaner extends Mage_Core_Block_Abstract
{
    protected function _toHtml()
    {
        return 
            "<script type=\"text/javascript\">
                function cleanCategoryCache(url)
                {
                    var messageContainer = $('messages');
                    new Ajax.Request(url, {
                        evalScripts: true,
                        onSuccess: function(transport) {
                            $(messageContainer).update(transport.responseText);
                        }
                    });
                }
            </script>";
    }
}