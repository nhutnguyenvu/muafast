<?php

class NBSSystem_Nitrogento_Helper_Url extends Mage_Core_Helper_Abstract
{
    private static $request;
    
    public static function getRequest()
    {
        if (!self::$request)
        {
            self::$request = new Zend_Controller_Request_Http();
        }
        
        return self::$request;
    }
    
    public static function getPathInfo()
    {
        if (self::getRequest()->getPathInfo() == "/")
        {
            return '';
        }
        
        return self::getRequest()->getPathInfo();
    }
    
    public static function getQueryString()
    {
        return self::getRequest()->getServer('QUERY_STRING');
    }
    
    public static function getRequestUri()
    {
        return self::getRequest()->getRequestUri();
    }
    
    public static function getServerName()
    {
        return self::getRequest()->getServer('SERVER_NAME');
    }
    
    public static function getCurrentUrl($queryStringFilters = '')
    {
        $request = self::getRequest();
        
        $port = $request->getServer('SERVER_PORT');
        if ($port) 
        {
            $port = (in_array($port, array(80, 443))) ? '' : ':' . $port;
        }
        
        $url = $request->getScheme() . '://' . $request->getHttpHost() . $port . self::getRequestUri();
        $url = (!empty($queryStringFilters)) ? self::_applyQueryStringFiltersToCurrentUrl($url, $queryStringFilters) : $url;
        return $url;
    }
    
    private static function _applyQueryStringFiltersToCurrentUrl($url, $queryStringFilters)
    {
        if ($queryStringFilters == '*')
        {
            $url = str_replace('?' . self::getQueryString(), '', $url);
        }
        elseif (is_array($queryStringFilters))
        {
            $url = str_replace('?' . self::getQueryString(), '', $url);
            $get = $_GET;
            
            foreach ($queryStringFilters as $queryStringFilter)
            {
                unset($get[$queryStringFilter]);
            }
            
            if (count($get) > 0)
            {
                $url .= '?';
                foreach ($get as $key => $value)
                {
                    $url .= $key . '=' . $value . '&';
                }
                
                $url = substr($url, 0, -1); 
            }
        }
        
        return $url;
    }
}