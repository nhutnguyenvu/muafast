<?php
class Luclong_Marketing_Block_Marketing extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getMarketing()     
     { 
        if (!$this->hasData('marketing')) {
            $this->setData('marketing', Mage::registry('marketing'));
        }
        return $this->getData('marketing');
        
    }
    
    public function checkexistfb($fbid){
        $write = Mage :: getSingleton ( 'core/resource' )-> getConnection ( 'core_write' ); 
        $readresult = $write -> query("SELECT marketing_id 
                                            FROM ".Mage::getSingleton('core/resource')->getTableName('marketing')."
                                            WHERE face_id = '$fbid'");
        $result = $readresult -> fetchAll();
        if(!empty($result)) return true;
    }
    
    public function showDetail($imageId)
    {
        $write = Mage :: getSingleton ( 'core/resource' )-> getConnection ('core_write'); 
	    $readresult = $write -> query("SELECT * 
                                    FROM ".Mage::getSingleton('core/resource')->getTableName('marketing')."
                                    WHERE marketing_id = '$imageId' 
                                  ");
        $row = $readresult->fetch();
		if($row) return $row;
    }
	
	public function showImage($imageId)
    {
        $write = Mage :: getSingleton ( 'core/resource' )-> getConnection ('core_write'); 
	    $readresult = $write -> query("SELECT * 
                                    FROM ".Mage::getSingleton('core/resource')->getTableName('marketing')."
                                    WHERE marketing_id = '$imageId'
                                  ");
        $row = $readresult->fetch();
		if($row) return $row;
    }
    
    public function showCustomerName($customer_id){
        $customer = Mage::getModel("customer/customer")->load($customer_id);
        $first = $customer->getFirstname();
        $last =  $customer->getLastname();
        return $name = $first.' '.$last;
    }
    
   
    public function checkUser($session_fbid){
        $write = Mage :: getSingleton ( 'core/resource' )-> getConnection ('core_write'); 
        $readresult = $write -> query("SELECT count(face_id) AS tong
                                    FROM ".Mage::getSingleton('core/resource')->getTableName('marketing')."
                                    WHERE face_id = $session_fbid
                                  ");
        $row = $readresult->fetch();
        return $row['tong'];
    }
    
    public function checkImage($userId){
        $write = Mage :: getSingleton ( 'core/resource' )-> getConnection ('core_write'); 
        $readresult = $write -> query("SELECT marketing_id 
                                    FROM ".Mage::getSingleton('core/resource')->getTableName('marketing')."
                                    WHERE user_id = $userId
                                  ");
        $row = $readresult->fetch();
        return $row['marketing_id'];
    }
    public function checkLogin(){
        if(Mage::getStoreConfig('sociallogin/general/enable_sociallogin')==1 && !Mage::helper('customer')->isLoggedIn()){
            return false;
        }
        else return true;
    }
    
    public function resizeImages(){
        $img = Mage::getModel('marketing/marketing')->getCollection()->addFilter('status', 1);
        $imageUrl = Mage::getBaseDir('media') . DS . $vendor['logo'];
    }
}