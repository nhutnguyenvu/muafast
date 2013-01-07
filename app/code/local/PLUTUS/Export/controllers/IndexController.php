<?php

/**
 * qikDropShipper Extension
 *
 * @category   FavoredMinds
 * @package    Vendor
 * @author     FavoredMinds Solution (geedubya15@gmail.com)
 * @copyright  Copyright (c) 2010 FavoredMinds.com (http://www.favoredminds.com)
 */
class PLUTUS_Export_IndexController extends Mage_Core_Controller_Front_Action {
    
    public function exportAction(){
        
        
        $params = $this->getRequest()->getParam("attr");
        $params = explode(",", $params);
        $data = array();
        $attribute = Mage::getModel('eav/config')->getAttribute('catalog_product', "manufacturer");
        
        if(count($params) > 0){
            $i = 0;
            foreach ($params as $param){
                $data[$i]['attribute'] = $param;
                $attribute = Mage::getModel('eav/config')->getAttribute('catalog_product', $param);
                foreach ( $attribute->getSource()->getAllOptions(true, true) as $option){
                     $data[$i]['label'][] = $option['label'];
                }
                $i++;
            }
        }
        
        $varPath =  Mage::getBaseDir("var");
       
        //export csv
        
        for($i=0; $i < count($data);$i++){
            
            $attribute = $data[$i]['attribute'];
            $time = $attribute.date("Y-m-d-h-i-s",time());
            file_put_contents($varPath."/$time.txt", $attribute, FILE_APPEND | LOCK_EX);
            $values = $data[$i]['label'];
            
            if(!empty($data[$i]['label'])){
                foreach ($values as $v){
                    file_put_contents($varPath."/$time.txt", $v."\n", FILE_APPEND | LOCK_EX);
                }
            }
            file_put_contents($varPath."/$time.txt", "\n",FILE_APPEND | LOCK_EX);
        }

    }
    public function appendAttributeAction(){
        
        $params = $this->getRequest()->getParam("attr");
        
        $params = explode(",", $params);
        
        $data = array();
        $data = array();
        if(count($params) > 0){
            $i = 0;
            foreach ($params as $param){
                $data[$i]['attribute'] = $param;

                $attribute = Mage::getModel('eav/config')->getAttribute('catalog_product', $param);
                foreach ( $attribute->getSource()->getAllOptions(true, true) as $option){
                     $data[$param][] = $option['label'];
                }
            }
        }
        $varPath =  Mage::getBaseDir("var");
        if(count($params) > 0){
            foreach ($params as $param){
                
                $varPath =  Mage::getBaseDir("var").$param.".txt";
                //$attribute = Mage::getModel('eav/config')->getAttribute('catalog_product', $param
                $attribute = Mage::getModel('eav/config')->getAttribute('catalog_product', $param);
                $option_import_arr = file($varPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                $attribute_exist_option = array();
                foreach ($attribute->getSource()->getAllOptions(true, true) as $option){
                     
                     $attribute_exist_option[] = $option['lable'];  
                }
                $option_label_add = array();
                foreach ($option_import_arr as $label){
                    if(!in_array($label,$attribute_exist_option)){
                        $option_label_add[] = $label;
                    }
                }
                
                
            }
        }
        
        
    }
    
    public function addAttributeValue($arg_attribute, $arg_value)
    {
        $attribute_model        = Mage::getModel('eav/entity_attribute');

        $attribute_code         = $attribute_model->getIdByCode('catalog_product', $arg_attribute);
        $attribute              = $attribute_model->load($attribute_code);

        if(!$this->attributeValueExists($arg_attribute, $arg_value))
        {
            $value['option'] = array($arg_value,$arg_value);
            $result = array('value' => $value);
            $attribute->setData('option',$result);
            $attribute->save();
        }

		$attribute_options_model= Mage::getModel('eav/entity_attribute_source_table') ;
        $attribute_table        = $attribute_options_model->setAttribute($attribute);
        $options                = $attribute_options_model->getAllOptions(false);

        foreach($options as $option)
        {
            if ($option['label'] == $arg_value)
            {
                return $option['value'];
            }
        }
        return false;
    }
    public function attributeValueExists($arg_attribute, $arg_value)
    {
        $attribute_model        = Mage::getModel('eav/entity_attribute');
        
        $attribute_options_model= Mage::getModel('eav/entity_attribute_source_table') ;

        $attribute_code         = $attribute_model->getIdByCode('catalog_product', $arg_attribute);
        $attribute              = $attribute_model->load($attribute_code);

        $attribute_table        = $attribute_options_model->setAttribute($attribute);
        $options                = $attribute_options_model->getAllOptions(false);

        foreach($options as $option)
        {
            if ($option['label'] == $arg_value)
            {
                return $option['value'];
            }
        }

        return false;
    }
}