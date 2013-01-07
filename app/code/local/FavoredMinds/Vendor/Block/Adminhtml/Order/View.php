<?php
/**
* qikDropShipper Extension
*
* @category   FavoredMinds
* @package    Vendor
* @author     FavoredMinds Solution (geedubya15@gmail.com)
* @copyright  Copyright (c) 2010 FavoredMinds.com (http://www.favoredminds.com)
*/

	class FavoredMinds_Vendor_Block_Adminhtml_Order_View extends Mage_Adminhtml_Block_Sales_Order_View
	{
		// print vendor order data into javascript script 
		protected function _beforeToHtml()
		{
			$helper 	= Mage::app()->getHelper('vendor');
			
			$order		= $this->getOrder();
			$items		= $order->getItemsCollection();
			
			/*
				tableData structure
				
				array(
					vendor_id => array(
						vendor_data		=> array(vendor data retrieved using getVendorUserInfo),
						items			=> array(
							0 => Model: catalog/product ,
							1 => Model: catalog/product ,
							2 => Model: catalog/product ,
							3 => Model: catalog/product ,
							4 => Model: catalog/product ,
							etc
						)
					)
				)
			
			*/
			
			$tableData	= array();
			
			$paymentData	= array("order_id" => $order->getId());
			
			foreach($items->getItems() as $item)
			{
				$itemData	= $item->toArray();
				
				$productId	= $itemData["product_id"];
				
				// load the product, get the manufacturer (vendor) and then get the commission for vendor to calculate amount
				$_product		= Mage::getModel('catalog/product')->load($productId);
				$manufacturer	= $_product->getAttributeText("manufacturer");
				
				$vendor_id		= $helper->getVendorByManufacturer($manufacturer);
				
				if($vendor_id)
				{
					$vendorData		= $helper->getVendorUserInfo($vendor_id);
					
					if(!isset($tableData[$vendor_id]))
					{
						$tableData[$vendor_id]	= array(
							"vendor_data"	=> $vendorData,
							"items"			=> array()
						);
					}
					
					$productData				= $_product->toArray();
					$productData["qty_ordered"]	= $itemData["qty_ordered"];
					
					array_push($tableData[$vendor_id]["items"],$productData);
				}
				
				
			}
			
			$OrderDataJson		= Zend_Json::encode($tableData);
			$paymentDataJson	= Zend_Json::encode($paymentData);
			
			
			
			
			
			echo "
				<script type=\"text/javascript\">
					var base_url = \"".$this->getBaseUrl()."\";
					var paymentData	= $paymentDataJson;
					var orderData	= $OrderDataJson;
				</script>";
			
			return parent::_beforeToHtml();
		}
	}
?>