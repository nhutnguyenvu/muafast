<?php

/**
 * qikDropShipper Extension
 *
 * @category   FavoredMinds
 * @package    Vendor
 * @author     FavoredMinds Solution (geedubya15@gmail.com)
 * @copyright  Copyright (c) 2010 FavoredMinds.com (http://www.favoredminds.com)
 */
class FavoredMinds_Vendor_Adminhtml_StatementsController extends Mage_Adminhtml_Controller_action {

    public function _construct() {
        $helper = Mage::app()->getHelper('vendor');
        if (!$helper->check()) {
            die("<script type='text/javascript'>window.location = '" . $this->getUrl("") . "admin';</script>");
        }
        parent::_construct();
    }

    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('favoredminds_vendor/statements')
                ->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));

        return $this;
    }

    public function indexAction() {
        $this->_initAction()->renderLayout();
    }

    public function payAction() {
        $this->loadLayout();
        $this->_setActiveMenu('favoredminds_vendor/statements');

        $this->_addBreadcrumb(Mage::helper('vendor')->__('Vendor Manager'), Mage::helper('vendor')->__('Vendor Manager'));

        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

        $this->_addContent($this->getLayout()->createBlock('vendor/adminhtml_statements_pay'));

        $this->renderLayout();
    }

    public function generateAction() {
        
        $this->loadLayout();
        $this->_setActiveMenu('favoredminds_vendor/statements');

        $this->_addBreadcrumb(Mage::helper('vendor')->__('Vendor Manager'), Mage::helper('vendor')->__('Vendor Manager'));

        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        
        $this->_addContent($this->getLayout()->createBlock('vendor/adminhtml_statements_generate'));
        
        //error_reporting(E_ALL | E_STRICT);
        //error_reporting(E_ERROR);
        
        $this->renderLayout();
    }

    public function processAction() {
        
        /*
        $statementId = 51;
        if(!empty($statementId)){
            $modelDetail = Mage::getModel("vendor/vendor_statementdetail");
            $modelDetail->saveStatementDetail($statementId);
        }
        */        
        $_helper = Mage::helper('vendor');

        $report_from = $this->getRequest()->getParam('report_from');
        $report_to = $this->getRequest()->getParam('report_to');

        $date_format = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
        $report_from = Mage::app()->getLocale()->date($report_from, $date_format, null, false)->toString('yyyy-MM-dd');
        //$report_to = Mage::app()->getLocale()->date($report_to, $date_format, null, false)->addDay(1)->toString('yyyy-MM-dd');
        $report_to = Mage::app()->getLocale()->date($report_to, $date_format, null, false)->toString('yyyy-MM-dd');

        $vendors = $this->getRequest()->getParam('vendors');

        if (in_array('-1', $vendors)) {
            $vendors = Mage::getModel('vendor/vendor')->getCollection()
                    ->addFieldToFilter('status', '1')
                    ->getAllIds();
        }

        $period = $this->getRequest()->getParam('statement_period');
        if (!$period) {
            $period = date('ym', strtotime($dateFrom));
        }

        $i = 0;

        $generator = Mage::getModel('vendor/statement_pdf_statement');
        foreach ($vendors as $vendor_id) {
            try {
                $statement = Mage::getModel('vendor/vendor_statement');
                if ($statement->load("{$vendor_id}-{$period}", 'statement_id')->getId()) {
                    //statement already exists
                    continue;
                }
                $statement->addData(array(
                    'vendor_id' => $vendor_id,
                    'order_date_from' => $report_from,
                    'order_date_to' => $report_to,
                    'statement_id' => "{$vendor_id}-{$period}",
                    'statement_date' => now(),
                    'statement_period' => $period,
                    'statement_filename' => "statement-{$vendor_id}-{$period}.pdf",
                    'created_at' => now(),
                ));

                $statement->fetchOrders($vendor_id);

                $statement->save();
                $i++;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/');
            }
            try{
                
                if(!empty($statement)){
                    $statementId = $statement->getId() ;  
                    if(!empty($statementId)){
                        //$statementId = $statement->getId();
                        $modelDetail = Mage::getModel("vendor/vendor_statementdetail");
                        $modelDetail->saveStatementDetail($statementId);
                    }
                }
                
                /*
                $statementId = 51;
                if(!empty($statementId)){
                    //$statementId = $statement->getId();

                    $modelDetail = Mage::getModel("vendor/vendor_statementdetail");
                    $modelDetail->saveStatementDetail($statementId);
                }
                */

            }
            catch (Exception $e) {
                Mage::log("Detail didn't save for statement");
                Mage::log($e->getMessage());
                $this->_redirect('*/*/');
            }
        }
        
        
        if ($i > 0) {
            Mage::getSingleton('adminhtml/session')->addSuccess(sprintf($_helper->__('Successfully generated %s statements'), $i));
            $this->_redirect('*/*/');
        } else {
            Mage::getSingleton('adminhtml/session')->addError($_helper->__('No statements generated'));
            $this->_redirect('*/*/');
        }
    }

    public function editAction() {
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('vendor/vendor_statement')->load($id);

        if ($model->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }

            Mage::register('vendorstatements_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('favoredminds_vendor/statements');

            $this->_addBreadcrumb(Mage::helper('vendor')->__('Vendor Manager'), Mage::helper('vendor')->__('Vendor Manager'));
            $this->_addBreadcrumb(Mage::helper('vendor')->__('Vendor Sales Statements'), Mage::helper('vendor')->__('Vendor Sales Statements'));

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('vendor/adminhtml_statements_edit'))
                    ->_addLeft($this->getLayout()->createBlock('vendor/adminhtml_statements_edit_tabs'));
            
            $this->_addContent($this->getLayout()->createBlock('vendor/adminhtml_statements')->setTemplate("vendor/statements/edit.phtml"));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('vendor')->__('Statement does not exist'));
            $this->_redirect('*/*/');
        }
    }

    public function newAction() {
        $this->_redirect('*/*/');
        //$this->_forward('edit');
    }

    public function saveAction() {
        if ($data = $this->getRequest()->getPost()) {
            $_helper = Mage::helper('vendor');

            try {
                $statement = Mage::getModel('vendor/vendor_statement');
                $statement->load($this->getRequest()->getParam('id'));

                $statement->setData($data)
                        ->setId($this->getRequest()->getParam('id'));

                $statement->save();
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/');
            }

            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('vendor')->__('Statement successfully updated'));
            $this->_redirect('*/*/');
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('vendor')->__('Wrong input data'));
            $this->_redirect('*/*/');
        }
    }

    public function deleteAction() {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $model = Mage::getModel('vendor/vendor_statement');

                $model->setId($this->getRequest()->getParam('id'))
                        ->delete();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Statement was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    public function massDeleteAction() {
        $vendorstatementsIds = $this->getRequest()->getParam('vendors_statements');
        if (!is_array($vendorstatementsIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select statement(s)'));
        } else {
            try {
                foreach ($vendorstatementsIds as $vendorstatementsId) {
                    $vendorstatements = Mage::getModel('vendor/vendor_statement')->load($vendorstatementsId);
                    $vendorstatements->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('adminhtml')->__(
                                'Total of %d record(s) were successfully deleted', count($vendorstatementsIds)
                        )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function massStatusAction() {
        $vendorstatementsIds = $this->getRequest()->getParam('vendors_statements');
        if (!is_array($vendorstatementsIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($vendorstatementsIds as $vendorstatementsId) {
                    $vendorstatements = Mage::getSingleton('vendor/vendor_statement')
                            ->load($vendorstatementsId)
                            ->setStatus($this->getRequest()->getParam('status'))
                            ->setIsMassupdate(true)
                            ->save();
                }
                $this->_getSession()->addSuccess(
                        $this->__('Total of %d record(s) were successfully updated', count($vendorstatementsIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function massDownloadAction() {
        $objIds = (array) $this->getRequest()->getParam('vendors_statements');
        if (!is_array($objIds)) {
            $this->_getSession()->addError($this->__('Please select statement(s)'));
        }
        try {
            $generator = Mage::getModel('vendor/statement_pdf_statement')->before();
            $statement = Mage::getModel('vendor/vendor_statement');
            foreach ($objIds as $id) {
                $statement = $statement->load($id);
                if (!$statement->getId()) {
                    continue;
                }
                $generator->addStatement($statement);
            }
            $pdf = $generator->getPdf();
            if (empty($pdf->pages)) {
                Mage::throwException(Mage::helper('vendor')->__('No statements found to print'));
            }
            $generator->insertTotalsPage()->after();
            Mage::helper('vendor')->sendDownload('statements.pdf', $pdf->render(), 'application/x-pdf');
        } catch (Mage_Core_Model_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()->addException($e, $this->__('There was an error while download vendor statement(s): %s', $e->getMessage()));
        }

        $this->_redirect('*/*/');
    }

    public function massEmailAction() {
        $objIds = (array) $this->getRequest()->getParam('vendors_statements');
        if (!is_array($objIds)) {
            $this->_getSession()->addError($this->__('Please select statement(s)'));
        }
        try {
            $statement = Mage::getModel('vendor/vendor_statement');
            foreach ($objIds as $id) {
                $statement = $statement->load($id);
                $statement->send();
            }
            //Mage::helper('vendor')->processQueue();
            $this->_getSession()->addSuccess(
                    $this->__('Total of %d statement(s) have been sent', count($objIds))
            );
        } catch (Mage_Core_Model_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()->addException($e, $this->__('There was an error while download vendor statement(s): %s', $e->getMessage()));
        }

        $this->_redirect('*/*/');
    }

    public function exportCsvAction() {
        $fileName = 'vendors_statements.csv';
        $content = $this->getLayout()->createBlock('vendor/adminhtml_statements_grid')
                ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction() {
        $fileName = 'vendors_statements.xml';
        $content = $this->getLayout()->createBlock('vendor/adminhtml_statements_grid')
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

}