<?php

/**
 * @name         :  Apptha One Step Checkout
 * @version      :  1.0
 * @since        :  Magento 1.5
 * @author       :  Prabhu Mano
 * @copyright    :  Copyright (C) 2011 Powered by Apptha
 * @license      :  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @Creation Date:  July 26 2012
 *
 * */
?>
<?php

require_once 'sociallogin/openid.php';

class Apptha_Sociallogin_IndexController extends Mage_Core_Controller_Front_Action {

    public function indexAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    /* Customer save action */

    public function customerAction($firstname, $lastname, $email, $provider, $provider_user_id) {
        $customer = Mage::getModel('customer/customer');
		
        /* getting customer collection */
        $collection = $customer->getCollection();
        if ($customer->getSharingConfig()->isWebsiteScope()) {
            $collection->addAttributeToFilter('website_id', Mage::app()->getWebsite()->getId());
        }
        if ($this->_getCustomerSession()->isLoggedIn()) {
            $collection->addFieldToFilter('entity_id', array('neq' => $this->_getCustomerSession()->getCustomerId()));
        }
        if ($provider == 'Facebook') {
            $provider_db = "facebook_uid";
        } else if ($provider == 'Google') {
            $provider_db = "google_uid";
        } else if ($provider == 'Yahoo') {
            $provider_db = "yahoo_uid";
        } else if ($provider == 'Linkedin') {
            $provider_db = "linkedin_uid";
        } else if ($provider == 'Twitter') {
            $provider_db = "twitter_uid";
        }
        $customers = Mage::getModel('customer/customer')->getCollection()->addFieldToFilter("$provider_db", "$provider_user_id")->load();
        /* If user not registered */
		
        foreach ($customers as $customerUid) {
            $customer_id_by_provider = $customerUid->getId();
            $customer_email_by_provider = $customerUid->getEmail();
        }
        $customer->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                ->loadByEmail($email);

        $customer_id_by_email = $customer->getId();
       
        if(!isset($customer_id_by_provider))
			$customer_id_by_provider = "";
			
            if ($customer_id_by_email == '' && $customer_id_by_provider == '') {
                $standardInfo['email'] = $email;
            } else if ($customer_id_by_provider != '') {
                $standardInfo['email'] = $customer_email_by_provider;
            } else if ($customer_id_by_email != '' && $customer_id_by_provider == '') {
                $standardInfo['email'] = $email;
            }
       
        
        /* getting customer params */

        $standardInfo['first_name'] = $firstname;
        $standardInfo['last_name'] = $lastname;
        $standardInfo['provider_id'] = $provider_user_id;
        $customer->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                ->loadByEmail($standardInfo['email']);

        if ($customer->getId()) {

            /* Save the provider user id */
            if ($provider_db == 'google_uid') {
                $customer->setGoogleUid($provider_user_id);
                $customer->save();
            } else if ($provider_db == 'yahoo_uid') {
                $customer->setYahooUid($provider_user_id);
                $customer->save();
            } else if ($provider_db == 'facebook_uid') {
                $customer->setFacebookUid($provider_user_id);
                $customer->save();
            } else if ($provider_db == 'twitter_uid') {
                $customer->setTwitterUid($provider_user_id);
                $customer->save();
            } else if ($provider_db == 'linkedin_uid') {
                $customer->setLinkedinUid($provider_user_id);
                $customer->save();
            }
            $this->_getCustomerSession()->setCustomerAsLoggedIn($customer);
            $this->_getCustomerSession()->addSuccess(
                    $this->__('Your account has been successfully connected through' . ' ' . $provider)
            );
            $link = Mage::getSingleton('customer/session')->getLink();
            if (!empty($link)) {

                $requestPath = trim($link, '/');
            }
            if ($requestPath == 'checkout/onestep') {
                $this->_redirect($requestPath);
                return;
            } else {
                // get referer url
                $redirect = Mage::getSingleton('customer/session')->getLink();
                //$redirect = $this->_loginPostRedirect();
				if($redirect[0] != "/")
					$redirect = "/".$redirect;
                $this->_redirectUrl($redirect);
                return;
            }
        }
        /* Generate Random Password */
        $randomPassword = $customer->generatePassword(8);
        $customer->setId(null)
                ->setSkipConfirmationIfEmail($standardInfo['email'])
                ->setFirstname($standardInfo['first_name'])
                ->setLastname($standardInfo['last_name'])
                ->setEmail($standardInfo['email'])
                ->setPassword($randomPassword)
                ->setConfirmation($randomPassword);
        if ($provider_db == 'google_uid') {
            $customer->setGoogleUid($provider_user_id);
            $customer->setYahooUid('');
            $customer->setFacebookUid('');
            $customer->setTwitterUid('');
            $customer->setLinkedinUid('');
        } else if ($provider_db == 'yahoo_uid') {
            $customer->setGoogleUid('');
            $customer->setYahooUid($provider_user_id);
            $customer->setFacebookUid('');
            $customer->setTwitterUid('');
            $customer->setLinkedinUid('');
        } else if ($provider_db == 'facebook_uid') {
            $customer->setFacebookUid($provider_user_id);
        } else if ($provider_db == 'twitter_uid') {
            $customer->setTwitterUid($provider_user_id);
        } else if ($provider_db == 'linkedin_uid') {
            $customer->setLinkedinUid($provider_user_id);
            $customer->setTwitterUid('');
            $customer->setFacebookUid('');
            $customer->setGoogleUid('');
            $customer->setYahooUid('');
        }


        if ($this->getRequest()->getParam('is_subscribed', false)) {
            $customer->setIsSubscribed(1);
        }
        /* registration will fail if tax required, also if dob, gender aren't allowed in profile */
        $errors = array();
        $validationCustomer = $customer->validate();
        if (is_array($validationCustomer)) {
            $errors = array_merge($validationCustomer, $errors);
        }
        $validationResult = true;

        if (true === $validationResult) {
            $customer->save();

            $this->_getCustomerSession()->addSuccess(
                    $this->__('Thank you for registering with %s', Mage::app()->getStore()->getFrontendName()) .
                    '. ' .
                    $this->__('You will receive welcome email with registration info in a moment.')
            );
            //if not change password or click here forget password

            $customer->sendNewAccountEmail();

            $this->_getCustomerSession()->setCustomerAsLoggedIn($customer);
            $link = Mage::getSingleton('customer/session')->getLink();
            if (!empty($link)) {

                $requestPath = trim($link, '/');
            }
            if ($requestPath == 'checkout/onestep') {
                $this->_redirect($requestPath);
                return;
            } else {
                //$redirect = $this->_loginPostRedirect();
                $redirect = $link;
				if($redirect[0] != "/")
					$redirect = "/".$redirect;
                $this->_redirectUrl($redirect);
                return;
            }
            //else set form data and redirect to registration
        } else {
            $this->_getCustomerSession()->setCustomerFormData($customer->getData());
            $this->_getCustomerSession()->addError($this->__('User profile can\'t provide all required info, please register and then connect with Apptha Social login.'));
            if (is_array($errors)) {
                foreach ($errors as $errorMessage) {
                    $this->_getCustomerSession()->addError($errorMessage);
                }
            }
            $this->_redirect('customer/account/create');
        }
    }

