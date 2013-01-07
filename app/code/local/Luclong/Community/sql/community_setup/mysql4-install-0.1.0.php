<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('community')};
CREATE TABLE {$this->getTable('community')} (
  `community_id` int(11) unsigned NOT NULL auto_increment,
  `product_id` int(11),
  `like` int(11),
  `comment` int(11),
  `share` int(11),
  `buy` int(11),
  `status` smallint(6) NOT NULL default '0',
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`community_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup(); 