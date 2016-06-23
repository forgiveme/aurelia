<?php

class Stardigital_Ordercountry_Model_Mysql4_Ordercountry extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        // Note that the ordercountry_id refers to the key field in your database table.
        $this->_init('ordercountry/ordercountry', 'ordercountry_id');
    }
}
