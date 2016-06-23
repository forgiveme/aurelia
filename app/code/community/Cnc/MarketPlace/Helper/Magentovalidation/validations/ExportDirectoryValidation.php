<?php
require_once('AbstractMagentoConfigValidation.php');

class ExportDirectoryValidation extends AbstractMagentoConfigValidation
{
    public function validate()
    {
        $export_dir = Mage::helper('marketplace/util')->getExportDir();

        // Check whether the export directory exists
        if (!is_dir($export_dir)) {

            // Create dir with read & write permission, creating nested dirs if required (unlikely)
            if(!mkdir($export_dir, 0770, true)) {
                return "The CSV Export directory does not exist and could not be created: $export_dir";
            }
        }

        // Check whether directory is writeable
        if (!is_writable($export_dir)) {
            return "The CSV Export directory is not writeable: $export_dir";
        }
    }
}