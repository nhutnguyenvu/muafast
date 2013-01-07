<?php


class FavoredMinds_Vendor_Controller_Router extends Mage_Core_Controller_Varien_Router_Abstract
{
	/* *
	 *@author : khanh.phan
	 */
    public function initControllerRouters($observer)
    {
        $front = $observer->getEvent()->getFront();

        $vendor = new FavoredMinds_Vendor_Controller_Router();
        $front->addRouter('info', $vendor);
    }

    /**
     * Checks
     *
     */
    public function match(Zend_Controller_Request_Http $request)
    {
         $params = trim($request->getPathInfo(), '/');
		 $vendor_id = $this->getVendorId($params);

	      if ($vendor_id) {
		        $request->setModuleName('vendor')
		            ->setControllerName('Index')
		            ->setActionName('productlist')
		            ->setParam('vendor_id',$vendor_id);
				$request->setAlias(
					Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS,
					 $params 
				);
				return true;
	       }
	  

		return false;
        
    }
	 public function getVendorId($vendorPath){
        
       $resource = Mage::getSingleton('core/resource');
		$read = $resource->getConnection('core_read');
		$select =  $read->select()
			->from($resource->getTableName('vendors'),array("*"))
			->where("path = '".trim($vendorPath)."'");
		$result =  $read->fetchRow($select);
       
          return  $result['vendor_id'];
    }
}