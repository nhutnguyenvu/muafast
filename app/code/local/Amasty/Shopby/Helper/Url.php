<?php
/**
 * @copyright   Copyright (c) 2009-2011 Amasty (http://www.amasty.com)
 */ 
class Amasty_Shopby_Helper_Url extends Mage_Core_Helper_Abstract
{
    private $_options = null;
    
    public function getCanonicalUrl($catUrl)
    {
        $key           = Mage::getStoreConfig('amshopby/seo/key');
        $canonicalType = Mage::getStoreConfig('amshopby/seo/canonical' . ($catUrl ? '_cat' : ''));
        $isSeo         = Mage::getStoreConfig('amshopby/seo/urls');
        
        if (0 == $canonicalType || !$isSeo){
            return ($catUrl ? $catUrl : Mage::getBaseUrl() . $key);
        }
        if (1 == $canonicalType){ // as is
            return Mage::helper('core/url')->getCurrentUrl();        
        }
        
        // show firts attribute value as canonical         
        $url = Mage::helper('core/url')->getCurrentUrl();
        // possible values
        // shopby
        // shopby/atrr_name_value1-value2/attr2_value3.html
        // shopby/value_name1/value_name2.html 
        // shopby/apple.html        
        $parts = explode($key, $url, 2);
        $attributes = '';
        if (isset($parts[1])){
            $attributes = trim($parts[1], '/');
        }
        
        // we should look for first "-" or first "/"
        $pos  = max(0, strpos($attributes, '-'));
        $pos2 = max(0, strpos($attributes, '/'));
        if ($pos && $pos2)
            $pos = min($pos, $pos2);
        else
            $pos = max($pos, $pos2);
        
        if ($pos){
            $url = Mage::getBaseUrl() . $key . '/' . substr($attributes, 0, $pos); 
            $suffix     = Mage::getStoreConfig('catalog/seo/category_url_suffix'); 
            if ($suffix){
                $url .= $suffix; 
            }            
        }
        return $url;      
    }
    
    //optimized version of the getFullUrl
    public function getOptionUrl($attrCode, $optLabel, $optId)
    {
        $url = Mage::getBaseUrl() . Mage::getStoreConfig('amshopby/seo/key'). '/';
        if (Mage::getStoreConfig('amshopby/seo/urls')){
            if (!Mage::getStoreConfig('amshopby/seo/hide_attributes')){
                $url .= $attrCode . '-';
            }
            $url .= $this->createKey($optLabel);
            $url .= Mage::getStoreConfig('catalog/seo/category_url_suffix');
        }
        else {
            $url .= '?' . $attrCode . '=' . $optId; 
        }

        return $url;      
    }    
    
    public function getFullUrl($query=array(), $clear=false, $cat = null)
    {
        $url = '';
        
        $cat    = $cat ? $cat : Mage::registry('current_category');
        $rootId = (int) Mage::app()->getStore()->getRootCategoryId();
        
        $mod  = Mage::app()->getRequest()->getModuleName();
        $isSearch = in_array(Mage::app()->getRequest()->getModuleName(), array('sqli_singlesearchresult', 'catalogsearch'));
        $isNewOrSale = (('catalognew' == $mod) || ('catalogsale' == $mod));
        
        $reservedKey = Mage::getStoreConfig('amshopby/seo/key');
        
        $base = Mage::getBaseUrl();
        
        if ($isSearch){
            $url = $base . 'catalogsearch/result/';     
        }
        elseif ($isNewOrSale) {
            $url = $base . $mod; 
            if ($cat)
                $query['cat'] = $cat->getId();  
        }
        elseif (!$cat) { // homepage, 
            $q = array_merge(Mage::app()->getRequest()->getQuery(), $query);
            $hasFilter = false;
            foreach ($q as $k=>$v){
                if (!in_array($k, array('p','mode','order','dir','limit')) && false === strpos('__', $k)){
                    $hasFilter = true;
                }
            }
            // homepage filter links 
            if ($hasFilter){
                 $url = $base . $reservedKey;  
            }
            // homepage sorting/paging url
            else {
                $url = $base;
            }
             
        }
        elseif ($cat->getId() == $rootId) {
            $url = $base . $reservedKey;    
        }
        else { // we have a valid category
            $url = $cat->getUrl();
            $pos = strpos($url,'?');
            $url = $pos ? substr($url, 0, $pos) : $url;
        }
        
        $query = array_merge(Mage::app()->getRequest()->getQuery(), $query);
       
        $params = array();
        //remove nulls and empty vals 
        foreach ($query as $k => $v){
            if ($v){
                if (is_array($v)){
                    $v = implode(',', $v);
                }                
                //sort values to avoid duplicate content
                if (strpos($v, ',')){
                    $v = explode(',', $v);
                    sort($v);
                    $v = implode(',', $v);
                }
                $params[$k] = $v;
            }
        }
        // sort attribute names to avoid duplicate content
        ksort($params);

        if ($isSearch) { // leave as it was before
            if ($params && !$clear)
                $url .= '?' . http_build_query($params);
            
            if ($clear)
                $url .= '?q=' . urlencode($params['q']);            
        } 
        else {
            if (!$clear){
                $query = $params;
                
                $attrPart = '';
                // 2) add attributes as keys, not as ids
                if (Mage::getStoreConfig('amshopby/seo/urls')){
                    $query = array();
                    $options = $this->getAllFilterableOptionsAsHash();
                    foreach ($params as $attrCode => $ids)
                    {
                        if (isset($options[$attrCode])){ // it is filterable attribute
                            $attrPart .= $this->_formatAttributePart($attrCode, $ids);
                        }
                        else {
                            $query[$attrCode] = $ids; // it is pager or smth else
                        }
                    }
                }
                
                if ($attrPart){
                    //remove category suffix if any
                    $suffix = Mage::getStoreConfig('catalog/seo/category_url_suffix');
                    
                    if ($suffix && '/' != $suffix)
                        $url = str_replace($suffix, '', $url);
                    else 
                        $url = rtrim($url, '/');
                      
                    //add identificator for router
                    if (!strpos($url . '/', '/' . $reservedKey . '/'))
                        $url .= '/' . $reservedKey;
                    // add attributes and options     
                    $url .= '/' . $attrPart;   
                    // add suffix back
                    if ($suffix && '/' != $suffix)
                        $url = rtrim($url, '/') . $suffix;  
                }
                
                // add other params as query string if any
                if ($query){
                    $url .= '?' . http_build_query($query);
                }
            }
            
        }
        
        return $url;
    }
    
