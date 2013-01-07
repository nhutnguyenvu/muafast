<?php
/**
* qikDropShipper Extension
*
* @category   FavoredMinds
* @package    Vendor
* @author     FavoredMinds Solution (geedubya15@gmail.com)
* @copyright  Copyright (c) 2010 FavoredMinds.com (http://www.favoredminds.com)
*/

class FavoredMinds_Vendor_Block_Adminhtml_Report_Sales extends Mage_Core_Block_Template {
  protected $_products	= array();

  public function __construct() {
    $this->_controller = 'adminhtml_home';
    $this->_blockGroup = 'vendor';
    $this->_headerText = "Vendors";
    parent::__construct();
  }

  protected function _prepareLayout() {
    return parent::_prepareLayout();
  }

  // get 'from' date
  public function getFrom() {
    $from		= $this->getRequest()->getParam("from");

    if(empty($from)) {
      $from	= date("d-m-Y",time()-604800);	// 7 days ago
    }

    return $from;
  }

  // get 'to' date
  public function getTo() {
    $to			= $this->getRequest()->getParam("to");

    if(empty($to)) {
      $to		= date("d-m-Y",time());	// now
    }

    return $to;
  }

  // retrieve orders collection filtered using 'from' & 'to' date & vendor id
  public function getOrderCollection() {
    $helper 	= Mage::app()->getHelper('vendor');

    // Y-m-d
    $todayDate  = Mage::app()->getLocale()->date()->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);

    $from		= explode("-",$this->getRequest()->getParam("from"));
    $to			= explode("-",$this->getRequest()->getParam("to"));

    if(count($from)<3) {
      $from	= date("Y-m-d",time()-604800);	// 7 days ago
    }
    else {
      $from	= $from[2]."-".$from[1]."-".$from[0];
    }

    if(count($to)<3) {
      $to		= date("Y-m-d",time()+86400); // now + 1 day // in order to include today results
    }
    else {
      $to		= mktime(0,0,0,$to[1],$to[0],$to[2])+86400;
      $to		= date("Y-m-d",$to);
    }

    if($from==null && $to==null) {
      $from	= $todayDate;
      $to		= $todayDate;
    }

    $collection	= $helper->getOrderCollection($from,$to);

    return $collection;
  }

  // returns vendor information
  public function getVendorInfo($vendor_id) {
    $helper 	= Mage::app()->getHelper('vendor');

    return $helper->getVendorUserInfo($vendor_id);
  }

  // returns data formatted for vendors sales page table
  public function getVendorSales() {
    $vendorSales		= array();
    $helper 			= Mage::app()->getHelper('vendor');

    if($helper->vendorIsLogged()) {
      $vendorId			= $helper->getVendorUserId();
    }
    else {
      $vendorId			= $this->getRequest()->getParam("id");
    }

    $orderCollection	= $this->getOrderCollection();

    foreach($orderCollection->getItems() as $_order) {
      $order_id			= $_order->getId();
      $itemsCollection	= $_order->getItemsCollection();

      foreach($itemsCollection as $item) {
        $product_id		= $item->getProductId();

        if(!isset($this->_products[$product_id]) || empty($this->_products[$product_id])) {
          $this->_products[$product_id]	= Mage::getModel('catalog/product')->load($product_id);
        }

        $_product		= $this->_products[$product_id];

        $manufacturer	= $_product->getAttributeText("manufacturer");
        $vendor_id		= $helper->getVendorByManufacturer($manufacturer);

        if(!empty($vendor_id) && (empty($vendorId) || $vendorId==$vendor_id) ) {
          !isset($vendorSales[$vendor_id]) ?
                  $vendorSales[$vendor_id]	= array()
                  : false;

          !isset($vendorSales[$vendor_id]["orders"]) ?
                  $vendorSales[$vendor_id]["orders"]	= array()
                  : false;

          !isset($vendorSales[$vendor_id]["orders"][$order_id]) ?
                  $vendorSales[$vendor_id]["orders"][$order_id]	= array()
                  : false;

          !isset($vendorSales[$vendor_id]["orders"][$order_id]["products"]) ?
                  $vendorSales[$vendor_id]["orders"][$order_id]["products"]	= array()
                  : false;

          !isset($vendorSales[$vendor_id]["orders"][$order_id]["order_info"]) ?
                  $vendorSales[$vendor_id]["orders"][$order_id]["order_info"]	= array()
                  : false;

          !isset($vendorSales[$vendor_id]["vendor_info"]) ?
                  $vendorSales[$vendor_id]["vendor_info"]	= $this->getVendorInfo($vendor_id)
                  : false;

          $vendorSales[$vendor_id]["orders"][$order_id]["order_info"]			= $_order->toArray();
          $vendorSales[$vendor_id]["orders"][$order_id]["order_info"]["id"]	= $order_id;

          $_product->addData(array("qty_ordered" => $item->getQtyOrdered()));


          array_push($vendorSales[$vendor_id]["orders"][$order_id]["products"],$_product->toArray());

        }

      }
    }

    return $vendorSales;
  }



}