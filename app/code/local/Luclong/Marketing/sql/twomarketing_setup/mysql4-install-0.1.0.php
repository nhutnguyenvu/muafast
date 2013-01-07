<?php

$installer = $this;

$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS {$this->getTable('marketing')};
CREATE TABLE {$this->getTable('marketing')} (
  `marketing_id` int(11) unsigned NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `face_id` varchar(255) NOT NULL default '',
  `count_like` int(11) NOT NULL default '0',
  `photo` varchar(100) NOT NULL default '',
  `description` text NOT NULL default '',
  `status` smallint(6) NOT NULL default '0',
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`marketing_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");
	
$installer->endSetup(); 