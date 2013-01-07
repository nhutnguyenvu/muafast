<?php
/**
* qikDropShipper Extension
*
* @category   FavoredMinds
* @package    Vendor
* @author     FavoredMinds Solution (geedubya15@gmail.com)
* @copyright  Copyright (c) 2010 FavoredMinds.com (http://www.favoredminds.com)
*/

class FavoredMinds_Vendor_Block_Adminhtml_Vendors_Grid extends Mage_Adminhtml_Block_Widget_Grid {
  public function __construct() {
    parent::__construct();
    $this->setId('vendorsGrid');
    $this->setDefaultSort('vendor_id');
    $this->setDefaultDir('ASC');
    $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection() {
    $collection = Mage::getModel('vendor/vendor')->getCollection();
    $this->setCollection($collection);
    return parent::_prepareCollection();
  }

  protected function _prepareColumns() {
    $hlp = Mage::helper('vendor');
    $baseUrl = $this->getUrl();

    $this->addColumn('vendor_id', array(
            'header'    => $hlp->__('ID'),
            'index'     => 'vendor_id',
            'width'     => 10,
            'type'      => 'number',
    ));

    $this->addColumn('company_name', array(
            'header'    => $hlp->__('Company'),
            'index'     => 'company_name',
            'type'      => 'text',
            'width'     => 150,
    ));

    $this->addColumn('username', array(
            'header'    => $hlp->__('Username'),
            'index'     => 'username',
            'type'      => 'text',
            'width'     => 150,
    ));

    $this->addColumn('email', array(
            'header'    => $hlp->__('Email'),
            'index'     => 'email',
            'type'      => 'text',
            'width'     => 150,
    ));

    $this->addColumn('firstname', array(
            'header'    => $hlp->__('First Name'),
            'index'     => 'firstname',
            'type'      => 'text',
            'width'     => 150,
    ));

    $this->addColumn('lastname', array(
            'header'    => $hlp->__('Last Name'),
            'index'     => 'lastname',
            'type'      => 'text',
            'width'     => 150,
    ));

    $this->addColumn('state', array(
            'header'    => $hlp->__('State'),
            'index'     => 'state',
            'type'      => 'text',
            'width'     => 150,
    ));

    $this->addColumn('zip', array(
            'header'    => $hlp->__('Zip'),
            'index'     => 'zip',
            'type'      => 'text',
            'width'     => 150,
    ));

    $this->addColumn('salesperweek', array(
            'header'    => $hlp->__('Sales/Week'),
            'index'     => 'salesperweek',
            'type'      => 'text',
            'width'     => 150,
    ));

    $this->addColumn('status', array(
        'header'    => $hlp->__('Status'),
        'align'     => 'left',
        'width'     => '80px',
        'index'     => 'status',
        'type'      => 'options',
        'options'   => Mage::getSingleton('vendor/status')->getOptionArray(),
    ));
    $this->addColumn('on_home', array(
        'header'    => $hlp->__('On Home'),
        'align'     => 'left',
        'width'     => '80px',
        'index'     => 'on_home',
        'type'      => 'options',
        'options'   => Mage::getSingleton('vendor/status')->getOptionHS(),
    ));
    $this->addColumn('on_slide', array(
        'header'    => $hlp->__('On Slide'),
        'align'     => 'left',
        'width'     => '80px',
        'index'     => 'on_slide',
        'type'      => 'options',
        'options'   => Mage::getSingleton('vendor/status')->getOptionHS(),
    ));

    $this->addColumn('action',
            array(
            'header'    =>  $hlp->__('Action'),
            'width'     => '100',
            'type'      => 'action',
            'getter'    => 'getId',
            'actions'   => array(
                    array(
                            'caption'   => $hlp->__('View'),
                            'url'       => array('base'=> '*/*/edit'),
                            'field'     => 'id'
                    )
            ),
            'filter'    => false,
            'sortable'  => false,
            'index'     => 'stores',
            'is_system' => true,
    ));

    $this->addRssList('vendor/rss/newvendors', Mage::helper('catalog')->__('New Vendors'));
    $this->addExportType('*/*/exportCsv', Mage::helper('vendor')->__('CSV'));
    $this->addExportType('*/*/exportXml', Mage::helper('vendor')->__('XML'));

    return parent::_prepareColumns();
  }

  protected function _prepareMassaction() {
    $this->setMassactionIdField('vendor_id');
    $this->getMassactionBlock()->setFormFieldName('vendor');

    $this->getMassactionBlock()->addItem('delete', array(
            'label'    => Mage::helper('vendor')->__('Delete'),
            'url'      => $this->getUrl('*/*/massDelete'),
            'confirm'  => Mage::helper('vendor')->__('Are you sure?')
    ));

    $statuses = Mage::getSingleton('vendor/status')->getOptionArray();

    //array_unshift($statuses, array('label'=>'', 'value'=>''));
    $this->getMassactionBlock()->addItem('status', array(
            'label'=> Mage::helper('vendor')->__('Change status'),
            'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
            'additional' => array(
                    'visibility' => array(
                            'name' => 'status',
                            'type' => 'select',
                            'class' => 'required-entry',
                            'label' => Mage::helper('vendor')->__('Status'),
                            'values' => $statuses
                    )
            )
    ));
    return $this;
  }

  public function getRowUrl($row) {
    return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}