<?php
/**
* qikDropShipper Extension
*
* @category   FavoredMinds
* @package    Vendor
* @author     FavoredMinds Solution (geedubya15@gmail.com)
* @copyright  Copyright (c) 2010 FavoredMinds.com (http://www.favoredminds.com)
*/

class FavoredMinds_Vendor_Model_Product_Type extends Mage_Catalog_Model_Product_Type {

	public function toOptionArray(){
    return parent::getAllOptions();
	}

  static public function getOptionArray()
  {
    $helper 		= Mage::app()->getHelper('vendor');
    if ($helper->vendorIsLogged()){
      //we remove the disabled options
      $a_avalable_types = explode(',', Mage::getStoreConfig('vendor/general/producttypes'));
      $options = array();
      foreach(self::getTypes() as $typeId=>$type) {
        if (in_array($typeId, $a_avalable_types))
          $options[$typeId] = Mage::helper('catalog')->__($type['label']);
      }

      return $options;
    }
    $res = parent::getOptionArray();
    return $res;
  }

  /**
   * Retrive all attribute options
   *
   * @return array
   */
  static public function getAllOptions() {
    //we will remove the unneeded options
    $res = parent::getAllOptions();
    return $res;
  }


  /**
   * Returns label for value
   * @param string $value
   * @return string
   */
  public function getLabel($value) {
    return parent::getOptionText($value);
  }

  /**
   * Returns array ready for use by grid
   * @return array
   */
  public function getGridOptions() {
    $items = $this->getAllOptions();
    $out = array();
    foreach($items as $item) {
      $out[$item['value']] = $item['label'];
    }
    return $out;
  }
}
