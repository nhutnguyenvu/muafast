<?php

/**
 * qikDropShipper Extension
 *
 * @category   FavoredMinds
 * @package    Vendor
 * @author     FavoredMinds Solution (geedubya15@gmail.com)
 * @copyright  Copyright (c) 2010 FavoredMinds.com (http://www.favoredminds.com)
 */
class FavoredMinds_Vendor_Block_Vendor_Renderer_Vendor extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    // Holds an associative array with vendor id and the associated label
    private static $_vendors = array(); // "singleton"

    public static function getVendorsArray() {
        // Make sure the static property is only populated once
        
        if (count(self::$_vendors) == 0) {
            $vendor = new FavoredMinds_Vendor_Model_Vendor();
            $vendors = $vendor->getCollection()->toOptionIdHash();
            self::$_vendors = $vendors;
        }

        return self::$_vendors;
    }
    /** @desc: get vendor for homepage
     *  @authot: nhut.nguyen
     *  @return: return list vendor for homepage 
     */
    public static function getVendorsOnHomePage() {
        
        if (count(self::$_vendors) == 0) {
            $vendor = new FavoredMinds_Vendor_Model_Vendor();
            $vendors = $vendor->getCollection()
                    ->addFieldToSelect("vendor_code")
                    ->addFieldToSelect("company_name")
                    ->addFieldToSelect("average_rate")
                    ->addFieldToSelect("aboutcompany")->addFieldToSelect("logo")
                    ->addFieldToFilter("on_home",1)
                    
                    ->addFieldToFilter("status",1);
           
            return $vendors;
        }
        
        return null;
    }
    /** @desc: get vendor on slide
     *  @authot: nhut.nguyen
     *  @return: return list vendor for slide
     */
    public static function getVendorsOnSlide() {
        
        $vendor = new FavoredMinds_Vendor_Model_Vendor();
        $vendors = $vendor->getCollection()
                ->addFieldToSelect("vendor_id")
                ->addFieldToSelect("company_name")
                ->addFieldToSelect("vendor_code")
                ->addFieldToFilter("on_slide",1)
                
                ->addFieldToFilter("status",1);
        
        return $vendors;
        
    }
    // Transforms the customer_group_id into corresponding label
    public function render(Varien_Object $row) {
        $val = $this->_getValue($row);
        $vendors = self::getVendorsArray();
        return $vendors[$val];
    }
    /** 
     * @desc: get Vendor List with limit
     * @author: nhut.nguyen
     */
    public function getVendorList(){
        
        $page_number = $this->getRequest()->getParam('page');
        if(empty($page_number)){
            $page_number = 1;
        }
        return Mage::getModel('vendor/vendor')->getVendorList($page_number);
    }
    /** 
     * @desc: get Pagination
     * @author: nhut.nguyen
     */
    public function getPagination(){
        $page_number = $this->getRequest()->getParam('page');
        if(empty($page_number)){
            $page_number = 1;
        }
        return Mage::getModel('vendor/vendor')->getPagination($page_number);
    }
    /** 
     * @desc: get Pagination
     * @author: nhut.nguyen
     */
    public static function getVendorInfo($vendor_code){
        return Mage::getModel("vendor/vendor_information")-> getVendorInfoByVendorCode($vendor_code);
    }
}