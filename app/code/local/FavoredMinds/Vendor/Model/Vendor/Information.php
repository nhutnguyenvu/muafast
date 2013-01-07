<?php

/**
 * qikDropShipper Extension
 *
 * @category   FavoredMinds
 * @package    Vendor
 * @author     FavoredMinds Solution (geedubya15@gmail.com)
 * @copyright  Copyright (c) 2010 FavoredMinds.com (http://www.favoredminds.com)
 */
class FavoredMinds_Vendor_Model_Vendor_Information extends Mage_Core_Model_Abstract {

    protected function _construct() {
        $this->_init('vendor/vendor_information');
        parent::_construct();
    }

    public function getVendorIdByUsername($username) {
        $resource = Mage::getSingleton('core/resource');
        $read = $resource->getConnection('core_read');
        $select = $read->select('vendor_id')
                ->from($resource->getTableName('vendors'))
                ->where("username = '" . $username . "'");

        $result = $read->fetchOne($select);
        return $result;
    }

    public function getVendorInfoByVendorCode($vendor_code,$column=array()) {
        

        $resource = Mage::getSingleton('core/resource');
        $read = $resource->getConnection('core_read');
        
        if(empty($column)){
            
            $select = $read->select()
                ->from($resource->getTableName('vendors'), array('vendor_id',
                    'vendor_code', "company_name", "average_rate","path"))
                ->where("vendor_code = '" . $vendor_code . "'");

            $result = $read->fetchRow($select);
        }
        else{
            
            $select = $read->select()
                ->from($resource->getTableName('vendors'), $column)
                ->where("vendor_code = '" . $vendor_code . "'");

            $result = $read->fetchRow($select);
        }
        

        return $result;
    }

}