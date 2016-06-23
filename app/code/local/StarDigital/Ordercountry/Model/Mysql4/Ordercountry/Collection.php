<?php

class Stardigital_Ordercountry_Model_Mysql4_Ordercountry_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('ordercountry/ordercountry');
    }
}
