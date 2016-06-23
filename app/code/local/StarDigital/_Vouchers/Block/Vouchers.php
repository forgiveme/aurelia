<?php

class Stardigital_Vouchers_Block_Vouchers extends Mage_Core_Block_Template
{
    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    public function getVouchers()
    {
        if (!$this->hasData('vouchers')) {
            $this->setData('vouchers', Mage::registry('vouchers'));
        }

        return $this->getData('vouchers');
    }
}
