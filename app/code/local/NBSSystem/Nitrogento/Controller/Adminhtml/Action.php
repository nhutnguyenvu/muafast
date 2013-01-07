<?php

class NBSSystem_Nitrogento_Controller_Adminhtml_Action extends Mage_Adminhtml_Controller_Action
{
    public function preDispatch()
    {
        parent::preDispatch();
        Mage::helper('nitrogento')->validateLicenceKey();
    }
}