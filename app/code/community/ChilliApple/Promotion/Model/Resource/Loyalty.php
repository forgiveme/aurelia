<?php
class ChilliApple_Promotion_Model_Resource_Loyalty extends Mage_Core_Model_Resource_Db_Abstract
{

	/**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('promotion/loyalty', 'loyalty_id');
    }

}
