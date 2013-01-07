<?php
/**
* qikDropShipper Extension
*
* @category   FavoredMinds
* @package    Vendor
* @author     FavoredMinds Solution (geedubya15@gmail.com)
* @copyright  Copyright (c) 2010 FavoredMinds.com (http://www.favoredminds.com)
*/

class FavoredMinds_Vendor_Block_Adminhtml_Statements_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('vendorstatementsGrid');
      $this->setDefaultSort('vendor_statement_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $helper 		= Mage::app()->getHelper('vendor');
      $collection = Mage::getModel('vendor/vendor_statement')->getCollection();
      if ($helper->vendorIsLogged()) {
        $collection->getSelect()->where('vendor_id="' . $helper->getVendorUserId() . '"');
      } else {
        //$collection->getSelect()->group('vendors.vendor_id');
      }
      /*
       ->addExpressionAttributeToSelect( 'total_my_attribute', 'SUM({{my_attribute}})*{{qty_ordered}}', 'my_attribute' );
       *
      */
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
        $hlp = Mage::helper('vendor');
        $baseUrl = $this->getUrl();

        $this->addColumn('vendor_statement_id', array(
            'header'    => $hlp->__('ID'),
            'index'     => 'vendor_statement_id',
            'width'     => 10,
            'type'      => 'number',
        ));

        $this->addColumn('statement_id', array(
            'header'    => $hlp->__('Statement Name'),
            'index'     => 'statement_id',
        ));

        $this->addColumn('order_date_from', array(
            'header' => $hlp->__('Begin Date'),
            'type'      => 'date',
            'index' => 'order_date_from',
            'width'     => 150,
        ));

        $this->addColumn('order_date_to', array(
            'header' => $hlp->__('End Date'),
            'type'      => 'date',
            'index' => 'order_date_to',
            'width'     => 150,
        ));

        $this->addColumn('created_at', array(
            'header'    => $hlp->__('Date/Time Created'),
            'index'     => 'created_at',
            'type'      => 'datetime',
            'width'     => 150,
        ));

        $helper 		= Mage::app()->getHelper('vendor');
        if (!$helper->vendorIsLogged()) {
          $this->addColumn('vendor_id', array(
                  'header'=> Mage::helper('vendor')->__('Vendor Name'),
                  'width' => '100px',
                  'index' => 'vendor_id',
                  'renderer' => new FavoredMinds_Vendor_Block_Vendor_Renderer_Vendor(),
                  'type' => 'options',
                  'options' => FavoredMinds_Vendor_Block_Vendor_Renderer_Vendor::getVendorsArray(),
          ));
        }
        /*

        $this->addColumn('vendor_id', array(
            'header' => $hlp->__('Vendor'),
            'index' => 'vendor_id',
            'type' => 'options',
            'options' => Mage::getSingleton('vendor/source')->setPath('vendors')->toOptionHash(),
        ));
         */

        $this->addColumn('total_comission', array(
            'header'    => $hlp->__('Total Store Commission'),
            'index'     => 'total_comission',
            'type'      => 'price',
            'currency_code' => Mage::app()->getStore()->getBaseCurrency()->getCode(),
        ));
        $this->addColumn('total_payout', array(
            'header'    => $hlp->__('Total Vendor Payout'),
            'index'     => 'total_payout',
            'type'      => 'price',
            'currency_code' => Mage::app()->getStore()->getBaseCurrency()->getCode(),
        ));

        $this->addColumn('status', array(
            'header'    => $hlp->__('Status'),
            'align'     => 'left',
            'width'     => '80px',
            'index'     => 'status',
            'type'      => 'options',
            'options'   => Mage::getSingleton('vendor/statement_status')->getOptionArray(),
        ));

        $helper 		= Mage::app()->getHelper('vendor');
        if (!$helper->vendorIsLogged()) {
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
                    ),
                    array(
                            'caption'   => $hlp->__('Pay'),
                            'url'       => array('base'=> '*/*/pay'),
                            'field'     => 'id'
                    ),
            ),
            'filter'    => false,
            'sortable'  => false,
            'index'     => 'stores',
            'is_system' => true,
    ));
        }



      $this->addRssList('vendor/rss/statements', Mage::helper('catalog')->__('Payment Statements'));
      $this->addExportType('*/*/exportCsv', Mage::helper('vendor')->__('CSV'));
      $this->addExportType('*/*/exportXml', Mage::helper('vendor')->__('XML'));
	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('vendor_statement_id');
        $this->getMassactionBlock()->setFormFieldName('vendors_statements');

        $helper 		= Mage::app()->getHelper('vendor');
        if (!$helper->vendorIsLogged()) {
        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('vendor')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('vendor')->__('Are you sure?')
        ));
        }

        $this->getMassactionBlock()->addItem('download', array(
             'label'    => Mage::helper('vendor')->__('Download'),
             'url'      => $this->getUrl('*/*/massDownload'),
             'confirm'  => Mage::helper('vendor')->__('Are you sure?')
        ));

        $helper 		= Mage::app()->getHelper('vendor');
        if (!$helper->vendorIsLogged()) {
        $this->getMassactionBlock()->addItem('email', array(
             'label'    => Mage::helper('vendor')->__('Email'),
             'url'      => $this->getUrl('*/*/massEmail'),
             'confirm'  => Mage::helper('vendor')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('vendor/statement_status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
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
        }
        return $this;
    }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}