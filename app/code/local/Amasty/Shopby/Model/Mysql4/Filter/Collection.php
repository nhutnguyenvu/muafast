<?php
/**
* @copyright Amasty.
*/  
class Amasty_Shopby_Model_Mysql4_Filter_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        $this->_init('amshopby/filter');
    }
    
    public function addTitles()
    {
        if (empty($this->_map))
            $this->_map = array();
            
        $this->_map['fields']['attribute_id'] = 'main_table.attribute_id';
        
        if (Mage::helper('amshopby')->isVersionLessThan(1, 4)){
            $this->getSelect()
                ->joinInner(array('a'=> $this->getTable('eav/attribute')), 'main_table.attribute_id = a.attribute_id', array('a.frontend_label','a.attribute_code','a.position'));            
        }
        else{
            $this->getSelect()
                 ->joinInner(array('a'=> $this->getTable('eav/attribute')), 'main_table.attribute_id = a.attribute_id', array('a.frontend_label','a.attribute_code'))
                 ->joinInner(array('ca'=> $this->getTable('catalog/eav_attribute')), 'main_table.attribute_id = ca.attribute_id', array('ca.position'));
        }
            
        return $this;
    }
}