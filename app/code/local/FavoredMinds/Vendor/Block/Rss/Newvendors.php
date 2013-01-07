<?php
/**
* qikDropShipper Extension
*
* @category   FavoredMinds
* @package    Vendor
* @author     FavoredMinds Solution (geedubya15@gmail.com)
* @copyright  Copyright (c) 2010 FavoredMinds.com (http://www.favoredminds.com)
*/

class FavoredMinds_Vendor_Block_Rss_Newvendors extends Mage_Rss_Block_Abstract {

  /**
   * Cache tag constant for feed notify stock
   *
   * @var string
   */
  const CACHE_TAG = 'block_html_vendor_rss_newvendors';

  protected function _construct() {
    $this->setCacheTags(array(self::CACHE_TAG));
    /*
        * setting cache to save the rss for 10 minutes
    */
    $this->setCacheKey('vendor_rss_newvendors');
    $this->setCacheLifetime(600);
  }

  protected function _toHtml() {
    $helper 		= Mage::app()->getHelper('vendor');
    $vendor = Mage::getModel('vendor/vendor');

    //we need to see the collection of new vendors
    $passDate = $vendor->getResource()->formatDate(mktime(0,0,0,date('m'),date('d')-7));

    $rssObj = Mage::getModel('rss/rss');
    $data = array('title' => $helper->__('New Vendors'),
            'description' => $helper->__('New Vendors'),
            'link'        => Mage::helper('adminhtml')->getUrl('vendor/adminhtml_vendors', array('_secure' => true, '_nosecret' => true)),
            'charset'     => 'UTF-8',
    );
    $rssObj->_addHeader($data);

    $collection = Mage::getModel('vendor/vendor')->getCollection();

    $collection->getSelect()
            ->where('main_table.created_time >= "' . $passDate . '"')
    ;

    $detailBlock = Mage::getBlockSingleton('vendor/rss_vendor_details');

    Mage::getSingleton('core/resource_iterator')
            ->walk($collection->getSelect(), array(array($this, 'addNewVendorsXmlCallback')), array('rssObj'=> $rssObj, 'vendor'=> $vendor, 'detailBlock' => $detailBlock));

    return $rssObj->createRssXml();
  }

  public function addNewVendorsXmlCallback($args) {
    $rssObj = $args['rssObj'];
    $detailBlock = $args['detailBlock'];
    $vendor = $args['vendor'];
    $vendor->load($args['row']['vendor_id']);
    if ($vendor && $vendor->getId()) {
      $title = Mage::helper('vendor')->__('Vendor "%s" created at %s', $vendor->getUsername(), $this->formatDate($vendor->getCreatedTime()));
      $url = Mage::helper('adminhtml')->getUrl('vendor/adminhtml_vendors/edit', array('_secure' => true, 'id' => $vendor->getId(), '_nosecret' => true));
      $detailBlock->setVendor($vendor);
      $data = array(
              'title'         => $title,
              'link'          => $url,
              'description'   => $detailBlock->toHtml()
      );
      $rssObj->_addEntry($data);
    }
  }
}