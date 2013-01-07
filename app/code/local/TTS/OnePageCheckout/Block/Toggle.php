<?php

class TTS_OnePageCheckout_Block_Toggle extends Mage_Core_Block_Template
{
    public function _prepareLayout()
    {
        if (!Mage::getStoreConfig('onepage_checkout/general/enabled')) return;
        if (Mage::getStoreConfig('onepage_checkout/general/ie6_ignore') && self::_isIE6()) return;
        $layout = $this->getLayout();
        $layout->getBlock('root')->setTemplate('page/1column.phtml');
        $layout->getBlock('content')->unsetChild('checkout.onepage');
        $block = $layout->getBlock('onepagecheckout.onepage');
        $layout->getBlock('content')->insert($block);
        $head = $layout->getBlock('head');
        $head->addItem('skin_js', 'js/tts/onepagecheckout/onepagecheckout.js');
        $head->addItem('skin_css', 'css/tts/onepagecheckout/onepagecheckout.css');
    }

    private static function _isIE6()
    {
        return preg_match('/\bmsie [1-6]/i', $_SERVER['HTTP_USER_AGENT']);
    }
}
