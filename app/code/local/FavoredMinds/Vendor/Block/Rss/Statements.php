<?php
/**
* qikDropShipper Extension
*
* @category   FavoredMinds
* @package    Vendor
* @author     FavoredMinds Solution (geedubya15@gmail.com)
* @copyright  Copyright (c) 2010 FavoredMinds.com (http://www.favoredminds.com)
*/

class FavoredMinds_Vendor_Block_Rss_Statements extends Mage_Rss_Block_Abstract {

  /**
   * Cache tag constant for feed notify stock
   *
   * @var string
   */
  const CACHE_TAG = 'block_html_vendor_rss_statements';

  protected function _construct() {
    $this->setCacheTags(array(self::CACHE_TAG));
    /*
        * setting cache to save the rss for 10 minutes
    */
    $this->setCacheKey('vendor_rss_statements');
    $this->setCacheLifetime(600);
  }

  protected function _toHtml() {
    $helper 		= Mage::app()->getHelper('vendor');
    $statement = Mage::getModel('vendor/vendor_statement');
    $passDate = $statement->getResource()->formatDate(mktime(0,0,0,date('m'),date('d')-7));

    $newurl = Mage::helper('adminhtml')->getUrl('vendor/adminhtml_statements', array('_secure' => true, '_nosecret' => true));
    $title = Mage::helper('vendor')->__('New Vendor Statement');

    $rssObj = Mage::getModel('rss/rss');
    $data = array('title' => $title,
            'description' => $title,
            'link'        => $newurl,
            'charset'     => 'UTF-8',
    );
    $rssObj->_addHeader($data);

    $collection = $statement->getCollection();

    $collection->getSelect()
            ->where("created_at > '$passDate'")
            ->order('created_at', 'desc')
    ;
    if ($helper->vendorIsLogged()) {
      $collection->getSelect()->where('vendor_id="' . $helper->getVendorUserId() . '"');
    }

    $detailBlock = Mage::getBlockSingleton('vendor/rss_statements_details');

    Mage::dispatchEvent('favoredminds_vendor_rss_new_collection_select', array('collection' => $collection));

    Mage::getSingleton('core/resource_iterator')
            ->walk($collection->getSelect(), array(array($this, 'addNewOrderXmlCallback')), array('rssObj'=> $rssObj, 'statement'=>$statement , 'detailBlock' => $detailBlock));

    return $rssObj->createRssXml();
  }

  public function addNewOrderXmlCallback($args) {
    $rssObj = $args['rssObj'];
    $statement = $args['statement'];
    $detailBlock = $args['detailBlock'];
    $statement->load($args['row']['vendor_statement_id']);
    if ($statement && $statement->getId()) {
      $_helper 		= Mage::app()->getHelper('vendor');
      $_vendor = $_helper->getVendor($statement->getVendorId());
      $title = Mage::helper('rss')->__('%s Vendor Sales Statement - %s', $_vendor->getCompanyName(), $this->formatDate($statement->getCreatedAt()));
      $url = Mage::helper('adminhtml')->getUrl('vendor/adminhtml_statement/edit', array('_secure' => true, 'id' => $statement->getId(), '_nosecret' => true));
      $detailBlock->setStatement($statement);
      $data = array(
              'title'         => $title,
              'link'          => $url,
              'description'   => $detailBlock->toHtml()
      );
      $rssObj->_addEntry($data);
    }
  }
}