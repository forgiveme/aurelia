<?php
class ChilliApple_Promotion_Model_Resource_Referafriend extends Mage_Core_Model_Resource_Db_Abstract
{

	/**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('promotion/referafriend', 'referafriend_id');
    }

}
