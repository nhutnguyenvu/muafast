<?php

class NBSSystem_Nitrogento_Model_Mysql4_Cache_Fullpage_Config extends NBSSystem_Nitrogento_Model_Mysql4_Cache_Config
{
    public function _construct()
    {
        $this->_init('nitrogento/cache_fullpage_config', 'config_id');
    }
}