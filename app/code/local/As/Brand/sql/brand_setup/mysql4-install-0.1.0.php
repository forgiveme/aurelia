<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('brand')};
CREATE TABLE {$this->getTable('brand')} (
  `brand_id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `filename` varchar(255) NOT NULL default '',
  `status` smallint(6) NOT NULL default '0',
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`brand_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");
$installer->addAttribute('catalog_product','brand',
	array(
	'group'=>'General',
	'input'=>'multiselect',
	'type'=>'text',
	 'source'=>'brand/attribute_source_brand',
	 'visible'=>'1',
	 'label'=>'Brands',
	 'backend_model'=>'eav/entity_attribute_backend_array',
	 'global'=> Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
	 'required' => false,
	 'user_defined' => true,
	 'apply_to' => 'simple,configurable,virtual',
	));
$installer->endSetup(); 
