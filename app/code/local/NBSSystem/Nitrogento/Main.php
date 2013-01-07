<?php

class NBSSystem_Nitrogento_Main
{
    private $_observers = array();
    private static $_instance;
    
    private function __construct()
    {
    	
    }
    
    public static function getInstance()
    {
        if (!self::$_instance)
        {
            $instanceName = self::getInstanceName(__CLASS__);
            self::$_instance = new $instanceName();
        }
        
        return self::$_instance;
    }
    
    public static function getInstanceName($class)
    {
        $instanceName = $class;
        $rewrites = NBSSystem_Nitrogento_Helper_Data::getSimpleConfig()->getNode('global/nitrogento/rewrites');
        
        if ($rewrites && is_array($rewrites->asArray()))
        {
            foreach ($rewrites->asArray() as $rewrited => $rewrite)
            {
                if (strtolower($class) == $rewrited && isset($rewrite['to']))
                {
                    $instanceName = $rewrite['to'];
                }
            }
        }
        
        return $instanceName;
    }
    
    public static function init()
    {
    	Mage::register('is_in_nitrogento_render_page_area', true);
    	
    	if (defined('COMPILER_INCLUDE_PATH')
    	 && file_exists(COMPILER_INCLUDE_PATH . DIRECTORY_SEPARATOR . '__checkout.php'))
    	{
    		@unlink(COMPILER_INCLUDE_PATH . DIRECTORY_SEPARATOR . '__checkout.php');
    	}
    	
    	return self::getInstance()->_loadObservers();
    }
    
    public function renderPage()
    {
        NBSSystem_Nitrogento_Model_Cache_Fullpage_Renderer_Factory::getInstance()->buildRenderer()->renderPage();
        Mage::unregister('is_in_nitrogento_render_page_area');
    }
    
    protected function _loadObservers()
    {
        $events = NBSSystem_Nitrogento_Helper_Data::getSimpleConfig()->getNode('global/nitrogento/events');
        
        if ($events && is_array($events->asArray()))
        {
            foreach ($events->asArray()  as $eventName => $observersContainer)
            {
                foreach ($observersContainer  as $observers)
                {
                    foreach ($observers  as $observer)
                    {
                        if (isset($observer['class']) && isset($observer['method']))
                        {
                            $observerClass = $observer['class'];
                            $this->_addObserver($eventName, new $observerClass(array('callback' => $observer['method'])));
                        }
                    }
                }
            }
        }
        
        return $this;
    } 
    
    protected function _addObserver($eventName, Varien_Event_Observer $observer)
    {
        if (!isset($this->_observers[$eventName]))
        {
            $this->_observers[$eventName] = array();
        }
        
        $this->_observers[$eventName][] = $observer;
    }
    
    public function dispatchEvent($eventName, $args = array())
    {
        if (!isset($this->_observers[$eventName]))
        {
            return;
        }
        
        foreach($this->_observers[$eventName] as $observer)
        {
            $method = $observer->getCallback();
            $observer->$method(new Varien_Object(array_merge($args, array('event' => new Varien_Event($args)))));
        }
    }
}