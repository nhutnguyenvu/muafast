<?php

/**
 * qikDropShipper Extension
 *
 * @category   FavoredMinds
 * @package    Vendor
 * @author     FavoredMinds Solution (geedubya15@gmail.com)
 * @copyright  Copyright (c) 2010 FavoredMinds.com (http://www.favoredminds.com)
 */
class FavoredMinds_Vendor_IndexController extends Mage_Core_Controller_Front_Action {

    public function _construct() {
        $helper = Mage::app()->getHelper('vendor');
        if (!$helper->check()) {
            die("<script type='text/javascript'>window.location='" . substr($_SERVER["REQUEST_URI"], 0, strlen($_SERVER["REQUEST_URI"]) - 6) . "';</script>");
        }
        parent::_construct();
    }

    protected function _initAction() {
        $this->loadLayout();
        return $this;
    }

    public function indexAction() {
        $this->loadLayout();

        $this->getLayout()->getBlock('head')->setTitle($this->__('Vendor'));
        $this->renderLayout();
    }

    public function voteAction() {

        $isAjax = Verz_Home_Helper_Data::isAjax();
        if ($isAjax) {
            $isLogin = Mage::helper('customer')->isLoggedIn();
            if ($isLogin) {

                $vendorId = $this->getRequest()->getParam("vendor_id");
                $rate = $this->getRequest()->getParam("rate");
                $customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
                //update to customer rating.
                $modelRating = Mage::getModel("vendor/vendor_customer_rating");
                //$modelRating->updateRating($vendorId,$rate)          
                $_rating_customer = $modelRating->getInfoByVC($vendorId, $customerId);

                if (empty($_rating_customer)) {

                    $data = array('customer_id' => $customerId,
                        'vendor_id' => $vendorId,
                        'created_at' => time(),
                        'updated_at' => time(),
                        'rate' => ($rate)
                    );
                    
                    $modelRating->addData($data);
                    
                    try {

                        $modelRating->save();
                        $_vendor = Mage::getModel("vendor/vendor")->load($vendorId);
                        $numRate = $_vendor->getNumRate();
                        $averageRate = $_vendor->getAverageRate();
                        $_vendor->setData("num_rate", $numRate + 1);
                        $_vendor->setData("average_rate", (round($averageRate * $numRate) + $rate) / ($numRate + 1));
                        $_vendor->save();
                        $result['error'] = 0;
                        $result['message'] = $this->__("Thanks for your voting");
                        echo json_encode($result);
                        
                    } catch (Exception $ex) {
                        Mage::log($ex->getMessage());
                        $result['error'] = 1;
                        $result['message'] = $this->__("Some error have happended");
                        echo json_encode($result);
                    }
                } else {
                    
                    //update rating of customer
                    $distance_time = time() - strtotime($update_at);
                    
                    $voteTime = Mage::getStoreConfig('vendor/vote/timevote', Mage::app()->getStore());
                    
                    if (empty($voteTime)) {
                        $voteTime = 0;
                    }
                    $update_at = $_rating_customer['updated_at'];
                    
                    $voteTime = 0;
                    
                    if ($distance_time > $voteTime * 3600) {
                        
                         
                        try {
                            $isSucess  = $modelRating->updateRating($vendorId, $customerId,$rate);
                            if(empty($isSucess)){
                                return false;
                            }

                            $modelRating->updateAverageVendorRating($vendorId);
                            $result['error'] = 0;
                            $result['message'] = $this->__("Thanks for your voting");
                            echo json_encode($result);
                            die;
                            
                        } catch (Exception $ex) {
                            Mage::log($ex->getMessage());
                            $result['error'] = 1;
                            $result['message'] = $this->__("Some error have happended");
                            echo json_encode($result);
                            die;
                        }
                    } else {
                        
                        $remainMinutes = round(($voteTime * 3600 - $distance_time) / 60);
                        $result['error'] = 1;
                        $result['message'] = $this->__("You voted for this boutique, please waiting $remainMinutes minute(s) to vote again");
                        echo json_encode($result);
                    }
                }
            } else {
                $result['error'] = 1;
                $result['message'] = $this->__("Please login your account before rating");
                echo json_encode($result);
                die;
            }
        }
        die;
    }
    /** 
     * @desc: List boutiques with pagination
     * @author : nhut.nguyen
     */
    public function showAction(){
        $this->loadLayout();
        $this->renderLayout();
    }
    
    public function slideAction()
    {
        $is_ajax = Mage::helper("home")->isAjax();
        
        if($is_ajax){

            $page = $this->getRequest()->getParam('page');
            $model = Mage::getModel("vendor/vendor");
            
            //$vendorList = $model->getVendorList($page);
            
            $page_arr  = $model->getPagination($page);
            
            $this->loadLayout ( 'content' );

            $this->getLayout ()->getBlock ( 'slide' )->setData ( 'page_arr', $page_arr );
            //$this->getLayout ()->getBlock ( 'slide' )->setData ( 'vendorList', $vendorList );

            $result ['result'] = $this->getLayout ()->getBlock ( 'slide' )->toHtml ();
            


            $result ['error'] = 0;
            //$result ['result'] = $this->__("This category doesn't contain data at a moment");

        }
        else{
            $this->loadLayout();
            $this->renderLayout();
        }
        echo $this->getResponse ()->setBody ( Mage::helper ( 'core' )->jsonEncode ( $result ) );
        exit();

    }
    public function productlistAction(){
		if($this->getRequest()->getParam('isAjax')==1){
		 $html['page'] = $this->getRequest()->getParam('p');
		 $html['product'] = $this->getLayout()->createBlock('vendor/vendor_renderer_vendor')->setTemplate('vendor/productlist_scoll.phtml')->toHtml();
		 $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($html));
		 return true;
		 }
        $vendor_id = $this->getRequest()->getParam("vendor_id");
		$vendor_data = Mage::getModel("vendor/vendor")->load($vendor_id); 
        $this->loadLayout();
		$this->getLayout()->getBlock('head')->setTitle(htmlspecialchars($vendor_data['company_name']));
        $this->getLayout()->getBlock('head')->setDescription(htmlspecialchars($vendor_data['aboutcompany']));
        $this->renderLayout();
       
    }
	/* @author : khanh.phan
		create page list vendor
	 */
	public function vendorlistAction(){
		if($this->getRequest()->getParam('isAjax')==1){
		 $html['page'] = $this->getRequest()->getParam('page');
		 $html['product'] = $this->getLayout()->createBlock('vendor/vendor_renderer_vendor')->setTemplate('vendor/vendorlist/list.phtml')->toHtml();
		 $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($html));
		 return true;
		 }
		$this->loadLayout();
	    $this->renderLayout();
	}
}