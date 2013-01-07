<?php

/**
 * qikDropShipper Extension
 *
 * @category   FavoredMinds
 * @package    Vendor
 * @author     FavoredMinds Solution (geedubya15@gmail.com)
 * @copyright  Copyright (c) 2010 FavoredMinds.com (http://www.favoredminds.com)
 */
class FavoredMinds_Vendor_Block_Adminhtml_Sales_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('vendorsalesGrid');
        //$this->setId('entity_idGrid');   
        
        $this->setDefaultSort('real_order_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setTemplate("vendor/widget/grid.phtml");
    }

    public function getFilterData() {
        //return Mage::registry('filter_data');
        
        $requestData = Mage::helper('adminhtml')->prepareFilterString($this->getRequest()->getParam('filter'));
        //var_dump($requestData);
        //die;
        //echo '<pre>';
        //print_r($requestData);
        //exit;
        //$requestData = $this->_filterDates($requestData, array('from', 'to'));
        $params = new Varien_Object();

        foreach ($requestData as $key => $value) {
            if (!empty($value)) {
                $params->setData($key, $value);
            }
        }

        //print_r($params);
        //exit;

        return $params;
    }
    public function setDefaultDir($dir="DESC")
    {
        $helper = Mage::app()->getHelper('vendor');
        $is_login = $helper->vendorIsLogged();
        if($is_login){
            return "DESC";
            $this->_defaultDir = "DESC";
        }
        $this->_defaultDir = $dir;
        return $this;
        
    }
    protected function _prepareCollection() {
        
        
        $helper = Mage::app()->getHelper('vendor');
        //we select the data here
        
        $collection = Mage::getModel('sales/order')->getCollection();
        
        $columnId = $this->getParam($this->getVarNameSort(), $this->_defaultSort);
        $dir      = $this->getParam($this->getVarNameDir(), $this->_defaultDir);
        
        if ($helper->vendorIsLogged()){
            $collection->getSelect()->reset()->from(            
                array("main_table"=>$helper->getTableName('sales_flat_order'))
                ,array("main_table.increment_id",
                    "main_table.entity_id",
                    "main_table.shipping_description",
                    
                    "created_at"=>"main_table.created_at",
                    "main_table.entity_id",
                    "item_number"=>"count(case product_type when 'simple' then 1 else null end)",
                    "tax_shipping"=>"(main_table.shipping_incl_tax/main_table.total_qty_ordered) * sum(Case 
                                                                                                When product_type = 'simple' Then oi.qty_ordered  
                                                                                                Else 0 End )",   
                    "total_price"=>"sum((oi.qty_ordered)*(oi.price))",
                    "main_table.status",
                ))
                ->join(array('oi' => $helper->getTableName('sales_flat_order_item')), 'oi.order_id=main_table.entity_id',
                array())
                ->join(array('pei' => $helper->getTableName('catalog_product_entity_int')), 'pei.entity_id=oi.product_id', array())
                ->joinNatural(array('ea' => $helper->getTableName('eav_attribute')),array())
                ->join(array('vendors' => $helper->getTableName('vendors')), 'vendors.vendor_code=pei.value', array('vendors.company_name'))
                ->where('ea.attribute_code="manufacturer"')
                ->where('vendors.vendor_id="' . $helper->getVendorUserId() . '"')
                
                ->group('main_table.entity_id')
                ->group('vendors.vendor_id');   
            /*
            $collection2 = Mage::getModel('sales/order')->getCollection();
            $collection2->getSelect()->reset()->from(            
                array("main_table"=>$helper->getTableName('sales_flat_order'))
                ,array("main_table.increment_id",
                    
                ))
                ->join(array('oi' => $helper->getTableName('sales_flat_order_item')), 'oi.order_id=main_table.entity_id',
                array("total_qty"=>"sum(oi.qty_ordered)"))
                ->join(array('pei' => $helper->getTableName('catalog_product_entity_int')), 'pei.entity_id=oi.product_id', array())
                ->joinNatural(array('ea' => $helper->getTableName('eav_attribute')),array())
                ->join(array('vendors' => $helper->getTableName('vendors')), 'vendors.vendor_code=pei.value', array('vendors.company_name'))
                ->where('ea.attribute_code="manufacturer"')
                ->where('vendors.vendor_id="' . $helper->getVendorUserId() . '"')
                ->where('oi.product_type="simple"')
                ->group('main_table.entity_id')
                ->group('vendors.vendor_id');
            */
            //$varient = new Varien_Object();
            //setDataToAll($key, $value=null)
            //$varient->addData(array("tax_shipping"=>2));
            
            //Varien
            /*
            foreach($collection2 as $col){
                $data = $col->getData();
                var_dump($data['total_qty']);
                
                $shipping_incl_tax/main_table.total_qty_ordered
                $collection->setDataToAll(array("tax_shipping"=>$data['total_qty'] * ));
            }
            */
            
                //echo $collection->getSelect()->__toString();
                //die;
        }
        else{
            
            $collection = Mage::getModel('sales/order')->getCollection();
            $collection->getSelect()
            ->join(array('oi' => $helper->getTableName('sales_flat_order_item')), 'oi.order_id = main_table.entity_id', array())
            ->join(array('pei' => $helper->getTableName('catalog_product_entity_int')), 'pei.entity_id=oi.product_id', array())
            ->joinNatural(array('ea' => $helper->getTableName('eav_attribute'),array()))
            ->join(array('vendors' => $helper->getTableName('vendors')), 'vendors.vendor_code=pei.value', array('vendors.company_name'))
            ->where('ea.attribute_code="manufacturer"')
            ->group('main_table.entity_id');
        }
        
        //thissetCountTotals($count=true)
        //$this->setCountTotals(42);
        
        /*
        echo $collection->getSelect()->__toString();
        die;*/
        
        
        /*
        if ($helper->vendorIsLogged()) {
            $collection->getSelect()->where('vendors.vendor_id="' . $helper->getVendorUserId() . '"');
        } else {
            //$collection->getSelect()->group('vendors.vendor_id');
        }
        */
        //echo count($collection);
        
        $this->setCollection($collection);
        

        return parent::_prepareCollection();
    }
    /*
    public function getCollection(){
        echo $this->_collection->getSelect()->__toString();
        die;
    }*/
    
    public function setStatusFilter($status) {
        $this->getCollection()->getSelect()
                ->where("main_table.status = '" . $status . "'");
        return $this;
    }
    protected function _setFilterValues($data)
    {
      
        if(isset($data['total_price'])){
            
            if(isset($data['total_price']['from'])){
                //$from = $data['total_price']['from'];
                //$this->getCollection()->getSelect()->having("total_price >= 2");
                //echo $this->getCollection()->getSelect()->__toString();
                 
            }
            
            if(isset($data['total_price']['to'])){
                //$to = $data['total_price']['to'];
                //$this->getCollection()->getSelect()->having("total_price"=>$to));
            }
            //unset($data['total_price']);
           
            //die;
            
             
        }
    
        
        foreach ($this->getColumns() as $columnId => $column) {
            //echo $columnId;
            
            if (isset($data[$columnId])
                && (!empty($data[$columnId]) || strlen($data[$columnId]) > 0)
                && $column->getFilter()
            ) {
                //echo $columnId;
                
                $column->getFilter()->setValue($data[$columnId]);
                $this->_addColumnFilterToCollection($column);
            }
           
        }
        
        return $this;
    }
   

    protected function _prepareColumns() {
        
        $helper = Mage::app()->getHelper('vendor');
        
        if($helper->vendorIsLogged()){
            
            $this->addColumn('real_order_id', array(
                'header' => Mage::helper('vendor')->__('Order #'),
                'width' => '80px',
                'type' => 'text',
                'index' => 'increment_id',
            ));
            
            $this->addColumn('created_at', array(
                'header' => Mage::helper('vendor')->__('Order Date'),         
                'index' => 'created_at',
                'filter_index' => 'main_table.created_at', // This parameter helps to resolve 
                'type' => 'datetime',
                'width' => '170px',
            ));
            $this->addColumn('item', array(
                'header'=>Mage::helper('vendor')->__('Danh Sách sản phẩm (C=Còn hàng,H=hết hàng)'),
                'filter'=>false,
                   'index'=>'item',
                'renderer'  => 'vendor/adminhtml_sales_items_items',
                'align' => 'center',
                'width'     => '200px',

            ));
            $this->addColumn('company_name', array(
                'header' => Mage::helper('vendor')->__('Tên cửa hàng'),
                'align' => 'left',
                'index' => 'company_name',
                'width' => '220px',
            ));
            $this->addColumn('count(oi.product_id)', array(
                'header' => Mage::helper('vendor')->__('Số items'),
                'width' => '80px',
                'type' => 'text',
                'index' => 'item_number',
                'filter_index' => 'count(oi.product_id)', // This parameter helps to resolve 
            ));
           
            $this->addColumn('status', array(
                'header' => Mage::helper('vendor')->__('Status'),
                'index' => 'status',
                'filter_index' => 'main_table.status', // This parameter helps to resolve 
                'type' => 'options',
                'width' => '70px',
                'options' => Mage::getSingleton('sales/order_config')->getStatuses(),
            ));
            
            /*
            $this->addColumn('tax_shipping', array(
                'header' => Mage::helper('vendor')->__("Shipping Cost"),
                'type' => 'currency',
                'index' => 'tax_shipping',
                'filter_index'=>'(main_table.shipping_incl_tax/main_table.total_qty_ordered) * (oi.qty_ordered)',
                'width' => '70px',
                'currency_code' => (string) Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE)
            ));
             */
            /*
            $this->addColumn('shipping_description', array(
                'header' => Mage::helper('vendor')->__('Shipping Method'),
                'align' => 'left',
                'index' => 'shipping_description',
            ));*/
            $this->addColumn('total_price', array(
                'header' => Mage::helper('vendor')->__('Thành tiền'),
                'type' => 'currency',
                'index' => 'total_price',
                'filter_index'=>'(oi.qty_ordered)*(oi.price)',
                'currency_code' => (string) Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE)
            ));
            
            $this->addColumn('action', array(
                'header' => Mage::helper('vendor')->__('Action'),
                'width' => '100',
                'type' => 'action',
                'getter' => 'getId',
                'actions' => array(
                    array(
                        'caption' => Mage::helper('vendor')->__('View'),
                        'url' => array('base' => '*/*/edit'),
                        'field' => 'id'
                    )
                ),
                'filter' => false,
                'sortable' => false,
                'index' => 'stores',
                'is_system' => true,
            ));
             

        }
        else{
             $this->addColumn('real_order_id', array(
                'header' => Mage::helper('vendor')->__('Order #'),
                'width' => '80px',
                'type' => 'text',
                'index' => 'increment_id',
            ));

            $this->addColumn('created_at', array(
                'header' => Mage::helper('vendor')->__('Order Date'),
                'index' => 'created_at',
                'filter_index' => 'main_table.created_at', // This parameter helps to resolve 
                'type' => 'datetime',
                'width' => '170px'
            ));

            $this->addColumn('company_name', array(
                'header' => Mage::helper('vendor')->__('Tên cửa hàng'),
                'align' => 'left',
                'index' => 'company_name',
                'width' => '220px',
            ));

            $this->addColumn('total_item_count', array(
                'header' => Mage::helper('vendor')->__('# Số mẫu'),
                'align' => 'left',
                'index' => 'total_item_count',
                'width' => '100px',
            ));

            $this->addColumn('shipping_incl_tax', array(
                'header' => Mage::helper('vendor')->__('Shipping Cost'),
                'index' => 'shipping_incl_tax',
                'type' => 'currency',
                'currency' => 'order_currency_code',
            ));

            $this->addColumn('shipping_description', array(
                'header' => Mage::helper('vendor')->__('Shipping Method'),
                'align' => 'left',
                'index' => 'shipping_description',
            ));

            $this->addColumn('status', array(
                'header' => Mage::helper('vendor')->__('Status'),
                'index' => 'status',
                'filter_index' => 'main_table.status', // This parameter helps to resolve 
                'type' => 'options',
                'width' => '70px',
                'options' => Mage::getSingleton('sales/order_config')->getStatuses(),
            ));
            
            $this->addColumn('item', array(
                'header'=>Mage::helper('vendor')->__('Danh Sách sản phẩm (C=Còn hàng,H=hết hàng)'),
                'filter'=>false,
                   'index'=>'item',
                'renderer'  => 'vendor/adminhtml_sales_items_items',
                'align' => 'center',
                'width'     => '200px',

            ));
            $this->addColumn('grand_total', array(
                'header' => Mage::helper('vendor')->__('Order Total'),
                'index' => 'grand_total',
                'type' => 'currency',
                'currency' => 'order_currency_code',
            ));

            $this->addColumn('action', array(
                'header' => Mage::helper('vendor')->__('Action'),
                'width' => '100',
                'type' => 'action',
                'getter' => 'getId',
                'actions' => array(
                    array(
                        'caption' => Mage::helper('vendor')->__('View'),
                        'url' => array('base' => '*/*/edit'),
                        'field' => 'id'
                    )
                ),
                'filter' => false,
                'sortable' => false,
                'index' => 'stores',
                'is_system' => true,
            ));
        }    

        $this->addExportType('*/*/exportCsv', Mage::helper('vendor')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('vendor')->__('XML'));

        $this->addRssList('vendor/rss/orders', Mage::helper('vendor')->__('New Orders'));
        
       

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction() {
        $this->setMassactionIdField('vendorsales_id');
        $this->getMassactionBlock()->setFormFieldName('vendorsales');
        return $this;
    }

    protected function _prepareMassaction1() {
        
        $this->setMassactionIdField('vendorsales_id');
        $this->getMassactionBlock()->setFormFieldName('vendorsales');

        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('vendor')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('vendor')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('vendor/status')->getOptionArray();

        array_unshift($statuses, array('label' => '', 'value' => ''));
        $this->getMassactionBlock()->addItem('status', array(
            'label' => Mage::helper('vendor')->__('Change status'),
            'url' => $this->getUrl('*/*/massStatus', array('_current' => true)),
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