<?php
/**
* @copyright Amasty.
*/  
class Amasty_Shopby_Block_List extends Mage_Core_Block_Template
{
    private $items = array();
    

    public function getItems()
    {
        return $this->items;
    }
    
    public function _sortByName($a, $b)
    {
        return strcmp($a['label'], $b['label']);
    }

}