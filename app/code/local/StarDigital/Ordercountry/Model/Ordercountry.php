<?php

class Stardigital_Ordercountry_Model_Ordercountry extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('ordercountry/ordercountry');
    }
}
