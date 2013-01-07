<?php

$installer = $this;
$installer->startSetup();
$installer->run("       
  ALTER TABLE `{$this->getTable('vendors')}`  ADD ( position_a int(10) unsigned  NULL, position_b int(10) unsigned NULL )" );
$installer->endSetup();