    /* function to get customer session */

    private function _getCustomerSession() {
        return Mage::getSingleton('customer/session');
    }

    /* function to redirect my account dashboard page */

    protected function _loginPostRedirect() {
        $session = $this->_getCustomerSession();

        if (!$session->getBeforeAuthUrl() || $session->getBeforeAuthUrl() == Mage::getBaseUrl()) {

            // Set default URL to redirect customer to
            $session->setBeforeAuthUrl(Mage::helper('customer')->getAccountUrl());

            // Redirect customer to the last page visited after logging in
            if ($session->isLoggedIn()) {
                if (!Mage::getStoreConfigFlag('customer/startup/redirect_dashboard')) {
                    if ($referer = $this->getRequest()->getParam(Mage_Customer_Helper_Data::REFERER_QUERY_PARAM_NAME)) {
                        $referer = Mage::helper('core')->urlDecode($referer);
                        if ($this->_isUrlInternal($referer)) {
                            $session->setBeforeAuthUrl($referer);
                        }
                    }
                } else if ($session->getAfterAuthUrl()) {
                    $session->setBeforeAuthUrl($session->getAfterAuthUrl(true));
                }
            } else {
                $session->setBeforeAuthUrl(Mage::helper('customer')->getLoginUrl());
            }
        } else if ($session->getBeforeAuthUrl() == Mage::helper('customer')->getLogoutUrl()) {
            $session->setBeforeAuthUrl(Mage::helper('customer')->getDashboardUrl());
        } else {
            if (!$session->getAfterAuthUrl()) {
                $session->setAfterAuthUrl($session->getBeforeAuthUrl());
            }
            if ($session->isLoggedIn()) {
                $session->setBeforeAuthUrl($session->getAfterAuthUrl(true));
            }
        }

        return $session->getBeforeAuthUrl(true);
    }

