<?php
/**
* qikDropShipper Extension
*
* @category   FavoredMinds
* @package    Vendor
* @author     FavoredMinds Solution (geedubya15@gmail.com)
* @copyright  Copyright (c) 2010 FavoredMinds.com (http://www.favoredminds.com)
*/

class FavoredMinds_Vendor_SalesController extends Mage_Core_Controller_Front_Action {
  public function indexAction() {

    $this->loadLayout();
    $this->renderLayout();
  }
}