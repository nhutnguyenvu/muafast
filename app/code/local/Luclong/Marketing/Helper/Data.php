<?php

class Luclong_Marketing_Helper_Data extends Mage_Core_Helper_Abstract
{
    // Check user uploaded photo
    public function checkUserUpload($fbid){
        $data = Mage::getModel('marketing/marketing')
                    ->getCollection()
                    ->addFieldToFilter('face_id',$fbid);
        return $data->count();
    }
	
	public function resize($image,$width,$height){
		$imageUrl = Mage::getBaseDir('media') . DS .'marketing'.DS. $image;
		$imageResized = Mage::getBaseDir('media') . DS . 'marketing' . DS . 'resized'.DS.$image;
		if (!file_exists($imageResized)) {

                $imageObj = new Varien_Image($imageUrl);
                $imageObj->constrainOnly(TRUE);
                $imageObj->keepAspectRatio(TRUE);
                $imageObj->keepFrame(FALSE);
             
                $imageObj->resize(null,$height);
				
				$width_Imageresize = $imageObj->getOriginalWidth();
                $val = ($width_Imageresize - $width) /2 ;
                $imageObj->crop(0, $val, $val,0);
				
                $imageObj->save($imageResized);
            }
		  return Mage::getBaseUrl('media') . 'marketing/resized/' .$image;
	}
	
	public function resizecustom($image,$width,$height){
		$image_arr = explode(".",$image);
		$image_new = $image_arr[0]."-".$width."x".$height.".".$image_arr[1];
		
		$imageUrl = Mage::getBaseDir('media') . DS .'marketing'.DS. $image;
		$imageResized = Mage::getBaseDir('media') . DS . 'marketing' . DS . 'custom'.DS.$image_new;
		if (!file_exists($imageResized)) {
			
                $imageObj = new Varien_Image($imageUrl);
                $imageObj->constrainOnly(TRUE);
                $imageObj->keepAspectRatio(TRUE);
                $imageObj->keepFrame(FALSE);
             
                $imageObj->resize(null,$height);
				
				$width_Imageresize = $imageObj->getOriginalWidth();
                $val = ($width_Imageresize - $width) /2 ;
                $imageObj->crop(0, $val, $val,0);
				
                $imageObj->save($imageResized);
            }
		  return Mage::getBaseUrl('media') . 'marketing/custom/' .$image_new;
	}
    
    public function resizemini($image,$width,$height){
		$imageUrl = Mage::getBaseDir('media') . DS .'marketing'.DS. $image;
		$imageResized = Mage::getBaseDir('media') . DS . 'marketing' . DS . 'mini'.DS.$image;
		if (!file_exists($imageResized)) {

                $imageObj = new Varien_Image($imageUrl);
                $imageObj->constrainOnly(TRUE);
                $imageObj->keepAspectRatio(TRUE);
                $imageObj->keepFrame(FALSE);
             
                $imageObj->resize(null,$height);
				
				$width_Imageresize = $imageObj->getOriginalWidth();
                $val = ($width_Imageresize - $width) /2 ;
                $imageObj->crop(0, $val, $val,0);
				
                $imageObj->save($imageResized);
            }
		  return Mage::getBaseUrl('media') . 'marketing/mini/' .$image;
	}
}