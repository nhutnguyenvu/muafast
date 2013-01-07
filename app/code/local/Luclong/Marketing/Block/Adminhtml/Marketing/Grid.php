<?php

class Luclong_Marketing_Block_Adminhtml_Marketing_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('marketingGrid');
      $this->setDefaultSort('marketing_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('marketing/marketing')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('marketing_id', array(
          'header'    => Mage::helper('marketing')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'marketing_id',
      ));

      $this->addColumn('username', array(
          'header'    => Mage::helper('marketing')->__('Username'),
          'align'     =>'left',
         // 'index'     => 'username',
          'renderer' => 'Luclong_Marketing_Block_Adminhtml_Renderer_User',
      ));
      
      
      $this->addColumn('email', array(
          'header'    => Mage::helper('marketing')->__('Email'),
          'align'     =>'left',
          //'index'     => 'email',
          'renderer' => 'Luclong_Marketing_Block_Adminhtml_Renderer_Email',
      ));
      
      $this->addColumn('face_id', array(
          'header'    => Mage::helper('marketing')->__('Face_id'),
          'align'     =>'left',
          'index'     => 'face_id',
      ));

	  /*
      $this->addColumn('content', array(
			'header'    => Mage::helper('marketing')->__('Item Content'),
			'width'     => '150px',
			'index'     => 'content',
      ));
	  */

      $this->addColumn('status', array(
          'header'    => Mage::helper('marketing')->__('Status'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'status',
          'type'      => 'options',
          'options'   => array(
              1 => 'Enabled',
              2 => 'Disabled',
			  3 =>'Not Accept',
          ),
      ));
	  
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('marketing')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('marketing')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
		
		$this->addExportType('*/*/exportCsv', Mage::helper('marketing')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('marketing')->__('XML'));
	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('marketing_id');
        $this->getMassactionBlock()->setFormFieldName('marketing');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('marketing')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('marketing')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('marketing/status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('marketing')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('marketing')->__('Status'),
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