<?php

/**
 * qikDropShipper Extension
 *
 * @category   FavoredMinds
 * @package    Vendor
 * @author     FavoredMinds Solution (geedubya15@gmail.com)
 * @copyright  Copyright (c) 2010 FavoredMinds.com (http://www.favoredminds.com)
 */
class FavoredMinds_Vendor_Model_Vendor extends Mage_Core_Model_Abstract {

    public function _construct() {
        $this->_init('vendor/vendor');
        parent::_construct();
    }

    public function getVendorList($page = false) {

        $collection = $this->getResourceCollection();
        /* @var $collection Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Collection */

        $collection->addFieldToSelect('company_name')
                ->addFieldToSelect('aboutcompany')
                ->addFieldToSelect("is_saling")
                ->addFieldToSelect('vendor_code')
                ->addFieldToSelect('average_rate')
                ->addFieldToSelect('logo')
				->addFieldToSelect("path")
                ->addFieldToFilter('status', 1)
                ->setOrder("average_rate","desc");
			if($name =  Mage::app()->getRequest()->getParam('vendor')){
			 $collection->addFieldToFilter('company_name',array('like'=>'%'.$name.'%'));
			}
        
        
        if (!empty($page))
            if (empty($this->_pageSize)) {
                $page_element = Mage::getStoreConfig('vendor/quantity_vendor/qty_on_list', Mage::app()->getStore());
                
                if(empty($page_element)){
                    $page_element = DEFAULT_NUMBER_VENDOR_ON_PAGE;
                }    
                $collection->setPageSize($page_element);
                $collection->setCurPage($page);
            }
        /*
          if ($sorted) {
          if (is_string($sorted)) {
          // $sorted is supposed to be attribute name
          $collection->addAttributeToSort($sorted);
          } else {
          $collection->addAttributeToSort('name');
          }
          }
         */
        return $collection;
    }

    public function getPagination($page_number) {
        
        
        $page_element = Mage::getStoreConfig('vendor/quantity_vendor/qty_on_list', Mage::app()->getStore());
        if(empty($page_element)){
            $page_element = DEFAULT_NUMBER_VENDOR_ON_PAGE;
        } 
        $total = $this->countVendorList();
        
        $last = intval(ceil($total / $page_element));
        
        //this makes sure the page number isn't below one, or more than our maximum pages 
        if ($page_number < 1) {
            $page_number = 1;
        } elseif ($page_number > $last) {
            $page_number = $last;
        }

        //This sets the range to display in our query 
        return array("total" => $total,
            "page_number" => intval($page_number),
            "last" => $last,
            "from" => ($page_number - 1) * $page_element,
            "to" => $page_number * $page_element);
    }

    public function countVendorList() {

            $collection = $this->getCollection()
                        /* @var $collection Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Collection */
                        ->addFieldToFilter('status', 1);
			if($name =  Mage::app()->getRequest()->getParam('name')){
			 $collection->addFieldToFilter('company_name',array('like'=>'%'.$name.'%'));
			}	
			return 	$collection->count();
    }
	
}