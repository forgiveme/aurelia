<?php
class As_Customfee_Model_Sales_Order_Total_Invoice_Customfee extends Mage_Sales_Model_Order_Invoice_Total_Abstract
{

    /**
     * Collect invoice total
     *
     * @param Mage_Sales_Model_Order_Invoice $invoice
     * @return As_Customfee_Model_Sales_Order_Total_Invoice_Customfee
     */
    public function collect(Mage_Sales_Model_Order_Invoice $invoice)
    {
        $order = $invoice->getOrder();

        $customfeeAmountLeft = $order->getCustomfeeAmount() - $order->getCustomfeeAmountInvoiced();
        $baseCustomfeeAmountLeft = $order->getBaseCustomfeeAmount() - $order->getBaseCustomfeeAmountInvoiced();

        if (abs($baseCustomfeeAmountLeft) < $invoice->getBaseGrandTotal()) {
            $invoice->setGrandTotal($invoice->getGrandTotal() + $customfeeAmountLeft);
            $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() + $baseCustomfeeAmountLeft);
        } else {
            $customfeeAmountLeft = $invoice->getGrandTotal() * -1;
            $baseCustomfeeAmountLeft = $invoice->getBaseGrandTotal() * -1;

            $invoice->setGrandTotal(0);
            $invoice->setBaseGrandTotal(0);
        }

        $invoice->setCustomfeeAmount($customfeeAmountLeft);
        $invoice->setBaseCustomfeeAmount($baseCustomfeeAmountLeft);

        return $this;
    }

}
