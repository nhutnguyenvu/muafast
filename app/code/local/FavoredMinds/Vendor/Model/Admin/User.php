<?php
/**
* qikDropShipper Extension
*
* @category   FavoredMinds
* @package    Vendor
* @author     FavoredMinds Solution (geedubya15@gmail.com)
* @copyright  Copyright (c) 2010 FavoredMinds.com (http://www.favoredminds.com)
*/

class FavoredMinds_Vendor_Model_Admin_User extends Mage_Admin_Model_User {
  public function findFirstAvailableMenu($parent=null, $path='', $level=0) {
    $helper 		= Mage::app()->getHelper('vendor');
    $vendorlogged	= $helper->vendorIsLogged() === true;
    // removing this line will cause vendors (not administrators) to have full access to all products, so please dont
    if($helper->check()) {

      if ($parent == null) {
        $parent = Mage::getConfig()->getNode('adminhtml/menu');
      }

      foreach ($parent->children() as $childName=>$child) {
        $aclResource = 'admin/' . $path . $childName;
        if(Mage::getSingleton('admin/session')->isAllowed($aclResource)) {
          if (!$child->children) {
            return (string)$child->action;
          } else if ($child->children) {
            $action = 'vendor/adminhtml_sales';
            //$action = $this->findFirstAvailableMenu($child->children, $path . $childName . '/', $level+1);
            return $action ? $action : (string)$child->action;
          }
        }
      }
    }
    $this->_hasAvailableResources = false;
    return '*/*/denied';
  }

}
