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
    /** 
     * @desc: List boutiques with pagination
     * @author : nhut.nguyen
     */
    public function showAction(){
        $this->loadLayout();
        $this->renderLayout();
    }
    public function voteAction(){
        
        $isAjax = Verz_Home_Helper_Data::isAjax();
        if($isAjax){
            $isLogin = Mage::helper('customer')->isLoggedIn();
            if($isLogin){ 
                
                $vendorId = $this->getRequest()->getParam("vendor_id");
                $rate = $this->getRequest()->getParam("rate");
                $customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
                
                $modelRating = Mage::getModel("vendor/vendor_customer_rating");
                $_rating_customer = $modelRating->getInfoByVC($vendorId,$customerId);   
                
                if(empty($_rating_customer)){
                    
                    $data = array('customer_id'=>$customerId,
                                'vendor_id'=>$vendorId,
                                'created_at'=>time(),
                                'updated_at'=>time(),
                                'rate'=>($rate)
                                );
                    $modelRating->addData($data);

                    try{
                        
                        $modelRating->save();
                        $_vendor = Mage::getModel("vendor/vendor")->load($vendorId);
                        $numRate = $_vendor->getNumRate();
                        $averageRate = $_vendor->getAverageRate();
                        $_vendor->setData("num_rate",$numRate + 1);
                        $_vendor->setData("average_rate",(round($averageRate * $numRate) + $rate) / ($numRate+1));
                        $_vendor->save();
                        $result['error'] = 0; 
                        $result['message'] = $this->__("Thanks for your voting");
                        echo json_encode($result);
                        
                    }   
                    catch(Exception $ex){
                        Mage::log($ex->getMessage());
                        $result['error'] = 1; 
                        $result['message'] = $this->__("Some error have happended");
                        echo json_encode($result);
                    }
                    
                }
                else{
                    
                    $voteTime = Mage::getStoreConfig('vendor/vote/timevote',Mage::app()->getStore());
                    
                    if(empty($voteTime)){
                        $voteTime = 0; 
                    }          
                    $update_at = $_rating_customer['updated_at'];
                    
                    $distance_time = time() - strtotime($update_at);
                    
                    if($distance_time >  $voteTime * 3600){
                        try{

                            $modelRating->addData(array("id"=>$_rating_customer['id'],"rate"=>$rate,"updated_at"=>time()));
                            $modelRating->save();
                            $_vendor = Mage::getModel("vendor/vendor")->load($vendorId);
                            
                            $numRate = $_vendor->getNumRate();
                            
                            $averageRate = $_vendor->getAverageRate();
                            
                            $_vendor->setData("average_rate",round(($averageRate * ($numRate)   + $rate) / ($numRate+1)));
                            
                            $_vendor->save();
                            
                            $result['error'] = 0; 
                            $result['message'] = $this->__("Thanks for your voting");
                            echo json_encode($result);
                            die;

                        }
                        catch(Exception $ex){
                            Mage::log($ex->getMessage());
                            $result['error'] = 1; 
                            $result['message'] = $this->__("Some error have happended");
                            echo json_encode($result);
                            die;
                        }
                    }    
                    else{
                        $remainMinutes = round(($voteTime * 3600 - $distance_time) / 60);
                        $result['error'] = 1; 
                        $result['message'] = $this->__("You voted for this boutique, please waiting $remainMinutes minute(s) to vote again");
                        echo json_encode($result);
                    }                
                }              
            }
            else{
                $result['error'] = 1; 
                $result['message'] = $this->__("Please login your account before rating");
                echo json_encode($result);
                die;    
            }
            
        }
        die;
    }

}