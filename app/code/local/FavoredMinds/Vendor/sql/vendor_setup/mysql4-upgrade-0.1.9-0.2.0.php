<?php

$installer = $this;
$installer->startSetup();
$installer->run("           
  ALTER TABLE `{$this->getTable('sales_flat_order_item')}`  ADD item_checked_status INT(1) NULL DEFAULT 0 " );
$installer->endSetup();



