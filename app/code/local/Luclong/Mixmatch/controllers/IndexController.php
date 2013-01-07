<?php

class Luclong_Mixmatch_IndexController extends Mage_Core_Controller_Front_Action {

    private $productsArrayList = Array();
    private $image_bg_dir = "";
    private $image_result_dir = "";
    private $width = "433";
    private $height = "629";

    public function indexAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function createAction() {
		
        if (count($this->productsArrayList)) {
            $productids = array_keys($this->productsArrayList);
            $collection = $this->checkproductgroup($productids);
			$product = NULL;
			
            if ($collection->count() > 0) {
                $id_first = $collection->getFirstItem()->getData('product_id');
				$tmpArray = explode("-", $id_first);
				$id = $tmpArray[0];
				
                $product = Mage::getModel('catalog/product')->load($id);
                //add images
                try {
                    $product->addImageToMediaGallery($this->image_result_dir, array('image', 'small_image', 'thumbnail'), false, false);
                } catch (Exception $e) {
                    return "line 30: ".$e->getMessage();
                }
            } else {
                $sku = "MIXNMATCH_" . implode("_", $productids);
                $name = "Bộ sản phẩm";
                $product = Mage::getModel('catalog/product')->load();
                $product->setSku($sku);
                $product->setName($name);
                $product->setDescription("Bộ sản phẩm");
                $product->setShortDescription("Bộ sản phẩm");
                $product->setPrice(0);
                $product->setTypeId('grouped');
                $product->setAttributeSetId(9); // need to look this up
                //	$product->setCategoryIds("20,24"); // need to look these up
                $product->setWeight(1.0);
                $product->setTaxClassId(2); // taxable goods
                $product->setVisibility(4); // catalog, search
                $product->setStatus(1); // enabled
                // assign product to the default website
                $product->setWebsiteIds(array(Mage::app()->getStore(true)->getWebsite()->getId()));

                // for stock

                $stockData = $product->getStockData();
                $stockData['is_in_stock'] = 1;
                $stockData['manage_stock'] = 1;
                $stockData['use_config_manage_stock'] = 1;
                $product->setStockData($stockData);
				
                //add images
                try {
                    $product->addImageToMediaGallery($this->image_result_dir, array('image', 'small_image', 'thumbnail'), false, false);
                } catch (Exception $e) {
                    return "line 66: ".$e->getMessage();
                }
                //add Associated products 

                $associated_products = array();
                foreach ($productids as $item) {
                    $associated_products[$item] = array("qty" => "", "position" => "");
                }
                $product->setGroupedLinkData($associated_products);
            }
			
            try {
                $product->save();
            } catch (Exception $e) {
                return "line 77: ".$e->getMessage();
            }
			
            return Array('url' => $product->getProductUrl(), 'id' => $product->getId(), 'img' => $product->getImageUrl());
        }
    }

    /*
     *
     *
     *
     */

    public function checkproductgroup($products = array()) {
        $collection = Mage::getModel('catalog/product_link')->getCollection()
                ->addFieldToFilter('linked_product_id', $products);
        $collection->getSelect()->group('product_id')->having('count(product_id)=' . count($products));

        return $collection;
    }

    /*
     *
     */

