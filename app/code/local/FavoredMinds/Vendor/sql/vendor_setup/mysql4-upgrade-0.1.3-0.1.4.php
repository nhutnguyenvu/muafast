<?php

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$configValuesMap = array(
  'vendor/apply/email_template' =>
  'vendor_apply_email_template',
);

foreach ($configValuesMap as $configPath=>$configValue) {
    $installer->setConfigData($configPath, $configValue);
}


