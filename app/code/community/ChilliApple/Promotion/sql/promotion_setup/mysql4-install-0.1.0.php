<?php

$installer = $this;

$installer->startSetup();

$installer->addAttribute('customer', 'loyalty', array(
    'type'      => 'decimal',
    'label'     => 'Loyalty',
    'input'     => 'text',
    'backend'   => '',
    'default'   => '0.00',
    'position'  => 100,
    'required'  => true
));


/*$loyaltyAttribute = Mage::getSingleton('eav/config')
    ->getAttribute('customer', 'loyalty');
$loyaltyAttribute->setData('used_in_forms', array(
    'adminhtml_customer'
));
$loyaltyAttribute->save();*/

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('loyalty')};
CREATE TABLE {$this->getTable('loyalty')} (
  `loyalty_id` int(11) unsigned NOT NULL auto_increment,
  `customer_id` int(11) unsigned NOT NULL,
  `order_id` int(11) unsigned,
  `description` varchar(255) NOT NULL default '',
  `amount` decimal(18,2) NOT NULL default '0.00',
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`loyalty_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('birthday')};
CREATE TABLE {$this->getTable('birthday')} (
  `birthday_id` int(11) unsigned NOT NULL auto_increment,
  `customer_id` int(11) unsigned NOT NULL,
  `coupon_code` varchar(255) NOT NULL default '',
  `amount` decimal(18,2) NOT NULL default '0.00',
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`birthday_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('referafriend')};
CREATE TABLE {$this->getTable('referafriend')} (
  `referafriend_id` int(11) unsigned NOT NULL auto_increment,
  `customer_id` int(11) unsigned NOT NULL,
  `order_id` int(11) unsigned,
  `friend_email` varchar(255) NOT NULL default '',
  `coupon_code` varchar(255) NOT NULL default '',
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`referafriend_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup(); 
