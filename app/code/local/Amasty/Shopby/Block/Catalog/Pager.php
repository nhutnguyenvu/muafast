<?php
/**
 * @copyright   Copyright (c) 2010 Amasty
 */ 
class Amasty_Shopby_Block_Catalog_Pager extends Mage_Page_Block_Html_Pager
{
    public function getPagerUrl($params=array())
    {
        return $this->getParentBlock()->getPagerUrl($params);
    }
    
}