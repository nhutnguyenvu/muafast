<?php

$installer = $this;

$installer->startSetup();

$installer = new Mage_Eav_Model_Entity_Setup('core_setup');
$installer->addAttribute('customer', 'facebook_uid', array('label'=> 'Facebook id',
'type'	 => 'varchar',
'input'	 => 'text',
'visible'	=> true,
'required'	=> true,
'position'	=> 1,
));
$installer->addAttribute('customer', 'google_uid', array('label'=> 'Google id',
'type'	 => 'varchar',
'input'	 => 'text',
'visible'	=> true,
'required'	=> true,
'position'	=> 1,
));
$installer->addAttribute('customer', 'yahoo_uid', array('label'=> 'Yahoo id',
'type'	 => 'varchar',
'input'	 => 'text',
'visible'	=> true,
'required'	=> true,
'position'	=> 1,
));
$installer->addAttribute('customer', 'linkedin_uid', array('label'=> 'Linkedin id',
'type'	 => 'varchar',
'input'	 => 'text',
'visible'	=> true,
'required'	=> true,
'position'	=> 1,
));
$installer->addAttribute('customer', 'twitter_uid', array('label'=> 'Twitter id',
'type'	 => 'varchar',
'input'	 => 'text',
'visible'	=> true,
'required'	=> true,
'position'	=> 1,
));
$installer->endSetup(); 