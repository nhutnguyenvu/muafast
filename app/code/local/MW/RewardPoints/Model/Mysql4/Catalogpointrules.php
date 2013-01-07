<?php

class MW_Rewardpoints_Model_Mysql4_Catalogpointrules extends Mage_Core_Model_Mysql4_Abstract
{
    const SECONDS_IN_DAY = 86400;

    public function _construct()
    {    
        $this->_init('rewardpoints/catalogpointrules', 'rule_id');
    }
    
    /**
     * Prepare object data for saving
     *
     * @param Mage_Core_Model_Abstract $object
     */
    public function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if (!$object->getFromDate()) {
            $date = Mage::app()->getLocale()->date();
            $date->setHour(0)
                ->setMinute(0)
                ->setSecond(0);
            $object->setFromDate($date);
        }
        if ($object->getFromDate() instanceof Zend_Date) {
            $object->setFromDate($object->getFromDate()->toString(Varien_Date::DATETIME_INTERNAL_FORMAT));
        }
       

        if (!$object->getToDate()) {
            $object->setToDate(new Zend_Db_Expr('NULL'));
        }
        else {
            if ($object->getToDate() instanceof Zend_Date) {
                $object->setToDate($object->getToDate()->toString(Varien_Date::DATETIME_INTERNAL_FORMAT));
            }
        }
        parent::_beforeSave($object);
    }
}