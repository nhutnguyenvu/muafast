<?php
class Luclong_Marketing_Block_Adminhtml_Renderer_User extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
 
    public function render(Varien_Object $row)
    {
   	   $user_id = $row->getData('user_id');
       $customer = Mage::getModel("customer/customer")->load($user_id);
       $first = $customer->getFirstname();
       $last =  $customer->getLastname();
       return $row = $first.' '.$last ;
    }
 
}