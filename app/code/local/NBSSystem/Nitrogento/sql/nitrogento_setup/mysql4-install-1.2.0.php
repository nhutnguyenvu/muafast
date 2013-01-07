<?php

$installer = $this;
$installer->startSetup();

if (!$installer->tableExists($installer->getTable('cache_blockhtml_config'))) 
{

$installer->run("
DROP TABLE IF EXISTS {$this->getTable('cache_blockhtml_config')};
CREATE TABLE {$this->getTable('cache_blockhtml_config')} (
    `config_id` int(11) unsigned NOT NULL auto_increment,
    `block_class` varchar(255) NOT NULL default '',
    `friendly_entry` varchar(255) NOT NULL default '',
    `block_template` varchar(255) NOT NULL default '',
    `helper_class` varchar(500) NOT NULL default '',
    `cache_lifetime` int(11) NOT NULL default 3600,
    `store_id` text,
    `activated` smallint(1) NOT NULL default 1,
    PRIMARY KEY (`config_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO {$this->getTable('cache_blockhtml_config')} (`block_class`, `friendly_entry`, `block_template`, `helper_class`, `cache_lifetime`, `store_id`, `activated`) VALUES
('Mage_Catalog_Block_Product_List', 'Catalog View', 'catalog/product/list.phtml', 'nitrogento/cache_blockhtml_catalog_product_list_impl', 3600, 'a:1:{i:0;i:0;}', 1),
('Mage_Page_Block_Html_Breadcrumbs', 'Breadcrumbs', 'page/html/breadcrumbs.phtml', 'nitrogento/cache_blockhtml_generic_dynamic_impl', 3600, 'a:1:{i:0;i:0;}', 1),
('Mage_Catalog_Block_Product_View', 'Product Detail', 'catalog/product/view.phtml', 'nitrogento/cache_blockhtml_catalog_product_view_impl', 3600, 'a:1:{i:0;i:0;}', 1),
('Mage_Core_Block_Template', '', 'directory/js/optional_zip_countries.phtml', 'nitrogento/cache_blockhtml_generic_static_impl', 3600, 'a:1:{i:0;i:0;}', 1),
('Mage_Core_Block_Template', '', 'callouts/left_col.phtml', 'nitrogento/cache_blockhtml_generic_static_impl', 3600, 'a:1:{i:0;i:0;}', 1),
('Mage_Core_Block_Template', '', 'callouts/right_col.phtml', 'nitrogento/cache_blockhtml_generic_static_impl', 3600, 'a:1:{i:0;i:0;}', 1),
('Mage_Checkout_Block_Cart', 'Checkout Cart empty', 'checkout/cart/noItems.phtml', 'nitrogento/cache_blockhtml_generic_static_impl', 3600, 'a:1:{i:0;i:0;}', 1),
('Mage_CatalogSearch_Block_Advanced_Form', 'Catalog Advanced Search Form', 'catalogsearch/advanced/form.phtml', 'nitrogento/cache_blockhtml_generic_static_impl', 3600, 'a:1:{i:0;i:0;}', 1);
");

}

if (!$installer->tableExists($installer->getTable('cache_blockhtml_learning'))) 
{

$installer->run("
DROP TABLE IF EXISTS {$this->getTable('cache_blockhtml_learning')};
CREATE TABLE {$this->getTable('cache_blockhtml_learning')} (
    `learning_id` int(11) unsigned NOT NULL auto_increment,
    `block_class` varchar(255) NOT NULL default '',
    `block_template` varchar(255) NOT NULL default '',
    `store_id` varchar(6),
    `count` int(11) NOT NULL default 1,
    `total_time` float NOT NULL,
    PRIMARY KEY (`learning_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

}

if (!$installer->tableExists($installer->getTable('cache_fullpage_config'))) 
{

$installer->run("
DROP TABLE IF EXISTS {$this->getTable('cache_fullpage_config')};
CREATE TABLE {$this->getTable('cache_fullpage_config')} (
    `config_id` int(11) unsigned NOT NULL auto_increment,
    `full_action_name` varchar(255) NOT NULL default '',
    `url_exceptions` text NOT NULL default '',
    `friendly_entry` varchar(255) NOT NULL default '',
    `helper_class` varchar(500) NOT NULL default '',
    `cache_lifetime` int(11) NOT NULL default 3600,
    `store_id` text,
    `activated` smallint(1) NOT NULL default 1,
    PRIMARY KEY (`config_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO {$this->getTable('cache_fullpage_config')} (`full_action_name`, `url_exceptions`, `friendly_entry`, `helper_class`, `cache_lifetime`, `store_id`, `activated`) VALUES
('cms_index_index', '', 'Homepage', 'nitrogento/cache_fullpage_cms_index_index_impl', 3600, 'a:1:{i:0;i:0;}', 1),
('catalog_product_view', '', 'Product View', 'nitrogento/cache_fullpage_catalog_product_view_impl', 3600, 'a:1:{i:0;i:0;}', 1),
('catalog_category_view', '', 'Category View', 'nitrogento/cache_fullpage_catalog_category_view_impl', 3600, 'a:1:{i:0;i:0;}', 1),
('cms_page_view', '', 'Cms Page', 'nitrogento/cache_fullpage_cms_page_view_impl', 3600, 'a:1:{i:0;i:0;}', 1),
('customer_account_login', '', 'Login Page', 'nitrogento/cache_fullpage_generic_impl', 3600, 'a:1:{i:0;i:0;}', 1),
('checkout_cart_index', '', 'Checkout Cart empty', 'nitrogento/cache_fullpage_generic_impl', 3600, 'a:1:{i:0;i:0;}', 1),
('contacts_index_index', '', 'Page Contact', 'nitrogento/cache_fullpage_generic_impl', 3600, 'a:1:{i:0;i:0;}', 1);
");

}

$installer->endSetup(); 