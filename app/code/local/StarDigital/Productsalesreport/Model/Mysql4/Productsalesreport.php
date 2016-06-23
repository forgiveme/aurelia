<?php

class Stardigital_Productsalesreport_Model_Mysql4_Productsalesreport extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        // Note that the productsalesreport_id refers to the key field in your database table.
        $this->_init('productsalesreport/productsalesreport', 'productsalesreport_id');
    }
}