    /* function for twitter login */

    public function twitterloginAction() {
        if (!class_exists('TwitterOAuth')) {
            require'sociallogin/twitter/twitteroauth.php';
            require 'sociallogin/config/twconfig.php';
        }
        //get twitter consumer key and secret
        $tw_oauth_token = Mage::getSingleton('customer/session')->getTwToken();
        $tw_oauth_token_secret = Mage::getSingleton('customer/session')->getTwSecret();
        $twitteroauth = new TwitterOAuth(YOUR_CONSUMER_KEY, YOUR_CONSUMER_SECRET, $tw_oauth_token, $tw_oauth_token_secret);
        //request the access token
        $access_token = $twitteroauth->getAccessToken($_GET['oauth_verifier']);
        //getting twitter user details
        $user_info = $twitteroauth->get('account/verify_credentials');
        if (isset($user_info->error)) {
            Mage::getSingleton('customer/session')->addError($this->__('Twitter Login connection failed'));
            $url = Mage::helper('customer')->getAccountUrl();
            return $this->_redirectUrl($url);
        } else {

            $firstname = $user_info->name;
            $twitter_id = $user_info->id;
            $email = Mage::getSingleton('customer/session')->getTwemail();
            $lastname = $user_info->name;

            if ($email == '' || $firstname == '') {
                //error message
                Mage::getSingleton('customer/session')->addError($this->__('Twitter Login connection failed'));
                $url = Mage::helper('customer')->getAccountUrl();
                return $this->_redirectUrl($url);
            } else {
                $this->customerAction($firstname, $lastname, $email, 'Twitter', $twitter_id);
            }
        }
    }

    /* function to save twitter email */

    public function twitterpostAction() {
        $provider = '';
        $twitter_email = (string) $this->getRequest()->getPost('email_value');
        Mage::getSingleton('customer/session')->setTwemail($twitter_email);
        $customer = Mage::getModel('customer/customer');
        $customer->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                ->loadByEmail($twitter_email);
        $customer_id_by_email = $customer->getId();
        $customer = Mage::getModel('customer/customer')->load($customer_id_by_email);
        $google_uid = $customer->getGoogleUid();
        if ($google_uid != '') {
            $provider.=' Google';
        }

        $facebook_uid = $customer->getFacebookUid();
        if ($facebook_uid != '') {
            $provider.=', Facebook';
        }
        $linkedin_uid = $customer->getLinkedinUid();
        if ($linkedin_uid != '') {
            $provider.=', Linkedin';
        }
        $yahoo_uid = $customer->getYahooUid();
        if ($yahoo_uid != '') {
            $provider.=', Yahoo';
        }
        $twitter_uid = $customer->getTwitterUid();
        $provider = ltrim($provider, ',');
       if($customer_id_by_email == '')
        {
            echo $url = Mage::helper('sociallogin')->getTwitterUrl();
        }
        else if($provider!='')
        {
            echo $this->__('This email is already associated with') . $provider;
        }
        else if(($provider=='' )&& ( $twitter_uid!=''))
        {
              echo $url = Mage::helper('sociallogin')->getTwitterUrl();
        }
        else 
        {
             echo $this->__('This email is already is registered!');
        }exit;
    }

    /* function for facebook login */

