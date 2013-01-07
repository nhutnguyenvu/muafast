<?php

class CJM_AutoSwatchesPlus_Block_System_Config_About
    extends Mage_Adminhtml_Block_Abstract
    implements Varien_Data_Form_Element_Renderer_Interface
{
	public function render(Varien_Data_Form_Element_Abstract $element)
	{
		
		$thismodule = 'CJM_AutoSwatchesPlus'; //CHANGES
		
		$feedurl = 'http://chadjmorgan.com/magedev/CJM_MageFeed.xml';
		$html = file_get_contents('http://chadjmorgan.com/magedev/about.html');		
		
		try {
    		$moduleinfo = new Zend_Feed_Rss($feedurl);
		} catch (Zend_Feed_Exception $e) {
    		$moduleinfo = 'false';
			echo "Error loading module info feed: {$e->getMessage()}\n";
		}
		
		if (($html === false) || ($moduleinfo === 'false')) 
		{ 
    		$html = '<div style="background-color:#FAFAFA; border:1px solid #D6D6D6; margin-bottom:10px; padding:10px 5px 5px 30px;">
						<p style="font-size:10px">CJM module information is currently experiencing difficulties... Please let me know if you see this message.</p>
						<p style="font-size:10px">For questions, suggestions, customizations or to hire CJM for freelance work, contact us at <a href="mailto:chad@chadjmorgan.com">chad@chadjmorgan.com</a> or <a href="http://www.chadjmorgan.com/?utm_source=magento_about_panel_error&utm_medium=info&utm_campaign=extension_info" target="_blank">chadjmorgan.com</a>. Thanks!</p>
					</div>';
			
			return $html; 
		
		} else { 
    		
			foreach ($moduleinfo as $item) {
    			if($item->name == $thismodule) {
					$modulename = strtoupper($item->title);
					$html = str_replace('%%MODNAME%%', $modulename, $html);
		
					$moduleversion = Mage::getConfig()->getNode("modules/".$thismodule."/version");
					$html = str_replace('%%MODVERS%%', $moduleversion, $html);
		
					$newestversion = $item->version;
					$html = str_replace('%%MODNEWEST%%', $newestversion, $html);
					break;
				}
			}
			
			return $html;
		} 			
    }
}
