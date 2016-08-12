<?php
require 'app/Mage.php';

if (!Mage::isInstalled()) {
    echo "Application is not installed yet, please complete install wizard first.";
    die;
}

Mage::app();

try {
	Mage::getModel('backupsuite/observer1')->execbackup();
} catch (Exception $e) {
    Mage::printException($e);
} 