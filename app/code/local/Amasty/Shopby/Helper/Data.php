<?php
/**
* @copyright Amasty.
*/ 
class Amasty_Shopby_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $filters = null;
    protected $icons   = null;
    
    protected function _getFilters()
    {
        if (is_null($this->filters)){
            //get all possible filters as collection
            $filterCollection = Mage::getResourceModel('amshopby/filter_collection')
                    ->addFieldToFilter('show_on_view', 1)
                    ->addTitles();
            // convert to array        
            $filters = array();    
            foreach ($filterCollection as $filter){
                $filters[$filter->getId()] = $filter;
            }   
            $this->filters = $filters;
        }
        return $this->filters;
    }
    
    public function init($productCollection)
    {
        // make sure we call this only once
        if (!is_null($this->icons))
            return;
            
        $filters = $this->_getFilters();

        $optionCollection = Mage::getResourceModel('amshopby/value_collection')
            ->addPositions()
            ->addFieldToFilter('img_medium', array('gt' => ''))  
            ->addValue();  
            
        $this->icons = array();
        if (!$filters)
            return;
        
        $hlp = Mage::helper('amshopby/url');
        foreach ($optionCollection as $opt){
            if (empty($filters[$opt->getFilterId()]))// it is possible when "use on viev" = "flase"
                continue;
                
            $filter = $filters[$opt->getFilterId()];
                            
            // seo urls fix when different values        
            $opt->setTitle($opt->getValue() ? $opt->getValue() : $opt->getTitle());
                
            $img  = $opt->getImgMedium();
            $code = $filter->getAttributeCode();
            $url  = $hlp->getOptionUrl($code, $opt->getTitle(), $opt->getOptionId());

            $this->icons[$opt->getOptionId()] = array(
                'url'   => str_replace('___SID=U&','', $url),
                'title' => $opt->getTitle(),
                'img'   => Mage::getBaseUrl('media') . 'amshopby/' . $img,  
                'pos'   => $filter->getPosition(),  
                'pos2'  => $opt->getSortOrder(),  
            );    
        }
        
    }    
    
    /**
     * Returns HTML with attribute images
     *
     * @param Mage_Catalog_Model_Product $product
     * @param string $mode (view, list, grid)
     * @param array $names arrtibute codes to show images for
     * @param bool $exclude flag to indicate taht we need to show all attributes beside specified in $names
     * @return unknown
     */
    public function showLinks($product, $mode='view', $names=array(), $exclude=false)
    {
        if ('view' == $mode){
            $this->init(array($product));    
        }
        $filters = $this->_getFilters();
        
        $items = array();
        foreach ($filters as $filter){
            $code = $filter->getAttributeCode(); 
            if (!$code){
                continue;
            }
            
            if ($names && in_array($code, $names) && $exclude)
                continue;
                
            if ($names && !in_array($code, $names) && !$exclude)
                continue;
            
            $optIds  = trim($product->getData($code), ','); 
            if (!$optIds && $product->isConfigurable()){
                $usedProds = $product->getTypeInstance(true)->getUsedProducts(null, $product);
                foreach ($usedProds as $child){
                    if ($child->getData($code)){
                        $optIds .= $child->getData($code) . ',';
                    }
                }
            }
            
            if ($optIds){
                $optIds = explode(',', $optIds);
                $optIds = array_unique($optIds);
                foreach ($optIds as $id){
                    if (!empty($this->icons[$id])){
                        $items[] = $this->icons[$id];
                    }
                }
            }
        }  
       
        //sort by position in the layered navigation
        usort($items, array('Amasty_Shopby_Helper_Data', '_srt'));
        
        //create block
        $block = Mage::getModel('core/layout')->createBlock('core/template')
            ->setArea('frontend')
            ->setTemplate('amshopby/links.phtml');
        $block->assign('_type', 'html')
            ->assign('_section', 'body')        
            ->setLinks($items)
            ->setMode($mode); // to be able to created different html
             
        return $block->toHtml();          
    }
    
    public static function _srt($a, $b)
     {
        $res = ($a['pos'] < $b['pos']) ? -1 : 1;
        if ($a['pos'] == $b['pos']){ 
            if ($a['pos2'] == $b['pos2'])
                $res = 0;
            else 
                $res = ($a['pos2'] < $b['pos2']) ? -1 : 1;
        }
        
        return $res;
     }
    
    public function isVersionLessThan($major=5, $minor=3)
    {
        $curr = explode('.', Mage::getVersion()); // 1.3. compatibility
        $need = func_get_args();
        foreach ($need as $k => $v){
            if ($curr[$k] != $v)
                return ($curr[$k] < $v);
        }
        return false;
    }
    
    /**
     * Gets params (6,17,89) from the request as array and sanitize them
     *
     * @param string $key attribute code
     * @return array
     */
    public function getRequestValues($key)
    {
       $v = Mage::app()->getRequest()->getParam($key);
       
       if (is_array($v)){//smth goes wrong
           return array();
       }
       
       if (preg_match('/^[0-9,]+$/', $v)){
            $v = array_unique(explode(',', $v));
       }
       else { 
            $v = array();
       }
       
       return $v;       
    } 
}