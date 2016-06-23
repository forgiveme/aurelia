<?php
$installer = $this;

$installer->startSetup();

$installer->run("
    ALTER TABLE  `".$this->getTable('sales/quote_address')."` ADD  `customfee_amount` DECIMAL( 10, 2 ) NOT NULL;
    ALTER TABLE  `".$this->getTable('sales/quote_address')."` ADD  `base_customfee_amount` DECIMAL( 10, 2 ) NOT NULL;
");

$installer->run("
    ALTER TABLE  `".$this->getTable('sales/order')."` ADD  `customfee_amount` DECIMAL( 10, 2 ) NOT NULL;
    ALTER TABLE  `".$this->getTable('sales/order')."` ADD  `base_customfee_amount` DECIMAL( 10, 2 ) NOT NULL;
");

$installer->run("
    ALTER TABLE  `".$this->getTable('sales/invoice')."` ADD  `customfee_amount` DECIMAL( 10, 2 ) NOT NULL;
    ALTER TABLE  `".$this->getTable('sales/invoice')."` ADD  `base_customfee_amount` DECIMAL( 10, 2 ) NOT NULL;
");

$installer->run("
    ALTER TABLE  `".$this->getTable('sales/order')."` ADD  `customfee_amount_invoiced` DECIMAL( 10, 2 ) NOT NULL;
    ALTER TABLE  `".$this->getTable('sales/order')."` ADD  `base_customfee_amount_invoiced` DECIMAL( 10, 2 ) NOT NULL;
");

$installer->endSetup();