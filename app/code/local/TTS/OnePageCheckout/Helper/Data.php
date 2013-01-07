<?php

class TTS_OnePageCheckout_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getConfigParam($key)
    {
        $key = 'onepage_checkout/' . $key;
        $data = Mage::getStoreConfig($key);
        if (!is_array($data)) return trim($data);
            else return array_map('trim', $data);
    }

    public function getGiftmessageBlock(Varien_Object $entity)
    {
        $type = 'onepage_checkout';

        return Mage::getSingleton('core/layout')->createBlock('giftmessage/message_inline')
            ->setTemplate('tts/onepagecheckout/onepage/shipping_method/giftmessage.phtml')
            ->setId('giftmessage_form_' . $this->_nextId++)
            ->setDontDisplayContainer(false)
            ->setEntity($entity)
            ->setType($type)->toHtml();
    }
}
