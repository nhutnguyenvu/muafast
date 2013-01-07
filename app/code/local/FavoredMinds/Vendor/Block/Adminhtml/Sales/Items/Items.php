<?php
class FavoredMinds_Vendor_Block_Adminhtml_Sales_Items_Items extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    const STATUS_PROCESSING = "processing";
    const STATUS_PENDING    = "pending";
    
    public function render(Varien_Object $row)
    {
        
        $this->setTemplate("vendor/order/items/items.phtml");
        $this->setOrder($row);
        return $this->toHtml();
    }
    public function getStatusAllowedUpdating(){
        
        return array(
                self::STATUS_PROCESSING,         
                self::STATUS_PENDING
        );
    }
}

?>

