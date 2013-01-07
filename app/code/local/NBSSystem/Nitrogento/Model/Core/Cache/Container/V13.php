<?php

class NBSSystem_Nitrogento_Model_Core_Cache_Container_V13 extends NBSSystem_Nitrogento_Model_Core_Cache_Container_Abstract
{
    public function getCache()
    {
        if (!$this->_cache) 
        {
            $backend = strtolower((string)$this->_getSimpleConfig()->getNode('global/cache/backend'));
            if (extension_loaded('apc') && ini_get('apc.enabled') && $backend=='apc') 
            {
                $backend = 'Apc';
                $backendAttributes = array(
                    'cache_prefix' => (string)$this->_getSimpleConfig()->getNode('global/cache/prefix')
                );
            } 
            elseif ('memcached' == $backend && extension_loaded('memcache')) 
            {
                $backend = 'Memcached';
                $memcachedConfig = $this->_getSimpleConfig()->getNode('global/cache/memcached');
                $backendAttributes = array(
                    'compression'               => (bool)$memcachedConfig->compression,
                    'cache_dir'                 => (string)$memcachedConfig->cache_dir,
                    'hashed_directory_level'    => (string)$memcachedConfig->hashed_directory_level,
                    'hashed_directory_umask'    => (string)$memcachedConfig->hashed_directory_umask,
                    'file_name_prefix'          => (string)$memcachedConfig->file_name_prefix,
                    'servers'                   => array(),
                );
                foreach ($memcachedConfig->servers->children() as $serverConfig) 
                {
                    $backendAttributes['servers'][] = array(
                        'host'          => (string)$serverConfig->host,
                        'port'          => (string)$serverConfig->port,
                        'persistent'    => (string)$serverConfig->persistent,
                    );
                }
            } 
            else 
            {
                $backend = 'File';
                $backendAttributes = array(
                    'cache_dir'                 => BP . DS . 'var' . DS . 'cache',
                    'hashed_directory_level'    => 1,
                    'hashed_directory_umask'    => 0777,
                    'file_name_prefix'          => 'mage',
                );
            }
            $lifetime = $this->_getSimpleConfig()->getNode('global/cache/lifetime');
            if ($lifetime !== false) 
            {
                $lifetime = (int) $lifetime;
            }
            else 
            {
                $lifetime = 7200;
            }
            $this->_cache = Zend_Cache::factory('Core', $backend,
                array(
                    'caching'                   => true,
                    'lifetime'                  => $lifetime,
                    'automatic_cleaning_factor' => 0,
                ),
                $backendAttributes
            );
        }
        
        return $this->_cache;
    }
} 