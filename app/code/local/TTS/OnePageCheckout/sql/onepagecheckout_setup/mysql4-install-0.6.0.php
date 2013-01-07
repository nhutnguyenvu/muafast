<?php

Mage::app('default');

$config = Mage::getModel('onepagecheckout/config_observer');

$config->reinitConfigData();

$websites = Mage::app()->getWebsites();

$list = array('');

foreach ($websites as $website)
{
    $list[] = $website->getCode();
}

foreach ($list as $website)
{
    $config->copyOrignCheckoutSettings($website);
    $config->copyOrignShippingSettings($website);
    $config->copyOrignSalesSettings($website);
}

$config->reinitConfigData();
