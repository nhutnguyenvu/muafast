<?php
/**
* qikDropShipper Extension
*
* @category   FavoredMinds
* @package    Vendor
* @author     FavoredMinds Solution (geedubya15@gmail.com)
* @copyright  Copyright (c) 2010 FavoredMinds.com (http://www.favoredminds.com)
*/

class FavoredMinds_Vendor_Model_Vendor_Statement extends Mage_Core_Model_Abstract {
  protected $_eventPrefix = 'favoredminds_vendor_statement';
  protected $_eventObject = 'statement';

  protected function _construct() {
    $this->_init('vendor/vendor_statement');
    parent::_construct();
  }

  public function fetchOrders($vId = 0) {
    $core = Mage::helper('core');
    $_helper = Mage::helper('vendor');
    $vendor = $_helper->getVendor($vId);

    $resource = Mage::getSingleton('core/resource');
    $read = $resource->getConnection('catalog_read');

	$helper = Mage::getStoreConfig('vendor');	

	if($helper['sales']['order_calculated'] == 'all_orders') {

		$select = $read->select()
			->from(array('sfoi' => $_helper->getTableName('sales_flat_order_item')), array('item_id as shipment_id', 'sfoi.*'))
			->join(array('main_table' => $_helper->getTableName('sales_flat_order')), 'main_table.entity_id=sfoi.order_id', array('main_table.created_at as order_created_at','shipping_incl_tax','total_qty_ordered'))
			->join(array('pei' => $_helper->getTableName('catalog_product_entity_int')), 'pei.entity_id=sfoi.product_id', array())
			->joinNatural(array('ea' => $_helper->getTableName('eav_attribute')))
			->join(array('vendors' => $_helper->getTableName('vendors')), 'vendors.vendor_code=pei.value', array('vendors.*'))
			->where('ea.attribute_code="manufacturer"')
			->where('cast(main_table.created_at as date) >= cast("' . $this->getOrderDateFrom() . '" as date)')
			->where('cast(main_table.created_at as date) <= cast("' . $this->getOrderDateTo() . '" as date)')
			->where('vendors.vendor_id = "' . $vId . '"')
			->order('main_table.entity_id', 'asc');
		
	}elseif($helper['sales']['order_calculated'] == 'vendor_completed_orders') {
		
		$select = $read->select()
			->from(array('sfoi' => $_helper->getTableName('sales_flat_order_item')), array('item_id as shipment_id', 'sfoi.*'))
			->join(array('main_table' => $_helper->getTableName('sales_flat_order')), 'main_table.entity_id=sfoi.order_id', array('main_table.created_at as order_created_at','shipping_incl_tax','total_qty_ordered'))
			->join(array('pei' => $_helper->getTableName('catalog_product_entity_int')), 'pei.entity_id=sfoi.product_id', array())
			->joinNatural(array('ea' => $_helper->getTableName('eav_attribute')))
			->join(array('vendors' => $_helper->getTableName('vendors')), 'vendors.vendor_code=pei.value', array('vendors.*'))
			->join(array('vs' => $_helper->getTableName('vendors_sales')), 'vendors.vendor_id=vs.vendor_id and vs.order_real_id = `sfoi`.order_id', array('vs.*'))
			->join(array('vsh' => $_helper->getTableName('vendors_sales_history')), 'vs.vendorsales_id=vsh.vendorsales_id', array('vsh.*'))
			->where('ea.attribute_code="manufacturer"')
			->where('cast(main_table.created_at as date) >= cast("' . $this->getOrderDateFrom() . '" as date)')
			->where('cast(main_table.created_at as date) <= cast("' . $this->getOrderDateTo() . '" as date)')
			->where('vendors.vendor_id = "' . $vId . '"')
			->where('vsh.status = "complete"')
			->order('main_table.entity_id', 'asc')
            ->group('main_table.entity_id')
            ->group('sfoi.item_id');

	}else{
		/*
        $select = $read->select()
			->from(array('sfoi' => $_helper->getTableName('sales_flat_order_item')), array('item_id as shipment_id', 'sfoi.*'))
			->join(array('main_table' => $_helper->getTableName('sales_flat_order')), 'main_table.entity_id=sfoi.order_id', array('main_table.created_at as order_created_at'))
			->join(array('pei' => $_helper->getTableName('catalog_product_entity_int')), 'pei.entity_id=sfoi.product_id', array())
			->joinNatural(array('ea' => $_helper->getTableName('eav_attribute')))
			->join(array('vendors' => $_helper->getTableName('vendors')), 'vendors.vendor_code=pei.value', array('vendors.*'))
			->where('ea.attribute_code="manufacturer"')
			->where('cast(main_table.created_at as date) >= cast("' . $this->getOrderDateFrom() . '" as date)')
			->where('cast(main_table.created_at as date) <= cast("' . $this->getOrderDateTo() . '" as date)')
			->where('vendors.vendor_id = "' . $vId . '"')
			->order('main_table.entity_id', 'asc');
        */
		$select = $read->select()
			->from(array('sfoi' => $_helper->getTableName('sales_flat_order_item')), array('item_id as shipment_id', 'sfoi.*'))
			->join(array('main_table' => $_helper->getTableName('sales_flat_order')), 'main_table.entity_id=sfoi.order_id', array('main_table.created_at as order_created_at','shipping_incl_tax','total_qty_ordered'))
			->join(array('pei' => $_helper->getTableName('catalog_product_entity_int')), 'pei.entity_id=sfoi.product_id', array())
			->joinNatural(array('ea' => $_helper->getTableName('eav_attribute')))
			->join(array('vendors' => $_helper->getTableName('vendors')), 'vendors.vendor_code=pei.value', array('vendors.*'))
			->join(array('vsi' => $_helper->getTableName('vendors_sales_items')), 'sfoi.item_id=vsi.item_id', array())
			->join(array('vsh' => $_helper->getTableName('vendors_sales_history')), 'vsh.vendor_sales_item_id = vsi.vendor_sales_item_id', array('sum(vsh.adjustment) as total_adjustment', 'max(vendor_sales_history_id) as current_status_id'))
			->where('ea.attribute_code="manufacturer"')
			->where('cast(main_table.created_at as date) >= cast("' . $this->getOrderDateFrom() . '" as date)')
			->where('cast(main_table.created_at as date) <= cast("' . $this->getOrderDateTo() . '" as date)')
			->where('vendors.vendor_id = "' . $vId . '"')
			->where('main_table.status = "complete"')
			->order('main_table.entity_id', 'asc')
			->group('vsi.item_id');
        
        
        //echo $select->__toString();
        //die;
		
	}

    $orders = array();
    $totals = array('items'=>0, 'subtotal'=>0, 'tax'=>0, 'shipping'=>0, 'handling'=>0, 'comission_amount'=>0, 'total_adjustment'=>0, 'total_payout'=>0,);

    $shipments = $read->fetchAll($select);

	foreach ($shipments as $shipment) {
      
	  //check if the order is completed - check the statuses of all the products
      if($shipment['parent_item_id'] != '') {
		continue;
	  }
      //get the shipping quote
      if ($product = Mage::getModel('catalog/product')->load($shipment['product_id'])) {
        $manufacturer = $product->getManufacturer();
        if ($manufacturer == $vendor->getVendorCode()) {
            $shipping = $product->getShippingCost();
            
        }
      }
     
      $order = array();
      $order['shipment_id'] = $shipment['shipment_id'];
      $order['date'] = $shipment['order_created_at'];
      $order['id'] = $shipment['order_id'];
      $qty = $shipment['qty_ordered'] - $shipment['qty_canceled'] - $shipment['qty_refunded'];
        
      $order['subtotal'] = $shipment['price'] * $qty;
      $order['items'] = $qty;
      //new
      $order['shipping'] = (floatval($shipment['shipping_incl_tax']) / $shipment['total_qty_ordered']) * $qty;
      
      //old
      //$order['shipping'] = $shipping * $qty;
      
      $order['tax'] = 0;
      $order['handling'] = 0;
      $order['comission_percent'] = $vendor->getCommission();
      $order['comission_percent'] *= 1;
      $order['comission_amount'] = round($order['subtotal'] * $order['comission_percent'] / 100, 2);
      $order['total_adjustment'] = $shipment['total_adjustment'];
      $order['total_payout'] = $order['subtotal'] + $order['shipping'] - $order['comission_amount'];
      $order['total_payout'] += $shipment['total_adjustment'];

      Mage::dispatchEvent('favoredminds_vendor_statement_row', array(
              'shipment'=>$shipment,
              'order'=>&$order
      ));

      foreach (array_keys($totals) as $k) {
        $totals[$k] += $order[$k];
        if ($k != 'items') {
          $order[$k] = $core->formatPrice($order[$k], false);
        }
      }

      $orders[$shipment['entity_id']] = $order;
    }

    $this->setTotalOrders(sizeof($orders));
    $this->setTotalComission($totals['comission_amount']);
    $this->setTotalPayout($totals['total_payout']);

    foreach ($totals as $key => &$total) {
      if ($key != 'items'){
          $total = $core->formatPrice($total, false);
      }
    }
    unset($total);

    $this->setOrdersData(Zend_Json::encode(compact('orders', 'totals')));
    return $this;
  }

