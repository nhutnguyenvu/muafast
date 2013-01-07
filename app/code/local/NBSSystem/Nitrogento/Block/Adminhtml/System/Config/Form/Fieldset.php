<?php

class NBSSystem_Nitrogento_Block_Adminhtml_System_Config_Form_Fieldset extends Mage_Adminhtml_Block_System_Config_Form_Fieldset
{
	protected function _getCollapseState($element)
    {
        if (Mage::app()->getFrontController()->getRequest()->getParam('group') == $element->getId())
        {
        	return 1;
        }
        
        return 0;
    }
}
