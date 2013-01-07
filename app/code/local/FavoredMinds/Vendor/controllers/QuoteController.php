<?php

/**
 * qikDropShipper Extension
 *
 * @category   FavoredMinds
 * @package    Vendor
 * @author     FavoredMinds Solution (geedubya15@gmail.com)
 * @copyright  Copyright (c) 2010 FavoredMinds.com (http://www.favoredminds.com)
 */
class FavoredMinds_Vendor_QuoteController extends Mage_Core_Controller_Front_Action {

    const TEMPLATE_ID = 31;
    
    public function indexAction() {
        
        
        //$templateId = Mage::getStoreConfig("vendor/apply/email_template");
        
        $this->loadLayout();
        $this->_initLayoutMessages('vendor/session');
        
        $session = Mage::getSingleton('core/session');
        if($this->getRequest()->isPost()){
            


            $post = $this->getRequest()->getPost();         
            $info = array();
            if (isset($post["bussinessname"]))
                $info['bussinessname'] = trim($post["bussinessname"]);
            else
                $info['bussinessname'] = '';
            
            if (isset($post["fullname"]))
                $info['fullname'] = trim($post["fullname"]); 
            else
                $info['fullname'] = '';
            
            if (isset($post["vendor_email"]))
                $info['vendor_email'] = $post["vendor_email"];
            else
                $info['vendor_email'] = '';
            
            if (isset($post["website"]) && !empty($post["website"]))
                $info['website'] = $post["website"]; 
            else
                $info['website'] = 'n/a';
            
            if (isset($post["boutiquename"]))
                $info['boutiquename'] = $post["boutiquename"]; 
            else
                $info['boutiquename'] = '';

            if (isset($post["address"]))
                $info['address'] = $post["address"];
            else
                $info['address'] = '';
            
            if(isset($post["telephone"]))
                $info['telephone'] = $post["telephone"];
            else
                $info['telephone'] = '';
           
            if (isset($post["smalldescription"]))
                $info['smalldescription'] = $post["smalldescription"]; 
            else
                $info['smalldescription'] = '';

            if (isset($post["facebook"]) && !empty($post["facebook"]))
                $info['facebook'] = $post["facebook"];
            else
                $info['facebook'] = 'n/a';
            
            foreach($info as $item){
                if(empty($item)){
                    
                    $session->addError($this->__('Some information is empty'));
                    $session->setData("postbtdata", $info);
                    $this->_redirect("dang_ki_cua_hang.html");
                    return;
                }
            }
            
            //$basePath = getcwd();
            //$vendorPath = $basePath . "/media/";
            //$baseUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
            //var_dump($_FILES["logo1"]);
            
            try{
                /*
                if (isset($_FILES["logo1"])) {
                
                    $logoName = $_FILES["logo1"]['name'];
                    $path_parts = pathinfo($logoName);
                    $logoName = (time().md5(rand(1,2))).".".$path_parts['extension'];
                    $logoPath = $vendorPath."vendor/btal/". $logoName;
                    $logoUrl = $baseUrl."media/vendor/btal/". $logoName;  
                         
                    @move_uploaded_file($_FILES["logo1"]["tmp_name"], $logoPath);
                    
                    $info["logo1"]  = $logoUrl;
                    
                    
                    
                //$write->query("update `$table` set logo = '$logo_name' where vendor_id = '$lastId';");
                }
                else{
                    $session->setData("postbtdata", $info);
                    $session->addError($this->__('File 1 is empty'));
                    $this->_redirect("vendor/quote/index");
                    return;
                }
                if (isset($_FILES["logo2"])) {
                
                    $logoName = $_FILES["logo2"]['name'];
                    $path_parts = pathinfo($logoName);
                    $logoName = (time().md5(rand(3,4))).".".$path_parts['extension'];
                    $logoPath = $vendorPath."vendor/btal/". $logoName;
                    $logoUrl = $baseUrl."media/vendor/btal/". $logoName;  
                    @move_uploaded_file($_FILES["logo2"]["tmp_name"], $logoPath);
                    $info["logo2"]  = $logoUrl;           
                    
                //$write->query("update `$table` set logo = '$logo_name' where vendor_id = '$lastId';");
                }
                else{
                    $session->setData("postbtdata", $info);
                    $session->addError($this->__('File 2 is empty'));
                    $this->_redirect("vendor/quote/index");
                    return;
                    
                }
                if (isset($_FILES["logo3"])) {
                
                    $logoName = $_FILES["logo3"]['name'];
                    $path_parts = pathinfo($logoName);
                    $logoName = (time().md5(rand(4,5))).".".$path_parts['extension'];
                    $logoPath = $vendorPath."vendor/btal/". $logoName;
                    $logoUrl     = $baseUrl."media/vendor/btal/". $logoName;  
                    
                    @move_uploaded_file($_FILES["logo3"]["tmp_name"], $logoPath);
                    $info["logo3"]  = $logoUrl;
                    
                }
                else{
                    $session->setData("postbtdata", $info);
                    $session->addError($this->__('File 3 is empty'));
                    $this->_redirect("vendor/quote/index");
                    return;
                }
                */
                $session->setData("postbtdata", $info);
                
                $result = $this->sendEmail($info);
                
                if($result==false){
                    $session->addError($this->__('Your email can not sent, we are sorry because of this convennience, please return another time'));
                }
                else{
                    $session->addSuccess($this->__("Thanks for your message, we will feedback soon"));
                }
                $this->_redirect("dang_ki_cua_hang.html");
                return;
            }
            catch(Exception $ex){
                Mage::log($ex->getMessage());
                $session->addError($this->__('Your email can not sent, we are sorry because of this convennience, please return another time'));
                $this->_redirect("dang_ki_cua_hang.html");
            }
        }
        else{
            
        }
        
        $this->renderLayout();
        
    }
    public function sendEmail($info){
        /**
        * $templateId can be set to numeric or string type value.
        * You can use Id of transactional emails (found in
        * "System->Trasactional Emails"). But better practice is
        * to create a config for this and use xml path to fetch
        * email template info (whatever from file/db).
        */
        //const EMAIL_TEMPLATE_XML_PATH = 'vendor/email_template';
        try{

            //$templateId = Mage::getStoreConfig("vendor/apply/email_template");
            $templateId = self::TEMPLATE_ID;
            
            $mailSubject = 'Đăng kí giang hàng';

            /**
            * $sender can be of type string or array. You can set identity of
            * diffrent Store emails (like 'support', 'sales', etc.) found
            * in "System->Configuration->General->Store Email Addresses"
            */
            $sender = Array('name'  => $info['fullname'],
                            'email' => $info['vendor_email']);

            /**
            * In case of multiple recipient use array here.
            */
            $emailTo = Mage::getStoreConfig("contacts/email/recipient_email");   
            $emailTo = trim($emailTo);
            
            /**
            * If $name = null, then magento will parse the email id
            * and use the base part as name.
            */
            
            
            
            $name = "Muafast";
            $vars = Array();
            $vars = $info;
            
            /* An example how you can pass magento objects and normal variables*/
            /*
            $vars = Array('customer'=>$customer,
                            'address' =>$address,
                            'varification_data'=>'fake data for example');*/

            /*This is optional*/
            $storeId = Mage::app()->getStore()->getId(); 

            $translate  = Mage::getSingleton('core/translate');

            Mage::getModel('core/email_template')
                ->setTemplateSubject($mailSubject)
                ->sendTransactional($templateId, $sender, $emailTo, $name, $vars, $storeId);

            $translate->setTranslateInline(true);
        }
        catch(Exception $ex){
            Mage::log($ex->getMessage());
            return false;
        }
        return true;
    }

}