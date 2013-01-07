<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Catalog view layer model
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Amasty_Shopby_Model_Layer extends Mage_Catalog_Model_Layer
{
  
    public function getProductCollection()
    {
        if (isset($this->_productCollections[$this->getCurrentCategory()->getId()])) {
            $collection = $this->_productCollections[$this->getCurrentCategory()->getId()];
        } else {
            $collection = $this->getCurrentCategory()->getProductCollection();
            $this->prepareProductCollection($collection);
            $this->_productCollections[$this->getCurrentCategory()->getId()] = $collection;
        }
		 
	  if(empty($vendor_id)&&($vendor_id = Mage::app()->getRequest()->getParam("vendor_id"))){
            
			$helper 		= Mage::app()->getHelper('vendor');
			$vendor_data = $helper->getVendorUserInfo($vendor_id);
			$collection->addAttributeToFilter('manufacturer', $vendor_data['vendor_code']);
        }
        if(empty($sp)&&(Mage::app()->getRequest()->getParam("sp")==true)){
			$dateToday = date('m/d/y');
            $tomorrow = mktime(0, 0, 0, date('m'), date('d')+1, date('y'));
            $dateTomorrow = date('m/d/y', $tomorrow);
			$collection->addAttributeToFilter('special_from_date', array('date' => true, 'to' => $dateToday))
                ->addAttributeToFilter('special_to_date', array('or'=> array(
                    0 => array('date' => true, 'from' => $dateTomorrow),
                    1 => array('is' => new Zend_Db_Expr('null')))
                ), 'left');
		
		}
		
        return $collection;
    }

   
}
