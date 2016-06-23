<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('preferences')};
CREATE TABLE IF NOT EXISTS {$this->getTable('preferences')} (
  `preferences_id` int(11) unsigned NOT NULL auto_increment,
  `customer_id` int(11) NOT NULL,
  `primary_concern` int(11) NOT NULL,
  `secondary_concern` int(11) NOT NULL,
  `other_brands` varchar(255) NOT NULL default '',
  `aurelia_feedback` varchar(255) NOT NULL default '',
  `skin_cares` varchar(255) NOT NULL default '',
  `has_glasses` smallint(6) NOT NULL default '0',
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`preferences_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

 CREATE TABLE IF NOT EXISTS {$this->getTable('primary_concern')} (
 `primary_concern_id` int(11) unsigned NOT NULL auto_increment,
 `title` varchar(255) NOT NULL,
 `created_time` datetime NULL,
 `update_time` datetime NULL,
  PRIMARY KEY (`primary_concern_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


 CREATE TABLE IF NOT EXISTS {$this->getTable('secondary_concern')} (
 `secondary_concern_id` int(11) unsigned NOT NULL auto_increment,
 `title` varchar(255) NOT NULL,
 `created_time` datetime NULL,
 `update_time` datetime NULL,
  PRIMARY KEY (`secondary_concern_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

 CREATE TABLE IF NOT EXISTS {$this->getTable('skincare')} (
 `skin_care_id` int(11) unsigned NOT NULL auto_increment,
 `title` varchar(255) NOT NULL,
 `created_time` datetime NULL,
 `update_time` datetime NULL,
  PRIMARY KEY (`skin_care_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


    ");

$installer->endSetup(); 

$installer=new Mage_Eav_Model_Entity_Setup('core_setup');
$installer->getConnection()
    ->addColumn($installer->getTable('sales/order'),'has_glasses', array(
        'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
        'nullable'  => false,
        'length'    => 6,
        'default'   => '0',
        'comment'   => 'Has Glasses'
        ));
$installer->endSetup();
