<?php
/**
* qikDropShipper Extension
*
* @category   FavoredMinds
* @package    Vendor
* @author     FavoredMinds Solution (geedubya15@gmail.com)
* @copyright  Copyright (c) 2010 FavoredMinds.com (http://www.favoredminds.com)
*/

class FavoredMinds_Vendor_Block_Adminhtml_Page_Menu extends Mage_Adminhtml_Block_Page_Menu {
	// overwrite menu restrictions in order to allow vendors see their menu options
	protected function _checkAcl($resource) {
		$helper 		= Mage::app()->getHelper('vendor');
		$vendorlogged	= $helper->vendorIsLogged();
		return parent::_checkAcl($resource);
	}
	
	protected function _buildMenuArray(Varien_Simplexml_Element $parent=null, $path='', $level=0)
    {
        if (is_null($parent)) {
            $parent = Mage::getSingleton('admin/config')->getAdminhtmlConfig()->getNode('menu');
        }
		
		if($this->_isAdmin() and isset($parent->favoredminds_vendor->children->info)) {
			unset($parent->favoredminds_vendor->children->info);
		}
		
		$parentArr = array();
        $sortOrder = 0;
        foreach ($parent->children() as $childName => $child) {
            if (1 == $child->disabled) {
                continue;
            }

            $aclResource = 'admin/' . ($child->resource ? (string)$child->resource : $path . $childName);
            if (!$this->_checkAcl($aclResource)) {
                continue;
            }

            if ($child->depends && !$this->_checkDepends($child->depends)) {
                continue;
            }

            $menuArr = array();

            $menuArr['label'] = $this->_getHelperValue($child);

            $menuArr['sort_order'] = $child->sort_order ? (int)$child->sort_order : $sortOrder;

            if ($child->action) {
                $menuArr['url'] = $this->_url->getUrl((string)$child->action, array('_cache_secret_key' => true));
            } else {
                $menuArr['url'] = '#';
                $menuArr['click'] = 'return false';
            }

            $menuArr['active'] = ($this->getActive()==$path.$childName)
                || (strpos($this->getActive(), $path.$childName.'/')===0);

            $menuArr['level'] = $level;

            if ($child->children) {
                $menuArr['children'] = $this->_buildMenuArray($child->children, $path.$childName.'/', $level+1);
            }
            $parentArr[$childName] = $menuArr;

            $sortOrder++;
        }

        uasort($parentArr, array($this, '_sortMenu'));

        while (list($key, $value) = each($parentArr)) {
            $last = $key;
        }
        if (isset($last)) {
            $parentArr[$last]['last'] = true;
        }
		
        return $parentArr;
    }

    protected function _isAdmin(){
        $userRole = Mage::getSingleton('admin/session')->getUser()->getRole();
        $roleName = $userRole->getRoleName();
        $roleId = $userRole->getRoleId();
        if ($roleName == 'Administrators' || $roleId == 1) {
            return true;
        }
		return false;
    }
}

?>