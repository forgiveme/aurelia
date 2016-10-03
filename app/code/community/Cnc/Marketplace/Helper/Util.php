<?php

/**
 * Utility functions for export files and plugin version
 */
class Cnc_Marketplace_Helper_Util extends Mage_Core_Helper_Abstract
{

    public function writeExportFile($type, $content)
    {

        if ($type == 'product') {
            $file = $this->getProductExportFilename();
        } else {
            $file = $this->getOfferExportFilename();
        }
        Mage::helper('marketplace/logger')->log('getProductsToUpload: CSV file ' . $file . ' about to be created ',
            $content);
        $file_res = file_put_contents($file, $content);
        if ($file_res === false) {
            Mage::helper('marketplace/logger')->log('getProductsToUpload', 'An error occurred writing to ' . $file);
        } else {
            Mage::helper('marketplace/logger')->log('getProductsToUpload', $file_res . ' bytes written to ' . $file);
        }
    }

    public function getOfferExportFilename()
    {
        return $this->getExportDir() . DIRECTORY_SEPARATOR . 'offer.csv';
    }

    public function getProductExportFilename()
    {
        return $this->getExportDir() . DIRECTORY_SEPARATOR . 'product.csv';
    }

    public function getExportDir()
    {

        $export_dir = Mage::getBaseDir('var') . DIRECTORY_SEPARATOR . 'cnc_marketplace';
        if (!is_dir($export_dir)){
            $this->createExportDirectory();
        }
        return $export_dir;
    }

    public function getCncMarketplacePluginVersion()
    {
        return Mage::getConfig()->getNode('modules/Cnc_Marketplace/version');
    }

    public function createExportDirectory()
    {
        $exportDirectory = new Varien_Io_File();
        $exportDirectory->checkAndCreateFolder(Mage::getBaseDir('var') . DS . 'cnc_marketplace', 0777);
    }

}