    public function fbloginAction() {
        // ob_start("ob_gzhandler");
        /* require_once(Mage::getBaseDir("lib").'/sociallogin/facebook/facebook.php');
        require 'sociallogin/config/fbconfig.php';
        //create facebook object
        $facebook = new FacebookApptha(array(
                    'appId' => APP_ID,
                    'secret' => APP_SECRET,
                    'cookie' => false,
                ));
        //getting facebook user details;
        $user = $facebook->getUser(); */
        $facebook_user_id = $this->getRequest()->getParam('face_id');
		$firstname = $this->getRequest()->getParam('firstname');
	    $lastname = $this->getRequest()->getParam('lastname');
	    $email = $this->getRequest()->getParam('email');
        if ($facebook_user_id && $email!="undefined") {
            try {
                //Create session
                Mage::getSingleton('core/session')->setFacebookID($facebook_user_id);
                // Proceed knowing you have a logged in user who's authenticated.
                //$user_profile = $facebook->api('/me');
                /* $firstname = $user_profile['first_name'];
                $email = $user_profile['email'];
                $lastname = $user_profile['last_name'];
                $facebook_user_id = $user_profile['id']; */

               //$facebook_user_id = $this->getRequest()->getParam('face_id');
                
                if ($email == '') {
                    //error message
                    Mage::getSingleton('customer/session')->addError($this->__('Facebook Login connection failed'));
                    $url = Mage::helper('customer')->getAccountUrl();
                    //$url = Mage::helper("core/url")->getCurrentUrl();
                    return $this->_redirectUrl($url);
                } else {
                    $this->customerAction($firstname, $lastname, $email, 'Facebook', $facebook_user_id);
                }
            } catch (FacebookApiException $e) {
                error_log($e);
                $user = null;
            }
        }
    }
	
//    /* function save marketing */
//    public function marketingAction($customerID, $facebook_user_id, $createTime){
//        $marketing = Mage::getModel('marketing/marketing');
//        try{
//            $marketing->setCustomer_id($customerID);
//            $marketing->setFace_id($facebook_user_id);
//            $marketing->setCreated_time($createTime);
//            $marketing->save();
//        }catch(Mage_Core_Exception $e){
//
//        }
//    }

    /* function for Google login */

    public function googlepostAction() {
        //create open id object for google
        $google_id = new LightOpenID(Mage::getBaseUrl());
        if ($google_id->mode == 'cancel') {
            Mage::getSingleton('customer/session')->addError($this->__('Google Login connection failed'));
            $url = Mage::helper('customer')->getAccountUrl();
            return $this->_redirectUrl($url);
        } elseif ($google_id->validate()) {
            //getting google user details
            $google_user_id = $google_id->identity;
            $google_user_id = str_replace('https://www.google.com/accounts/o8/id?id=', '', $google_user_id);
            $data = $google_id->getAttributes();
            $email = $data['contact/email'];
            $firstname = $data['namePerson/first'];
            $lastname = $data['namePerson/last'];

            if ($email == '') {
                //error message
                Mage::getSingleton('customer/session')->addError($this->__('Google Login connection failed'));
                $url = Mage::helper('customer')->getAccountUrl();
                return $this->_redirectUrl($url);
            } else {
                $this->customerAction($firstname, $lastname, $email, 'Google', $google_user_id);
            }
        }
    }

    /* function for Yahoo login */

    public function yahoopostAction() {
        //create open id object for yahoo
        $yahoo_id = new LightOpenID(Mage::getBaseUrl());

        if ($yahoo_id->mode == 'cancel') {
            Mage::getSingleton('customer/session')->addError($this->__('Yahoo Login connection failed'));
            $url = Mage::helper('customer')->getAccountUrl();
            return $this->_redirectUrl($url);
        } elseif ($yahoo_id->validate()) {
            //getting yahoo user details
            $yahoo_user_id = $yahoo_id->identity;
            $yahoo_user_id = str_replace('https://me.yahoo.com/a/', '', $yahoo_user_id);
            $data = $yahoo_id->getAttributes();

            $name = explode(' ', $data['namePerson']);
            $email = $data['contact/email'];
            $firstname = $name[0];
            $lastname = $name[1];

            if ($email == '') {
                //error message
                Mage::getSingleton('customer/session')->addError($this->__('Yahoo Login connection failed'));
                $url = Mage::helper('customer')->getAccountUrl();
                return $this->_redirectUrl($url);
            } else {
                $this->customerAction($firstname, $lastname, $email, 'Yahoo', $yahoo_user_id);
            }
        }
    }

  
    /* Redirect to index page if login page */

