<?php

class CJM_AutoSwatchesPlus_Block_System_Config_Swatches
    extends Mage_Adminhtml_Block_Abstract
    implements Varien_Data_Form_Element_Renderer_Interface
{
	public function render(Varien_Data_Form_Element_Abstract $element)
	{
       	$swatch_attributes = Mage::helper('autoswatchesplus')->getSwatchList();
		$html = '<div style="background:url(\'http://chadjmorgan.com/magedev/mage_blank_back.jpg\') scroll #ccc;border:1px solid #CCCCCC;margin-bottom:10px;padding:10px 5px 5px 20px;">
		<ul>
			<li>
				<h4>CURRENT SWATCH ATTRIBUTES</h4>
				<p style="font-size:10px; color:#666666;">
				&#8226;&nbsp;'.$swatch_attributes.'

				</p></li></ul></div>';
        
        return $html;
    }
}
