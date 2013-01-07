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
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog fieldset element renderer
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class FavoredMinds_Vendor_Block_Catalog_Form_Renderer_Fieldset_Element
    extends Mage_Adminhtml_Block_Catalog_Form_Renderer_Fieldset_Element
{
    /**
     * Initialize block template
     */
    var $_is_login = false;
    protected function _construct()
    {
        
        $_helper = Mage::app()->getHelper('vendor');
        
        $this->_is_login = $_helper->vendorIsLogged();

        if ($this->_is_login == true) {

            $this->setTemplate('vendor/catalog/form/renderer/fieldset/element.phtml');
        }
        else{
            $this->setTemplate('catalog/form/renderer/fieldset/element.phtml');
        }   
    }
    public function render(Varien_Data_Form_Element_Abstract $element){

        $this->_element = $element;

        $id = $element->getHtmlId();

        $hiding = $this->getElementHiding();
        
        if($this->_is_login==true){
            if(in_array($id,$hiding)){
                return false;
            }
        }
        
        return $this->toHtml();
    }
    public function getElementHiding(){

        return array("simple_product_weight",
                     "simple_product_sku",
                     "simple_product_status");
    }
}