    public function loginAction() {

        if ($this->_getCustomerSession()->isLoggedIn()) {
            $this->_redirect('*/*/');
            return;
        } else if (Mage::getStoreConfig('sociallogin/general/enable_sociallogin') == 1) {
            $this->_redirect('');
            return;
        }
        $this->getResponse()->setHeader('Login-Required', 'true');
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
        $this->renderLayout();
    }

    /* Redirect to index page if register page */

    public function createAction() {

        if ($this->_getCustomerSession()->isLoggedIn()) {
            $this->_redirect('*/*/');
            return;
        } else {
            $this->_redirect();
            return;
        }

        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->renderLayout();
    }

    public function _isVatValidationEnabled($store = null) {
        return Mage::helper('customer/address')->isVatValidationEnabled($store);
    }

    /* Welcome messsage for the registered Customers */

    public function _welcomeCustomer(Mage_Customer_Model_Customer $customer, $isJustConfirmed = false) {
        $this->_getCustomerSession()->addSuccess(
                $this->__('Thank you for registering with %s.', Mage::app()->getStore()->getFrontendName())
        );


        if ($this->_isVatValidationEnabled()) {
            // Show corresponding VAT message to customer
            $configAddressType = Mage::helper('customer/address')->getTaxCalculationAddressType();
            $userPrompt = '';
            switch ($configAddressType) {
                case Mage_Customer_Model_Address_Abstract::TYPE_SHIPPING:
                    $userPrompt = $this->__('If you are a registered VAT customer, please click <a href="%s">here</a> to enter you shipping address for proper VAT calculation', Mage::getUrl('customer/address/edit'));
                    break;
                default:
                    $userPrompt = $this->__('If you are a registered VAT customer, please click <a href="%s">here</a> to enter you billing address for proper VAT calculation', Mage::getUrl('customer/address/edit'));
            }
            $this->_getCustomerSession()->addSuccess($userPrompt);
        }

        $customer->sendNewAccountEmail(
                $isJustConfirmed ? 'confirmed' : 'registered', '', Mage::app()->getStore()->getId()
        );

        $successUrl = Mage::getUrl('customer/account', array('_secure' => true));

        if ($this->_getCustomerSession()->getBeforeAuthUrl()) {
            $successUrl = $this->_getCustomerSession()->getBeforeAuthUrl(true);
        }
        return $successUrl;
    }

    /* customer login action if entered using default login form */

    public function customerloginpostAction() {
        $session = $this->_getCustomerSession();
        //get customer credentials 
        $login['username'] = $this->getRequest()->getPost('email_value');
        $login['password'] = $this->getRequest()->getPost('password_value');		
        //customer login
        if ($session->isLoggedIn()) {
            echo 'Already loggedin';
            return;
        }
        if ($this->getRequest()->isPost()) {
            if (!empty($login['username']) && !empty($login['password'])) {
                try {
                    $session->login($login['username'], $login['password']);
                    if ($session->getCustomer()->getIsJustConfirmed()) {
                        echo $this->_welcomeCustomer($session->getCustomer(), true);
                    }
                } catch (Mage_Core_Exception $e) {
                    switch ($e->getCode()) {
                        case Mage_Customer_Model_Customer::EXCEPTION_EMAIL_NOT_CONFIRMED:
                            $value = Mage::helper('customer')->getEmailConfirmationUrl($login['username']);
                            echo $message = Mage::helper('customer')->__('Account Not Confirmed', $value);
                            break;
                        case Mage_Customer_Model_Customer::EXCEPTION_INVALID_EMAIL_OR_PASSWORD:
                            echo $message = $this->__('Invalid Email Address or Password');
                            break;
                        default:
                            echo $message = $e->getMessage();
                    }
                    $session->setUsername($login['username']);
                } catch (Exception $e) {
                    //this may sometimes disclose the password
                }
                //after logging in redirect to the respective page    
                if ($session->getCustomer()->getId()) {
                    $link = Mage::getSingleton('customer/session')->getLink();

                    if (!empty($link)) {

                        $requestPath = trim($link, '/');
                    }
                    if ($requestPath == 'checkout/onestep') {
                        echo $requestPath;
                    } else {
                        echo $this->_loginPostRedirect();
                    }
                }
            }
        }
    }

