<?php

$installer = $this;
$installer->startSetup();
$installer->run("           
  ALTER TABLE `{$this->getTable('vendors')}` ADD path VARCHAR(255) NOT NULL DEFAULT '' ");   
$installer->endSetup();