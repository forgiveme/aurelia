<?php

class Review_Reminder_Helper_Data extends Mage_Core_Helper_Abstract
{

const XML_PATH_EXTENSION_ENABLED = 'reminder/status/extension_enable';
const XML_PATH_NUM_OF_DAYS_AFTER_ORDER = 'reminder/general_settings/number_of_days';

 public function isExtensionEnabled()
    {
        if($this->getConfigExtensionEnabled()){
            return true;
        }else{
            return false;
        }
    }

   public function getConfigExtensionEnabled($store = null)
    {
         return Mage::getStoreConfig(self::XML_PATH_EXTENSION_ENABLED, $store);
    }
	

}