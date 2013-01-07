<?php

class Luclong_Community_Block_Adminhtml_Community_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('communityGrid');
      $this->setDefaultSort('community_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('community/community')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('community_id', array(
          'header'    => Mage::helper('community')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'community_id',
      ));

      $this->addColumn('product_id', array(
          'header'    => Mage::helper('community')->__('Sản phẩm'),
          'align'     =>'left',
          //'index'     => 'product_id',
          'renderer' => 'Luclong_Community_Block_Adminhtml_Renderer_ProductName',
      ));
      
      $this->addColumn('like', array(
          'header'    => Mage::helper('community')->__('Số Like'),
          'align'     =>'left',
          'index'     => 'like',
      ));
      
      $this->addColumn('comment', array(
          'header'    => Mage::helper('community')->__('Số Comment'),
          'align'     =>'left',
          'index'     => 'comment',
      ));
      
      $this->addColumn('share', array(
          'header'    => Mage::helper('community')->__('Số Share'),
          'align'     =>'left',
          'index'     => 'share',
      ));
      
      $this->addColumn('buy', array(
          'header'    => Mage::helper('community')->__('Số Buy'),
          'align'     =>'left',
          'index'     => 'buy',
      ));

	  /*
      $this->addColumn('content', array(
			'header'    => Mage::helper('community')->__('Item Content'),
			'width'     => '150px',
			'index'     => 'content',
      ));
	  */

      $this->addColumn('status', array(
          'header'    => Mage::helper('community')->__('Status'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'status',
          'type'      => 'options',
          'options'   => array(
              1 => 'Enabled',
              2 => 'Disabled',
          ),
      ));
	  
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('community')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('community')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
		
		$this->addExportType('*/*/exportCsv', Mage::helper('community')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('community')->__('XML'));
	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('community_id');
        $this->getMassactionBlock()->setFormFieldName('community');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('community')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('community')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('community/status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('community')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('community')->__('Status'),
                         'values' => $statuses
                     )
             )
        ));
        return $this;
    }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}