<?php

class Stardigital_Vouchers_Model_Sales_Order extends Mage_Sales_Model_Order
{
    public function sendNewOrderEmail()
    {
        $giftCardVoucherGenerated = false;

        $order = Mage::getModel('sales/order')->load($this->getId());

        $silverpop = Mage::getModel('silverpop/silverpop');

        if (Mage::getModel('silverpop/silverpop')->isSilverpopEnabled()) {
            if ($order->getCustomerId() != NULL) {
                $silverpop->processRegisteredOrder($order);
            } else {
                $silverpop->processGuestOrder($order);
            }
        }

        if (Mage::helper('vouchers')->isSilverpopVouchersEnabled()) {
            Mage::getModel('vouchers/silverpop')->createOrder($order);
            $giftCardVoucherGenerated = Mage::helper('vouchers')->generateVouchers($order);
        }

        if ($giftCardVoucherGenerated && Mage::helper('vouchers')->isSilverpopTransactEnabled()) {
            $vouchers = Mage::helper('vouchers')->getOrderVouchers($order);
            Mage::getModel('vouchers/transact')->sendGidtCardNewOrderEmail($order, $vouchers);

            return $this;
        } else {
            return parent::sendNewOrderEmail();
        }
    }
}
