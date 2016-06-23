<?php

class ChilliApple_Preferences_Model_Mysql4_Preferences_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('preferences/preferences');
    }
}