    public function uploadimageAction() {

        if (isset($_FILES['image']['name']) && $_FILES['image']['name'] != '') {
            try {

                $uploader = new Varien_File_Uploader('image');
                $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
                $uploader->setAllowRenameFiles(false);
                $uploader->setFilesDispersion(false);

                $path = Mage::getBaseDir('media') . DS . 'mixnmatch' . DS;
                $uploader->save($path, $_FILES['image']['name']);
                $response['src'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . "mixnmatch/" . $_FILES['image']['name'];
            } catch (Exception $e) {
                $response['status'] = 'ERROR';
                $response['message'] = $this->__('Cannot upload.');
            }
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
    }

    public function ajaxAction() {
        $this->loadLayout();
        $response = $this->getLayout()->getBlock('mixnmatch_result_list')->toHtml();
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
    }

    public function ajaxsearchAction() {

        $this->loadLayout();
        $response = array();
        $response['category'] = $this->getLayout()->getBlock('category')->toHtml();
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
    }

    public function infoproductAction() {
        if ($id = $this->getRequest()->getParam("id")) {

            $_product = Mage::getModel('catalog/product')->load($id);


            $imgWidth = Mage::helper('catalog/image')->init($_product, 'mixnmatch')->getOriginalWidth();
            $imgHeight = Mage::helper('catalog/image')->init($_product, 'mixnmatch')->getOriginalHeight();
            $resizeWidth = 100;
            $resizeHeight = $resizeWidth * $imgHeight / $imgWidth;

            $html = '
		   <div class="mm-editing" id="mm-editing_' . $_product->getId() . '" style="width:' . $resizeWidth . 'px;height:' . $resizeHeight . 'px;" >
			   <img 
			   id="img_' . $_product->getId() . '"
			   style="width:' . $resizeWidth . 'px;height:' . $resizeHeight . 'px;"
			   src="' . Mage::helper('catalog/image')->init($_product, 'mixnmatch') . '" 
			   width="' . $resizeWidth . '" 
			   height="' . $resizeHeight . '"/>
			   <div class="mm-edit-bar">
				   <span class="mm-bt-edit mm-bt-fordward" title="Đưa lên phía trên"></span>
				   <span class="mm-bt-edit mm-bt-backward" title="Đưa xuống phía dưới"></span>
				   <span class="mm-bt-edit mm-bt-remove" title="Xóa bỏ"></span>
			   </div>
		   </div>';
            //$html = '<div id="#test">AA</div>';
            // print_r(Mage::helper('catalog/image')->init($_product, 'image'));
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($html));
        }
    }

    public function createimageAction() {
		// ->setQuality(100)
		try {
		
            if ($str_pid_ppos = $this->getRequest()->getParam("str_pid_ppos")) {
			
                $paramStringList = explode(";", $str_pid_ppos);
                array_pop($paramStringList);
                foreach ($paramStringList as $key => $value) {
                    $tmpArray = explode(",", $value);
                    if ($tmpArray[0] != "") { // if product id exist
                        $product_id = array_shift($tmpArray);
                        $this->productsArrayList[$product_id] = $tmpArray;
                    }
                }
                
                // sort products by array
				function cmpItemByZIndex($a, $b)
				{
					if ($a[4] == $b[4]) {
						return 0;
					}
					return ($a[4] < $b[4]) ? -1 : 1;
				}

				uasort($this->productsArrayList, "cmpItemByZIndex");
				
				// load background
                $this->image_result_dir = Mage::getBaseDir('media') . DS . 'mixmatch' . DS . 'grouped_img' . DS . gettimeofday(true) . ".jpg";
				
                $image_result = imagecreatetruecolor($this->width, $this->height);
				if($this->getRequest()->getParam("frame_bg")){
					$this->image_bg_dir = Mage::getBaseDir() . $this->getRequest()->getParam("frame_bg");
					$image_bg = imagecreatefromjpeg($this->image_bg_dir);
					$image_bg_w = imagesx($image_bg);
					$image_bg_h = imagesy($image_bg);
					imagecopyresampled(
                        $image_result, $image_bg, 0, 0, 0, 0, $image_bg_w, $image_bg_h, $image_bg_w, $image_bg_h);
				}
				
				// draw all item
				// print_r($this->productsArrayList);
                foreach ($this->productsArrayList as $pid => $param) {
				
					$item_w = $param[0];
					$item_h = $param[1];
					$item_l = $param[2];
					$item_t = $param[3];
					$item_z = $param[4];
					
					//load product image
					$_product = Mage::getModel('catalog/product')->load($pid);
					
					$imgWidth = Mage::helper('catalog/image')->init($_product, 'mixnmatch')->getOriginalWidth();
					$imgHeight = Mage::helper('catalog/image')->init($_product, 'mixnmatch')->getOriginalHeight();
					
					$dirImageItem = Mage::getBaseDir('media') . "/catalog/product" . $_product->getMixnmatch();
					// echo $dirImageItem;
					$image_item_tmp = imagecreatefromjpeg($dirImageItem);
					imagecopyresampled(
						$image_result, $image_item_tmp, $item_l, $item_t, 0, 0, $item_w, $item_h, $imgWidth, $imgHeight);
					imagedestroy($image_item_tmp);
                }
				
                imagejpeg($image_result, $this->image_result_dir);
                imagedestroy($image_result);
				// echo $this->image_result_dir;
				if(file_exists($this->image_result_dir)){
					$groupedProduct = $this->createAction();
					
					$jsonArray["redirect_uri"] = $groupedProduct['url'];
					$jsonArray["link"] = $groupedProduct['url'];
					$jsonArray["picture"] = $groupedProduct['img'];
					$jsonArray["name"] = $this->getRequest()->getParam("sharename");
					$jsonArray["caption"] = $this->getRequest()->getParam("sharename");
					$jsonArray["description"] = $this->getRequest()->getParam("sharedes");
					$jsonArray["id"] = $groupedProduct['id'];

					$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($jsonArray));
					
				} else {
					$html = "Cannot create image";
					$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($html));
				}
                
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
    
    public function collectionAction() {
        $this->loadLayout();
        $this->renderLayout();
    }
    
    public function productajaxAction() {
        $this->loadLayout();
        $response = $this->getLayout()->getBlock('mixnmatch_product_detail')->toHtml();
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
    }
}