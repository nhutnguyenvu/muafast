<?php
/**
* qikDropShipper Extension
*
* @category   FavoredMinds
* @package    Vendor
* @author     FavoredMinds Solution (geedubya15@gmail.com)
* @copyright  Copyright (c) 2010 FavoredMinds.com (http://www.favoredminds.com)
*/

class FavoredMinds_Vendor_Model_Mysql4_Vendor_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {
  public function _construct() {
    parent::_construct();
    $this->_init('vendor/vendor');
  }
  public function setIgnoreIdFilter($indexes) {
    if( !count($indexes) > 0 ) {
      return $this;
    }
    $this->_select->where('main_table.vendor_id NOT IN(?)', $indexes);
    return $this;
  }

  public function toOptionIdArray() {
    return parent::_toOptionArray('vendor_id', 'company_name');
  }

  public function toOptionIdHash() {
    return parent::_toOptionHash('vendor_id', 'company_name');
  }

  public function toOptionArray() {
    return parent::_toOptionArray('vendor_code', 'company_name');
  }
  public function toOptionHash() {
    return parent::_toOptionHash('vendor_code', 'company_name');
  }
}