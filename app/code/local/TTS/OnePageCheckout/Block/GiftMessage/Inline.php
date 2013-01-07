<?php

class TTS_OnePageCheckout_Block_GiftMessage_Inline extends Mage_GiftMessage_Block_Message_Inline
{
    public function isItemMessagesAvailable($item)
    {
        $type = substr($this->getType(), 0, 5) == 'multi' ? 'address_item' : 'item';
        return Mage::helper('giftmessage/message')->isMessagesAvailable($type, $item);
    }

    public function isItemsAvailable()
    {
        return count($this->getItems()) > 0;
    }

    public function isMessagesAvailable()
    {
        return Mage::helper('giftmessage/message')->isMessagesAvailable('quote', $this->getEntity());
    }
}
