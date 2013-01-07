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
 * admin product edit tabs
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
 
class FavoredMinds_Vendor_Block_Catalog_Product_Edit_Tabs extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tabs
{
    const GROUP_INFO_AO = 262;
    const GROUP_INFO_DAM = 263;
    const GROUP_INFO_DOLOT = 264;
    const GROUP_INFO_GIAY = 265;
    const GROUP_INFO_NUOCHOA = 266;
    const GROUP_INFO_PHUKIEN = 267;
    const GROUP_INFO_QUAN = 268;
    const GROUP_INFO_VAY = 269;
    const GROUP_INFO_MYPHAM = 279;
    const GROUP_PRODUCT_IMAGE_NAME = "Images";


    protected function _prepareLayout()
    {
        $product = $this->getProduct();

        if (!($setId = $product->getAttributeSetId())) {
            $setId = $this->getRequest()->getParam('set', null);
        }

        if ($setId) {
            
            $groupCollection = Mage::getResourceModel('eav/entity_attribute_group_collection')
                ->setAttributeSetFilter($setId)
                ->setSortOrder()
                ->load();
            

            $_helper = Mage::app()->getHelper('vendor');
            $is_login = $_helper->vendorIsLogged();
            
            foreach ($groupCollection as $group) {
                $attributes = $product->getAttributes($group->getId(), true);
                // do not add groups without attributes

                foreach ($attributes as $key => $attribute) {
                    if( !$attribute->getIsVisible() ) {
                        unset($attributes[$key]);
                    }
                }

                if (count($attributes)==0) {
                    continue;
                }
                
                //echo $group->getId().$group->getAttributeGroupName()."__";
                $group_name = $group->getAttributeGroupName();
                if ($is_login) {
                    
                    $group_id = $group->getId();
                                        
                    $group_info = $this->getGroupInfoList();

                    if(in_array($group_id,$group_info) || strtolower($group_name) == strtolower(self::GROUP_PRODUCT_IMAGE_NAME)){
                        $this->addTab('group_'.$group->getId(), array(
                        'label'     => Mage::helper('catalog')->__($group->getAttributeGroupName()),
                        'content'   => $this->_translateHtml($this->getLayout()->createBlock($this->getAttributeTabBlock(),
                            'adminhtml.catalog.product.edit.tab.attributes')->setGroup($group)
                                ->setGroupAttributes($attributes)
                                ->toHtml()),
                        ));
                    }
                    
                    
                }
                else{
                    $this->addTab('group_'.$group->getId(), array(
                    'label'     => Mage::helper('catalog')->__($group->getAttributeGroupName()),
                    'content'   => $this->_translateHtml($this->getLayout()->createBlock($this->getAttributeTabBlock(),
                        'adminhtml.catalog.product.edit.tab.attributes')->setGroup($group)
                            ->setGroupAttributes($attributes)
                            ->toHtml()),
                    ));
                }
                
            }
            
            if (Mage::helper('core')->isModuleEnabled('Mage_CatalogInventory')) {
                $this->addTab('inventory', array(
                    'label'     => Mage::helper('catalog')->__('Inventory'),
                    'content'   => $this->_translateHtml($this->getLayout()
                        ->createBlock('adminhtml/catalog_product_edit_tab_inventory')->toHtml()),
                ));
            }
            
            /**
             * Don't display website tab for single mode
             */
            $_user = Mage::getSingleton('admin/session')->getUser();
            $role  =  $_user->getRole();
            $role_id = $role->getId();
            
            
            if (!$is_login) {
                if (!Mage::app()->isSingleStoreMode()) {
                    $this->addTab('websites', array(
                        'label'     => Mage::helper('catalog')->__('Websites'),
                        'content'   => $block = $this->getLayout()
                            ->createBlock('adminhtml/catalog_product_edit_tab_websites')->toHtml()
                    ));
                }
            }
            
            $this->addTab('categories', array(
                'label'     => Mage::helper('catalog')->__('Categories'),
                'url'       => $this->getUrl('*/*/categories', array('_current' => true)),
                'class'     => 'ajax',
            ));
            
            
            if (!$is_login) {
                
                $this->addTab('related', array(
                    'label'     => Mage::helper('catalog')->__('Related Products'),
                    'url'       => $this->getUrl('*/*/related', array('_current' => true)),
                    'class'     => 'ajax',
                ));

                $this->addTab('upsell', array(
                    'label'     => Mage::helper('catalog')->__('Up-sells'),
                    'url'       => $this->getUrl('*/*/upsell', array('_current' => true)),
                    'class'     => 'ajax',
                ));

                $this->addTab('crosssell', array(
                    'label'     => Mage::helper('catalog')->__('Cross-sells'),
                    'url'       => $this->getUrl('*/*/crosssell', array('_current' => true)),
                    'class'     => 'ajax',
                ));
            }
            
            $storeId = 0;
            
            if ($this->getRequest()->getParam('store')) {
                $storeId = Mage::app()->getStore($this->getRequest()->getParam('store'))->getId();
            }

            $alertPriceAllow = Mage::getStoreConfig('catalog/productalert/allow_price');
            $alertStockAllow = Mage::getStoreConfig('catalog/productalert/allow_stock');

            if (($alertPriceAllow || $alertStockAllow) && !$product->isGrouped()) {
                $this->addTab('productalert', array(
                    'label'     => Mage::helper('catalog')->__('Product Alerts'),
                    'content'   => $this->_translateHtml($this->getLayout()
                        ->createBlock('adminhtml/catalog_product_edit_tab_alerts', 'admin.alerts.products')->toHtml())
                ));
            }

            if( $this->getRequest()->getParam('id', false) ) {
                if (Mage::helper('catalog')->isModuleEnabled('Mage_Review')) {
                    if (Mage::getSingleton('admin/session')->isAllowed('admin/catalog/reviews_ratings')){
                        $this->addTab('reviews', array(
                            'label' => Mage::helper('catalog')->__('Product Reviews'),
                            'url'   => $this->getUrl('*/*/reviews', array('_current' => true)),
                            'class' => 'ajax',
                        ));
                    }
                }
                if (Mage::helper('catalog')->isModuleEnabled('Mage_Tag')) {
                    if (Mage::getSingleton('admin/session')->isAllowed('admin/catalog/tag')){
                        $this->addTab('tags', array(
                         'label'     => Mage::helper('catalog')->__('Product Tags'),
                         'url'   => $this->getUrl('*/*/tagGrid', array('_current' => true)),
                         'class' => 'ajax',
                        ));

                        $this->addTab('customers_tags', array(
                            'label'     => Mage::helper('catalog')->__('Customers Tagged Product'),
                            'url'   => $this->getUrl('*/*/tagCustomerGrid', array('_current' => true)),
                            'class' => 'ajax',
                        ));
                    }
                }

            }

            /**
             * Do not change this tab id
             * @see Mage_Adminhtml_Block_Catalog_Product_Edit_Tabs_Configurable
             * @see Mage_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tabs
             */
            
            if (!$is_login) {
                if (!$product->isGrouped()) {
                    $this->addTab('customer_options', array(
                        'label' => Mage::helper('catalog')->__('Custom Options'),
                        'url'   => $this->getUrl('*/*/options', array('_current' => true)),
                        'class' => 'ajax',
                    ));
                }
            }
            

        }
        else {
            $this->addTab('set', array(
                'label'     => Mage::helper('catalog')->__('Settings'),
                'content'   => $this->_translateHtml($this->getLayout()
                    ->createBlock('adminhtml/catalog_product_edit_tab_settings')->toHtml()),
                'active'    => true
            ));
        }
        
        //return parent::_prepareLayout();
    }
    public function getGroupInfoList(){

        return array(self::GROUP_INFO_AO,
                     self::GROUP_INFO_DAM,
                     self::GROUP_INFO_DOLOT,
                     self::GROUP_INFO_GIAY,
                     self::GROUP_INFO_NUOCHOA,
                     self::GROUP_INFO_PHUKIEN,
                     self::GROUP_INFO_QUAN,
                     self::GROUP_INFO_VAY,
                     self::GROUP_INFO_MYPHAM

            );
    }
    
}
