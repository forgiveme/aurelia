<?php

class ChilliApple_Promotion_Model_Mysql4_Promotion extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the promotion_id refers to the key field in your database table.
        $this->_init('promotion/promotion', 'promotion_id');
    }
}