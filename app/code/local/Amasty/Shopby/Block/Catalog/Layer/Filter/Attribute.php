<?php

class Amasty_Shopby_Block_Catalog_Layer_Filter_Attribute extends Mage_Catalog_Block_Layer_Filter_Attribute
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('amshopby/attribute.phtml');
    }
    
    public function getItemsAsArray()
    {
        $items = array(); 
        foreach (parent::getItems() as $itemObject){
            $item = array();
            $item['url']   = $this->htmlEscape($itemObject->getUrl());
            
            if ($this->getSingleChoice()){ /** sinse @version 1.3.0 */
                $query = array(
                    $this->getRequestValue() => $itemObject->getIsSelected() ? null : $itemObject->getOptionId(),
                    Mage::getBlockSingleton('page/html_pager')->getPageVarName() => null 
                ); 
                $item['url'] = Mage::helper('amshopby/url')->getFullUrl($query);            
            }
            $item['label'] = $itemObject->getLabel();
            $item['descr'] = $itemObject->getDescr();

            $item['count'] = '';
         	$item['countValue']  = $itemObject->getCount();
            if (!$this->getHideCounts()) {
                $item['count']  = ' (' . $itemObject->getCount() . ')';
            }
            
            $item['image'] = '';
            if ($itemObject->getImage()){
                $item['image'] = Mage::getBaseUrl('media') . 'amshopby/' . $itemObject->getImage();
            }

            $item['css'] = 'amshopby-attr';
            if (in_array($this->getDisplayType(), array(1,3))) //dropdown and images
                $item['css'] = '';

            if ($itemObject->getIsSelected()){
				$item['selected']=$itemObject->getIsSelected();
                $item['css'] .= '-selected';
                if (3 == $this->getDisplayType()) //dropdown
                    $item['css'] = 'selected';
            }

            if ($this->getSeoRel()){ 
                $item['css'] .= '" rel="nofollow';  
            }            
            
            $items[] = $item;
        }
        
        $sortBy = $this->getSortBy();
        $functions = array(1 => '_sortByName', 2 => '_sortByCounts');
        if (isset($functions[$sortBy])){
            usort($items, array($this, $functions[$sortBy]));
        }
        
        // add less/more
        $max = $this->getMaxOptions();
        $i   = 0;
        foreach ($items as $k => $item){
            $style = '';
            if ($max && (++$i > $max)){
                $style = 'style="display:none" class="amshopby-attr-' . $this->getRequestValue() . '"'; 
            } 
            $items[$k]['style'] = $style;
        }
        $this->setShowLessMore($max && ($i > $max));
        
        return $items;
    }
    
    public function _sortByName($a, $b)
    {
        return strcmp($a['label'], $b['label']);
    }
    
    public function _sortByCounts($a, $b)
    {
        if ($a['countValue'] == $b['countValue']) {
        	return 0;
        }

        return ($a['countValue'] < $b['countValue'] ? 1 : -1);
    }
    
    public function getRequestValue()
    {
        return $this->_filter->getAttributeModel()->getAttributeCode();
    }
    
    public function getItemsCount()
     {
        $cnt     = parent::getItemsCount();
        $showAll = !Mage::getStoreConfig('amshopby/general/hide_one_value'); 
        return ($cnt > 1 || $showAll) ? $cnt : 0;
     }
     
    public function getRemoveUrl()
    {
        $query = array(
            $this->getRequestValue() => null,
            Mage::getBlockSingleton('page/html_pager')->getPageVarName() => null // exclude current page from urls
        );
        
        $url = Mage::helper('amshopby/url')->getFullUrl($query);
        return $url;        
    }
}