<?php
/**
* qikDropShipper Extension
*
* @category   FavoredMinds
* @package    Vendor
* @author     FavoredMinds Solution (geedubya15@gmail.com)
* @copyright  Copyright (c) 2010 FavoredMinds.com (http://www.favoredminds.com)
*/

class FavoredMinds_Vendor_Block_Vendor_Renderer_Vendor extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

  // Holds an associative array with vendor id and the associated label
  private static $_vendors = array(); // "singleton"

  public static function getVendorsArray() {
    // Make sure the static property is only populated once
    if (count(self::$_vendors) == 0) {
      $vendor = new FavoredMinds_Vendor_Model_Vendor();
      $vendors = $vendor->getCollection()->toOptionIdHash();
      self::$_vendors = $vendors;
    }

    return self::$_vendors;
  }

  // Transforms the customer_group_id into corresponding label
  public function render(Varien_Object $row) {
    $val = $this->_getValue($row);
    $vendors = self::getVendorsArray();
    return $vendors[$val];
  }
  /** 
     * @desc: get Vendor List with limit
     * @author: nhut.nguyen
     */
    public function getVendorList(){
        
        $page_number = $this->getRequest()->getParam('page');
        if(empty($page_number)){
            $page_number = 1;
        }
        return Mage::getModel('vendor/vendor')->getVendorList($page_number);
    }
    /** 
     * @desc: get Pagination
     * @author: nhut.nguyen
     */
    public function getPagination(){
        $page_number = $this->getRequest()->getParam('page');
        if(empty($page_number)){
            $page_number = 1;
        }
        return Mage::getModel('vendor/vendor')->getPagination($page_number);
    }
    /** 
     * @desc: get Pagination
     * @author: nhut.nguyen
     */
    public static function getVendorInfo($vendor_code,$column=array()){
        
        return Mage::getModel("vendor/vendor_information")->getVendorInfoByVendorCode($vendor_code,$column);
    }
     /** @desc: get vendor on slide
     *  @authot: nhut.nguyen
     *  @return: return list vendor for slide
     */
    public static function getVendorsOnSlide($is_array=true) {
        
        $vendor = Mage::getModel("vendor/vendor");
        
        $vendors = $vendor->getCollection()
                ->addFieldToSelect("vendor_id")
                ->addFieldToSelect("company_name")
                ->addFieldToSelect("vendor_code")
                ->addFieldToSelect("is_saling")
                ->addFieldToFilter("on_slide",1)
                ->addFieldToFilter("status",1)
                ->addFieldToSelect("logo")
                ->addFieldToSelect("path")
                ->addFieldToSelect("aboutcompany")
                ->addFieldToSelect("about_short_company")
                ->addFieldToSelect("about_mshort_company")
                ->setOrder("position_b","ASC");
        
        if($is_array){
            $vendors = $vendors->toArray();
            if(count($vendors['totalRecords']) > 0){
                return $vendors['items'];
            }
            return array();

        }
        return $vendors;
        
    }
     /** @desc: get vendor for homepage
     *  @authot: nhut.nguyen
     *  @return: return list vendor for homepage 
     */
    public static function getVendorsOnHomePage($is_array=true) {
        
        if (count(self::$_vendors) == 0) {
            $vendor = new FavoredMinds_Vendor_Model_Vendor();
            $vendors = $vendor->getCollection()
                    ->addFieldToSelect("vendor_code")
                    ->addFieldToSelect("vendor_id")
                    ->addFieldToSelect("is_saling")
                    ->addFieldToSelect("company_name")
                    ->addFieldToSelect("average_rate")
                    ->addFieldToSelect("aboutcompany")
                    ->addFieldToSelect("about_short_company")
                    ->addFieldToSelect("about_mshort_company")
					->addFieldToSelect("logo")
					->addFieldToSelect("path")
                    ->addFieldToFilter("on_home",1)
                    ->addFieldToFilter("status",1)
                    ->setOrder("position_a","ASC");
			$pagesize = Mage::getStoreConfig('vendor/quantity_vendor/qty_on_home');
			if($pagesize){
                   $vendors->setPageSize($pagesize)
                    ->setCurPage(1);
				}
            if($is_array){
                $vendors = $vendors->toArray();
                if(count($vendors['totalRecords']) > 0){
                    return $vendors['items'];
                }
                return array();
                
            }
            return $vendors;
        }
        return array();
    }
    /** @desc: get Resource Logo At Front end
     *  @author: nhut.nguyen
     *  @param: vendor with format array or object
     *  @return: url rezise vendor;
     */
    public function getLogoSrcFrontEndByVendor($vendor) {

       //$helper = Mage::app()->getHelper('vendor');
        //check if the vendor is registered
        if(is_object($vendor)){
            $vendor = $vendor->getData();
        }
        //var_dump($vendor);


        $imageUrl = Mage::getBaseDir('media') . DS . $vendor['logo'];

        if (!file_exists($imageUrl) || empty($vendor['logo'])) {
            return $this->getSkinUrl($this->__('images/logo.gif'));
        }
        //if we have the resized file
        if (Mage::getStoreConfig('vendor/general/logo_resize') == 1) {
            //resize the logo
            // path of the resized image to be saved
            // here, the resized image is saved in media/resized folder
            $imageResized = Mage::getBaseDir('media') . DS . 'vendor' . DS . $vendor['vendor_id'] . DS . 'resized_' . basename($vendor['logo']);

            if (!file_exists($imageResized)) {

                $imageObj = new Varien_Image($imageUrl);
                $imageObj->constrainOnly(TRUE);
                $imageObj->keepAspectRatio(TRUE);
                $imageObj->keepFrame(FALSE);
                if (Mage::getStoreConfig('vendor/general/logo_height') > 0) {
                    $imageObj->resize(Mage::getStoreConfig('vendor/general/logo_width'), Mage::getStoreConfig('vendor/general/logo_height'));
                } else {
                    $imageObj->resize(Mage::getStoreConfig('vendor/general/logo_width'));
                }
                $imageObj->save($imageResized);
            }

            return Mage::getBaseUrl('media') . 'vendor/' . $vendor['vendor_id'] . '/' . 'resized_' . basename($vendor['logo']);
        } else {
            return Mage::getBaseUrl('media') . $vendor['logo'];
        }
    }
    /** @desc: get Resource Logo At Front end
     *  @author: nhut.nguyen
     *  @param: vendor with format array or object
     *  @return: url rezise vendor;
     */
    public function getLogoSrcFrontEnd($vendor) {
        
       //$helper = Mage::app()->getHelper('vendor');
        //check if the vendor is registered
        if(is_object($vendor)){
            $vendor = $vendor->getData();
        }
        //var_dump($vendor);
        
        
        $imageUrl = Mage::getBaseDir('media') . DS . $vendor['logo'];
        
        if (!file_exists($imageUrl) || empty($vendor['logo'])) {
            return $this->getSkinUrl($this->__('images/logo.gif'));
        }
        //if we have the resized file
        if (Mage::getStoreConfig('vendor/general/logo_resize') == 1) {
            //resize the logo
            // path of the resized image to be saved
            // here, the resized image is saved in media/resized folder
            $imageResized = Mage::getBaseDir('media') . DS . 'vendor' . DS . $vendor['vendor_id'] . DS . 'resized_' . basename($vendor['logo']);

            if (!file_exists($imageResized)) {

                $imageObj = new Varien_Image($imageUrl);
                $imageObj->constrainOnly(TRUE);
                $imageObj->keepAspectRatio(TRUE);
                $imageObj->keepFrame(FALSE);
                if (Mage::getStoreConfig('vendor/general/logo_height') > 0) {
                    $imageObj->resize(Mage::getStoreConfig('vendor/general/logo_width'), Mage::getStoreConfig('vendor/general/logo_height'));
                } else {
                    $imageObj->resize(Mage::getStoreConfig('vendor/general/logo_width'));
                }
                $imageObj->save($imageResized);
            }

            return Mage::getBaseUrl('media') . 'vendor/' . $vendor['vendor_id'] . '/' . 'resized_' . basename($vendor['logo']);
        } else {
            return Mage::getBaseUrl('media') . $vendor['logo'];
        }
    }
    public function getProductListFromVendor($vendor_id=false){
       
        if(empty($vendor_id)){
            $vendor_id = $this->getRequest()->getParam("vendor_id");
        }
        $helper 		= Mage::app()->getHelper('vendor');
        $vendor_data = $helper->getVendorUserInfo($vendor_id);
		

        $collection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('manufacturer')
            ->addAttributeToSelect('short_description')
            ->addAttributeToSelect("image")
            ->addAttributeToSelect("small_image")
            ->addAttributeToSelect("url_key")        
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('attribute_set_id')
            ->addAttributeToSelect('type_id')
            ->joinField('qty',
                'cataloginventory/stock_item',
                'qty',
                'product_id=entity_id',
                '{{table}}.stock_id=1',
                'left')
            ->addAttributeToFilter('manufacturer', $vendor_data['vendor_code'])
			->setOrder("entity_id","desc");
		 //khanh.phan
		
		if($cat=$this->getRequest()->getParam("cat")){
		$category=Mage::getModel('catalog/category')->load($cat);
		 $collection->addCategoryFilter($category);
		 }
		
		Mage::getSingleton('catalog/product_status')->addSaleableFilterToCollection($collection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);
        Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($collection);
		$page = $this->getRequest()->getParam('p');
        if(empty($page)){
            $page = 1;
        }
		
		$page_element = Mage::getStoreConfig('catalog/frontend/grid_per_page');
		$collection->setPageSize($page_element);
		$collection->setCurPage($page);
	
        //  end  
        return  $collection;
            

    }
	//khanh.phan
	public function getPaginationProductListFromVendor() {
	
	 $page_element = Mage::getStoreConfig('catalog/frontend/grid_per_page');	
	 $total=$this->getCountProductListFromVendor();
	 $last = intval(ceil($total / $page_element));
	 $page = $this->getRequest()->getParam('p');
        if(empty($page)){
            $page = 1;
        }elseif ($page > $last) {
            $page = $last;
        }
        return array(
            "page" => intval($page),
            "last" => $last
          );
	 
	}
	public function getCountProductListFromVendor($vendor_id=false){
       
        if(empty($vendor_id)){
            $vendor_id = $this->getRequest()->getParam("vendor_id");
        }
        $helper 		= Mage::app()->getHelper('vendor');
        $vendor_data = $helper->getVendorUserInfo($vendor_id);

        $collection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToFilter('manufacturer', $vendor_data['vendor_code']);
		Mage::getSingleton('catalog/product_status')->addSaleableFilterToCollection($collection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);
        Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($collection);
	
        return  $collection->count();
            

    }
	
    

}