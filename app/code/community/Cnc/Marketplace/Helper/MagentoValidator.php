<?php
require_once('Magentovalidation/ValidationsLoader.php');

/**
 * Validate Magento Config
 **/
class Cnc_Marketplace_Helper_MagentoValidator extends Mage_Core_Helper_Abstract
{

    public static function fireValidations()
    {

        $validators = ValidationsLoader::getValidators();

        $messages = array();

        foreach ($validators as $validator) {
            $message = $validator->validate();
            if(!empty($message)){
                array_push($messages, $message);
            }
        }

        return $messages;
    }

}
