<?php
/**
* qikDropShipper Extension
*
* @category   FavoredMinds
* @package    Vendor
* @author     FavoredMinds Solution (geedubya15@gmail.com)
* @copyright  Copyright (c) 2010 FavoredMinds.com (http://www.favoredminds.com)
*/

class FavoredMinds_Vendor_Adminhtml_SetupController extends Mage_Adminhtml_Controller_action
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
      ->_setActiveMenu('favoredminds_vendor/setup')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Vendors Manager'), Mage::helper('adminhtml')->__('Vendors General Setup'));
		return $this;
	}   
 
	public function indexAction()
	{
		$this->_initAction()
			->renderLayout();
	}
	
	public function postAction()
	{
		$this->_initAction()
			->renderLayout();
			
		$post		= $this->getRequest()->getPost();
		$helper 	= Mage::app()->getHelper('vendor');
		$session	= Mage::getSingleton('core/session');
		
		$action		= $post["doaction"];
		
		switch($action)
		{
			
			case "product_attribute":
				
				$attribute_code		= $post["attribute_code"];
				$helper->installProductAttribute($attribute_code);
				
				$session->addSuccess($this->__("Attribute <i>$attribute_code</i> has been installed"));
				
			break;
			
			break;
			
		}
		
		
		$this->_redirect("vendor/adminhtml_setup");
	}
	
}