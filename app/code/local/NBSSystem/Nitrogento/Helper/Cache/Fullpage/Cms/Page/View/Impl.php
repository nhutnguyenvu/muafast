<?php

class NBSSystem_Nitrogento_Helper_Cache_Fullpage_Cms_Page_View_Impl extends NBSSystem_Nitrogento_Helper_Cache_Fullpage_Abstract
{
    public function buildCacheTags()
    {
        return array_merge(array('cms_page'), parent::buildBaseCacheTags());
    }
}