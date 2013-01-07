<?php
/**
* @copyright Amasty.
*/  
class Amasty_Shopby_Adminhtml_PageController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
	    $this->loadLayout(); 
        $this->_setActiveMenu('catalog/amshopby/pages');
        $this->_addContent($this->getLayout()->createBlock('amshopby/adminhtml_page')); 	    
 	    $this->renderLayout();
    }

	public function newAction() 
	{
        $this->editAction(); 
	}
	
    public function editAction() 
    {
		$id     = (int) $this->getRequest()->getParam('id');
		$model  = Mage::getModel('amshopby/page')->load($id);
		
        $cond = $model->getCond();
        if ($cond){
            $cond = unserialize($cond);
            $i=0;
            foreach ($cond as $k=>$v){
                $model->setData('attr_' . $i, $k);
                $model->setData('option_' . $i, $v);
                
                ++$i;
            }
        }

		if ($id && !$model->getId()) {
    		Mage::getSingleton('adminhtml/session')->addError(Mage::helper('amshopby')->__('Page does not exist'));
			$this->_redirect('*/*/');
			return;
		}
		
		$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
		if (!empty($data)) {
			$model->setData($data);
		}
		
		Mage::register('amshopby_page', $model);

		$this->loadLayout();
		$this->_setActiveMenu('catalog/amshopby');
		$this->_title('Edit');
        $this->_addContent($this->getLayout()->createBlock('amshopby/adminhtml_page_edit'));
        
		$this->renderLayout();
	}

	public function saveAction() 
	{
	    $id     = $this->getRequest()->getParam('id');
	    $model  = Mage::getModel('amshopby/page');
	    $data   = $this->getRequest()->getPost();
		if ($data) {
			$model->setData($data)->setId($id);
            
			try {
			    
                $cond = array();
                for ($i=0; $i < $model->getNum(); ++$i){
                    $cond[$model->getData('attr_' . $i)] = $model->getData('option_' . $i);
                }
                $model->setCond(serialize($cond));
			    
				$model->save();
				Mage::getSingleton('adminhtml/session')->setFormData(false);
				
				$msg = Mage::helper('amshopby')->__('Page has been successfully saved');
                Mage::getSingleton('adminhtml/session')->addSuccess($msg);
                if ($this->getRequest()->getParam('continue')){
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                }
                else {
                    $this->_redirect('*/*');
                }
               
				
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $id));
            }	
            		    
            return;
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('amshopby')->__('Unable to find a page to save'));
        $this->_redirect('*/*/');
	} 
	
    public function optionsAction()
    {
        $name = 'option_' . substr($this->getRequest()->getParam('name'),-1);
        $result = '<input id="'.$name.'" name="'.$name.'" value="" class="input-text" type="text" />';
        
        $code = $this->getRequest()->getParam('code');
        if (!$code){
            $this->getResponse()->setBody($result);
            return;
        }
        
        $attribute = Mage::getModel('catalog/product')->getResource()->getAttribute($code);
        if (!$attribute){
            $this->getResponse()->setBody($result);
            return;            
        }

        if (!in_array($attribute->getFrontendInput(), array('select', 'multiselect')) ){
            $this->getResponse()->setBody($result);
            return;            
        }
        
        $options = $attribute->getFrontend()->getSelectOptions();
        //array_shift($options);  
        
        $result = '<select id="'.$name.'" name="'.$name.'" class="select">';
        foreach ($options as $option){
            $result .= '<option value="'.$option['value'].'">'.$option['label'].'</option>';      
        }
        $result .= '</select>';    
        
        $this->getResponse()->setBody($result);
        
    }    
		
    public function massDeleteAction()
    {
        $ids = $this->getRequest()->getParam('pages');
        if(!is_array($ids)) {
             Mage::getSingleton('adminhtml/session')->addError(Mage::helper('amshopby')->__('Please select page(s)'));
        } 
        else {
            try {
                foreach ($ids as $id) {
                    $model = Mage::getModel('amshopby/page')->load($id);
                    $model->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($ids)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/');
    }
    
    public function deleteAction() 
    {
		if ($this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('amshopby/page');
				 
				$model->setId($this->getRequest()->getParam('id'))
					->delete();
					 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('amshopby')->__('Page has been deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	} 
	
    protected function _title($text = null, $resetIfExists = true)
    {
        if (version_compare(Mage::getVersion(), '1.4') < 0){
            return $this;
        }
        return parent::_title($this->__($text), $resetIfExists);
    } 	
    
    protected function _setActiveMenu($menuPath)
    {
        $this->getLayout()->getBlock('menu')->setActive($menuPath);
        $this
            ->_title('Catalog')
            ->_title('Improved Navigation')	 
            ->_title('Pages')
        ;	 
        return $this;
    }     
}