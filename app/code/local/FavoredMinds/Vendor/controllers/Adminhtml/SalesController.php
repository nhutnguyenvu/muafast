<?php

/**
 * qikDropShipper Extension
 *
 * @category   FavoredMinds
 * @package    Vendor
 * @author     FavoredMinds Solution (geedubya15@gmail.com)
 * @copyright  Copyright (c) 2010 FavoredMinds.com (http://www.favoredminds.com)
 */
class FavoredMinds_Vendor_Adminhtml_SalesController extends Mage_Adminhtml_Controller_action {

    public function _construct() {
        $helper = Mage::app()->getHelper('vendor');
        if (!$helper->check()) {
            die("<script type='text/javascript'>window.location = '" . $this->getUrl("") . "admin';</script>");
        }
        parent::_construct();
    }

    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('favoredminds_vendor/sales')
                ->_addBreadcrumb(Mage::helper('adminhtml')->__('Orders'), Mage::helper('adminhtml')->__('Order'));

        return $this;
    }

    public function indexAction() {
        $this->_initAction()
                ->renderLayout();
    }

    public function viewAction() {
        
        $id = $this->getRequest()->getParam('id');
        $this->loadLayout();
        $this->renderLayout();
    }

    public function editAction() {

        $id = $this->getRequest()->getParam('id');
        //$model  = Mage::getModel('vendor/vendor_sales')
        $model = Mage::getModel('sales/order')->load($id);

        if ($model->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }

            Mage::register('vendorsales_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('vendorsales/items');

            $this->_addBreadcrumb(Mage::helper('vendor')->__('Item Manager'), Mage::helper('vendor')->__('Vendor Order'));
            $this->_addBreadcrumb(Mage::helper('vendor')->__('Item News'), Mage::helper('vendor')->__('Order News'));

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('vendor/adminhtml_sales_edit'));
            //$this->_addLeft($this->getLayout()->createBlock('vendor/adminhtml_sales_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('vendor')->__('Order does not exist'));
            $this->_redirect('*/*/');
        }
    }

    public function newAction() {
        $this->_forward('edit');
    }

    public function saveAction() {

        
        if ($data = $this->getRequest()->getPost()) {

            if (!empty($data['comment_text'])) {
                Mage::getSingleton('adminhtml/session')->setCommentText($data['comment_text']);
            }

            $comment = '';
            if (!empty($data['comment_text'])) {
                $comment = $data['comment_text'];
            }

            $orderId = $this->getRequest()->getParam('id');
            if ($orderId) {
                $_order = Mage::getModel('sales/order')->load($orderId);
            } else {
                //we do not have such order - redirect to view
                $this->_redirect('*/*/');
            }
            /**
             * Check order existing
             */
            if (!$_order->getId()) {
                $this->_getSession()->addError($this->__('The order no longer exists.'));
                return false;
            }
            $order_real_id = $this->getRequest()->getParam('id');
            $_helper = Mage::app()->getHelper('vendor');
            if ($_helper->vendorIsLogged()) {
                $_vendor = $_helper->getVendorUserInfo($_helper->getVendorUserId());
                $vendor_id = $_vendor['vendor_id'];
            }
            $tracking_added = false;

            if (((string) $data['tracking'] != '')) {
                if (!$_order->canShip()) {
                    Mage::getSingleton('adminhtml/session')->addError('Cannot do another shipment for the order.');
                    Mage::getSingleton('adminhtml/session')->setFormData($data);
                    $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                }
                try {
                    $data['comment_customer_notify'] = true;
                    $shipment = false;
                    /**
                     * Check shipment create availability
                     */
                    $_items = $_order->getItemsCollection();
                    $values = array();
                    foreach ($_items as $_item) {
                        //check if the item is shipped
                        $shipped = false;
                        $shipments = Mage::getModel('sales/order_shipment')->getCollection()
                                ->addAttributeToSelect('*')
                                ->addAttributeToFilter('sfsi.product_id', $_item->getProductId())
                                ->addAttributeToFilter('order_id', $order_real_id)
                                ->addAttributeToSort('main_table.entity_id', 'asc')
                        ;
                        $shipments->getSelect()
                                ->join(array('sfsi' => $_helper->getTableName('sales_flat_shipment_item')), 'sfsi.parent_id=main_table.entity_id', array())
                        ;
                        foreach ($shipments as $id => $shipment) {
                            $shipped = true;
                        }
                        if ($shipped) {
                            continue;
                        }

                        if ($_helper->vendorIsLogged()) {
                            //we filter the output by vendor
                            if ($product = Mage::getModel('catalog/product')->load($_item->getProductId())) {
                                $manufacturer = $product->getManufacturer();
                                if ($manufacturer == $_vendor['vendor_code']) {
                                    $values[$product->getId()]['qty'] = $_item->getQtyOrdered();
                                    $values[$product->getId()]['item'] = $_item->getId();
                                }
                            }
                        } else {
                            $product = Mage::getModel('catalog/product')->load($_item->getProductId());
                            $values[$product->getId()]['qty'] = $_item->getQtyOrdered();
                            $values[$product->getId()]['item'] = $_item->getId();
                        }
                    }

                    $savedQtys = array();
                    //quantities for shipping
                    foreach ($data['products'] as $key => $product) {
                        $savedQtys[$values[$product]['item']] = $values[$product]['qty'];
                    }
                    if (sizeof($values) > 0) {
                        $shipment = Mage::getModel('sales/service_order', $_order)->prepareShipment($savedQtys);

                        $tracking['carrier_code'] = 'custom';
                        $tracking['title'] = $data['carrier'];
                        $tracking['number'] = $data['tracking'];
                        $track = Mage::getModel('sales/order_shipment_track')
                                ->addData($tracking);
                        $shipment->addTrack($track);
                        if (!empty($data['comment_text'])) {
                            $shipment->addComment($data['comment_text'], isset($data['comment_customer_notify']));
                        }

                        if ($shipment) {
                            $shipment->register();

                            if (!empty($data['send_email'])) {
                                $shipment->setEmailSent(true);
                            }

                            $shipment->getOrder()->setCustomerNoteNotify(!empty($data['send_email']));
                            $shipment->getOrder()->setIsInProcess(true);
                            $transactionSave = Mage::getModel('core/resource_transaction')
                                    ->addObject($shipment)
                                    ->addObject($shipment->getOrder())
                                    ->save();
                            $shipment->sendEmail(!empty($data['send_email']), $comment);

                            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('vendor')->__('Tracking information was successfully added'));
                            Mage::getSingleton('adminhtml/session')->setFormData(false);
                            $tracking_added = true;
                        }
                    }
                } catch (Exception $e) {
                    Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                    Mage::getSingleton('adminhtml/session')->setFormData($data);
                    $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                    return;
                }
            }

            $response = false;

            $notify = isset($data['is_customer_notified']) ? $data['is_customer_notified'] : true;
            $visible = isset($data['is_visible_on_front']) ? $data['is_visible_on_front'] : true;

            $_items = $_order->getItemsCollection();
            $values = array();
            foreach ($_items as $_item):
                //check if the item is shipped
                $shipped = false;
                $shipments = Mage::getModel('sales/order')->getCollection()
                        ->addAttributeToSelect('*')
                        ->addAttributeToFilter('sfsi.product_id', $_item->getProductId())
                        ->addAttributeToFilter('order_id', $order_real_id)
                        ->addAttributeToSort('main_table.entity_id', 'asc')
                ;
                $shipments->getSelect()
                        ->join(array('sfoi' => $_helper->getTableName('sales_flat_order_item')), 'sfoi.order_id=main_table.entity_id', array())
                ;

                if ($_helper->vendorIsLogged()) {
                    //we filter the output by vendor
                    if ($product = Mage::getModel('catalog/product')->load($_item->getProductId())) {
                        $manufacturer = $product->getManufacturer();
                        if ($manufacturer == $_vendor['vendor_code']) {
                            $values[$product->getId()]['qty'] = $_item->getQtyOrdered();
                            $values[$product->getId()]['item'] = $_item->getId();
                        }
                    }
                } else {
                    $product = Mage::getModel('catalog/product')->load($_item->getProductId());
                    $values[$product->getId()]['qty'] = $_item->getQtyOrdered();
                    $values[$product->getId()]['item'] = $_item->getId();
                }
            endforeach;

            //we also check the vendor sales tables
            $write = Mage::getSingleton('core/resource')->getConnection('core_write');

            $table = $_helper->getTableName("vendors_sales");
            $result = $write->query("select * from `$table` where order_real_id='$order_real_id' and vendor_id = '$vendor_id';");

            $line = $result->fetch();

            if ((int) $line['vendorsales_id'] > 0) {
                // we have the sales record
                $sale_id = $line['vendorsales_id'];
            } else {
                $write->query(" INSERT INTO `$table`(`vendor_id`,`order_real_id`) VALUES('$vendor_id','$order_real_id'); ");
                $sale_id = $write->lastInsertId();
            }

            //if we have the tracking information - check the shipping also
            $adjustment = 0;
            if (($data['adjustment_type'] == 'Negative')) {
                $adjustment = -1 * $data['adjustment'];
            } else if ($data['adjustment_type'] == 'Positive') {
                $adjustment = $data['adjustment'];
            }
            foreach ($data['products'] as $key => $product) {
                //check each product id
                $_productObj = Mage::getModel("catalog/product")->load($product);
                $product_name = $_productObj->getName();
                $item_id = $values[$product]['item'];
                $table = $_helper->getTableName("vendors_sales_items");
                $result = $write->query("select * from `$table` where vendorsales_id='$sale_id' and item_id = '$item_id';");
                $line = $result->fetch();

                if ((int) $line['vendor_sales_item_id'] > 0) {
                    // we have the sales record
                    $sale_item_id = $line['vendor_sales_item_id'];
                } else {
                    $write->query(" INSERT INTO `$table`(`item_id`,`product_id`,`vendorsales_id`) VALUES('$item_id','$product','$sale_id'); ");
                    $sale_item_id = $write->lastInsertId();
                }
                //updating the item status - surely insert new line
                $table = $_helper->getTableName("vendors_sales_history");
                $sales_history_comment = addslashes($comment);

                //adjustment is done only once
                if ($tracking_added) {
                    $write->query(" INSERT INTO `$table`(created_date, `vendorsales_id`,`vendor_sales_item_id`,`tracking_code`,`comments`,`status`,`adjustment`,`product_name`) VALUES(now(), '$sale_id','$sale_item_id', '{$data['tracking']}', '{$sales_history_comment}', '{$data['status']}', '{$adjustment}','{$product_name}'); ");
                } else {
                    $write->query(" INSERT INTO `$table`(created_date, `vendorsales_id`,`vendor_sales_item_id`,`comments`,`status`,`adjustment`,`product_name`) VALUES(now(), '$sale_id','$sale_item_id', '{$sales_history_comment}', '{$data['status']}', '{$adjustment}','{$product_name}'); ");
                }
                $adjustment = 0;
                $sale_item_id = $write->lastInsertId();
            }
            //status update
            switch ($data['status']) {
                case 'Cancelled':
                    $cancelled = true;
                    //check if we have all the vendors set the order to cancelled
                    $_order = Mage::getModel('sales/order')->load($this->getRequest()->getParam('id'));
                    $_items = $_order->getItemsCollection();
                    $values = array();
                    //select all the items
                    foreach ($_items as $_item):
                        if ($_helper->vendorIsLogged()) {
                            //we filter the output by vendor
                            if ($product = Mage::getModel('catalog/product')->load($_item->getProductId())) {
                                $manufacturer = $product->getManufacturer();
                                if ((int) $manufacturer > 0) {
                                    $values[$product->getId()]['qty'] = $_item->getQtyOrdered();
                                    $values[$product->getId()]['item'] = $_item->getId();

                                    $table1 = $_helper->getTableName("vendors_sales_items");
                                    $table2 = $_helper->getTableName("vendors_sales_history");
                                    $item_id = $_item->getId();
                                    $result = $write->query("select vsh.* from `$table1` vsi, `$table2` vsh where vsi.vendor_sales_item_id = vsh.vendor_sales_item_id and vsi.item_id = '$item_id' order by vsh.vendor_sales_history_id desc;");
                                    $line = $result->fetch();
                                    if ($line['status'] == 'Cancelled') {
                                        
                                    } else {
                                        $cancelled = false;
                                    }
                                }
                            }
                        }
                    endforeach;

                    if ($cancelled) {
                        try {
                            $_order->cancel()
                                    ->save();
                            $this->_getSession()->addSuccess(
                                    $this->__('The order has been cancelled.')
                            );
                        } catch (Mage_Core_Exception $e) {
                            $this->_getSession()->addError($e->getMessage());
                        } catch (Exception $e) {
                            $this->_getSession()->addError($this->__('The order has not been cancelled.'));
                            Mage::logException($e);
                        }
                    }
                    break;
                case 'On Hold':
                    $cancelled = true;
                    //check if we have all the vendors set the order to cancelled
                    $_order = Mage::getModel('sales/order')->load($this->getRequest()->getParam('id'));
                    $_items = $_order->getItemsCollection();
                    $values = array();
                    //select all the items
                    foreach ($_items as $_item):
                        if ($_helper->vendorIsLogged()) {
                            //we filter the output by vendor
                            if ($product = Mage::getModel('catalog/product')->load($_item->getProductId())) {
                                $manufacturer = $product->getManufacturer();
                                if ((int) $manufacturer > 0) {
                                    $values[$product->getId()]['qty'] = $_item->getQtyOrdered();
                                    $values[$product->getId()]['item'] = $_item->getId();

                                    $table1 = $_helper->getTableName("vendors_sales_items");
                                    $table2 = $_helper->getTableName("vendors_sales_history");
                                    $item_id = $_item->getId();
                                    $result = $write->query("select vsh.* from `$table1` vsi, `$table2` vsh where vsi.vendor_sales_item_id = vsh.vendor_sales_item_id and vsi.item_id = '$item_id' order by vsh.vendor_sales_history_id desc;");
                                    $line = $result->fetch();
                                    if ($line['status'] == 'On Hold') {
                                        
                                    } else {
                                        $cancelled = false;
                                    }
                                }
                            }
                        }
                    endforeach;

                    if ($cancelled) {
                        //check if we have all the vendors set the order to hold
                        $_order = Mage::getModel('sales/order')->load($this->getRequest()->getParam('id'));
                        try {
                            $_order->hold()
                                    ->save();
                            $this->_getSession()->addSuccess(
                                    $this->__('The order has been put on hold.')
                            );
                        } catch (Mage_Core_Exception $e) {
                            $this->_getSession()->addError($e->getMessage());
                        } catch (Exception $e) {
                            $this->_getSession()->addError($this->__('The order was not put on hold.'));
                        }
                    }
                    break;
                default:
                    if ($data['status'] != '') {
                        $cancelled = true;
                        //check if we have all the vendors set the order to cancelled
                        $_order = Mage::getModel('sales/order')->load($this->getRequest()->getParam('id'));
                        $_items = $_order->getItemsCollection();
                        $values = array();
                        //select all the items
                        foreach ($_items as $_item):
                            if ($_helper->vendorIsLogged()) {
                                //we filter the output by vendor
                                if ($product = Mage::getModel('catalog/product')->load($_item->getProductId())) {
                                    $manufacturer = $product->getManufacturer();
                                    if ((int) $manufacturer > 0) {
                                        $values[$product->getId()]['qty'] = $_item->getQtyOrdered();
                                        $values[$product->getId()]['item'] = $_item->getId();

                                        $table1 = $_helper->getTableName("vendors_sales_items");
                                        $table2 = $_helper->getTableName("vendors_sales_history");
                                        $item_id = $_item->getId();
                                        $result = $write->query("select vsh.* from `$table1` vsi, `$table2` vsh where vsi.vendor_sales_item_id = vsh.vendor_sales_item_id and vsi.item_id = '$item_id' order by vsh.vendor_sales_history_id desc;");
                                        $line = $result->fetch();
                                        if ($line['status'] == $data['status']) {
                                            
                                        } else {
                                            $cancelled = false;
                                        }
                                    }
                                }
                            }
                        endforeach;
                        if ($cancelled) {
                            $_order->addStatusHistoryComment($comment, strtolower($data['status']))
                                    ->setIsVisibleOnFront($visible)
                                    ->setIsCustomerNotified($notify);

                            $comment = trim(strip_tags($comment));

                            $_order->save();
                            $_order->sendOrderUpdateEmail($notify, $comment);
                        }
                    }
            }
        }

        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('vendor')->__('Added Comments'));
        if ($this->getRequest()->getParam('back')) {
            $this->_redirect('*/*/edit', array('id' => $order_real_id));
            return;
        }
        $this->_redirect('*/*/');
    }

    public function deleteAction() {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $model = Mage::getModel('vendor/vendor_sales');

                $model->setId($this->getRequest()->getParam('id'))
                        ->delete();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    public function massDeleteAction() {
        $vendorsalesIds = $this->getRequest()->getParam('vendorsales');
        if (!is_array($vendorsalesIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($vendorsalesIds as $vendorsalesId) {
                    $vendorsales = Mage::getModel('vendor/vendor_sales')->load($vendorsalesId);
                    $vendorsales->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('adminhtml')->__(
                                'Total of %d record(s) were successfully deleted', count($vendorsalesIds)
                        )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function massStatusAction() {
        $vendorsalesIds = $this->getRequest()->getParam('vendorsales');
        if (!is_array($vendorsalesIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($vendorsalesIds as $vendorsalesId) {
                    $vendorsales = Mage::getSingleton('vendor/vendor_sales')
                            ->load($vendorsalesId)
                            ->setStatus($this->getRequest()->getParam('status'))
                            ->setIsMassupdate(true)
                            ->save();
                }
                $this->_getSession()->addSuccess(
                        $this->__('Total of %d record(s) were successfully updated', count($vendorsalesIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function exportCsvAction() {
        $fileName = 'vendor_sales.csv';
        $content = $this->getLayout()->createBlock('vendor/adminhtml_sales_grid')
                ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction() {
        $fileName = 'vendor_sales.xml';
        $content = $this->getLayout()->createBlock('vendor/adminhtml_sales_grid')
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
    
    public function updateVendorCheckItemAction(){

        //echo $str_item_status;
        //die;
        
        $str_item_status = $this->getRequest()->getParam("item_string");
        //remove ; at last
        //substr_replace($str_item_status, ";", strlen($str_item_status) - 1);
        $arrlast = str_split($str_item_status);
        
        if($arrlast[count($arrlast) - 1] == ";"){
            
            $str_item_status = substr_replace($str_item_status ,"",-1);
        }
        
        if(empty($str_item_status)){
            echo false;
        }
        else{
            
            $itemList = explode(";", $str_item_status);
            $flag = true;
            
            foreach ($itemList as $itemStr){
                
                $itemArr = explode("_",$itemStr);
                
                $_item = Mage::getModel("sales/order_item")->load($itemArr[0]);

                if(!empty($_item)){
                    if($itemArr[1]=="in"){
                        
                        $_item->setItemCheckedStatus(FavoredMinds_Vendor_Helper_Vendor::IS_IN_SALE);
                        $_item->save();
                    }
                    if($itemArr[1]=="out"){
                        
                        $_item->setItemCheckedStatus(FavoredMinds_Vendor_Helper_Vendor::IS_OUT_SALE);
                        $_item->save();
                        
                    }
                    
                    
                }
                else{
                    Mage::log($str_item_status." Invalid System",null,"vendor.log");
                    $flag = false;
                    
                }
                
            }
            if($flag){
                echo true;
            }
            else{
                echo false;
            }
            
        }
        
        exit();
    }

}