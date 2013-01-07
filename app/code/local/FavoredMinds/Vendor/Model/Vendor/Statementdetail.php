<?php

/**
 * qikDropShipper Extension
 *
 * @category   FavoredMinds
 * @package    Vendor
 * @author     FavoredMinds Solution (geedubya15@gmail.com)
 * @copyright  Copyright (c) 2010 FavoredMinds.com (http://www.favoredminds.com)
 */
class FavoredMinds_Vendor_Model_Vendor_Statementdetail extends Mage_Core_Model_Abstract {
    /* protected $_eventPrefix = 'favoredminds_vendor_statement';
      protected $_eventObject = 'statement'; */

    const ALL_ORDER = "all_orders";
    const ORDER_COMPLETE = "completed_orders";
    const VENDOR_ORDER_COMPLETE = "vendor_completed_orders";
    protected function  _construct() {
        $this->_init('vendor/vendor_statementdetail');
        parent::_construct();
    }
    public function saveStatementDetail($statementId) {
        
        $core = Mage::helper('core');
        $_helper = Mage::helper('vendor');
    
        $helper = Mage::getStoreConfig('vendor');	

        $status = $helper['sales']['order_calculated'];
        
        if(empty($statementId)){
            return false;
        }
        
        $statementObj = Mage::getModel('vendor/vendor_statement')->load($statementId);
        $statementInfo = $statementObj->getData();
        $date_from = $statementInfo['order_date_from'];
        $date_to = $statementInfo['order_date_to'];
        $vendor_id = $statementInfo['vendor_id'];
        
        $vendor = Mage::getModel("vendor/vendor")->load($vendor_id);
        $resource = Mage::getSingleton('core/resource');
        $read = $resource->getConnection('catalog_read');
        
        if(empty($statementObj)){
            return false;
        }
        else{
            if($status==self::ALL_ORDER){
                
                $select = $read->select()
                    ->from(array('sfoi' => $_helper->getTableName('sales_flat_order_item')), array('item_id as shipment_id', 'sfoi.*'))
                    ->join(array('main_table' => $_helper->getTableName('sales_flat_order')), 'main_table.entity_id=sfoi.order_id', array('main_table.created_at as order_created_at',"main_table.entity_id",'shipping_incl_tax","total_qty_ordered'))
                    ->join(array('pei' => $_helper->getTableName('catalog_product_entity_int')), 'pei.entity_id=sfoi.product_id', array())
                    ->joinNatural(array('ea' => $_helper->getTableName('eav_attribute')))
                    ->join(array('vendors' => $_helper->getTableName('vendors')), 'vendors.vendor_code=pei.value', array('vendors.*'))
                    ->where('ea.attribute_code="manufacturer"')
                    ->where('cast(main_table.created_at as date) >= cast("' . $date_from . '" as date)')
                    ->where('cast(main_table.created_at as date) <= cast("' . $date_to . '" as date)')
                    ->where('vendors.vendor_id = "' . $vendor_id . '"')
                    ->order('main_table.entity_id', 'asc');
                    

            }
            
            elseif($status==self::ORDER_COMPLETE){
                
                $select = $read->select()
                ->from(array('sfoi' => $_helper->getTableName('sales_flat_order_item')), array('item_id as shipment_id', 'sfoi.*'))
                ->join(array('main_table' => $_helper->getTableName('sales_flat_order')), 'main_table.entity_id=sfoi.order_id', array('main_table.created_at as order_created_at',"main_table.entity_id","shipping_incl_tax","total_qty_ordered"))
                ->join(array('pei' => $_helper->getTableName('catalog_product_entity_int')), 'pei.entity_id=sfoi.product_id', array())
                ->joinNatural(array('ea' => $_helper->getTableName('eav_attribute')))
                ->join(array('vendors' => $_helper->getTableName('vendors')), 'vendors.vendor_code=pei.value', array('vendors.*'))
                ->join(array('vsi' => $_helper->getTableName('vendors_sales_items')), 'sfoi.item_id=vsi.item_id', array())
                ->join(array('vsh' => $_helper->getTableName('vendors_sales_history')), 'vsh.vendor_sales_item_id = vsi.vendor_sales_item_id', array('sum(vsh.adjustment) as total_adjustment', 'max(vendor_sales_history_id) as current_status_id'))
                ->where('ea.attribute_code="manufacturer"')
                ->where('cast(main_table.created_at as date) >= cast("' . $date_from . '" as date)')
                ->where('cast(main_table.created_at as date) <= cast("' . $date_to . '" as date)')
                ->where('vendors.vendor_id = "' . $vendor_id . '"')
                ->where('main_table.status = "complete"')
                ->order('main_table.entity_id', 'asc')
                ->group('vsi.item_id');          
            }  
            elseif($status==self::VENDOR_ORDER_COMPLETE){
                $select = $read->select()
                ->from(array('sfoi' => $_helper->getTableName('sales_flat_order_item')), array('item_id as shipment_id', 'sfoi.*'))
                ->join(array('main_table' => $_helper->getTableName('sales_flat_order')), 'main_table.entity_id=sfoi.order_id', array('main_table.created_at as order_created_at',"main_table.entity_id","shipping_incl_tax","total_qty_ordered"))
                ->join(array('pei' => $_helper->getTableName('catalog_product_entity_int')), 'pei.entity_id=sfoi.product_id', array())
                ->joinNatural(array('ea' => $_helper->getTableName('eav_attribute')))
                ->join(array('vendors' => $_helper->getTableName('vendors')), 'vendors.vendor_code=pei.value', array('vendors.*'))
                ->join(array('vs' => $_helper->getTableName('vendors_sales')), 'vendors.vendor_id=vs.vendor_id and vs.order_real_id = `sfoi`.order_id', array('vs.*'))
                ->join(array('vsh' => $_helper->getTableName('vendors_sales_history')), 'vs.vendorsales_id=vsh.vendorsales_id', array('vsh.*'))
                ->where('ea.attribute_code="manufacturer"')
                ->where('cast(main_table.created_at as date) >= cast("' . $date_from . '" as date)')
                ->where('cast(main_table.created_at as date) <= cast("' . $date_to . '" as date)')
                ->where('vendors.vendor_id = "' . $vendor_id . '"')
                ->where('vsh.status = "complete"')
                ->order('main_table.entity_id', 'asc')
                ->group('main_table.entity_id')
                ->group('sfoi.item_id');
                
            }
            
            
            $shipments = $read->fetchAll($select);   
           
            
            foreach ($shipments as $shipment) {
                
                
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
                $order['id'] = $shipment['entity_id'];
                $qty = $shipment['qty_ordered'] - $shipment['qty_canceled'] - $shipment['qty_refunded'];
                $order['subtotal'] = $shipment['price'] * $qty;
                $order['items'] = $qty;
                
                
                //new 
                $order['shipping'] = (floatval($shipment['shipping_incl_tax']) / $shipment['total_qty_ordered']) * $qty;
                
                
                
                //$order['shipping'] = $shipping * $qty; old
                $order['tax'] = 0;
                $order['handling'] = 0;
                $order['comission_percent'] = $vendor->getCommission();
                $order['comission_percent'] *= 1;
                $order['comission_amount'] = round($order['subtotal'] * $order['comission_percent'] / 100, 2);
                $order['total_adjustment'] = $shipment['total_adjustment'];
                $order['total_payout'] = $order['subtotal'] + $order['shipping'] - $order['comission_amount'];

                $order['total_payout'] += $shipment['total_adjustment'];

                $detail_data = array("vendor_statement_id"=>$statementId,
                                    "order_id"=>$order['id'],
                                    "vendor_id"=>$vendor_id,
                                    "total_price"=>($shipment['price'] * $qty),
                                    "total_quantity"=>$qty,
                                    "created_at"=>  date("Y-m-d h:i:s",time()),
                                    "updated_at"=>  date("Y-m-d h:i:s",time())
                                        ); 
                
                $detail_data ['total_payout']   =  $order['total_payout'];
                //echo floatval($order['shipping']);
                //$detail_data ['total_shipping'] = floatval($order['shipping']);
                //$detail_data ['total_shipping'] = floatval(200);
                
                
                
                $orderObj = Mage::getModel('sales/order')->load($order['id']);
                $itemList = $orderObj->getItemsCollection();
                $count = $itemList->count();
                $productSerialize = array();
                $productInfo = array();
                $i = 0;
                if($count > 0){
                    foreach($itemList as $item){

                        if ($product = Mage::getModel('catalog/product')->load($item->getProductId())) {
                            $manufacturer = $product->getManufacturer();
                            if ($manufacturer ==  $vendor->getVendorCode()) {
                                $productInfo[$i]['name'] = $product->getName();
                                $productInfo[$i]['qty'] = $item->getQtyOrdered();
                                $productInfo[$i]['price'] = $item->getPriceInclTax();
                                $i++;

                            }
                        }    


                    }
                    $productSerialize = serialize($productInfo);
                    $detail_data['order_detail_information'] =  $productSerialize;
                }
                
                $detail_data['total_shipping'] = $order['shipping'];
                    
                $this->addData($detail_data);
                
                $this->save();
                $this->unsetData();    
            }                   
        }
    } 
    public function getStatementDetail($statementId) {
        
        $resource = Mage::getSingleton('core/resource');
		$read = $resource->getConnection('core_read');
		$select =  $read->select()
			->from($resource->getTableName('vendors_statements_detailshow'),
             array("order_id","order_detail_information","sum(total_price) as total_price","sum(total_payout) as total_payout","sum(total_quantity) as total_quantity",
                 "sum(total_shipping) as total_shipping"))
			->where("vendor_statement_id = '".$statementId."'")
            ->group("order_id");
            
                
		$result =  $read->fetchAll($select);
        
        return $result;
    }

}