<?php

class ChilliApple_Preferences_Model_Mysql4_Preferences extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the preferences_id refers to the key field in your database table.
        $this->_init('preferences/preferences', 'preferences_id');
    }
}