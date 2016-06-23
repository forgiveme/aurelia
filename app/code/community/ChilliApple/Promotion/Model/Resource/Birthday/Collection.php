<?php
class ChilliApple_Promotion_Model_Resource_Birthday_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Define resource model
     *
     */
    protected function _construct()
    {
        $this->_init('promotion/birthday');

    }
    
}
