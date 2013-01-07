<?php

class NBSSystem_Nitrogento_Block_Adminhtml_Cache_Blockhtml_Config_Grid extends NBSSystem_Nitrogento_Block_Adminhtml_Cache_Common_Grid
{
    public function __construct()
    {
        parent::__construct();
        // This is the primary key of the database
        $this->setDefaultSort('config_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }
    
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('nitrogento/cache_blockhtml_config')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    
    protected function _prepareColumns()
    {
        $this->addColumn('friendly_entry', array(
            'header'    => Mage::helper('nitrogento')->__('Friendly Entry'),
            'align'     => 'left',
            'index'     => 'friendly_entry',
        ));
        
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
                
        $this->addColumn('cache_lifetime', array(
            'header'    => Mage::helper('nitrogento')->__('Cache Lifetime'),
            'align'     => 'left',
            'index'     => 'cache_lifetime',
            'getter'    => 'getFormattedCacheLifetime'
        ));
        
        $this->addColumn('store_id', array(
            'header'        => Mage::helper('cms')->__('Store View'),
            'index'         => 'store_id',
            'type'          => 'store',
            'store_all'     => true,
            'store_view'    => true,
            'sortable'      => false
        ));
        
        $this->addColumn('status', array(
            'header'    => Mage::helper('nitrogento')->__('Actived'),
            'width'     => '120',
            'align'     => 'left',
            'index'     => 'activated',
            'type'      => 'options',
            'options'   => array(0 => Mage::helper('nitrogento')->__('Disabled'), 1 => Mage::helper('nitrogento')->__('Enabled')),
            'frame_callback' => array($this, 'decorateStatus')
        ));
        
        return parent::_prepareColumns();
    }
    
    /**
     * Add mass-actions to grid
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('config_id');
        $this->getMassactionBlock()->setFormFieldName('config_ids');
        
        $this->getMassactionBlock()->addItem('enable', array(
            'label'         => Mage::helper('nitrogento')->__('Enable'),
            'url'           => $this->getUrl('*/*/massEnable'),
        ));
        
        $this->getMassactionBlock()->addItem('disable', array(
            'label'    => Mage::helper('nitrogento')->__('Disable'),
            'url'      => $this->getUrl('*/*/massDisable'),
        ));
        
        $this->getMassactionBlock()->addItem('refresh', array(
            'label'    => Mage::helper('nitrogento')->__('Refresh'),
            'url'      => $this->getUrl('*/*/massRefresh'),
        ));
        
        $this->getMassactionBlock()->addItem('delete', array(
            'label'    => Mage::helper('nitrogento')->__('Delete'),
            'url'      => $this->getUrl('*/*/massDelete'),
        ));
        
        return $this;
    }
}