  public function send() {
    $hlp = Mage::helper('vendor');
    $core = Mage::helper('core');
    $_helper = Mage::helper('vendor');
    $vendor = $_helper->getVendorUserInfo($this->getVendorId());
    $data = array();

    $statement = Mage::getModel('vendor/statement_pdf_statement')->before()->addStatement($this)->after()->getPdf();

    $data['_ATTACHMENTS'][] = array(
            'content'    => $statement->render(),
            'filename'   => $this->getStatementFilename(),
            'type'       => 'application/x-pdf',
    );

    $data += array(
            'statement'  => $this,
            'vendor'     => $vendor,
            'company_name'     => $vendor['company_name'],
            'store'      => Mage::app()->getDefaultStoreView(),
            'store_name'      => Mage::app()->getDefaultStoreView(),
            'date_from'  => $core->formatDate($this->getOrdersDateFrom()),
            'date_to'    => $core->formatDate($this->getOrdersDateTo()),
    );

    $template = Mage::getStoreConfig('vendor/email/statement_template');
    $mailTemplate 	= Mage::getModel('core/email_template');
    $mailTemplate->setDesignConfig(array('area' => 'frontend'))
          ->sendTransactional(
            $template,
            array(
            "name"	=> Mage::getStoreConfig('vendor/email/sendername'),
            "email"	=> Mage::getStoreConfig('vendor/email/senderaddress')
            ),
            $vendor['email'],
            $vendor['company_name'],
            $data
    );

    if (!$this->getEmailSent()) {
      $this->setEmailSent(1)->save();
    }

    return $this;
  }
}