<?php
class Luclong_Community_CronController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	$this->loadLayout();     
		$this->renderLayout();
    }
	
	public function ajaxAction()
    {
    	$link = $this->getRequest()->getParams('link');
		echo $link;
    }
	
	public function checkExist($productId){
		$data = Mage::getModel('community/community')->getCollection()->addFilter('product_id',$productId);;
		if(count($data)>0){
			return true;
		}else{
			return false;
		}
	}
	
	public function checkbeforeUpdate($productId,$likes,$comments,$shares,$ordered_qty){
		$data = Mage::getModel('community/community')->getCollection()->addFilter('product_id',$productId);
		foreach($data as $item){
			if($item->like != $likes || $item->comment != $comments || $item->share != $shares || $item->buy != $ordered_qty){
				return true;
			}else return false;
		}
	}
	
	public function updatelike($productId,$likes,$comments,$shares,$ordered_qty){
		
		if($likes != 0 || $ordered_qty != 0){
			if($this->checkbeforeUpdate($productId,$likes,$comments,$shares,$ordered_qty)){
				$timeNow = now();
				$sql = "update `".Mage::getSingleton('core/resource')->getTableName('community')."` set `like`='".$likes."',`comment`='".$comments."',`share`='".$shares."', `buy`='".$ordered_qty."', `update_time`='".$timeNow."' where product_id=".$productId;
				$connection = Mage::getSingleton('core/resource')->getConnection('core_write');     
				try {
					$connection->query($sql);
					echo $productId." -- update successfull <br>";
				} catch (Exception $e){
					echo $e->getMessage();
				}
			}
		}
	}
	
	public function savelikeAction($link,$productId) {
        
		$url = "http://api.facebook.com/restserver.php?method=links.getStats&urls=".urlencode($link);
		$xml = file_get_contents($url);
		$xml = simplexml_load_string($xml);
		$shares =  $xml->link_stat->share_count;
		$likes =  $xml->link_stat->like_count;
		$comments = $xml->link_stat->comment_count;
		$total = $xml->link_stat->total_count;
		$ordered_qty = Mage::getResourceModel('reports/product_collection')
            ->addOrderedQty()
			->addAttributeToFilter('entity_id',$productId)->getFirstItem()->getData('ordered_qty');
		
		$data['product_id'] = $productId;
		$data['like'] = $likes;
		$data['comment'] = $comments;
		$data['share'] = $shares;
		$data['buy'] = $ordered_qty;
		$data['status'] = 1;
		
		
		echo $productId.'--'.$likes.'--'.$comments.'--'.$shares.'--'.$ordered_qty.'<br>';
		
		if($likes != 0 || $ordered_qty != 0){
			try {
				$model = Mage::getModel('community/community');
				
				if($this->checkExist($productId)){
					$this->updatelike($productId,$likes,$comments,$shares,$ordered_qty);
				}else{
					$community_id = $model->getCommunity_id();
					if ($model->getCreated_time == NULL || $model->getUpdate_time() == NULL) {
						$data['created_time'] = now();
						$data['update_time'] = now();
					} else {
						$data['update_time'] = now();
					}
					$model->setData($data)->save();
					echo $productId.' -- insert successfull <br>';
				}
			} catch (Exception $e){
				echo $e->getMessage();
			}
		}
    }
    
	// update product into cronjob table
    public function runpageAction(){
		
		$total = Mage::getModel('catalog/product')->getCollection();
		$count_page = ceil(count($total)/20);
		if($count_page>1){
			$start = 0; $item_on_page = 20;
			for($i=1;$i<=$count_page;$i++){
				$start = $item_on_page*$i;
				$end = $start+$item_on_page;
				$products = Mage::getModel('catalog/product')->getCollection()
															->setPageSize($item_on_page)
															->setCurPage($i);
				$data = array();
				foreach($products as $prod) {
					$data[] = $prod->getId();
				}

				// Insert to product list table
				$timeNow = now();
				$data_ = implode(',',$data);
				$sql = "Insert into `".Mage::getSingleton('core/resource')->getTableName('product_list')."` (product_id,status,created_time) VALUES('".$data_."','0','".$timeNow."')";
				$connection = Mage::getSingleton('core/resource')->getConnection('core_write');     
				try {
					Mage::log($connection->query($sql),NULL,'cronpage.log');
				} catch (Exception $e){
					echo $e->getMessage();
				}
			}
		}
    }
    
	public function runcronlikeAction(){
		$select = "Select id,product_id,status From `".Mage::getSingleton('core/resource')->getTableName('product_list')."` Where status = 0 ORDER BY id ASC";
		$connection = Mage::getSingleton('core/resource')->getConnection('core_write');     
		try {
			$data = $connection->fetchAll($select);
			if(!empty($data)){
				$itemId = array();
				foreach($data as $item){
					// update processing for id
					if($item['status']==0){
						$sql = "update `".Mage::getSingleton('core/resource')->getTableName('product_list')."` set `status`='1' where id=".$item['id'];
						$connection = Mage::getSingleton('core/resource')->getConnection('core_write');     
						try {
							$connection->query($sql);
						} catch (Exception $e){
							echo $e->getMessage();
						}
						// start run cronlike
						$itemId = explode(",",$item['product_id']);
						for($i=0;$i<count($itemId);$i++){
							$product = Mage::getModel('catalog/product')->load($itemId[$i]);
							$this->savelikeAction($product->getProductUrl(),$itemId[$i]);
						}
					}
				}
			}else $this->testAction();
		} catch (Exception $e){
			echo $e->getMessage();
			// update error 
			$update = "update `".Mage::getSingleton('core/resource')->getTableName('product_list')."` set `status`='2',`error`='".$e->getMessage()."' where id=".$item['id'];
			$connection = Mage::getSingleton('core/resource')->getConnection('core_write');     
			try {
				$connection->query($update);
			} catch (Exception $e){
				echo $e->getMessage();
			}
		}
	}
	
    public function cronlikeAction(){
		// Select only visible products, the one that show in our store
		
		$products = Mage::getModel('catalog/product')->getCollection();
		$i=0;
		foreach($products as $prod) {
			$product = Mage::getModel('catalog/product')->load($prod->getId());
			$this->savelikeAction($product->getProductUrl(),$prod->getId());
		}
    }
}