<?php

class CJM_AutoSwatchesPlus_Block_Rewrite_Adminhtml_Catalog_Product_Attribute_Edit_Tabs 
                        extends Mage_Adminhtml_Block_Catalog_Product_Attribute_Edit_Tabs
{
    protected function _beforeToHtml()
    {
        parent::_beforeToHtml();
		$swatch_attributes = Mage::helper('autoswatchesplus')->getSwatchAttributes();
        if(in_array(Mage::registry('entity_attribute')->getData('attribute_code'), $swatch_attributes))
        {
            $this->addTab('swatches', array(
                'label'     => Mage::helper('autoswatchesplus')->__('Manage Swatches'),
                'title'     => Mage::helper('autoswatchesplus')->__('Manage Swatches'),
                'content'   => $this->getLayout()->createBlock('autoswatchesplus/swatches')->toHtml(),
            ));
            
            return Mage_Adminhtml_Block_Widget_Tabs::_beforeToHtml();
        }
        
        return $this;
    }
}