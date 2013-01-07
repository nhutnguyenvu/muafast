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
 * @package     Mage_CatalogSearch
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog Search Controller
 *
 * @category   Mage
 * @package    Mage_CatalogSearch
 * @module     Catalog
 */
 require_once 'Mage/CatalogSearch/controllers/AdvancedController.php';
class Luclong_Custom_CatalogSearch_AdvancedController extends Mage_CatalogSearch_AdvancedController
{

    public function indexAction()
    {

        $this->loadLayout();
		 try {
            Mage::getSingleton('catalogsearch/advanced')->addFilters($this->getRequest()->getQuery());
        } catch (Mage_Core_Exception $e) {
            Mage::getSingleton('catalogsearch/session')->addError($e->getMessage());
            
        }
        $this->_initLayoutMessages('catalogsearch/session');
        $this->renderLayout();
    }
	public function resultajaxAction(){
		if($this->getRequest()->getParam('isAjax') == 1){
			
		$this->loadLayout();
		
		 try {
            Mage::getSingleton('catalogsearch/advanced')->addFilters($this->getRequest()->getQuery());
			  $response = $this->getLayout()->getBlock('catalogsearch_advanced_result')->toHtml();
			 
        } catch (Mage_Core_Exception $e) {
			
            $response =$e->getMessage();
        }
			$response = Zend_Json::encode($response);
			$this->getResponse()->setBody($response);
      
		}
	}
 
}
