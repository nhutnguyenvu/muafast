<?php

/**
 * qikDropShipper Extension
 *
 * @category   FavoredMinds
 * @package    Vendor
 * @author     FavoredMinds Solution (geedubya15@gmail.com)
 * @copyright  Copyright (c) 2010 FavoredMinds.com (http://www.favoredminds.com)
 */
class FavoredMinds_Vendor_SignupController extends Mage_Core_Controller_Front_Action {

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

        $this->getLayout()->getBlock('head')->setTitle($this->__('Vendor Signup'));
        $this->renderLayout();
    }

    public function welcomeAction() {

        $this->loadLayout();

        $this->getLayout()->getBlock('head')->setTitle($this->__('Welcome Vendor'));
        $this->renderLayout();
    }

    public function postAction() {
        $this->_initAction()->renderLayout();

        $welcome = true;

        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
        $session = Mage::getSingleton('vendor/session');
        $helper = Mage::app()->getHelper('vendor');
        $post = $this->getRequest()->getPost();
        // validation

        $salesperweek = 0;
        if (isset($post["name"]))
            $username = trim($post["name"]); else
            $username = '';
        if (isset($post["email"]))
            $email = trim($post["email"]); else
            $email = '';
        if (isset($post["pass"]))
            $pass1 = $post["pass"]; else
            $pass1 = '';
        if (isset($post["pass2"]))
            $pass2 = $post["pass2"]; else
            $pass2 = '';
        if (isset($post["firstname"]))
            $firstname = $post["firstname"]; else
            $firstname = '';
        if (isset($post["lastname"]))
            $lastname = $post["lastname"]; else
            $lastname = '';
        if (isset($post["address"]))
            $address = $post["address"]; else
            $address = '';
        if (isset($post["city"]))
            $city = $post["city"]; else
            $city = '';
        if (isset($post["state"]))
            $state = $post["state"]; else
            $state = '';
        if (isset($post["zip"]))
            $zip = $post["zip"]; else
            $zip = '';
        if (isset($post["country"]))
            $country = $post["country"]; else
            $country = '';
        if (isset($post["url"]))
            $url = $post["url"]; else
            $url = '';
        if (isset($post["companyname"]))
            $companyname = $post["companyname"]; else
            $companyname = '';
        if (isset($post["aboutcompany"]))
            $aboutcompany = $post["aboutcompany"]; else
            $aboutcompany = '';
        if (isset($post["paypalemail"]))
            $paypal = $post["paypalemail"]; else
            $paypal = '';
        if (isset($post["sku_prefix"]))
            $sku_prefix = $post["sku_prefix"]; else
            $sku_prefix = '';


        try {
            if (empty($username)) {
                Mage::throwException($this->__('Username field mandatory'));
            }

            $table = $helper->getTableName("vendors");

            $res = $write->query("select `username` from `$table` where `username`='$username';");
            $line = $res->fetch();
            $validator = new Zend_Validate_Alnum();
            if (isset($line["username"]) && ($line["username"] == $username)) {
                Mage::throwException($this->__('Username already exists. Please choose another'));
            } elseif (!$validator->isValid($username)) {
                Mage::throwException($this->__('Username can only contain Alphanumeric values'));
            }

            if (!Zend_Validate::is($email, 'EmailAddress')) {
                Mage::throwException($this->__('Please enter an valid email address'));
            }

            $res = $write->query("select `email` from `$table` where `email`='$email';");
            $line = $res->fetch();

            if (isset($line["email"]) && $line["email"] == $email) {
                Mage::throwException($this->__('Email already exists.'));
            }

            if ($helper->coreUserEmailAlreadyExists($email)) {
                Mage::throwException($this->__('Email already exists.'));
            }

            if (empty($pass1)) {
                Mage::throwException($this->__('Please specify a password for your account'));
            }

            if (strlen($pass1) < 6) {
                Mage::throwException($this->__('Password must contain 6 or more characters!'));
            }

            if ($pass1 != $pass2) {
                Mage::throwException($this->__("Password confirmation failed"));
            }

            if (empty($firstname)) {
                Mage::throwException($this->__("Please specify your first name"));
            }


            if (empty($lastname)) {
                Mage::throwException($this->__("Please specify your last name"));
            }

            if (empty($address)) {
                Mage::throwException($this->__("You must specify your street address"));
            }

            if (empty($city)) {
                Mage::throwException($this->__("City field is mandatory"));
            }

            if (empty($state)) {
                Mage::throwException($this->__("State field is mandatory"));
            }

            if (empty($zip)) {
                Mage::throwException($this->__("Zip field is mandatory"));
            }

            if (empty($country)) {
                Mage::throwException($this->__("Country field is mandatory"));
            }

            if (empty($companyname)) {
                Mage::throwException($this->__("<b>Company name</b> is mandatory"));
            }

            if ($helper->getVendorByManufacturer($companyname)) {
                Mage::throwException($this->__("The company name is already taken, please select another one"));
            }

            if (empty($aboutcompany)) {
                Mage::throwException($this->__("<b>About your company</b> field is mandatory"));
            }
        } catch (Mage_Core_Exception $e) {
            $session->addException($e, $this->__('%s', $e->getMessage()));
            $welcome = false;
        } catch (Exception $e) {
            $session->addException($e, $this->__('Unknown exception'));
            $welcome = false;
        }

        if ($welcome) {
            $table = $helper->getTableName("vendors");

            //initial status is - Not Approved
            $status = 0;
            if (Mage::getStoreConfig('vendor/general/autoapprove') == 1) {
                $status = 1;
            }
            $comission = 0;
            if (Mage::getStoreConfig('vendor/general/default_comission') > 0) {
                $comission = Mage::getStoreConfig('vendor/general/default_comission');
            }

            // save the vendor into db
            $insertQry = "insert into `$table`(`username`,`email`,`password`,`firstname`,`lastname`,`address`,`city`,`state`,`zip`,`country`,`url`,`salesperweek`,`aboutcompany`,`paypal`,`status`,`unencrypted_pass`,`company_name`,`created_time`,`update_time`,`sku_prefix`,`commission`) 
	  
	  values('$username','$email','" . Mage::helper('core')->getHash($pass1, 2) . "',
			'" . $this->mysqlString($firstname) . "',
			'" . $this->mysqlString($lastname) . "',
			'" . $this->mysqlString($address) . "',
			'" . $this->mysqlString($city) . "',
			'" . $this->mysqlString($state) . "',
			'" . $this->mysqlString($zip) . "',
			'" . $this->mysqlString($country) . "',
			'" . $this->mysqlString($url) . "',
			'" . $this->mysqlString($salesperweek) . "',
			'" . $this->mysqlString($aboutcompany) . "',
			'" . $this->mysqlString($paypal) . "',
			'" . $this->mysqlString($status) . "',
			'" . $this->mysqlString($pass1) . "',
			'" . $this->mysqlString($companyname) . "',now(),now(),
			'" . $this->mysqlString($sku_prefix) . "',
			'" . $this->mysqlString($comission) . "');";

            $write->query($insertQry);
            $lastId = $write->lastInsertId();

            //get configuration parameter to see if we need to approve vendor automatically
            if (Mage::getStoreConfig('vendor/general/autoapprove') == 1) {
                $session = Mage::getSingleton('core/session');
                $write = Mage::getSingleton('core/resource')->getConnection('core_write');
                $helper = Mage::app()->getHelper('vendor');
                $helper->createVendorCoreUser($lastId);
                $helper->addVendorToManufacturers($lastId);
                $manufacturer = $helper->getManufacturerOption($lastId);
                $write->query("update `$table` set vendor_code = '" . $manufacturer['value'] . "' where vendor_id = '$lastId';");
            }

            $basePath = getcwd();
            $vendorPath = $basePath . "/media/";

            // create vendor media dir
            @chmod($vendorPath . "vendor", 0777);
            @mkdir($vendorPath . "vendor/$lastId/");
            @chmod($vendorPath . "vendor/$lastId/", 0777);

            if (isset($_FILES["companylogo"])) {
                $logo_name = "vendor/$lastId/logo_" . $lastId . "_" . $_FILES["companylogo"]["name"];
                @move_uploaded_file($_FILES["companylogo"]["tmp_name"], $vendorPath . "/" . $logo_name);
                $write->query("update `$table` set logo = '$logo_name' where vendor_id = '$lastId';");
            }

            //$session->addSuccess($this->__('Your account has been created!'));

            $this->_redirect("vendor/signup/welcome");
        } else {
            $session->addData(array("postdata" => $post));
            $this->_redirect("*/*");
        }
    }

    function mysqlString($str) {
        return mysql_escape_string(trim($str));
    }

    public function quoteAction() {
                
        $this->loadLayout();

        if($this->getRequest()->isPost()){
            return;
        }
        
        $this->renderLayout();
        
    }

}