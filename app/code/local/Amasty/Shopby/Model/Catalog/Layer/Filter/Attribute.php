<?php
/**
 * @copyright   Copyright (c) 2010 Amasty (http://www.amasty.com)
 */

class Amasty_Shopby_Model_Catalog_Layer_Filter_Attribute extends Mage_Catalog_Model_Layer_Filter_Attribute
{
    // start version dependend functions
    
    protected function _getRemoveImage()
    {
         if (Mage::helper('amshopby')->isVersionLessThan(1, 4)){
            return Mage::getDesign()->getSkinUrl('images/list_remove_btn.gif');
         }
         else {
            return Mage::getDesign()->getSkinUrl('images/btn_remove.gif');
         }
            
    }
    
    protected function _getCount($attribute)
    {
         $optionsCount = array();
         if (Mage::helper('amshopby')->isVersionLessThan(1, 4)){
            $optionsCount = Mage::getSingleton('catalogindex/attribute')->getCount(
                $attribute,
                $this->_getBaseCollectionSql()
            );
         }
         else {
            // clone select from collection with filters
            $select = $this->_getBaseCollectionSql();
            
            // reset columns, order and limitation conditions
            $select->reset(Zend_Db_Select::COLUMNS);
            $select->reset(Zend_Db_Select::ORDER);
            $select->reset(Zend_Db_Select::LIMIT_COUNT);
            $select->reset(Zend_Db_Select::LIMIT_OFFSET);
    
            $connection = $this->_getResource()->getReadConnection();
            $tableAlias = $attribute->getAttributeCode() . '_idx';
            $conditions = array(
                "{$tableAlias}.entity_id = e.entity_id",
                $connection->quoteInto("{$tableAlias}.attribute_id = ?", $attribute->getAttributeId()),
                $connection->quoteInto("{$tableAlias}.store_id = ?", $this->getStoreId()),
            );
    
            $select
                ->join(
                    array($tableAlias => $this->_getResource()->getMainTable()),
                    join(' AND ', $conditions),
                    array('value', 'count' => "COUNT(DISTINCT {$tableAlias}.entity_id)"))
                ->group("{$tableAlias}.value");
    
            $optionsCount = $connection->fetchPairs($select);
         } 
         
         return $optionsCount;       
    }
    
    protected function _getIsFilterableAttribute($attribute)
    {
        $res = null;
        if (Mage::helper('amshopby')->isVersionLessThan(1, 4)){ 
            $res = $attribute->getIsFilterable();
        }
        else{
             $res = parent::_getIsFilterableAttribute($attribute);
        }
        
        return $res;  
    }
    
    protected function _getAttributeTableAlias()
    {
        $alias = null;
        if (Mage::helper('amshopby')->isVersionLessThan(1, 4)){ 
            $alias = 'attr_index_' . $this->getAttributeModel()->getId(); 
        }
        else{
            $alias = $this->getAttributeModel()->getAttributeCode() . '_idx';
        }
        return $alias;  
    }
    
    /**
     * Apply attribute filter to product collection
     *
     * @param array $value
     * @return null
     */
    protected function applyFilterToCollection($value)
    {
        $collection = $this->getLayer()->getProductCollection();
        $attribute  = $this->getAttributeModel();
        $alias      = $this->_getAttributeTableAlias();
        
        
        if(Mage::helper('amshopby')->isVersionLessThan(1, 4)){
            $collection->getSelect()->join(
                array($alias => Mage::getSingleton('core/resource')->getTableName('catalogindex/eav')),
                $alias . '.entity_id=e.entity_id',
                array()
            )
            ->where("$alias.store_id = ?", Mage::app()->getStore()->getId())
            ->where("$alias.attribute_id = ?", $attribute->getId())
            ->where("$alias.value IN (?)", $value);
        }
        else{
            $connection = $this->_getResource()->getReadConnection();
            $conditions = array(
                "{$alias}.entity_id = e.entity_id",
                $connection->quoteInto("{$alias}.attribute_id = ?", $attribute->getAttributeId()),
                $connection->quoteInto("{$alias}.store_id = ?",     $collection->getStoreId()),
                $connection->quoteInto("{$alias}.value IN(?)",      $value)
            );
    
            $collection->getSelect()->join(
                array($alias => $this->_getResource()->getMainTable()),
                join(' AND ', $conditions),
                array()
            );            
        }
        
        if (count($value) > 1)
            $collection->getSelect()->distinct(true);

        return null;
    }      
    // END version dependend code
    

