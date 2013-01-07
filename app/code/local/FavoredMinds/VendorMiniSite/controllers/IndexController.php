<?php
/**
 * qikDropShipper Extension
 *
 * @category   FavoredMinds
 * @package    Vendor
 * @author     FavoredMinds Solution (geedubya15@gmail.com)
 * @copyright  Copyright (c) 2010 FavoredMinds.com (http://www.favoredminds.com)
 */

class FavoredMinds_VendorMiniSite_IndexController extends Mage_Core_Controller_Front_Action {

	public function _construct() {
		parent::_construct();
	}

	protected function _initAction() {
		$this->loadLayout();
		return $this;
	}


	public function indexAction() {
		$this->loadLayout();
		$this->getLayout()->getBlock('head')->setTitle($this->__('Vendors List'));
		$this->renderLayout();
	}









}