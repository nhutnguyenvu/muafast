<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('product_list')};
CREATE TABLE {$this->getTable('product_list')} (
  `id` int(11) unsigned NOT NULL auto_increment,
  `product_id` varchar(255),
  `status` smallint(6) NOT NULL default '0',
  `error` varchar(100),
  `created_time` datetime NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup(); 