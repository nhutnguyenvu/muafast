<?php
class Luclong_Community_Block_Adminhtml_Renderer_ProductName extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
 
    public function render(Varien_Object $row)
    {
   	   $product_id = $row->getData('product_id');
       $name_product = Mage::getModel('catalog/product')->load($product_id);
       return $row = $name_product->getName();
    }
 
}