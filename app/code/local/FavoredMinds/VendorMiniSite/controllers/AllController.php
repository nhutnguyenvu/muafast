<?php
/**
 * qikDropShipper Extension
 *
 * @category   FavoredMinds
 * @package    Vendor
 * @author     FavoredMinds Solution (geedubya15@gmail.com)
 * @copyright  Copyright (c) 2010 FavoredMinds.com (http://www.favoredminds.com)
 */

class FavoredMinds_VendorMiniSite_AllController extends Mage_Core_Controller_Front_Action {

	public function _construct() {
		parent::_construct();
	}

	protected function _initAction() {
		$this->loadLayout();
		return $this;
	}


	public function indexAction() {
		$name = $this->getRequest()->getParam('name');
		$vendors = Mage::getModel('vendor/vendor')->getCollection();
		$vendors->addFieldToFilter('username', $name);
		foreach($vendors as $vendor){
			$companyname = $vendor->getCompany_name();
			break;
		}
		$this->loadLayout();

		$this->getLayout()->getBlock('head')->setTitle($this->__($companyname.' Profile'));
		$this->renderLayout();
	}

}