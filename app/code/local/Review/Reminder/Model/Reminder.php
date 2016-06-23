<?php

class Review_Reminder_Model_Reminder extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('reminder/reminder');
    }
}