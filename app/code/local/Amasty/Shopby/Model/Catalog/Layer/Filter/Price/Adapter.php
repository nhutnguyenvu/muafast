<?php

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2010-2012 Amasty (http://www.amasty.com)
 */
if (method_exists('Mage', 'getEdition')) { // CE 1.7+, EE 1.12+
	class Amasty_Shopby_Model_Catalog_Layer_Filter_Price_Adapter extends Amasty_Shopby_Model_Catalog_Layer_Filter_Price_Price17ce
	{
	   public function _construct()
	   {
			parent::_construct();
	   }
	}
} 
else { // CE 1.3.2 - 1.6.2 
    
	class Amasty_Shopby_Model_Catalog_Layer_Filter_Price_Adapter extends Amasty_Shopby_Model_Catalog_Layer_Filter_Price_Price14ce
	{
	   public function _construct()
	   {
	   		parent::_construct();
	   }
	}
}