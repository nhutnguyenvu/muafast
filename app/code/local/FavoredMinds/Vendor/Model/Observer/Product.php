<?php
/**
* qikDropShipper Extension
*
* @category   FavoredMinds
* @package    Vendor
* @author     FavoredMinds Solution (geedubya15@gmail.com)
* @copyright  Copyright (c) 2010 FavoredMinds.com (http://www.favoredminds.com)
*/

class FavoredMinds_Vendor_Model_Observer_Product {
  /**
   * Remove the tabs fromthe product edit page in the Magento admin
   *
   * @param Varien_Event_Observer $observer
   */

  public function removeTabs(Varien_Event_Observer $observer) {
    $block = $observer->getEvent()->getBlock();

    if ( $block instanceof Mage_Adminhtml_Block_Catalog_Product_Edit_Tabs ) {
      if ( $this->_getRequest()->getActionName() == 'new' ) {
        //predefine the selected product types
        $tab_ids = $block->getTabsIds();
      }
      if ( $this->_getRequest()->getActionName() == 'edit' || $this->_getRequest()->getParam('type') ) {
        //make sure to display the needed tabs
        $attributeSetId = Mage::getModel('eav/entity_attribute_set')->load('Attribute Test Set To Remove', 'attribute_set_name')->getAttributeSetId();

        $product = Mage::registry('product');
        $productAttributeSetId = $product->getAttributeSetId();

        if ( $productAttributeSetId == $attributeSetId ) {

          $tab_ids = $block->getTabsIds();
          $counter = 1;

          foreach ($tab_ids as $tab) {
            if ($tab == 'inventory' ||
                $tab == 'crosssell' ||
                $tab == 'tags' ||
                $tab == 'customer_options' ||
                $tab == 'upsell' ||
                ( $counter <= 7 && $counter > 1)
            ) {
              $block->removeTab($tab);
            }
            $counter++;
          }
        }
      }
    }
  }


  /*
         * Shortcut to getRequest
  */
  protected function _getRequest() {
    return Mage::app()->getRequest();
  }
}
 