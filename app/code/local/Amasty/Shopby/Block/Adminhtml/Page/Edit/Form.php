<?php
/**
 * @author Amasty
 */ 
class Amasty_Shopby_Block_Adminhtml_Page_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
    $form = new Varien_Data_Form(array(
      'id' => 'edit_form',
      'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
      'method' => 'post'));
    
    $form->setUseContainer(true);
    $this->setForm($form);
    $hlp = Mage::helper('amshopby');
    
    $model = Mage::registry('amshopby_page');
    
    if (!$model->getId()){
        $fldInfo = $form->addFieldset('setup', array('legend'=> $hlp->__('Page Setup')));
        
        $fldInfo->addField('num', 'text', array(
          'label'     => $hlp->__('Number of Conditions'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'num',
        )); 
    }
    else {
        $fldMeta = $form->addFieldset('tags', array('legend'=> $hlp->__('Meta Tags')));
        $fldMeta->addField('num', 'hidden', array(
          'name'      => 'num',
        )); 
        $fldMeta->addField('use_cat', 'select', array(
          'label'     => $hlp->__('Add to Category Metas'),
          'name'      => 'use_cat',
          'values'    => array(Mage::helper('catalog')->__('No'), Mage::helper('catalog')->__('Yes')),
        )); 
        $fldMeta->addField('meta_title', 'text', array(
          'label'     => $hlp->__('Page Title'),
          'name'      => 'meta_title',
        )); 
        $fldMeta->addField('meta_descr', 'text', array(
          'label'     => $hlp->__('Meta Description'),
          'name'      => 'meta_descr',
        )); 
        $fldMeta->addField('url', 'text', array(
          'label'     => $hlp->__('Canonical Url'),
          'name'      => 'url',
        )); 
        
        
        $fldInfo = $form->addFieldset('info', array('legend'=> $hlp->__('Page Text')));
        $fldInfo->addField('title', 'text', array(
          'label'     => $hlp->__('Title'),
          'name'      => 'title',
        )); 
        $fldInfo->addField('cms_block', 'text', array(
          'label'     => $hlp->__('CMS block'),
          'name'      => 'cms_block',
        )); 
        
        $filters = $model->getAllFilters(true);   
             
        for ($i=0; $i < $model->getNum(); ++$i){
            $fldCond = $form->addFieldset('cond'. $i, array('legend'=> $hlp->__('Condition #' . ($i+1))));
            $fldCond->addField('attr_' . $i, 'select', array(
              'label'     => $hlp->__('Filter'),
              'name'      => 'attr_'.$i,
              'values'    => $filters,
              'class'     => 'required-entry',
              'required'  => true,
              'onchange'  => 'showOptions(this)',
            )); 
            $fldCond->addField('option_' . $i, 'text', array(
              'label'     => $hlp->__('Value'),
              'class'     => 'required-entry',
              'required'  => true,
              'name'      => 'option_'.$i,
            )); 
        }
    }
    
      
    //set form values
    $data = Mage::getSingleton('adminhtml/session')->getFormData();
    if ($data) {
        $form->setValues($data);
        Mage::getSingleton('adminhtml/session')->setFormData(null);
    }
    elseif ($model) {
        $form->setValues($model->getData());
    } 
    
    return parent::_prepareForm();
  }
}