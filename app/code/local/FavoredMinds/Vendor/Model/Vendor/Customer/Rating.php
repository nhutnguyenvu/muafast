<?php
/**
* qikDropShipper Extension
*
* @category   FavoredMinds
* @package    Vendor
* @author     FavoredMinds Solution (geedubya15@gmail.com)
* @copyright  Copyright (c) 2010 FavoredMinds.com (http://www.favoredminds.com)
*/

class FavoredMinds_Vendor_Model_Vendor_Customer_Rating extends Mage_Core_Model_Abstract {
	
	protected function _construct() {
		$this->_init('vendor/vendor_customer_rating');
		parent::_construct();
	}
    public function updateRating($vendorId, $customerId,$rate) {
        $resource = Mage::getSingleton('core/resource');
        $write = $resource->getConnection('core_write');
        
        $where['vendor_id = ?'] = $vendorId;
        $where['customer_id = ?'] = $customerId;

        try {                   
            $write->update($resource->getTableName("customer_rating"),array("rate"=>$rate),$where);                        
        } catch (Exception $e) {
            $write->rollBack();
            Mage::log($e->getMessage());
            return false;
        }
        return true;
    }
    public function getInfoByVC($vendorId, $customerId) {
        
		$resource = Mage::getSingleton('core/resource');
		$read = $resource->getConnection('core_read');
		$select =  $read->select()
			->from($resource->getTableName('customer_rating'),array("id","vendor_id","customer_id","updated_at"))
			->where("vendor_id = '".$vendorId."'")
            ->where("customer_id = '".$customerId."'");
                
		$result =  $read->fetchRow($select);
        
        return $result;
		
    }
    /** @desc: get list rated vendor and their rating information
     *  @return
     */
    public function getVendorRatingList() {
        $resource = Mage::getSingleton('core/resource');
		$read = $resource->getConnection('core_read');
		$select =  $read->select()
                ->from($resource->getTableName('customer_rating'),
                        array("*","count(rate) as num_rate","round(avg(rate)) as average_rate"))
                ->group('vendor_id');
        
        $result =  $read->fetchAll($select);
        
        return $result;
    }
    
    public function updateAverageVendorRating($vendorId){
        
        $resource = Mage::getSingleton('core/resource');
		$read = $resource->getConnection('core_read');
		$select =  $read->select()
                ->from($resource->getTableName('customer_rating'),
                        array("count(rate) as num_rate","round(avg(rate)) as average_rate"))
                ->where("vendor_id = '".$vendorId."'")
                ->group('vendor_id');
        
        
        $result =  $read->fetchRow($select);
        
        $write = $resource->getConnection('core_write');
        
        $where['vendor_id = ?'] = $vendorId;
        
        try {                   
            $write->update($resource->getTableName("vendors"),array("num_rate"=>$result['num_rate'],
                                                    "average_rate"=>$result['average_rate']),$where);                        
        } catch (Exception $e) {
            $write->rollBack();
            Mage::log($e->getMessage());
            return false;
        }
        return true;
    }
    public function updateRatingVendorList(){
        
        $listInfo  = $this->getVendorRatingList();
        
        $model = Mage::getModel("vendor/vendor");
        if(empty($listInfo)){
            return false;
        }
        else{
            try{
                $resource = Mage::getSingleton('core/resource');
                $write = $resource->getConnection('core_write');
                $write->beginTransaction();
                for($i =0 ;$i < count($listInfo);$i++){
                    $row = $listInfo[$i];
                    $where['vendor_id = ?'] = $row['vendor_id'];
                    try {                   
                        $write->update($resource->getTableName("vendors"),array("num_rate"=>$row['num_rate'],
                                                                "average_rate"=>$row['average_rate']),$where);                        
                    } catch (Exception $e) {
                        $write->rollBack();
                        Mage::log($e->getMessage());
                        return false;
                    }
                }
                $write->commit();
            }
            catch(Exception $e){
                
                $write->rollBack();
                Mage::log($e->getMessage());
                return false;
                
            }
            return true;
        }
        return false;
        
    }
}