<?php

class NBSSystem_Nitrogento_Model_Cache_Config extends Mage_Core_Model_Abstract
{
    public function getFormattedCacheLifetime()
    {
        $days = intval($this->getData('cache_lifetime') / 86400);
        $restDays = $this->getData('cache_lifetime') % 86400;
        
        $hours = intval($restDays / 3600);
        $restHours = $restDays % 3600;
        
        $mins = intval($restHours / 60);
        $restMins = $restHours % 60;
        
        $formattedCacheLifetime = "";
        
        if ($days > 0)
        {
            $formattedCacheLifetime .= $days . "d ";
        }
        
        if ($hours > 0)
        {
            $formattedCacheLifetime .= $hours . "h ";
        }
        
        if ($mins > 0)
        {
            $formattedCacheLifetime .= $mins . "m ";
        }
        
        if ($restMins > 0)
        {
            $formattedCacheLifetime .= $restMins . "s ";
        }
        //var_dump($formattedCacheLifetime);die;
        return $formattedCacheLifetime;
    }
    
    public function getConfigCollection()
    {
        if (!$this->hasData('config_collection'))
        {
            $this->setConfigCollection($this->getCollection());
        }
        
        return $this->getData('config_collection');
    }
    
    public function getAverageTime()
    {
        return round($this->getData('total_time') / $this->getData('count'), 4) . "s";
    }
    
    public function getTotalTime()
    {
        return round($this->getData('total_time'), 4) . "s";
    }
}