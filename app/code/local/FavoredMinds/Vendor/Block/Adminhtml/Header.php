<?php
/**
* qikDropShipper Extension
*
* @category   FavoredMinds
* @package    Vendor
* @author     FavoredMinds Solution (geedubya15@gmail.com)
* @copyright  Copyright (c) 2010 FavoredMinds.com (http://www.favoredminds.com)
*/

class FavoredMinds_Vendor_Block_Adminhtml_Header extends Mage_Core_Block_Template
{
  public function __construct()
  {
    //check if the user is logged in
    parent::__construct();
  }

  public function getLogoSrc(){
    $helper 		= Mage::app()->getHelper('vendor');
    //check if the vendor is registered
    if ($helper->vendorIsLogged()) {
      $vendor = $helper->getVendorUserInfo($helper->getVendorUserId());
      $imageUrl = Mage::getBaseDir('media') . DS . $vendor['logo'];
      if (!file_exists($imageUrl) || empty($vendor['logo'])){
        return $this->getSkinUrl($this->__('images/logo.gif'));
      }
      //if we have the resized file
      if (Mage::getStoreConfig('vendor/general/logo_resize') == 1){
        //resize the logo
        // path of the resized image to be saved
        // here, the resized image is saved in media/resized folder
        $imageResized = Mage::getBaseDir('media').DS.'vendor'.DS. $helper->getVendorUserId().DS . 'resized_' . basename($vendor['logo']);

        if(!file_exists($imageResized)){
          $imageObj = new Varien_Image($imageUrl);
          $imageObj->constrainOnly(TRUE);
          $imageObj->keepAspectRatio(TRUE);
          $imageObj->keepFrame(FALSE);
          if(Mage::getStoreConfig('vendor/general/logo_height') > 0){
            $imageObj->resize(Mage::getStoreConfig('vendor/general/logo_width'), Mage::getStoreConfig('vendor/general/logo_height'));
          } else {
            $imageObj->resize(Mage::getStoreConfig('vendor/general/logo_width'));
          }
          $imageObj->save($imageResized);
        }
        return Mage::getBaseUrl('media') . 'vendor/' . $helper->getVendorUserId() . '/' . 'resized_' . basename($vendor['logo']);
      } else {
        return Mage::getBaseUrl('media') . $vendor['logo'];
      }
    } else {
      //display original logo
      return $this->getSkinUrl($this->__('images/logo.gif'));
    }
  }
}