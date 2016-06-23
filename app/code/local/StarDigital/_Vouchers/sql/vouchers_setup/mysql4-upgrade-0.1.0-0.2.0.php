<?php

$installer = $this;

$installer->startSetup();

$installer->run(
    "
        ALTER TABLE {$this->getTable('vouchers')} ADD COLUMN `rule_id` int(11) NULL;
    "
);
