<?php

class Stardigital_Vouchers_Model_Vouchers extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('vouchers/vouchers');
    }
}
