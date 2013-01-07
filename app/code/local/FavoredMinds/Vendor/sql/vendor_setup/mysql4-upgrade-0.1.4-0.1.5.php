<?php

$installer = $this;
$installer->startSetup();
$installer->run(" 
        ALTER TABLE `{$this->getTable('vendors_sales_history')}` ADD product_name VARCHAR(255) NOT NULL DEFAULT '' ");
$installer->endSetup();


