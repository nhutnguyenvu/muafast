<?php

$installer = $this;
$installer->startSetup();
$installer->run(" 
            
   DROP TABLE IF EXISTS `{$this->getTable('vendors_statements_detailshow')}`;

    CREATE TABLE `{$this->getTable('vendors_statements_detailshow')}` (
  `vendor_statement_detail_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `vendor_statement_id` int(10) unsigned DEFAULT NULL,
  `order_id` int(10) DEFAULT NULL,
  `vendor_id` int(10) DEFAULT NULL,
  `total_price` float DEFAULT NULL,
  `total_payout` float DEFAULT NULL,
  `total_shipping` float DEFAULT NULL,  
  `total_quantity` int(10) DEFAULT NULL,
  `order_detail_information` text COLLATE utf8_unicode_ci,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`vendor_statement_detail_id`),
  KEY `FK_vendor_statement_detail` (`vendor_statement_id`),
  CONSTRAINT `FK_vendor_statement_detail` FOREIGN KEY (`vendor_statement_id`) REFERENCES `vendors_statements` (`vendor_statement_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");
    
$installer->endSetup();