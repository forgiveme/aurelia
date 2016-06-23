<?php

class ChilliApple_Preferences_Model_Primaryconcern extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('preferences/primaryconcern');
    }
}
