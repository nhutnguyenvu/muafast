<?php
/**
* qikDropShipper Extension
*
* @category   FavoredMinds
* @package    Vendor
* @author     FavoredMinds Solution (geedubya15@gmail.com)
* @copyright  Copyright (c) 2010 FavoredMinds.com (http://www.favoredminds.com)
*/

class FavoredMinds_Vendor_Block_Catalog_Product_Grid extends Mage_Adminhtml_Block_Catalog_Product_Grid {

  protected function _prepareColumns() {
    $this->addColumn('entity_id',
            array(
            'header'=> Mage::helper('catalog')->__('ID'),
            'width' => '50px',
            'type'  => 'number',
            'index' => 'entity_id',
    ));
    $this->addColumn('name',
            array(
            'header'=> Mage::helper('catalog')->__('Name'),
            'index' => 'name',
    ));

    $store = $this->_getStore();
    if ($store->getId()) {
      $this->addColumn('custom_name',
              array(
              'header'=> Mage::helper('catalog')->__('Name in %s', $store->getName()),
              'index' => 'custom_name',
      ));
    }

    $this->addColumn('type',
            array(
            'header'=> Mage::helper('catalog')->__('Type'),
            'width' => '60px',
            'index' => 'type_id',
            'type'  => 'options',
            'options' => Mage::getSingleton('catalog/product_type')->getOptionArray(),
    ));

    $sets = Mage::getResourceModel('eav/entity_attribute_set_collection')
            ->setEntityTypeFilter(Mage::getModel('catalog/product')->getResource()->getTypeId())
            ->load()
            ->toOptionHash();

    $this->addColumn('set_name',
            array(
            'header'=> Mage::helper('catalog')->__('Attrib. Set Name'),
            'width' => '100px',
            'index' => 'attribute_set_id',
            'type'  => 'options',
            'options' => $sets,
    ));

    $this->addColumn('sku',
            array(
            'header'=> Mage::helper('catalog')->__('SKU'),
            'width' => '80px',
            'index' => 'sku',
    ));

    $store = $this->_getStore();
    $this->addColumn('price',
            array(
            'header'=> Mage::helper('catalog')->__('Price'),
            'type'  => 'price',
            'currency_code' => $store->getBaseCurrency()->getCode(),
            'index' => 'price',
    ));
    /*
    $this->addColumn('qty',
            array(
            'header'=> Mage::helper('catalog')->__('Qty'),
            'width' => '100px',
            'type'  => 'number',
            'index' => 'qty',
    ));
     *
     */
    /*
   $this->addColumn('is_qty', array(
            'header'=>Mage::helper('vendor')->__('Danh Sách sản phẩm (C=Còn hàng,H=hết hàng)'),
            'filter'=>false,
               'index'=>'is_qty',
            'renderer'  => 'vendor/adminhtml_catalog_product_qty',
            'align' => 'center',
            'width'     => '200px',

        )); */
//	 print_r(Mage::getSingleton('catalog/product_status')->getOptionArray());
//	FavoredMinds_Vendor_Block_Catalog_Product_Renderer_Vendor::getMannu();

    $helper 		= Mage::app()->getHelper('vendor');
    $is_login = $helper->vendorIsLogged();

    if (!$is_login) {
      $this->addColumn('company_name', array(
              'header'=> Mage::helper('vendor')->__('Vendor'),
              'width' => '100px',
              'index' => 'manufacturer',
              'type' => 'options',
              'options' => FavoredMinds_Vendor_Block_Catalog_Product_Renderer_Vendor::getMannu(),
      ));
    }
	
	/*
	
	 $helper 		= Mage::app()->getHelper('vendor');
    if (!$helper->vendorIsLogged()) {
      $this->addColumn('manufacturer', array(
              'header'=> Mage::helper('vendor')->__('Vendor'),
              'width' => '100px',
              'index' => 'manufacturer',
              'renderer' => new FavoredMinds_Vendor_Block_Catalog_Product_Renderer_Vendor(),
              'type' => 'options',
              'options' => FavoredMinds_Vendor_Block_Catalog_Product_Renderer_Vendor::getVendorsArray(),
      ));
    }
	*/
    $this->addColumn('visibility',
            array(
            'header'=> Mage::helper('catalog')->__('Visibility'),
            'width' => '70px',
            'index' => 'visibility',
            'type'  => 'options',
            'options' => Mage::getModel('catalog/product_visibility')->getOptionArray(),
    ));
    

    
    $this->addColumn('feature',
        array(
        'header'=> Mage::helper('catalog')->__('Hàng Nóng'),
        'width' => '100px',
        'sortable'  => false,
        'index'     => 'featured',
        'type'      => 'options',
        'options'   => Mage::getModel('catalog/product')->getFeaturedArray()
    ));
    
    
    /*
    if(!$is_login){
        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('websites',
              array(
              'header'=> Mage::helper('catalog')->__('Websites'),
              'width' => '100px',
              'sortable'  => false,
              'index'     => 'websites',
              'type'      => 'options',
              'options'   => Mage::getModel('core/website')->getCollection()->toOptionHash(),
            ));
        }
    }
    */

    $this->addColumn('action',
            array(
            'header'    => Mage::helper('catalog')->__('Action'),
            'width'     => '50px',
            'type'      => 'action',
            'getter'     => 'getId',
            'actions'   => array(
                    array(
                            'caption' => Mage::helper('catalog')->__('Edit'),
                            'url'     => array(
                                    'base'=>'*/*/edit',
                                    'params'=>array('store'=>$this->getRequest()->getParam('store'))
                            ),
                            'field'   => 'id'
                    )
            ),
            'filter'    => false,
            'sortable'  => false,
            'index'     => 'stores',
    ));
    
    parent::_prepareColumns();
    unset($this->_columns['websites']);
    return $this;
    
  }
} 