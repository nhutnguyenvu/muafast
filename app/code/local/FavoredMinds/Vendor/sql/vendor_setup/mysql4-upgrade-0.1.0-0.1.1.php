<?php 

    $installer = $this;
    $installer->startSetup();
    $installer->run(" 
            ALTER TABLE vendors ADD on_home TINYINT (1) NOT NULL DEFAULT '0',
            ADD on_slide TINYINT (1) NOT NULL  DEFAULT '0' ");
$installer->endSetup();
