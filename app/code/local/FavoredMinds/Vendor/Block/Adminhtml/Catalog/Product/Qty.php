<?php
/**
* qikDropShipper Extension
*
* @category   FavoredMinds
* @package    Vendor
* @author     FavoredMinds Solution (geedubya15@gmail.com)
* @copyright  Copyright (c) 2010 FavoredMinds.com (http://www.favoredminds.com)
*/

class FavoredMinds_Vendor_Block_Adminhtml_Catalog_Product_Qty extends  Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    const IN_STATUS = 0;
    const OUT_STATUS = 0;
    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }
    public function render(Varien_Object $row)
    {
        $this->setTemplate("vendor/catalog/product/grid/renderer/qty.phtml");
        $this->setProduct($row);
        return $this->toHtml();
    }
    public function getQtyStatus(){

        return array(
                self::IN_STATUS,
                self::OUT_STATUS
        );
    }
    public function getAvaliableStock(){
        
    }

    
}