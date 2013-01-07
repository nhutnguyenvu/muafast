<?php
/**
* qikDropShipper Extension
*
* @category   FavoredMinds
* @package    Vendor
* @author     FavoredMinds Solution (geedubya15@gmail.com)
* @copyright  Copyright (c) 2010 FavoredMinds.com (http://www.favoredminds.com)
*/

class FavoredMinds_Vendor_Block_Rss_Products extends Mage_Rss_Block_Abstract {

  /**
   * Cache tag constant for feed notify stock
   *
   * @var string
   */
  const CACHE_TAG = 'block_html_vendor_rss_products';

  protected function _construct() {
    $this->setCacheTags(array(self::CACHE_TAG));
    /*
        * setting cache to save the rss for 10 minutes
    */
    $this->setCacheKey('vendor_rss_products');
    $this->setCacheLifetime(600);
  }

  protected function _toHtml() {
    $helper 		= Mage::app()->getHelper('vendor');
    $order = Mage::getModel('sales/order');
    $passDate = $order->getResource()->formatDate(mktime(0,0,0,date('m'),date('d')-7));

    $newurl = Mage::helper('adminhtml')->getUrl('adminhtml/sales_order', array('_secure' => true, '_nosecret' => true));
    $title = Mage::helper('vendor')->__('New Vendor Sales');

    $rssObj = Mage::getModel('rss/rss');
    $data = array('title' => $title,
            'description' => $title,
            'link'        => $newurl,
            'charset'     => 'UTF-8',
    );
    $rssObj->_addHeader($data);

    $collection = $order->getCollection()
            ->addAttributeToFilter('created_at', array('date'=>true, 'from'=> $passDate))
            ->addAttributeToSort('created_at','desc');

    $collection->getSelect()
            ->join(array('oi' => $helper->getTableName('sales_flat_order_item')), 'oi.order_id=main_table.entity_id', array())
            ->join(array('pei' => $helper->getTableName('catalog_product_entity_int')), 'pei.entity_id=oi.item_id', array())
            ->joinNatural(array('ea' => $helper->getTableName('eav_attribute')))
            ->join(array('vendors' => $helper->getTableName('vendors')), 'vendors.vendor_code=pei.value', array('vendors.company_name'))
            ->where('ea.attribute_code="manufacturer"')
            ->group('main_table.entity_id')
            ->group('vendors.vendor_id')
    ;
    if ($helper->vendorIsLogged()) {
      $collection->getSelect()->where('vendors.vendor_id="' . $helper->getVendorUserId() . '"');
    }

    $detailBlock = Mage::getBlockSingleton('rss/order_details');

    //Mage::dispatchEvent('rss_order_new_collection_select', array('collection' => $collection));

    Mage::getSingleton('core/resource_iterator')
            ->walk($collection->getSelect(), array(array($this, 'addNewOrderXmlCallback')), array('rssObj'=> $rssObj, 'order'=>$order , 'detailBlock' => $detailBlock));

    return $rssObj->createRssXml();
  }

  public function addNewOrderXmlCallback($args) {
    $rssObj = $args['rssObj'];
    $order = $args['order'];
    $detailBlock = $args['detailBlock'];
    $order->reset()->load($args['row']['entity_id']);
    if ($order && $order->getId()) {
      $title = Mage::helper('rss')->__('Order #%s created at %s', $order->getIncrementId(), $this->formatDate($order->getCreatedAt()));
      $url = Mage::helper('adminhtml')->getUrl('adminhtml/sales_order/view', array('_secure' => true, 'order_id' => $order->getId(), '_nosecret' => true));
      $detailBlock->setOrder($order);
      $data = array(
              'title'         => $title,
              'link'          => $url,
              'description'   => $detailBlock->toHtml()
      );
      $rssObj->_addEntry($data);
    }
  }
}