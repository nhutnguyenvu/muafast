<?php
/**
* @copyright Amasty.
*/
class Amasty_Shopby_Model_Mysql4_Value_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        $this->_init('amshopby/value');
    }
    
    public function addPositions()
    {
        if (empty($this->_map))
            $this->_map = array();
            
        $this->_map['fields']['option_id'] = 'main_table.option_id';
        
        $this->getSelect()->joinInner(
            array('o'=> $this->getTable('eav/attribute_option')), 
            'main_table.option_id = o.option_id', 
            array('o.sort_order')
        );
            
        return $this;
    } 
    
    public function addValue()
    {
        $storeId = Mage::app()->getStore()->getId();
        $this->getSelect()->joinLeft(
            array('ov' => $this->getTable('eav/attribute_option_value')), 
            'main_table.option_id = ov.option_id AND ov.store_id=' . $storeId, 
            array('ov.value')
        );
            
        return $this;
    }    
}