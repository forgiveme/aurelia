<?php

class ChilliApple_Promotion_Model_Loyalty extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        /////
        $this->_init('promotion/loyalty');
    }
}
