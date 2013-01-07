<?php
/**
* qikDropShipper Extension
*
* @category   FavoredMinds
* @package    Vendor
* @author     FavoredMinds Solution (geedubya15@gmail.com)
* @copyright  Copyright (c) 2010 FavoredMinds.com (http://www.favoredminds.com)
*/

class FavoredMinds_Vendor_Model_Mysql4_Vendor extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the vendor_id refers to the key field in your database table.
        $this->_init('vendor/vendor', 'vendor_id');
    }

    protected function _initUniqueFields()
    {
        $this->_uniqueFields = array(array(
            'field' => 'username',
            'title' => Mage::helper('vendor')->__('Vendor Username')
        ));
        return $this;
    }

    protected function _beforeDelete(Mage_Core_Model_Abstract $vendor)
    {
      /*
        if ($group->usesAsDefault()) {
            Mage::throwException(Mage::helper('vendor')->__('The group "%s" cannot be deleted.', $group->getCode()));
        }
       *
       */
        return parent::_beforeDelete($vendor);
    }

    protected function _afterDelete(Mage_Core_Model_Abstract $vendor)
    {
      /*
        $customerCollection = Mage::getResourceModel('customer/customer_collection')
            ->addAttributeToFilter('group_id', $group->getId())
            ->load();
        foreach ($customerCollection as $customer) {
            $defaultGroupId = Mage::getStoreConfig(Mage_Customer_Model_Group::XML_PATH_DEFAULT_ID, $customer->getStoreId());
            $customer->setGroupId($defaultGroupId);
            $customer->save();
        }
       *
       */
        return parent::_afterDelete($group);
    }
}