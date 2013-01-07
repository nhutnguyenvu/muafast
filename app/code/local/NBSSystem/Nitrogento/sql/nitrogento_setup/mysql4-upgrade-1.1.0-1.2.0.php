<?php

$installer = $this;
$installer->startSetup();

$installer->run("
UPDATE {$this->getTable('cache_fullpage_config')} SET `helper_class` = 'nitrogento/cache_fullpage_cms_page_view_impl' WHERE `full_action_name` = 'cms_page_view';
UPDATE {$this->getTable('cache_blockhtml_config')} SET `helper_class` = 'nitrogento/cache_blockhtml_generic_dynamic_impl' WHERE `block_template` = 'page/html/breadcrumbs.phtml';
DELETE FROM {$this->getTable('cache_blockhtml_config')} WHERE `block_template` = 'page/html/footer.phtml';
DROP TABLE IF EXISTS {$this->getTable('optimization_config')};
ALTER TABLE {$this->getTable('cache_blockhtml_config')} CHANGE `store_id` `store_id` text;
ALTER TABLE {$this->getTable('cache_fullpage_config')} CHANGE `store_id` `store_id` text;
ALTER TABLE {$this->getTable('cache_blockhtml_config')} DROP COLUMN `customer_group_id`;
ALTER TABLE {$this->getTable('cache_blockhtml_learning')} DROP COLUMN `customer_group_id`;
ALTER TABLE {$this->getTable('cache_blockhtml_config')} DROP COLUMN `package`;
ALTER TABLE {$this->getTable('cache_blockhtml_learning')} DROP COLUMN `package`;
ALTER TABLE {$this->getTable('cache_blockhtml_config')} DROP COLUMN `theme`;
ALTER TABLE {$this->getTable('cache_blockhtml_learning')} DROP COLUMN `theme`;
UPDATE {$this->getTable('cache_blockhtml_config')} SET `store_id` = 'a:1:{i:0;i:0;}';
UPDATE {$this->getTable('cache_fullpage_config')} SET `store_id` = 'a:1:{i:0;i:0;}';
INSERT INTO {$this->getTable('cache_blockhtml_config')} (`block_class`, `friendly_entry`, `block_template`, `helper_class`, `cache_lifetime`, `store_id`, `activated`) VALUES
('Mage_CatalogSearch_Block_Advanced_Form', 'Catalog Advanced Search Form', 'catalogsearch/advanced/form.phtml', 'nitrogento/cache_blockhtml_generic_static_impl', 3600, 'a:1:{i:0;i:0;}', 1);
");

$installer->endSetup();