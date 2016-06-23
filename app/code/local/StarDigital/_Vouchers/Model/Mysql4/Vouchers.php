<?php

class Stardigital_Vouchers_Model_Mysql4_Vouchers extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        // Note that the vouchers_id refers to the key field in your database table.
        $this->_init('vouchers/vouchers', 'vouchers_id');
    }
}
