<?php 

    $installer = $this;
    $installer->startSetup();
    $installer->run(" 
            ALTER TABLE vendors ADD num_rate INT (11) NOT NULL DEFAULT '0' ");
$installer->endSetup();
