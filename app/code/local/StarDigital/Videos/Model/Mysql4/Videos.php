<?php
class StarDigital_Videos_Model_Mysql4_Videos extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("videos/videos", "video_id");
    }
}