<?php
$installer = $this;
$installer->startSetup();

$installer->run("ALTER TABLE {$installer->getTable('sales/order')}  ADD `is_mirakl_order` SMALLINT(1) NULL DEFAULT '0' COMMENT '1=Yes;0=No';");

$installer->run("ALTER TABLE {$installer->getTable('sales/order_grid')}  ADD `is_mirakl_order` SMALLINT(1) NULL DEFAULT '0' COMMENT '1=Yes;0=No';");

$installer->endSetup();
