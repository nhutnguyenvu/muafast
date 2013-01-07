<?php

class NBSSystem_Nitrogento_Helper_Cache_Fullpage_Cms_Index_Index_Impl extends NBSSystem_Nitrogento_Helper_Cache_Fullpage_Abstract
{
    public function buildCacheTags()
    {
        return array_merge(array('cms_page'), parent::buildBaseCacheTags());
    }
}