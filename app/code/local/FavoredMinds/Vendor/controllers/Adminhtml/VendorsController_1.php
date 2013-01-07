<?php

/**
 * qikDropShipper Extension
 *
 * @category   FavoredMinds
 * @package    Vendor
 * @author     FavoredMinds Solution (geedubya15@gmail.com)
 * @copyright  Copyright (c) 2010 FavoredMinds.com (http://www.favoredminds.com)
 */
class FavoredMinds_Vendor_Adminhtml_VendorsController extends Mage_Adminhtml_Controller_action {

    public function _construct() {
        $helper = Mage::app()->getHelper('vendor');
        if (!$helper->check()) {
            die("<script type='text/javascript'>window.location = '" . $this->getUrl("") . "admin';</script>");
        }
        parent::_construct();
    }

    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('favoredminds_vendor/view')
                ->_addBreadcrumb(Mage::helper('vendor')->__('Vendors'), Mage::helper('vendor')->__('Vendor'));

        return $this;
    }

    public function indexAction() {
        $this->_initAction()->renderLayout();
    }

    public function editAction() {
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('vendor/vendor')->load($id);

        if ($model->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }

            Mage::register('vendor_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('vendor/items');

            $this->_addBreadcrumb(Mage::helper('vendor')->__('Vendor Manager'), Mage::helper('vendor')->__('Vendor Manager'));
            $this->_addBreadcrumb(Mage::helper('vendor')->__('Vendor News'), Mage::helper('vendor')->__('Vendor News'));

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('vendor/adminhtml_vendors_edit'))
                    ->_addLeft($this->getLayout()->createBlock('vendor/adminhtml_vendors_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('vendor')->__('Item does not exist'));
            $this->_redirect('*/*/');
        }
    }

    public function newAction() {
        $this->_forward('edit');
    }

    public function saveAction() {
        $helper = Mage::app()->getHelper('vendor');
        if ($data = $this->getRequest()->getPost()) {

            $value = $data['filename'];

            if (is_array($value) && !empty($value['delete'])) {
                $data['logo'] = '';
            }
            $_logo = 'logo_' . $_FILES['filename']['name'];
            if ($data['unencrypted_pass'] != '') {
                $data['password'] = Mage::helper('core')->getHash($data['unencrypted_pass'], 2);
            }
            if ($data['new_password'] != '') {
                $data['unencrypted_pass'] = $data['new_password'];
                $data['password'] = Mage::helper('core')->getHash($data['new_password'], 2);
            }

            $model = Mage::getModel('vendor/vendor');

            $model->setData($data)
                    ->setId($this->getRequest()->getParam('id'));

            try {
                if ((int) $this->getRequest()->getParam('id') == 0) {
                    $model->setCreatedTime(Mage::getSingleton('core/date')->gmtDate())
                            ->setUpdateTime(Mage::getSingleton('core/date')->gmtDate());
                } else {
                    $model->setUpdateTime(Mage::getSingleton('core/date')->gmtDate());
                }

                $model->save();

                $helper->createVendorCoreUser($model->getVendorId());
                $helper->SyncVendorCoreUser($model->getVendorId());

                //add the vendor user sync routine
                $user_id = $helper->getVendorCoreUserId($model->getVendorId());
                $helper->SyncVendorCoreUser($model->getVendorId(), $user_id);
                $helper->addVendorToManufacturers($model->getVendorId());
                $manufacturer = $helper->getManufacturerOption($model->getVendorId());
                $model->setVendorCode($manufacturer['value']);
                //$model->addData(array('vendor_code' => $manufacturer));

                $model->save();
                //add url rewrite to front end

                if (isset($_FILES['filename']['name']) && $_FILES['filename']['name'] != '') {
                    try {
                        /* Starting upload */
                        $uploader = new Varien_File_Uploader('filename');

                        // Any extention would work
                        $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
                        $uploader->setAllowRenameFiles(false);

                        // Set the file upload mode
                        // false -> get the file directly in the specified folder
                        // true -> get the file in the product like folders
                        //	(file.jpg will go in something like /media/f/i/file.jpg)
                        $uploader->setFilesDispersion(false);

                        // We set media as the upload dir
                        $path = Mage::getBaseDir('media') . DS . 'vendor' . DS . $model->getId() . DS;
                        $uploader->save($path, 'logo_' . $_FILES['filename']['name']);

                        $lastId = $model->getId();
                        $model->setLogo("vendor/$lastId/" . $_logo);
                        $model->save();
                    } catch (Exception $e) {
                        Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                        $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                    }

                    //this way the name is saved in DB
                }

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('vendor')->__('Vendor was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('vendor')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
    }

    public function deleteAction() {
        $helper = Mage::app()->getHelper('vendor');
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                //$model = Mage::getModel('vendor/vendor');
                //$model->setId($this->getRequest()->getParam('id'))->delete();
                $helper->deleteVendor($this->getRequest()->getParam('id'));

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('vendor')->__('Vendor was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    public function massDeleteAction() {
        $helper = Mage::app()->getHelper('vendor');
        $vendorIds = $this->getRequest()->getParam('vendor');
        if (!is_array($vendorIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($vendorIds as $vendorId) {
                    $helper->deleteVendor($vendorId);
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('adminhtml')->__(
                                'Total of %d record(s) were successfully deleted', count($vendorIds)
                        )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function massStatusAction() {
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
        $helper = Mage::app()->getHelper('vendor');
        $session = Mage::getSingleton('core/session');
        $vendorIds = $this->getRequest()->getParam('vendor');
        if (!is_array($vendorIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($vendorIds as $vendorId) {
                    $vendor = Mage::getSingleton('vendor/vendor')
                            ->load($vendorId)
                            ->setStatus($this->getRequest()->getParam('status'))
                            ->setIsMassupdate(true)
                            ->save();
                    //add the vendor user sync routine
                    $user_id = $helper->getVendorCoreUserId($vendorId);
                    $helper->SyncVendorCoreUser($vendorId, $user_id);
                    $helper->addVendorToManufacturers($vendorId);

                    $manufacturers = $helper->getManufacturers();
                    $manufacturer = $vendor->getCompanyName();
                    $found = false;
                    foreach ($manufacturers as $_manufacturer) {
                        if ($_manufacturer["label"] == $manufacturer) {
                            $found = true;
                            $vendor->setVendorCode($_manufacturer['value']);
                            $vendor->save();
                        }
                    }
                }
                $this->_getSession()->addSuccess(
                        $this->__('Total of %d record(s) were successfully updated', count($vendorIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function exportCsvAction() {
        $fileName = 'vendors.csv';
        $content = $this->getLayout()->createBlock('vendor/adminhtml_vendors_grid')
                ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction() {
        $fileName = 'vendors.xml';
        $content = $this->getLayout()->createBlock('vendor/adminhtml_vendors_grid')
                ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse($fileName, $content, $contentType = 'application/octet-stream') {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK', '');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename=' . $fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }
    /** @desc: vendor generate voting
     *  @param: 
     *  @return: generate voting for vendor
     */
    public function updateVotingAction(){
        
        $modelRating = Mage::getModel("vendor/vendor_customer_rating");    
        if($modelRating->updateRatingVendor()){
            echo "Rating was updated";
        }
        else{
            echo "Rating wasn't updated, some datumn mistook";
        }
        die;
    }
}