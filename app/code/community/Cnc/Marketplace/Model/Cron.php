<?php

class Cnc_Marketplace_Model_Cron
{
    public function setStatus()
    {
        $model = Mage::getModel('marketplace/crontable');
        $collection = $model->getCollection();
        foreach ($collection as $item) {
            $values = $item->getData();
            $recursive = isset($values['recursive_value']) ? $values['recursive_value'] : '';
            $timecreated = strftime("%Y-%m-%d %H:%M:%S", mktime(date("H"), date("i"), date("s"), date("m"), date("d"), date("Y")));
            $next_time = $values['next_time'];
            $new_nexttime = date("Y-m-d H:i:s", strtotime('+' . $recursive . ' minutes', strtotime($next_time)));
            $days_arr = explode(',', $values['days']);
            $dw = date("l", strtotime($values['next_time']));
            if ($values['name'] == 'product_upload') {
                $jobCode = 'marketplace_products';
                $collections = Mage::getModel('marketplace/crontable')->getCollection()->addCronSelect('product_upload');
                $result = $collections->getData();
                if ($result[0] && isset($result[0])) {
                    $id = $result[0]['id'];
                }
                $data = array(
                    'name' => $values['name'],
                    'type' => $values['type'],
                    'date' => $values['date_sp'],
                    'executed' => 'True',
                    'next_time' => $new_nexttime,
                    'recursive_value' => $values['minutes'],
                    'days' => $values['days'],
                    'weekdays' => $values['weekdays']
                );
                $model = Mage::getModel('marketplace/crontable')->load($id)->addData($data);
                try {
                    $model->setId($id)->save();
                } catch (Exception $e) {
                    echo $e->getMessage();
                }
            } else {
                $jobCode = 'marketplace_offers';
                $collections = Mage::getModel('marketplace/crontable')->getCollection()->addCronSelect('offer_upload');
                $result = $collections->getData();
                if ($result[0] && isset($result[0])) {
                    $id = $result[0]['id'];
                }
                $data = array(
                    'name' => $values['name'],
                    'type' => $values['type'],
                    'date' => $values['date_sp'],
                    'executed' => 'True',
                    'next_time' => $new_nexttime,
                    'recursive_value' => $values['minutes'],
                    'days' => $values['days'],
                    'weekdays' => $values['weekdays']
                );
                $model = Mage::getModel('marketplace/crontable')->load($id)->addData($data);
                try {
                    $model->setId($id)->save();
                } catch (Exception $e) {
                    echo $e->getMessage();
                }
            }
            if (in_array($dw, $days_arr)) {
                $timecreated = strftime("%Y-%m-%d %H:%M:%S", mktime(date("H"), date("i"), date("s"), date("m"), date("d"), date("Y")));
                $timescheduled = $next_time;
                $resource = Mage::getSingleton('core/resource');
                $readConnection = $resource->getConnection('core_read');
                $query = 'SELECT * FROM ' . $resource->getTableName('cron/schedule') . ' where job_code = "' . $jobCode . '" AND status = "pending"';
                $results = $readConnection->fetchAll($query);
                $size = count($results);
                if ($size <= 5) {
                    try {
                        $schedule = Mage::getModel('cron/schedule');
                        $schedule->setJobCode($jobCode)->setCreatedAt($timecreated)->setScheduledAt($timescheduled)->setStatus(Mage_Cron_Model_Schedule::STATUS_PENDING)->save();
                    } catch (Exception $e) {
                        throw new Exception(Mage::helper('cron')->__('Unable to save Cron expression'));
                    }
                }
            }
        }
    }

    public function executeProducts()
    {
        $helper = Mage::helper('marketplace');
        Mage::helper('marketplace/logger')->log("Product Uploaded - Cron");
        $field_attributes = $helper->getProductsToUpload(true);
        return $field_attributes;
    }

    public function executeOffers()
    {
        $stores = Mage::app()->getStores();

        foreach ($stores as $key => $store) {

            if (Mage::getStoreConfig('marketplace/configuration/active')) {
                $helper = Mage::helper('marketplace');
                Mage::helper('marketplace/logger')->log("Offer Uploaded - Cron");
                Mage::app()->setCurrentStore($store);
                $helper->getOffersToUpload(true);
            }
        }

    }

    public function executeLogCleaner()
    {
        Mage::helper('marketplace/logger')->log('Entering Logcleaner->execute (possibly via cron)', '');

        // Retrieve files in our log directory
        $logDir = Mage::getBaseDir('log');
        $files = scandir($logDir);

        $date_current = time();
        // Set log file age limit at 30 days
        $file_age_limit = 30 * 24 * 60 * 60;

        foreach ($files as $file) {

            // Go through file names - if any match the regular expression, we can work
            // out which date they refer to, then determine whether it is time to delete the file.
            if (preg_match('(^cnc_marketplace.([0-9]{4}-[0-9]{2}-[0-9]{2}).log$)', $file, $matches)) {

                $date_file = strtotime($matches[1]);
                if ($date_file < $date_current - $file_age_limit) {

                    // File is older than the age limit.  We need to delete it.
                    if (unlink($logDir . DIRECTORY_SEPARATOR . $matches[0])) {
                        Mage::helper('marketplace/logger')->log('File ' . $matches[0] . ' successfully deleted.', '');
                    } else {
                        Mage::helper('marketplace/logger')->log('File ' . $matches[0] . ' could not be deleted.', '');
                    }
                }
            }
        }
    }
}
