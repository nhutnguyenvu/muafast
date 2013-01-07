<?php

$installer = $this;
$installer->startSetup();
$installer->run("           
  ALTER TABLE `{$this->getTable('vendors')}`  ADD about_mshort_company VARCHAR(255) NULL DEFAULT '' " );
$installer->endSetup();



