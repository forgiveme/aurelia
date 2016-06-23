<?php

$installer = $this;

$installer->startSetup();

$installer->run(
    "
        DROP TABLE IF EXISTS {$this->getTable('vouchers')};
        CREATE TABLE {$this->getTable('vouchers')} (
            `vouchers_id` int(11) unsigned NOT NULL auto_increment,
            `code` varchar(255) NOT NULL default '',
            `customer_email` varchar(255) NOT NULL,
            `order_id` int(11) unsigned NOT NULL,
            `value` decimal(12,4) unsigned NOT NULL,
            `type` varchar(255) NOT NULL,
            `from_date` date NOT NULL,
            `to_date` date NOT NULL,
            PRIMARY KEY (`vouchers_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

        ALTER TABLE {$this->getTable('sales_flat_order')} ADD COLUMN `voucher_generated` TINYINT(1) NOT NULL DEFAULT '0';
    "
);

$installer->endSetup();
