<?php
/**
* @copyright Amasty.
*/ 
class Amasty_Shopby_Block_Top extends Mage_Core_Block_Template
{
    private $options = array();
    
    private function trim($str)
    {
        $str = strip_tags($str);  
        $str = str_replace('"', '', $str); 
        return trim($str, " -");
    }
    
    
    protected function _isPageHandled()
    {
        $page = Mage::getModel('amshopby/page'); 
        foreach ($page->getCollection() as $p){
            if ($p->match() && $p->getNum() > $page->getNum()){
                $page = $p;
            }
        }
        
        if (!$page->getNum())
            return false;
            
        $head = $this->getLayout()->getBlock('head');
        
        //canonical
        if (!Mage::helper('amshopby')->isVersionLessThan(1, 4)){
            $url = Mage::getSingleton('catalog/layer')->getCurrentCategory()->getUrl();
            $head->removeItem('link_rel', $url);  
            $head->addLinkRel('canonical', $page->getUrl());  
        }
        
        // metas
        $title = $head->getTitle();
        // trim prefix if any
        $prefix = Mage::getStoreConfig('design/head/title_prefix');
        $prefix = htmlspecialchars(html_entity_decode(trim($prefix), ENT_QUOTES, 'UTF-8'));            
        if ($prefix){
            $title = substr($title, strlen($prefix));    
        }
        $suffix = Mage::getStoreConfig('design/head/title_suffix');            
        $suffix = htmlspecialchars(html_entity_decode(trim($suffix), ENT_QUOTES, 'UTF-8'));            
        if ($suffix){
            $title = substr($title, 0, -1-strlen($suffix));    
        }   
        $descr = $head->getDescription();
      
        $titleSeparator = Mage::getStoreConfig('amshopby/general/title_separator');
        $descrSeparator = Mage::getStoreConfig('amshopby/general/descr_separator');
        
        if ($page->getUseCat()){
            $title = $title . $titleSeparator . $page->getMetaTitle();     
            $descr = $descr . $descrSeparator . $page->getMetaDescr();     
        }
        else {
            $title = $page->getMetaTitle();     
            $descr = $page->getMetaDescr();     
        }

        $head->setTitle($this->trim($title)); 
        $head->setDescription($this->trim($descr));             
        
        // in-page description
        $page->setShowOnList(true);
        $this->options = array($page);
        
        return true;    
        
    }    
    
    protected function _prepareLayout()
    {
        if ($this->_isPageHandled()){
             return parent::_prepareLayout();        
        }
        
        $hasCanonical = !Mage::helper('amshopby')->isVersionLessThan(1, 4);
        if ($hasCanonical){
            $url = Mage::getSingleton('catalog/layer')->getCurrentCategory()->getUrl();
            
            $head = $this->getLayout()->getBlock('head');
            //remove canonical URL for the categories starting from CE 1.4.x
            $head->removeItem('link_rel', $url);
            
            $isShopby = in_array(Mage::app()->getRequest()->getModuleName(), array(Mage::getStoreConfig('amshopby/seo/key'), 'amshopby')); 
            if ($isShopby)
                $url = '';    
            $head->addLinkRel('canonical', Mage::helper('amshopby/url')->getCanonicalUrl($url));
        }   
        
        $filters = Mage::getResourceModel('amshopby/filter_collection')
                ->addTitles()
                ->setOrder('position');
                
        $hash = array();
        $robotsIndex  = 'index';
        $robotsFollow = 'follow';
        foreach ($filters as $f){
            $code = $f->getAttributeCode();
            $vals = Mage::helper('amshopby')->getRequestValues($code);
            if ($vals){
                foreach($vals as $v){
                    $hash[$v] = $f->getShowOnList();            
                }
                if ($f->getSeoNofollow()){
                    $robotsIndex = 'noindex';
                }
                if ($f->getSeoNoindex()){
                    $robotsFollow = 'nofollow';                
                }
            }
        }
        
        $head = $this->getLayout()->getBlock('head');
        if ($head){
            if ('noindex' == $robotsIndex || 'nofollow' == $robotsFollow)
                $head->setRobots($robotsIndex .', '. $robotsFollow);
        }
        
        if (!$hash)
            return parent::_prepareLayout();    
        
        $options = Mage::getResourceModel('amshopby/value_collection')
            ->addFieldToFilter('option_id', array('in' => array_keys($hash)))
            ->load();
          
        $cnt = $options->count();
        if (!$cnt)
            return parent::_prepareLayout(); 
            
        //some of the options value have wrong value; 
        if ($cnt && $cnt < count($hash)){
            return parent::_prepareLayout(); 
            // or make 404 ?
        }            
              
        // sort options by attribute ids and add "show_on_list" property
        foreach ($options as $opt){ 
            $id = $opt->getOptionId();
            
            $opt->setShowOnList($hash[$id]);
            $hash[$id] = clone $opt;         
        }
        
        // unset "fake"  options (not object)
        foreach ($hash as $id => $opt){
            if (!is_object($opt))
                unset($hash[$id]);     
        }    
        if (!$hash)
            return parent::_prepareLayout();        

        if ($head){
            $title = $head->getTitle();
            // trim prefix if any
            $prefix = Mage::getStoreConfig('design/head/title_prefix');
            $prefix = htmlspecialchars(html_entity_decode(trim($prefix), ENT_QUOTES, 'UTF-8'));            
            if ($prefix){
                $title = substr($title, strlen($prefix));    
            }
            $suffix = Mage::getStoreConfig('design/head/title_suffix');            
            $suffix = htmlspecialchars(html_entity_decode(trim($suffix), ENT_QUOTES, 'UTF-8'));            
            if ($suffix){
                $title = substr($title, 0, -1-strlen($suffix));    
            }   
            $descr = $head->getDescription();
          
            $titleSeparator = Mage::getStoreConfig('amshopby/general/title_separator');
            $descrSeparator = Mage::getStoreConfig('amshopby/general/descr_separator');
            foreach ($hash as $opt){
                if ($opt->getMetaTitle())
                    $title .= $titleSeparator . $opt->getMetaTitle();
                    
                if ($opt->getMetaDescr())
                    $descr .= $descrSeparator . $opt->getMetaDescr();
            }
   
            $head->setTitle($this->trim($title)); 
            $head->setDescription($this->trim($descr)); 
        }
        $this->options = $hash;
              
        return parent::_prepareLayout();
    }
    
    public function getOptions()
    {
        $res = array();
        foreach ($this->options as $opt){
            if (!$opt->getShowOnList())
                continue;
                
            $item = array();
            $item['title'] = $this->htmlEscape($opt->getTitle());
            $item['descr'] = $opt->getDescr();
            $item['cms_block'] = '';
            
            $blockName = $opt->getCmsBlock();
            if ($blockName) {
                $item['cms_block'] = $this->getLayout()
                    ->createBlock('cms/block')
                    ->setBlockId($blockName)
                    ->toHtml();
            }
            
            $item['image'] = '';
            if ($opt->getImgBig())
                $item['image'] = Mage::getBaseUrl('media') . '/amshopby/' . $opt->getImgBig();
            $res[] = $item;
        }
        return $res;
    }

}