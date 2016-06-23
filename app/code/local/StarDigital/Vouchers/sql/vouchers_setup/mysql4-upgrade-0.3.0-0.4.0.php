<?php

$installer = $this;

$installer->startSetup();

$installer->run(
    "
        ALTER TABLE {$this->getTable('vouchers')} ADD COLUMN `is_gift` tinyint(1) NOT NULL DEFAULT 1;
    "
);
