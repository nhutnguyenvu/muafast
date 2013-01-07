<?php

class NBSSystem_Nitrogento_Model_Cache_Blockhtml_Observer extends Varien_Object
{
    protected function _construct()
    {
        $this->setLearningModeEnabled(Mage::getStoreConfig('nitrogento/cache_blockhtml/learning_mode'));
        $this->setCacheBlockhtmlEnabled(Mage::helper("nitrogento")->isCacheBlockhtmlEnabled());
        
        if ($this->getData('cache_blockhtml_enabled'))
        {
            Mage::getSingleton('nitrogento/cache_blockhtml_config')->populateCurrentInfos();
        }
    }
    
    public function decidePutBlockInCache($observer)
    {
        // If block cache is disabled -> exit
        if (!$this->getData('cache_blockhtml_enabled'))
        {
            return;
        }
        
        $currentBlock = $observer->getEvent()->getBlock();
        $cacheBlockhtmlConfig = Mage::getSingleton('nitrogento/cache_blockhtml_config');
        
        if ($cacheBlockhtmlConfig->tryBlockMatchWithCacheBlockhtmlConfig($currentBlock))
        {
            // Populate block with cache blockhtml config properties
            $currentBlock->setCacheBlockhtmlConfig($cacheBlockhtmlConfig);
            $cacheHelper = Mage::helper($cacheBlockhtmlConfig->getHelperClass());
            
            if ($cacheHelper->isBlockCachable($currentBlock))
            {
                $currentBlock->addData(array(
                    'cache_lifetime' => $cacheBlockhtmlConfig->getCacheLifetime(),
                    'cache_tags' => $cacheHelper->buildCacheTags($currentBlock),
                    'cache_key' => NBSSystem_Nitrogento_Helper_Data::formatCacheKey($cacheHelper->buildCacheKey($currentBlock))
                ));
            }
        }
    }
    
    public function addBlockInLearningMode($observer)
    {
        if (!$this->getData('learning_mode_enabled'))
        {
            return;
        }
        
        // Retrieve current block
        $currentBlock = $observer->getEvent()->getBlock();
        Varien_Profiler::stop(Mage::helper('nitrogento')->getBlockTimerKey($currentBlock));
        
        if (!($currentBlock instanceof Mage_Core_Block_Template))
        {
            return;
        }
        
        $cacheBlockhtmlLearning = Mage::getModel('nitrogento/cache_blockhtml_learning');
        $cacheBlockhtmlLearning->populateCurrentInfos();
        $cacheBlockhtmlLearning->addData(array(
            'block_class' => get_class($currentBlock),
            'block_template' => $currentBlock->getTemplate()
        ));
        
        $collection = $cacheBlockhtmlLearning->getCollection()
            ->addLearningFieldsToFilter($cacheBlockhtmlLearning)
            ->load();
        
        $timers = Varien_Profiler::getTimers();
        $currentBlockTimer = $timers[Mage::helper('nitrogento')->getBlockTimerKey($currentBlock)];
        
        if (count($collection) == 0)
        {
            $cacheBlockhtmlLearning->setCount(1)->setTotalTime($currentBlockTimer['sum'])->save();
        }
        else
        {
            $cacheBlockhtmlLearning->addData($collection->getFirstItem()->getData());
            $cacheBlockhtmlLearning->setCount($collection->getFirstItem()->getCount() + 1)
                ->setTotalTime($currentBlockTimer['sum'] + $collection->getFirstItem()->getTotalTime())
                ->save();
        }
    }
    
    public function startTimer($observer)
    {
        if ($this->getData('learning_mode_enabled'))
        {
            Varien_Profiler::enable();
            Varien_Profiler::start(Mage::helper('nitrogento')->getBlockTimerKey($observer->getEvent()->getBlock()));
        }
    }
}