<?php
/**
* @copyright Amasty.
*/  
class Amasty_Shopby_Model_Mysql4_Value extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        $this->_init('amshopby/value', 'value_id');
    }
    
    public function getFilterId($optionId)
    {
        $db = $this->_getReadAdapter();
        $sql = $db->select()
            ->from(array('f' => $this->getTable('amshopby/filter')), array('f.filter_id'))
            ->joinInner(array('o' => $this->getTable('eav/attribute_option')), 'o.attribute_id = f.attribute_id', array())
            ->where('o.option_id = ?', $optionId)
            ->limit(1);
        return $db->fetchOne($sql);  
    }

}