    /* customer register action if entered using default register form */

    public function createPostAction() {
        
        $customer = Mage::getModel('customer/customer');
        $session = $this->_getCustomerSession();
        if ($session->isLoggedIn()) {
            $this->_redirect('*/*/');
            return;
        }
        $session->setEscapeMessages(true); // prevent XSS injection in user input
        if ($this->getRequest()->isPost()) {
          $data = $this->getRequest()->getPost();
		  if( $_SESSION['security_code'] == $data['security_code']) {
      
            $errors = array();

            if (!$customer = Mage::registry('current_customer')) {
                $customer = Mage::getModel('customer/customer')->setId(null);
            }

            /* @var $customerForm Mage_Customer_Model_Form */
            $customerForm = Mage::getModel('customer/form');
            $customerForm->setFormCode('customer_account_create')
                    ->setEntity($customer);

            $customerData = $customerForm->extractData($this->getRequest());
            if ($this->getRequest()->getParam('is_subscribed', false)) {
                $customer->setIsSubscribed(1);
            }

            /**
             * Initialize customer group id
             */
            $customer->getGroupId();

            if ($this->getRequest()->getPost('create_address')) {
                /* @var $address Mage_Customer_Model_Address */
                $address = Mage::getModel('customer/address');
                /* @var $addressForm Mage_Customer_Model_Form */
                $addressForm = Mage::getModel('customer/form');
                $addressForm->setFormCode('customer_register_address')
                        ->setEntity($address);

                $addressData = $addressForm->extractData($this->getRequest(), 'address', false);
                $addressErrors = $addressForm->validateData($addressData);
                if ($addressErrors === true) {
                    $address->setId(null)
                            ->setIsDefaultBilling($this->getRequest()->getParam('default_billing', false))
                            ->setIsDefaultShipping($this->getRequest()->getParam('default_shipping', false));
                    $addressForm->compactData($addressData);
                    $customer->addAddress($address);

                    $addressErrors = $address->validate();
                    if (is_array($addressErrors)) {
                        $errors = array_merge($errors, $addressErrors);
                    }
                } else {
                    $errors = array_merge($errors, $addressErrors);
                }
            }

            try {
                $customerErrors = $customerForm->validateData($customerData);

                if ($customerErrors !== true) {
                    $errors = array_merge($customerErrors, $errors);
                } else {
                    $customerForm->compactData($customerData);
                    $customer->setPassword($this->getRequest()->getPost('password'));
                    $customer->setConfirmation($this->getRequest()->getPost('confirmation'));
                    $customerErrors = $customer->validate();
                    if (is_array($customerErrors)) {
                        $errors = array_merge($customerErrors, $errors);
                    }
                }

                $validationResult = count($errors) == 0;

                if (true === $validationResult) {
                    $customer->save();

                    Mage::dispatchEvent('customer_register_success', array('account_controller' => $this, 'customer' => $customer)
                    );

                    if ($customer->isConfirmationRequired()) {
                        $customer->sendNewAccountEmail(
                                'confirmation', $session->getBeforeAuthUrl(), Mage::app()->getStore()->getId()
                        );
                        $session->addSuccess($this->__('Account confirmation is required. Please, check your email for the confirmation link. To resend the confirmation email please <a href="%s">click here</a>.', Mage::helper('customer')->getEmailConfirmationUrl($customer->getEmail())));
                        echo Mage::getUrl('/index', array('_secure' => true));
                        return;
                    } else {
                        $session->setCustomerAsLoggedIn($customer);
                        echo $url = $this->_welcomeCustomer($customer);
                        return;
                    }
                } else {
                    $session->setCustomerFormData($this->getRequest()->getPost());
                    if (is_array($errors)) {
                        foreach ($errors as $errorMessage) {
                            $session->$errorMessage;
                        }
                        echo $errorMessage;
                        return;
                    } else {
                        $session->addError($this->__('Invalid customer data'));
                    }
                }
            } catch (Mage_Core_Exception $e) {
                $session->setCustomerFormData($this->getRequest()->getPost());
                if ($e->getCode() === Mage_Customer_Model_Customer::EXCEPTION_EMAIL_EXISTS) {
                    echo $message = $this->__('Already exists');
                    $session->setEscapeMessages(false);
                    return;
                } else {
                    echo $message = $e->getMessage();
                    return;
                }
                $session->addError($message);
            } catch (Exception $e) {

                $session->setCustomerFormData($this->getRequest()->getPost())
                        ->addException($e, $this->__('Cannot save the customer.'));
            }
            } else {
                $se = $_SESSION['security_code'];
                Mage::getSingleton('core/session')->unsMyValue($se);
                echo $message = $this->__('Catpcha không hợp lệ');
                return;
  		    }
    }
        echo Mage::getUrl('*/index', array('_secure' => true));
    }

