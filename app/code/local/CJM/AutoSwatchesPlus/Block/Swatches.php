<?php

class CJM_AutoSwatchesPlus_Block_Swatches extends Mage_Core_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('autoswatchesplus/swatches.phtml');
        $this->_doUpload();
    }
    
    protected function _doUpload()
    {
        $uploadDir = Mage::getBaseDir('media') . DIRECTORY_SEPARATOR . 
                                                    'autoswatchesplus' . DIRECTORY_SEPARATOR . 'swatches' . DIRECTORY_SEPARATOR;
                                                    
        /**
        * Deleting
        */
        $toDelete = Mage::app()->getRequest()->getPost('autoswatchesplus_swatch_delete');
        if ($toDelete)
        {
            foreach ($toDelete as $optionId => $del)
            {
                if ($del)
                {
                    @unlink($uploadDir . $optionId . '.jpg');
                }
            }
        }
        
        /**
        * Uploading files
        */
        if (isset($_FILES['autoswatchesplus_swatch']) && isset($_FILES['autoswatchesplus_swatch']['error']))
        {
            foreach ($_FILES['autoswatchesplus_swatch']['error'] as $optionId => $errorCode)
            {
                if (UPLOAD_ERR_OK == $errorCode)
                {
                    move_uploaded_file($_FILES['autoswatchesplus_swatch']['tmp_name'][$optionId], $uploadDir . $optionId . '.jpg');
                }
            }
        }
    }
    
    public function getOptionsCollection()
    {
        $optionCollection = Mage::getResourceModel('eav/entity_attribute_option_collection')
                ->setAttributeFilter(Mage::registry('entity_attribute')->getId())
                ->setPositionOrder('desc', true)
                ->load();
        return $optionCollection;
    }
    
    public function getIcon($option)
    {
        return Mage::helper('autoswatchesplus')->getSwatchUrl($option->getId());
    }
    
    public function getSubmitUrl()
    {
        return Mage::helper('core/url')->getCurrentUrl();
    }
}