<?php

class Luclong_Marketing_IndexController extends Mage_Core_Controller_Front_Action {

    public function indexAction() {
        
        //$this->_redirect('*/*/toideptrongmatai');
        $this->loadLayout();
        $this->renderLayout();
        //$this->uploadAction();
    }

    public function toideptrongmataiAction() {
        $this->loadLayout();
        $this->renderLayout();
        //$this->uploadAction();
    }
    
    public function uploadAction() {
        $this->loadLayout();
        $this->renderLayout();
		
    }
    
    public function listAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function detailAction(){
        $this->loadLayout();
        $this->renderLayout();
    }
	
	public function ajaxAction(){
        $this->loadLayout();
		$response = $this->getLayout()->getBlock('ajax.marketing')->toHtml();
	 	$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
    }
    
    public function saveAction() {
        if($data = $this->getRequest()->getPost()){
            $session_fbid = Mage::getSingleton('core/session')->getFacebookID();
            
            // check user exist
            //$block = new Apptha_Sociallogin_Block_Sociallogin();              
           $write = Mage :: getSingleton ( 'core/resource' )-> getConnection ('core_write'); 
            $readresult = $write -> query("SELECT count(face_id) AS tong
                                    FROM ".Mage::getSingleton('core/resource')->getTableName('marketing')."
                                    WHERE face_id = $session_fbid
                                  ");
            $row = $readresult->fetch();
			
           if($row['tong'] == 0){
                if(isset($_FILES['photo']['name']) && $_FILES['photo']['name'] != '') {
					if(round($_FILES['photo']['size']/1024)<2048){
						try {    
							$uploader = new Varien_File_Uploader('photo');

							$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
							$uploader->setAllowRenameFiles(true);
							$uploader->setFilesDispersion(false);
                            
                            // rename image
						
							$new_image_name = $session_fbid.$_FILES['photo']['name'];
							// Set media as the upload dir
							$media_path  = Mage::getBaseDir('media').'/marketing/';                

							
                            // Upload the image
							$uploader->save($media_path, $new_image_name);
							$media_path_show = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);
							$imageUrl = $media_path_show.'/marketing/' . $new_image_name;

							//get data marketing to save 
							$data['photo'] = $new_image_name;
							$data['face_id'] = $session_fbid;
							//$data['description'] = $datapost['description'];
							
                            Mage::helper('marketing')->resize($data['photo'],100,150);
							
                            $model = Mage::getModel('marketing/marketing')->setData($data)->save();
							$marketing_id = $model->getMarketing_id();
							if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
								$model->setCreatedTime(now())
									  ->setUpdateTime(now());
								} else {
										$model->setUpdateTime(now());
								}
							$model->setStatus(2);	
							$model->save()->getId();
							
							$response['message']=$this->__('Cảm ơn bạn đã tham gia chương trình! Hình của bạn sẽ được duyệt trước khi hiển thị!');
                          
						  $response['image']=$imageUrl;
						   $response['act']=Mage::getUrl('marketing/index/update');
						     $response['id']=$marketing_id;
							//  $response['des']=$model->getData('description');
						
							
						} 
						catch (Exception $e) {
							print $e->getMessage();
							die;
						}  
					}else{ $response['message']= $this->__('Bạn vui lòng gửi hình nhỏ hơn 2Mb');}							
                }
           }else{
                	$response['message']=$this->__('Cảm ơn bạn đã tham gia chương trình! Hình của bạn sẽ được duyệt trước khi hiển thị!');
					$response['act']=Mage::getUrl('marketing/index/update');
				
            }
        }
		   $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
    }
    
    public function updateAction(){
			//$imgId = $this->getRequest()->getParam('imgId');
            $data = $this->getRequest()->getPost();
            $session_fbid = Mage::getSingleton('core/session')->getFacebookID();
            $imgId = $data['imgId'];
            //$imgName = $_FILES['photo']['name'];
            
			
            try {
                if(!isset($_FILES['photo']['name'])||$_FILES['photo']['name'] == ''){
                     unset($data['photo']);
                     $imageUrl = Mage::getModel('marketing/marketing')->getCollection()->addFieldToFilter('marketing_id', $imgId);
                }else{
					if(round($_FILES['photo']['size']/1024)<2048){
						$uploader = new Varien_File_Uploader('photo');
			
						$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
						$uploader->setAllowRenameFiles(true);
						$uploader->setFilesDispersion(false);
			
						// rename image
					
						$new_image_name = $session_fbid.$_FILES['photo']['name'];
						// Set media as the upload dir
						$media_path  = Mage::getBaseDir('media').DS.'marketing'.DS; 
						$path = $media_path.$_FILES['photo']['name'];               
						//echo $path;
						//exit();
						if (file_exists($path)) {
						unlink($path);
						}
                            $imageResized = Mage::getBaseDir('media').DS.'marketing'.DS.'resized'.DS.$_FILES['photo']['name'];
						if (file_exists($imageResized)) {
						unlink($imageResized);
						}
                        
						//echo $new_image_name;
					   // exit();
						// Upload the image
						$uploader->save($media_path, $new_image_name);
                        
						$media_path_show = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);
						$imageUrl = $media_path_show.'/marketing/' . $new_image_name;
						$data['photo']=$new_image_name;
                        
                        Mage::helper('marketing')->resize($data['photo'],100,150);
					}else{ $response['message']= $this->__('Bạn vui lòng gửi hình nhỏ hơn 2Mb');}
                }   
				
				$data["status"] = 2;
                $model = Mage::getModel('marketing/marketing')->load($imgId)->addData($data);
                   
                            if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
                                $model->setCreatedTime(now())
                                      ->setUpdateTime(now());
                                } else {
                                        $model->setUpdateTime(now());
                                }
                            
                            $model->setId($imgId)->save();
                            //echo "Data updated successfully.";   
							$response['message']=$this->__('Thông tin của bạn đã được cập nhật! Hình của bạn sẽ được duyệt lại!');
                          
						  $response['image']=$imageUrl;
						  //$response['des']=$model->getData('description');
            } catch (Exception $e){
                       	$response['message']= $e->getMessage();
            }
         $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
    }
}