    /* ForgetPassword action */

    public function forgotPasswordPostAction() {
        $email = (string) $this->getRequest()->getPost('email_value');
        /** @var $customer Mage_Customer_Model_Customer */
        $customer = Mage::getModel('customer/customer')
                ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                ->loadByEmail($email);
        if ($customer->getId()) {
            try {
                $newResetPasswordLinkToken = Mage::helper('customer')->generateResetPasswordLinkToken();
                $customer->changeResetPasswordLinkToken($newResetPasswordLinkToken);
                $customer->sendPasswordResetConfirmationEmail();
            } catch (Exception $exception) {
                $this->_getCustomerSession()->addError($exception->getMessage());
                return;
            }
        }
        echo 'sent';
        return;
    }

/* Captcha */

var $font = 'media/captcha/arial.ttf';
    
    public function generateCode($characters) {
		/* list all possible characters, similar looking characters and vowels have been removed */
		$possible = '23456789bcdfghjkmnpqrstvwxyz';
		$code = '';
		$i = 0;
		while ($i < $characters) { 
			$code .= substr($possible, mt_rand(0, strlen($possible)-1), 1);
			$i++;
		}
		return $code;
	}

	public function CaptchaSecurityImages($width='120',$height='40',$characters='6') {
		$code = $this->generateCode($characters);
		/* font size will be 75% of the image height */
		$font_size = $height * 0.75;
		$image = @imagecreate($width, $height) or die('Cannot initialize new GD image stream');
		/* set the colours */
		$background_color = imagecolorallocate($image, 255, 255, 255);
		$text_color = imagecolorallocate($image, 20, 40, 100);
		$noise_color = imagecolorallocate($image, 100, 120, 180);
		/* generate random dots in background */
		for( $i=0; $i<($width*$height)/3; $i++ ) {
			imagefilledellipse($image, mt_rand(0,$width), mt_rand(0,$height), 1, 1, $noise_color);
		}
		/* generate random lines in background */
		for( $i=0; $i<($width*$height)/150; $i++ ) {
			imageline($image, mt_rand(0,$width), mt_rand(0,$height), mt_rand(0,$width), mt_rand(0,$height), $noise_color);
		}
		/* create textbox and add text */
		$textbox = imagettfbbox($font_size, 0, $this->font, $code) or die('Error in imagettfbbox function');
		$x = ($width - $textbox[4])/2;
		$y = ($height - $textbox[5])/2;
		imagettftext($image, $font_size, 0, $x, $y, $text_color, $this->font , $code) or die('Error in imagettftext function');
		/* output captcha image to browser */
		header('Content-Type: image/jpeg');
		imagejpeg($image);
		imagedestroy($image);
		$_SESSION['security_code'] = $code;
	}
	public function captchaAction(){
	//echo 'fff';
	//exit;
	$width = isset($_GET['width']) ? $_GET['width'] : '120';
	$height = isset($_GET['height']) ? $_GET['height'] : '40';
	$characters = isset($_GET['characters']) && $_GET['characters'] > 1 ? $_GET['characters'] : '6';
	
	$captcha = $this->CaptchaSecurityImages($width,$height,$characters);
	}


}