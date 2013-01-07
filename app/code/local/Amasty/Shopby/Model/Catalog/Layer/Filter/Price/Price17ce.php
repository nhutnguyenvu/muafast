<?php
/**
 * @copyright   Copyright (c) 2010 Amasty (http://www.amasty.com)
 */  
class Amasty_Shopby_Model_Catalog_Layer_Filter_Price_Price17ce extends Mage_Catalog_Model_Layer_Filter_Price
{
	public function _construct()
	{	
		parent::_construct();
	}  
	  
    /**
     * Apply price range filter to collection
     *
     * @return Mage_Catalog_Model_Layer_Filter_Price
     */
    public function apply(Zend_Controller_Request_Abstract $request, $filterBlock)
    {
        $filterBlock->setPriceFrom(Mage::helper('amshopby')->__('From'));
        $filterBlock->setPriceTo(Mage::helper('amshopby')->__('To'));
        
        $filter = $request->getParam($this->getRequestVar());
        if (!$filter) {
            return $this;
        }

        //validate filter
        $filterParams = explode(',', $filter);
        $filter = $this->_validateFilter($filterParams[0]);
        if (!$filter) {
            return $this;
        }

        list($from, $to) = $filter;
        
        
        
        $filterBlock->setPriceFrom($from > 0.01 ? $from : '');
		$filterBlock->setPriceTo($to > 0.01 ? $to : ''); 

    	/*
         * Workaround for defect related to decreasing price for layered navgiation
         */
		$to = $to + Mage_Catalog_Model_Resource_Layer_Filter_Price::MIN_POSSIBLE_PRICE;
        
		$this->setInterval(array($from, $to));

        $priorFilters = array();
        for ($i = 1; $i < count($filterParams); ++$i) {
            $priorFilter = $this->_validateFilter($filterParams[$i]);
            if ($priorFilter) {
                $priorFilters[] = $priorFilter;
            } else {
                //not valid data
                $priorFilters = array();
                break;
            }
        }
        if ($priorFilters) {
            $this->setPriorIntervals($priorFilters);
        }
        $this->_applyPriceRange();
        $this->getLayer()->getState()->addFilter($this->_createItem(
            $this->_renderRangeLabel(empty($from) ? 0 : $from, $to),
            $filter
        ));

        return $this;
    }
    
	/**
     * Retrieve resource instance
     *
     * @return Amasty_Shopby_Model_Mysql4_Price17
     */
    protected function _getResource()
    {
        if (is_null($this->_resource)) {
            $this->_resource = Mage::getSingleton('amshopby/mysql4_price17');
        }
        return $this->_resource;
    }
    
    public function getItemsCount()
    {
    	$cnt = parent::getItemsCount();
        return ($cnt > 0) ? $cnt : 1;
    }
    
    public function getMaxPriceInt()
    {
    	return $this->_getResource()->getMaxPrice($this);
    }
    
 	public function getMinPriceInt()
    {
    	return $this->_getResource()->getMinPrice($this);
    }
    
	protected function _getItemsData()
    {
        if (!Mage::getStoreConfig('amshopby/general/use_custom_ranges')) {
        	$this->setInterval(array());
            return parent::_getItemsData();
        }
            
        $key = $this->_getCacheKey();

        $data = $this->getLayer()->getAggregator()->getCacheData($key);
        if ($data === null) {
            $ranges = $this->_getCustomRanges();
            $counts = $this->_getResource()->getFromToCount($this, $ranges);
            $data = array();
            
            foreach ($counts as $index => $count) {
                if (!$index) // index may be NULL if some products has price out of all ranges
                    continue;
                    
                $from  = $ranges[$index][0];
                $to    = $ranges[$index][1];
                $data[] = array(
                    'label' => $this->_renderRangeLabel($from, $to),
                    'value' => $from . '-' . $to,
                    'count' => $count,
                    'pos'   => $from,
                );
            }
            usort($data, array($this, '_srt')); 

            $tags = array(
                Mage_Catalog_Model_Product_Type_Price::CACHE_TAG,
            );
            $tags = $this->getLayer()->getStateTags($tags);
            $this->getLayer()->getAggregator()->saveCacheData($data, $key, $tags);
        }
        return $data;
    }
    
    public function _srt($a, $b)
    {
        $res = ($a['pos'] < $b['pos']) ? -1 : 1;
        return $res;
    } 
    
	protected function _getCustomRanges()
	{
        $ranges = array();
        $collection = Mage::getModel('amshopby/range')->getCollection()
            ->setOrder('price_frm','asc')
            ->load();
            
        $rate = Mage::app()->getStore()->getCurrentCurrencyRate(); 
        $prev = 0;   
        foreach ($collection as $range){
            $from = $range->getPriceFrm()*$rate;
            $prev = $to = $range->getPriceTo()*$rate;
            
            $ranges[$range->getId()] = array($from, $to);
        }
        
        if (!$ranges){
            echo "Please set up Custom Ranges in the Admin > Catalog > Improved Navigation > Ranges";
            exit;
        }
        
        return $ranges;
	}
}