<?php
/**
* qikDropShipper Extension
*
* @category   FavoredMinds
* @package    Vendor
* @author     FavoredMinds Solution (geedubya15@gmail.com)
* @copyright  Copyright (c) 2010 FavoredMinds.com (http://www.favoredminds.com)
*/
$installer = $this;

$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS `{$this->getTable('vendors')}`;
CREATE TABLE  `{$this->getTable('vendors')}` (
  `vendor_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `city` varchar(255) NOT NULL,
  `state` varchar(255) NOT NULL,
  `zip` varchar(45) NOT NULL,
  `country` varchar(45) NOT NULL,
  `url` text NOT NULL,
  `salesperweek` int(10) unsigned NOT NULL,
  `aboutcompany` text NOT NULL,
  `paypal` varchar(255) NOT NULL,
  `status` int(10) unsigned NOT NULL,
  `unencrypted_pass` varchar(255) NOT NULL,
  `company_name` varchar(45) NOT NULL,
  `commission` int(10) unsigned NOT NULL DEFAULT '40',
  `automatic_paypal` int(10) unsigned NOT NULL DEFAULT '0',
  `logo` varchar(255) NOT NULL DEFAULT '',
  `vendor_code` int(10) unsigned NOT NULL DEFAULT '0',
  `sku_prefix` varchar(20) NOT NULL DEFAULT '',
   created_time         datetime default NULL,
   update_time          datetime default NULL,
  PRIMARY KEY (`vendor_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$this->getTable('vendors_sales')};
create table {$this->getTable('vendors_sales')}
(
   vendorsales_id       int(11) unsigned not null auto_increment,
   vendor_id            int(11),
   order_id             varchar(255) not null default '',
   order_real_id        int(11),
   created_time         datetime default NULL,
   update_time          datetime default NULL,
   primary key (vendorsales_id)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$this->getTable('vendors_sales_history')};
create table {$this->getTable('vendors_sales_history')}
(
   vendorsales_id       int(11),
   vendor_sales_history_id int(11) not null auto_increment,
   vendor_sales_item_id int(11),
   created_date         datetime,
   tracking_code        varchar(255),
   comments             text,
   status        varchar(255),
   adjustment        decimal,
   primary key (vendor_sales_history_id)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$this->getTable('vendors_sales_items')};
create table {$this->getTable('vendors_sales_items')}
(
   vendor_sales_item_id int(11) not null auto_increment,
   vendorsales_id       int(11),
   item_id              int(11),
   product_id           int(11),
   price                decimal,
   shipping_price       decimal,
   primary key (vendor_sales_item_id)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$this->getTable('vendors_statements')};
CREATE TABLE IF NOT EXISTS `{$this->getTable('vendors_statements')}` (
`vendor_statement_id` int(11) unsigned NOT NULL auto_increment,
`vendor_id` int(10) unsigned NOT NULL,
`statement_id` varchar(30) NOT NULL,
`statement_filename` varchar(128) not null,
`statement_period` varchar(20) not null,
`order_date_from` date not null,
`order_date_to` date not null,
`total_orders` mediumint not null,
`total_comission` decimal(12,4) not null,
`total_payout` decimal(12,4) not null,
`created_at` datetime not null,
`orders_data` longtext not null,
`email_sent` tinyint not null,
`status` tinyint not null default '2',
PRIMARY KEY  (`vendor_statement_id`),
KEY `FK_vendor_vendor_statement` (`vendor_id`),
KEY `IDX_statement_period` (`statement_period`),
KEY `IDX_vendor_id` (`vendor_id`),
KEY `IDX_email_sent` (`email_sent`),
UNIQUE `IDX_statement_id` (`statement_id`),
CONSTRAINT `FK_vendor_statement` FOREIGN KEY (`vendor_id`) REFERENCES `{$this->getTable('vendors')}` (`vendor_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

    ");

$installer->endSetup(); 