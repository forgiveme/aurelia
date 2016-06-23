<?php
class Cnc_MarketPlace_Model_Logcleaner {

    /*
     * Function (to be run as cron) to delete old log files.
     */
    public function execute()
    {
        Mage::helper('marketplace/logger')->log('Entering Logcleaner->execute (possibly via cron)','');

        // Retrieve files in our log directory
        $logDir=Mage::getBaseDir('log');
        $files = scandir($logDir);

        $date_current = time();
        // Set log file age limit at 30 days
        $file_age_limit = 30 * 24 * 60 * 60;

        foreach ($files as $file) {
            
            // Go through file names - if any match the regular expression, we can work
            // out which date they refer to, then determine whether it is time to delete the file.
            if (preg_match('(^cnc_marketplace.([0-9]{4}-[0-9]{2}-[0-9]{2}).log$)',$file,$matches)) {

                $date_file = strtotime($matches[1]);
                if ($date_file < $date_current - $file_age_limit) {

                    // File is older than the age limit.  We need to delete it.
                    if (unlink($logDir.DIRECTORY_SEPARATOR.$matches[0])) {
                        Mage::helper('marketplace/logger')->log('File '.$matches[0].' successfully deleted.','');
                    } else {
                        Mage::helper('marketplace/logger')->log('File '.$matches[0].' could not be deleted.','');
                    }
                }
            }
        }
    }

}