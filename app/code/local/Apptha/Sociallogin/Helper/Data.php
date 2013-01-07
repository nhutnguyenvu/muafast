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

class Apptha_Sociallogin_Helper_Data extends Mage_Core_Helper_Abstract {
    /* function to get twitter aunthenticate url */

    public function getTwitterUrl() {
        require'sociallogin/twitter/twitteroauth.php';
        require 'sociallogin/config/twconfig.php';
        $twitteroauth = new TwitterOAuth(YOUR_CONSUMER_KEY, YOUR_CONSUMER_SECRET);
// Requesting authentication tokens, the parameter is the URL we will be redirected to
        $request_token = $twitteroauth->getRequestToken(Mage::getBaseUrl() . 'sociallogin/index/twitterlogin');
        if ($twitteroauth->http_code == 200) {
            $tw_oauth_token = Mage::getSingleton('customer/session')->setTwToken($request_token['oauth_token']);
            $tw_oauth_token_secret = Mage::getSingleton('customer/session')->setTwSecret($request_token['oauth_token_secret']);
            return $twitteroauth->getAuthorizeURL($request_token['oauth_token']);
        }
    }

    /* function to get google aunthenticate url */

    public function getGoogleUrl() {
        $google_id = new LightOpenID(Mage::getBaseUrl());
        $google_id->identity = 'https://www.google.com/accounts/o8/id';
        $google_id->required = array(
            'namePerson/first',
            'namePerson/last',
            'contact/email',
        );
        $google_id->returnUrl = Mage::getBaseUrl() . 'sociallogin/index/googlepost';
        return $url = $google_id->authUrl();
    }

    /* function to get yahoo aunthenticate url */

    public function getYahooUrl() {
        $yahoo_id = new LightOpenID(Mage::getBaseUrl());
        $yahoo_id->identity = 'https://me.yahoo.com';
        $yahoo_id->required = array(
            'namePerson',
            'namePerson/last',
            'contact/email',
            'namePerson/friendly',
        );

        $yahoo_id->returnUrl = Mage::getBaseUrl() . 'sociallogin/index/yahoopost';
        return $url = $yahoo_id->authUrl();
    }



    

}