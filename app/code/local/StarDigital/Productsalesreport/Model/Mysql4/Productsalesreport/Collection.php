<?php

class Stardigital_Productsalesreport_Model_Mysql4_Productsalesreport_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('productsalesreport/productsalesreport');
    }
}
