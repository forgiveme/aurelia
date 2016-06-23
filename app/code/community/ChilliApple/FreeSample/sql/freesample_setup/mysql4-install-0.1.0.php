<?php

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');

$setup->startSetup();
$setup->addAttribute('catalog_product', 'sample',
		array(
			'group'	=> 'General',
			'input'	=> 'select',
			'type'	=> 'int',
			'label'	=> 'Sample',
			'backend' => '',
			'visible' => 1,
			'required' => 1,
			'user_defined' => 1,
			'searchable' => 0,
			'filterable' => 0,
			'comparable' => 0,
			'visible_on_front' => 0,
			'visible_in_advanced_search'	=> 0,
			'is_html_allowed_on_front' => 0,
			'source' => 'eav/entity_attribute_source_boolean',
			'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
			'apply_to' => 'simple',
		     )
		  );

$setup->endSetup(); 
