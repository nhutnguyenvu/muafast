<?php
/**
 * @author Amasty
 */ 
class Amasty_Shopby_Model_Page extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('amshopby/page');
    }
    
    public function getAllFilters($addEmpty=false){
        $collection = Mage::getModel('amshopby/filter')->getResourceCollection()
            ->addTitles();
            
        $values = array();
        if ($addEmpty){
            $values[''] = '';
        }
        foreach ($collection as $row){
            $values[$row->getAttributeCode()] = $row->getFrontendLabel();
        } 
        return $values;
    }
    
    public function match()
    {
        $cond = $this->getCond();
        if (!$cond){
            return false;
        }
            
        $cond = unserialize($cond);
        foreach ($cond as $k => $v){
            $vals = $vals = Mage::helper('amshopby')->getRequestValues($k);
            if (!in_array($v, $vals)){
                return false;
            }
        }
        return true;
    }
}