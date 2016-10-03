<?php
/**
 * Created by Daniel Rafique.
 * For: Style.com
 * Date: 15/08/2016
 * Time: 11:27
 * Copyright all rights reserved to author of this content.
 */

$installer = $this;

// Is Mirakl Order
$installer->run("ALTER TABLE {$installer->getTable('marketplace/ordertable')}  ADD `is_mirakl_order` SMALLINT(1) NULL DEFAULT '0' COMMENT '1=Yes;0=No';");

$installer->endSetup();
