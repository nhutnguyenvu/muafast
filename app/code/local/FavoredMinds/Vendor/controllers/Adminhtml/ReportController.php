<?php
/**
* qikDropShipper Extension
*
* @category   FavoredMinds
* @package    Vendor
* @author     FavoredMinds Solution (geedubya15@gmail.com)
* @copyright  Copyright (c) 2010 FavoredMinds.com (http://www.favoredminds.com)
*/

class FavoredMinds_Vendor_Adminhtml_ReportController extends Mage_Adminhtml_Controller_action
{
	
	public function _construct()
	{
		$helper			= Mage::app()->getHelper('vendor');
		if(!$helper->check())
		{
			die("<script type='text/javascript'>window.location = '".$this->getUrl("")."admin';</script>");
		}
		parent::_construct();
	}
	
	protected function _initAction()
	{
		$this->loadLayout()
			->_setActiveMenu('report/vendor_report_sales')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Vendors Manager'), Mage::helper('adminhtml')->__('Vendors Sales'));
		return $this;
	}

	public function salesAction()
	{
		$this->_initAction()
			->renderLayout();
	}

	public function indexAction()
	{
		$this->_initAction()
			->renderLayout();
	}
	
}