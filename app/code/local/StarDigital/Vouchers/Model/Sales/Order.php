<?php

class Stardigital_Vouchers_Model_Sales_Order extends Mage_Sales_Model_Order
{
    public function sendNewOrderEmail()
    {
        $giftCardVoucherGenerated = false;
        $monthlyDealVoucherGenerated = false;
        $spopOrderEmailSent = false;

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

        if (Mage::helper('vouchers')->isSilverpopTransactEnabled()) {
		 	if (Mage::helper('vouchers')->isMonthlyDealVouchersEnabled()) {
                $monthlyDealVoucherGenerated = Mage::helper('vouchers')->generateMonhtlyDealVouchers($order);
            }
			
            if ($giftCardVoucherGenerated) {
                $vouchers = Mage::helper('vouchers')->getOrderVouchers($order, "giftVouchers");
                Mage::getModel('vouchers/transact')->sendGidtCardNewOrderEmail($order, $vouchers);
                $spopOrderEmailSent = true;
            }

            if ($monthlyDealVoucherGenerated) {
                $vouchers = Mage::helper('vouchers')->getOrderVouchers($order, "monthlyDealVouchers");
                Mage::getModel('vouchers/transact')->sendDealNewOrderEmail($order, $vouchers);
            }
        }

        if ($spopOrderEmailSent) {
            return $this;
        }

        return parent::sendNewOrderEmail();
    }
}
