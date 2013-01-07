<?php

class NBSSystem_Nitrogento_Block_Adminhtml_Cache_Blockhtml_Learning_Grid extends NBSSystem_Nitrogento_Block_Adminhtml_Cache_Common_Grid
{
    public function __construct()
    {
        parent::__construct();
        // This is the primary key of the database
        $this->setDefaultSort('total_time');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }
    
    protected function _prepareCollection()
    {
        $collection = Mage::getSingleton('nitrogento/cache_blockhtml_learning')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    
    protected function _prepareColumns()
    {
        $this->addColumn('block_class', array(
            'header'    => Mage::helper('nitrogento')->__('Block Class'),
            'align'     => 'left',
            'index'     => 'block_class',
        ));
        
        $this->addColumn('block_template', array(
            'header'    => Mage::helper('nitrogento')->__('Block Template'),
            'align'     => 'left',
            'index'     => 'block_template',
        ));
                
        $this->addColumn('count', array(
            'header'    => Mage::helper('nitrogento')->__('Count'),
            'align'     => 'left',
            'index'     => 'count',
        ));
        
        $this->addColumn('average_time', array(
            'header'    => Mage::helper('nitrogento')->__('Average Time'),
            'align'     => 'left',
            'getter'    => 'getAverageTime'
        ));
        
        $this->addColumn('total_time', array(
            'header'    => Mage::helper('nitrogento')->__('Total Time'),
            'align'     => 'left',
            'index'     => 'total_time',
            'getter'    => 'getTotalTime'
        ));
        
        $this->addColumn('store_id', array(
            'header'        => Mage::helper('cms')->__('Store View'),
            'index'         => 'store_id',
            'type'          => 'store',
            'store_all'     => true,
            'store_view'    => true,
            'sortable'      => false
        ));
        
        return parent::_prepareColumns();
    }
    
    /**
     * Add mass-actions to grid
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('learning_id');
        $this->getMassactionBlock()->setFormFieldName('learning_ids');

        $this->getMassactionBlock()->addItem('putInStaticCacheConfig', array(
            'label'         => Mage::helper('nitrogento')->__('Put in Static Cache Config'),
            'url'           => $this->getUrl('*/*/massPutInStaticCacheConfig'),
        ));
        
        $this->getMassactionBlock()->addItem('putInDynamicCacheConfig', array(
            'label'         => Mage::helper('nitrogento')->__('Put in Dynamic Cache Config'),
            'url'           => $this->getUrl('*/*/massPutInDynamicCacheConfig'),
        ));

        return $this;
    }
}