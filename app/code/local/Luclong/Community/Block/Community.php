<?php
class Luclong_Community_Block_Community extends Mage_Catalog_Block_Product_List
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
    public function getCommunity()     
    { 
        if (!$this->hasData('community')) {
            $this->setData('community', Mage::registry('community'));
        }
        return $this->getData('community');
        
    }
    
    public function getLike($product_id){
        $write = Mage :: getSingleton ( 'core/resource' )-> getConnection ('core_write'); 
        $readresult = $write -> query("SELECT *
                                        FROM ".Mage::getSingleton('core/resource')->getTableName('community')."
                                        WHERE product_id = $product_id
                                  ");
        return  $readresult->fetch();    
    }
}