    public function saveParams($request)
    {
        $options = $this->getAllFilterableOptionsAsHash();
        if (!$options){
            return true;
        }
       
        $currentParams = Mage::registry('amshopby_current_params'); 
        if (!$currentParams){
            return true;        
        }
        
        // brand-amd-canon/price-100,200 or  amd-canon/100,200  
        $hideAttributeNames = Mage::getStoreConfig('amshopby/seo/hide_attributes');
        
        foreach ($currentParams as $params){
            
            $attrCode = '';
            
            $params   = explode('-', $params);
            $firstOpt = $params[0]; 
            
            if ($hideAttributeNames && !$this->isDecimal($firstOpt)){
                $attrCode = $this->_getAttributeCodeByOptionKey($firstOpt, $options);
            }
            else {
                $attrCode = $firstOpt;
                array_shift($params); // remove first element  
            }
            
            if ($attrCode && isset($options[$attrCode])){
                $query = array();
                if ($this->isDecimal($attrCode)){
                    
                    $v = $params[0];
                    if (count($params) > 1){
                        $v = $params[0] . '-' . $params[1];
                    }

                    if ($v === '' || is_null($v))
                        return false;
                        
                    $query[$attrCode] = $v;    
                }
                else {
                    
                    $ids = $this->_convertOptionKeysToIds($params, $options[$attrCode]);
                    $ids = $ids ? join(',', $ids) : $request->getParam($attrCode);  // fix for store changing 

                    $v = is_array($ids) ? '' : $ids; // just in case 
                    if (!$v)
                        return false;
                    $query[$attrCode] = $v;                    
                }
                $request->setQuery($query);
            }
            else { // we have undefined string 
                return false;
            }
        }
        
        return true;
        
    }
    
    public function isDecimal($attrCode)
    {
        return in_array($attrCode, array('special_price','price'));
    }
    
    public function getQuery()
    {
        $q = Mage::app()->getRequest()->getQuery();
        if ($q) {
            $q = '?' . http_build_query($q);
        }
        else {
            $q = '';
        }
        
        return $q;
    }
    
    public function createKey($optionLabel)
    {
        $key = Mage::helper('catalog/product_url')->format($optionLabel);
        $key = preg_replace('/[^0-9a-z,]+/i', '_', $key);
        $key = strtolower($key);
        $key = trim($key, '_-');

        return $key;
    } 

    public function getCategoryUrl($cat)
    {
        $pager = Mage::getBlockSingleton('page/html_pager')->getPageVarName();
        return $this->getFullUrl(array($pager => ''), false, $cat);
    } 
    
    public function getAllFilterableOptionsAsHash()
    {
        if (is_null($this->_options))
        {
            $hash = array();
            $attributes = Mage::getModel('catalog/layer')->getFilterableAttributes();
            foreach ($attributes as $a){
                $code        = $a->getAttributeCode();
                $hash[$code] = array();
                
                foreach ($a->getSource()->getAllOptions() as $o){
                    if ($o['value']){ // skip first empty
                        $hash[$code][$this->createKey($o['label'])] = $o['value'];
                    }
                }
            }
            $this->_options = $hash;
        }
        
        return $this->_options;
    }
    
    private function _convertIdToKeys($options, $ids)
    {
        $options = array_flip($options);
        
        $keys = array();
        foreach (explode(',', $ids) as $optionId){
            if (isset($options[$optionId])){
                $keys[] = $options[$optionId];
            }
        }
        return join('-', $keys);
    } 
    
    private function _formatAttributePart($attrCode, $ids)
    {
        if ($this->isDecimal($attrCode)){
            return $attrCode . '-' . $ids . '/'; // always show price and other decimal attributes
        }

        $options = $this->getAllFilterableOptionsAsHash();
        $part    = $this->_convertIdToKeys($options[$attrCode], $ids); 
        
        if (!$part){
            return '';
        }
        
        $hideAttributeNames = Mage::getStoreConfig('amshopby/seo/hide_attributes');
        $part =  $hideAttributeNames ? $part : ($attrCode . '-' . $part);
        $part .=  '/';
        
        return $part;
    } 
    
    private function _getAttributeCodeByOptionKey($key, $optionsHash)
    {
        if (!$key) {
            return false;
        }
        
        foreach ($optionsHash as $code => $values){
            if (isset($values[$key])){
                return $code;
            }
        }
        
        return false;      
    }
    
    private function _convertOptionKeysToIds($keys, $values)
    {
        $ids = array();
        foreach ($keys as $k){
            if (isset($values[$k])){
                $ids[] = $values[$k];
            }
        }
                
        return $ids;
    }
    
    
}