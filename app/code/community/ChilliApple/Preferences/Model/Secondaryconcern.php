<?php

class ChilliApple_Preferences_Model_Secondaryconcern extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('preferences/secondaryconcern');
    }
}