    /**
     * Apply attribute option filter to product collection
     *
     * @param   Zend_Controller_Request_Abstract $request
     * @param   Varien_Object $filterBlock
     * @return  Mage_Catalog_Model_Layer_Filter_Attribute
     */
    public function apply(Zend_Controller_Request_Abstract $request, $filterBlock)
    {
        $currentVals = Mage::helper('amshopby')->getRequestValues($this->_requestVar);
        if ($currentVals) {
            $this->applyFilterToCollection($currentVals);
          
            //generate Status Block
            $attribute = $this->getAttributeModel();      
            $text = '';
            foreach ($attribute->getSource()->getAllOptions() as $option) {
                $k = array_search($option['value'], $currentVals);
                if (false !== $k){
    
                    $exclude = $currentVals;
                    unset($exclude[$k]);
                    $exclude = implode(',', $exclude);
                    if (!$exclude)
                        $exclude = null;
                    
                    $query = array(
                        $this->getRequestVar() => $exclude,
                        Mage::getBlockSingleton('page/html_pager')->getPageVarName() => null // exclude current page from urls
                    );
                    //$url = Mage::getUrl('*/*/*', array('_current'=>true, '_use_rewrite'=>true, '_query'=>$query));                
                    $url = Mage::helper('amshopby/url')->getFullUrl($query);
                    
                    $text .= $option['label'] 
                          . '&nbsp;'
                          . '<a href="' . $url . '">'
                          . '<img src="' . $this->_getRemoveImage() . '" alt="' . Mage::helper('catalog')->__('Remove This Item') . '" />'
                          . '</a>, ';
                }
            }
            
            if ($text) {
                $text = substr($text, 0, -2);
            }
            
            $state = $this->_createItem($text, $currentVals)
                        ->setVar($this->_requestVar)
                        ->setIsMultiValues(true);
                        
            $this->getLayer()->getState()->addFilter($state);
        }
        return $this;
    }

    /**
     * Get data array for building attribute filter items
     *
     * @return array
     */
    protected function _getItemsData()
    {
        $attribute = $this->getAttributeModel();
        $this->_requestVar = $attribute->getAttributeCode();


        $options = $attribute->getFrontend()->getSelectOptions();
        $optionsCount = $this->_getCount($attribute);
        $data = array();

        foreach ($options as $option) {
            if (is_array($option['value'])) {
                continue;
            }
            if (!Mage::helper('core/string')->strlen($option['value'])) {
                continue;
            }
            $currentVals = Mage::helper('amshopby')->getRequestValues($this->_requestVar);
            $ind = array_search($option['value'], $currentVals);
            if (false === $ind){
                $currentVals[] = $option['value'];
            }
            else {
                $currentVals[$ind]  = null;
                unset($currentVals[$ind]);    
            }
            
            $currentVals = implode(',', $currentVals);
            $cnt = isset($optionsCount[$option['value']]) ? $optionsCount[$option['value']] : 0;    
            if ($cnt || $this->_getIsFilterableAttribute($attribute) != self::OPTIONS_ONLY_WITH_RESULTS) {
                    $data[] = array(
                        'label'     => $option['label'],
                        'value'     => $currentVals,
                        'count'     => $cnt,
                        'option_id' => $option['value'],
                    );
            }
        }
        return $data;
    }

    
    protected function _initItems()
    {
        $data  = $this->_getItemsData();
        $items = array();
        foreach ($data as $itemData) {
            $item = $this->_createItem(
                $itemData['label'],
                $itemData['value'],
                $itemData['count']
            );
            $item->setOptionId($itemData['option_id']);
            $items[] = clone $item;
        }
        $this->_items = $items;
        return $this;
    } 
    
    //start new functions
    
    // will work for both 1.3 and 1.4
    protected function _getBaseCollectionSql()
    {
        $alias = $this->_getAttributeTableAlias();
        
        $baseSelect = clone parent::_getBaseCollectionSql();
        
        $oldWhere = $baseSelect->getPart(Varien_Db_Select::WHERE);
        $newWhere = array();

        foreach ($oldWhere as $cond){
           if (!strpos($cond, $alias))
               $newWhere[] = $cond;
        }
  
        if ($newWhere && substr($newWhere[0], 0, 3) == 'AND')
           $newWhere[0] = substr($newWhere[0], 3);        
        
        $baseSelect->setPart(Varien_Db_Select::WHERE, $newWhere);
        
        $oldFrom = $baseSelect->getPart(Varien_Db_Select::FROM);
        $newFrom = array();
        
        foreach ($oldFrom as $name=>$val){
           if ($name != $alias)
               $newFrom[$name] = $val;
        }
        $baseSelect->setPart(Varien_Db_Select::FROM, $newFrom);

        return $baseSelect;
    }   
}