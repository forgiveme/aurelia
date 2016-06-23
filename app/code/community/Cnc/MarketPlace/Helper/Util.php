<?php

/**
 * Provides filenames for use in csv exports.
 */
class Cnc_MarketPlace_Helper_Util extends Mage_Core_Helper_Abstract
{
    public function getOfferExportFilename()
    {
        return $this->getExportDir().DIRECTORY_SEPARATOR.'offer.csv';
    }

    public function getProductExportFilename()
    {
        return $this->getExportDir().DIRECTORY_SEPARATOR.'product.csv';
    }

    public function getExportDir()
    {
        $export_dir = Mage::helper('marketplace/data')->getConfigurationData('export_dir_location');

        if ($export_dir == 'default') {
            $export_dir = Mage::getBaseDir('var').DIRECTORY_SEPARATOR.'cnc_marketplace';
        }
        return $export_dir;
    }

    public function getCncMarketPlacePluginVersion()
    {
        return Mage::getConfig()->getNode('modules/Cnc_MarketPlace/version');
    }

}