<?php

$installer = $this;
$installer->startSetup();
$installer->run("           
  ALTER TABLE `{$this->getTable('vendors')}` ADD is_saling TINYINT (1) NOT NULL DEFAULT 0, ADD about_short_company VARCHAR(255) NULL DEFAULT '' " );
$installer->endSetup();



