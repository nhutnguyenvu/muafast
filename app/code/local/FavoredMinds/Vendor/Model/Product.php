<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog product model
 *
 * @method Mage_Catalog_Model_Resource_Product getResource()
 * @method Mage_Catalog_Model_Resource_Product _getResource()
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class FavoredMinds_Vendor_Model_Product extends Mage_Catalog_Model_Product
{
   
    protected function _beforeSave()
    {

        $_helper = Mage::helper('vendor');

        $data = $this->getData();

        if(isset($data["stock_data"]['qty'])){
            $qty = intval($data["stock_data"]['qty']);
        }
        else{
            $qty = 0;
        }

        $is_in_stock = 0;

        if($qty > 0){
            $is_in_stock = $data["stock_data"]['is_in_stock'] = 1;
        }

        if ($_helper->vendorIsLogged()) {

            $id = $this->getId();
            
            $this->setData("website_ids",array(1));
            
            if(empty($id)){
                
                $vendorInfo = $_helper->getVendorUserInfo($_helper->getVendorUserId());
            
                $attributeSetModel = Mage::getModel("eav/entity_attribute_set");
                $attributeSetModel->load($this->getAttributeSetId());
                $attributeSetName = $attributeSetModel->getAttributeSetName();
                if($this->isConfigurable()){
                    $sku = $this->getRandomeConfigrableSku($attributeSetName);
                }
                else{
                    $sku = $this->getRandomeSku($attributeSetName);
                }


                $this->setData("sku",$sku);
                $this->setData("website_ids",array(1));
                $this->setData("tax_class_id",0);
                $this->setData("weight",0);

                $this->setData("manufacturer",$vendorInfo['vendor_code']);
                $this->setData("short_description","n/a");


                $this->setData("stock_data",
                                array("use_config_manage_stock"=>"1",
                                        "qty"=>$qty,
                                        "use_config_min_qty"=>"1",
                                        "use_config_min_sale_qty"=>"1",
                                        "use_config_max_sale_qty"=>"1",
                                        "is_qty_decimal"=>"1",
                                        "is_decimal_divided"=>"1",
                                        "use_config_backorders"=>"1",
                                        "use_config_notify_stock_qty"=>"1",
                                        "use_config_enable_qty_increments","1",
                                        "use_config_qty_increments"=>"1",
                                        "is_in_stock"=>1,
                                    )
                              );
            }
            
               
        }
     
        $this->cleanCache();
        $this->setTypeHasOptions(false);
        $this->setTypeHasRequiredOptions(false);

        $this->getTypeInstance(true)->beforeSave($this);

        $hasOptions         = false;
        $hasRequiredOptions = false;

        /**
         * $this->_canAffectOptions - set by type instance only
         * $this->getCanSaveCustomOptions() - set either in controller when "Custom Options" ajax tab is loaded,
         * or in type instance as well
         */
        $this->canAffectOptions($this->_canAffectOptions && $this->getCanSaveCustomOptions());
        if ($this->getCanSaveCustomOptions()) {
            $options = $this->getProductOptions();
            if (is_array($options)) {
                $this->setIsCustomOptionChanged(true);
                foreach ($this->getProductOptions() as $option) {
                    $this->getOptionInstance()->addOption($option);
                    if ((!isset($option['is_delete'])) || $option['is_delete'] != '1') {
                        $hasOptions = true;
                    }
                }
                foreach ($this->getOptionInstance()->getOptions() as $option) {
                    if ($option['is_require'] == '1') {
                        $hasRequiredOptions = true;
                        break;
                    }
                }
            }
        }

        /**
         * Set true, if any
         * Set false, ONLY if options have been affected by Options tab and Type instance tab
         */
        if ($hasOptions || (bool)$this->getTypeHasOptions()) {
            $this->setHasOptions(true);
            if ($hasRequiredOptions || (bool)$this->getTypeHasRequiredOptions()) {
                $this->setRequiredOptions(true);
            } elseif ($this->canAffectOptions()) {
                $this->setRequiredOptions(false);
            }
        } elseif ($this->canAffectOptions()) {
            $this->setHasOptions(false);
            $this->setRequiredOptions(false);
        }
        parent::_beforeSave();
    }
    public function getRandomeConfigrableSku($attributeSetLabel){
        $_helper = Mage::helper('vendor');

        $attributeSetLabel = strtoupper($attributeSetLabel);

        $vendorId = $_helper->getVendorUserId();

        $skufile = "CS".$vendorId.$attributeSetLabel.str_replace("-","",date("d-m-y",time()));

        $varPath = Mage::getBaseDir("var");
        $fileName = $varPath."/sku/$skufile.txt";
        $randomNumber = "";
        if(file_exists($fileName)){

            $data = file($fileName,FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES);

            arsort($data);


            if(count($data)){
               $maxSku = $data[count($data) - 1];
            }
            else{
                $maxSku = $skufile."R0001";
            }



            $randomNumber = intval(str_replace($skufile."R", "", $maxSku));


            $randomNumber = $randomNumber + 1;

            $digit = strlen((string) $randomNumber);


            for($i=0;$i < 4 - $digit ; $i++ ){
                $randomNumber = "0".(string)$randomNumber;
            }

            file_put_contents($fileName, $skufile."R".$randomNumber."\r\n",FILE_APPEND|LOCK_EX);

            return $skufile."R".$randomNumber;
        }
        else{
            file_put_contents($fileName, $skufile."R0001". "\r\n" ,FILE_APPEND|LOCK_EX);
            return $skufile."R0001";
        }
    }
    public function getRandomeSku($attributeSetLabel){

        $_helper = Mage::helper('vendor');
        
        $attributeSetLabel = strtoupper($attributeSetLabel);
        
        $vendorId = $_helper->getVendorUserId();
        
        $skufile = "S".$vendorId.$attributeSetLabel.str_replace("-","",date("d-m-y",time()));

        $varPath = Mage::getBaseDir("var");
        $fileName = $varPath."/sku/$skufile.txt";
        $randomNumber = "";
        if(file_exists($fileName)){
            
            $data = file($fileName,FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES);
            
            arsort($data);
            
            
            if(count($data)){
               $maxSku = $data[count($data) - 1];
            }
            else{
                $maxSku = $skufile."R0001";
            }
            
            
            
            $randomNumber = intval(str_replace($skufile."R", "", $maxSku));
            
            
            $randomNumber = $randomNumber + 1;
            
            $digit = strlen((string) $randomNumber);
            
            
            for($i=0;$i < 4 - $digit ; $i++ ){
                $randomNumber = "0".(string)$randomNumber;
            }
            
            file_put_contents($fileName, $skufile."R".$randomNumber."\r\n",FILE_APPEND|LOCK_EX);
            
            return $skufile."R".$randomNumber;
        }
        else{
            file_put_contents($fileName, $skufile."R0001". "\r\n" ,FILE_APPEND|LOCK_EX);
            return $skufile."R0001";
        }
           
    }
    static public function getFeaturedArray()
    {
        return array(
            1    => "Enable",
            0   => "Disabled"
        );
    }
    
}
