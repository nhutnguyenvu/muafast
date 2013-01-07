<?php
/**
* qikDropShipper Extension
*
* @category   FavoredMinds
* @package    Vendor
* @author     FavoredMinds Solution (geedubya15@gmail.com)
* @copyright  Copyright (c) 2010 FavoredMinds.com (http://www.favoredminds.com)
*/

class FavoredMinds_Vendor_Block_Adminhtml_Information_Edit_Form extends Mage_Adminhtml_Block_Widget_Form {
	protected function _prepareForm() {
		$_helper 		= Mage::app()->getHelper('vendor');
		$_vendor = $_helper->getVendorUserInfo($_helper->getVendorUserId());
		$vendor_id = $_vendor['vendor_id'];
		
		$id     = $vendor_id;
		if(!$this->getRequest()->getParam('id')) {
			$good_id=$id;
		} else
			$good_id=$this->getRequest()->getParam('id');
		
		$form = new Varien_Data_Form(array(
                    'id' => 'edit_form',
                    'action' => $this->getUrl('*/*/save', array('id' => $good_id)),
                    'method' => 'post',
                    'enctype' => 'multipart/form-data'
        ));
		
		$form->setUseContainer(true);
		$this->setForm($form);
		return parent::_prepareForm();
	}
}