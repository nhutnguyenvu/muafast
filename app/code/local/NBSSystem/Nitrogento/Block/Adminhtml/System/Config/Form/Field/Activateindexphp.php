<?php

class NBSSystem_Nitrogento_Block_Adminhtml_System_Config_Form_Field_Activateindexphp extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $indexPhpPath = BP . DS . 'index.php';
        $indexPhpContent = file_get_contents($indexPhpPath);
        
        if (!preg_match('/NBSSystem_Nitrogento_Main/', $indexPhpContent))
        {
            $button = $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'id'        => 'activatephp_button',
                    'label'     => Mage::helper('nitrogento')->__('Activate Index.php'),
                    'onclick'   => 'setLocation(\'' . $this->getUrl('adminhtml/cache_fullpage_config/activateIndexphp') . '\')'
                ));
            
            return $button->toHtml();
        }
        else
        {
            return '<span style="color:#EB5E00"><strong>' . Mage::helper('nitrogento')->__('Index php has been Activated')  . '</strong></span>';
        }
    }
}