<?php

class ChilliApple_FreeSample_Model_Mysql4_FreeSample extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the freesample_id refers to the key field in your database table.
        $this->_init('freesample/freesample', 'freesample_id');
    }
}