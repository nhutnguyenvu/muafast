<?php 

    $installer = $this;
    $installer->startSetup();
    $installer->run(" 
            ALTER TABLE vendors ADD average_rate INT (2) NOT NULL DEFAULT '0';
            
            DROP TABLE IF EXISTS `customer_rating`;

            CREATE TABLE `customer_rating` (
                `id` int(10) NOT NULL AUTO_INCREMENT,
                `customer_id` int(10) unsigned DEFAULT NULL,
                `vendor_id` int(10) unsigned DEFAULT NULL,
                `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
                `Comment` text COLLATE utf8_unicode_ci,
                `rate` int(2) DEFAULT '0',
            PRIMARY KEY (`id`),
            KEY `FK_user_rating` (`vendor_id`),
            KEY `FK_user_rating_customer` (`customer_id`),
            CONSTRAINT `FK_user_rating` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`vendor_id`) ON DELETE CASCADE,
            CONSTRAINT `FK_user_rating_customer` FOREIGN KEY (`customer_id`) REFERENCES `customer_entity` (`entity_id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
            ");
    
$installer->endSetup();