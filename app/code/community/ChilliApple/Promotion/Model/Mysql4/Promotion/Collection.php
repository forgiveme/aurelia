<?php

class ChilliApple_Promotion_Model_Mysql4_Promotion_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('promotion/promotion');
    }
}