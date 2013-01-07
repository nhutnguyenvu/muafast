<?php
/**
* qikDropShipper Extension
*
* @category   FavoredMinds
* @package    Vendor
* @author     FavoredMinds Solution (geedubya15@gmail.com)
* @copyright  Copyright (c) 2010 FavoredMinds.com (http://www.favoredminds.com)
*/

class FavoredMinds_Vendor_Block_Adminhtml_Setup extends Mage_Core_Block_Template
{
	protected $_products	= array();
	
	public function __construct()
	{
		$this->_controller = 'adminhtml_setup';
		$this->_blockGroup = 'vendor';
		$this->_headerText = "Vendors";
		parent::__construct();
	}
	
	protected function _prepareLayout()
	{
		return parent::_prepareLayout();
	}
	
    
}