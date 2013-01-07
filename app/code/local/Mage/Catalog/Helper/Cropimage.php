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
 * Catalog image helper
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Helper_Cropimage extends Mage_Catalog_Helper_Image
{
  
	public function cropImage($width,$height,$image){
	// Mage::log("Your Log Message");
    // Mage::log("This is a log", null, custom.log);
       try{
		//str_replace(Mage::getBaseUrl('media'),Mage::getBaseDir('media').DS,$image); 
			if($image->getOriginalHeight() > $image->getOriginalWidth()){
				$image->keepFrame(false)
					  ->resize($width);
			}else{
				$image->keepFrame(false)
					  ->resize(null,$height);
			}
			$Imageresize = str_replace(Mage::getBaseUrl('media'),Mage::getBaseDir('media').DS,$image); 
			if (file_exists($Imageresize)) {
				
				$imageObj = new Varien_Image($Imageresize);
				$height_Imageresize = $imageObj->getOriginalHeight();
				$width_Imageresize = $imageObj->getOriginalWidth();
				if($height_Imageresize > $height){
					$val = ($height_Imageresize - $height) /2 ;
					$imageObj->crop($val, 0, 0,$val);
					$imageObj->save($Imageresize);
				}
				if($width_Imageresize > $width){
							$val = ($width_Imageresize - $width) /2 ;
							$imageObj->crop(0, $val, $val,0);
							$imageObj->save($Imageresize);
				}
				
			}
		}catch(Exception $e){}
		return $image;
	}
        
        
     
        
}
