<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('mixmatch')};
CREATE TABLE {$this->getTable('mixmatch')} (
  `mixmatch_id` int(11) unsigned NOT NULL auto_increment,
  `user_id` int(11) unsigned NOT NULL,
  `product_id` int(11) unsigned NOT NULL,
  `image` varchar(255) NOT NULL default '',
  `like` int(11) unsigned NULL,
  `share` int(11) unsigned NULL,
  `comment` int(11) unsigned NULL,
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`mixmatch_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup(); 