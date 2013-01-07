<?php

class Luclong_Marketing_Adminhtml_MarketingController extends Mage_Adminhtml_Controller_action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('marketing/items')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}

	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('marketing/marketing')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('marketing_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('marketing/items');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('marketing/adminhtml_marketing_edit'))
				->_addLeft($this->getLayout()->createBlock('marketing/adminhtml_marketing_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('marketing')->__('Item does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		$this->_forward('edit');
	}
 
	public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {
			
			
			if(isset($_FILES['photo']['name']) && $_FILES['photo']['name'] != '') {
                if(round($_FILES['photo']['size']/1024)<2048){
    				try {	
    					/* Starting upload */	
    					$uploader = new Varien_File_Uploader('photo');
    					
    					// Any extention would work
    	           		$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
    					$uploader->setAllowRenameFiles(false);
    					
    					// Set the file upload mode 
    					// false -> get the file directly in the specified folder
    					// true -> get the file in the product like folders 
    					//	(file.jpg will go in something like /media/f/i/file.jpg)
    					$uploader->setFilesDispersion(false);
    							
    					// We set media as the upload dir
    					$path = Mage::getBaseDir('media') . DS . 'marketing' ;
    					$uploader->save($path, $_FILES['photo']['name'] );
    					
    				} catch (Exception $e) {
    		      
    		        }
    	        
    		        //this way the name is saved in DB
    	  			$data['photo'] = $_FILES['photo']['name'];
	           }else{ echo "Bạn vui lòng gửi hình nhỏ hơn 2Mb";}
            }else{
	           unset($data['photo']);
			}
            
            //echo $data['photo'];
            //exit();
	  			
	  			
			$model = Mage::getModel('marketing/marketing');		
			$model->setData($data)
				->setId($this->getRequest()->getParam('id'));
			
			try {
				if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
					$model->setCreatedTime(now())
						->setUpdateTime(now());
				} else {
					$model->setUpdateTime(now());
				}	
				
				$model->save();
				
				//send mail 
				
				if($data['status']==1){
					$obj= array();
				    $sender = Array('name' => Mage::getStoreConfig('trans_email/ident_general/name'),
                	'email' => Mage::getStoreConfig('trans_email/ident_general/email'));
					$store = Mage::app()->getStore();
					$translate  = Mage::getSingleton('core/translate');

					$customer = Mage::getModel('customer/customer')->load($model->getData('user_id'));
					$email = $customer->getEmail();
					$name = $customer->getFirstname()." ".$customer->getLastname();
					$obj['customername']=$name;
					$obj['url']=Mage::getUrl('marketing/index/detail').'?imageId='.$model->getId();
					$sendemail=Mage::getModel('core/email_template');
					$sendemail->sendTransactional('marketing_email_msg',
					                                                         $sender,
					                                                         $email,
					                                                         $name,
					                                                         $obj,
					                                                         $store->getId());
					$translate->setTranslateInline(true);														 
			    }
				//end sendmail
				
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('marketing')->__('Item was successfully saved'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);

				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $model->getId()));
					return;
				}
				$this->_redirect('*/*/');
				return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('marketing')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
	}
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('marketing/marketing');
				 
				$model->setId($this->getRequest()->getParam('id'))
					->delete();
					 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

    public function massDeleteAction() {
        $marketingIds = $this->getRequest()->getParam('marketing');
        if(!is_array($marketingIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($marketingIds as $marketingId) {
                    $marketing = Mage::getModel('marketing/marketing')->load($marketingId);
                    $marketing->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($marketingIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
	
    public function massStatusAction()
    {
        $marketingIds = $this->getRequest()->getParam('marketing');
        if(!is_array($marketingIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($marketingIds as $marketingId) {
                    $marketing = Mage::getSingleton('marketing/marketing')
                        ->load($marketingId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($marketingIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
  
    public function exportCsvAction()
    {
        $fileName   = 'marketing.csv';
        $content    = $this->getLayout()->createBlock('marketing/adminhtml_marketing_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'marketing.xml';
        $content    = $this->getLayout()->createBlock('marketing/adminhtml_marketing_grid')
            ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }
}