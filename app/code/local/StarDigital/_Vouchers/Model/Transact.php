<?php

class Stardigital_Vouchers_Model_Transact extends Mage_Core_Model_Abstract
{
    protected $_giftCardCampaignId;
    protected $_monthlyDealCampaignId;
    protected $_transactUrl;

    public function _construct()
    {
        $this->_transactUrl = Mage::getStoreConfig('silverpop/transact/transact_url');
        $this->_giftCardCampaignId = Mage::getStoreConfig('silverpop/transact/gift_card_id');
        $this->_monthlyDealCampaignId = Mage::getStoreConfig('silverpop/transact/monthly_deal_id');

        $this->_init('vouchers/silverpop');
    }

    protected function _transactSendToSilverpop($xml_data)
    {
        $ch = curl_init($this->_transactUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, "$xml_data");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
    }

    protected function _getPaymentHtml($order)
    {
        $storeId = $order->getStoreId();
        $paymentBlock = Mage::helper('payment')->getInfoBlock($order->getPayment())
            ->setIsSecureMode(true);
        $paymentBlock->getMethod()->setStore($storeId);

        return strip_tags($paymentBlock->toHtml());
    }

    protected function _getOrderItemsHtml($order, $vouchers)
    {
        $items = $order->getAllItems();
        $return = '';

        $return = '
        <table width="510" cellspacing="0" cellpadding="10" border="0" style="border:0px solid #eaeaea;color:#666;font-family:Georgia,\'Times New Roman\',Times,serif;font-weight:normal">
            <thead>
                <tr>
                    <th bgcolor="#EAEAEA" align="left" style="font-size:13px;color:#666">Item</th>
                    <th bgcolor="#EAEAEA" align="left" style="font-size:13px;color:#666">Product Code</th>
                    <th bgcolor="#EAEAEA" align="center" style="font-size:13px;color:#666">Qty</th>
                    <th bgcolor="#EAEAEA" align="right" style="font-size:13px">Subtotal</th>
                </tr>
            </thead>
        ';

        foreach ($items AS $item) {
            if ($item->getParentItem()) {
                continue;
            }

            $return .= '
            <tbody>
                <tr>
                    <td valign="top" align="left" style="font-size:12px;font-family:Georgia,\'Times New Roman\',Times,serif;color:#999999;padding:9px 10px;border-bottom:1px dotted #666">
            ';

            $return .= $item->getName();

            foreach ($vouchers AS $voucher) {
                if ($voucher['product_id'] == $item->getProductId()) {
                    $return .= '
                        <dl>
                            <dt>Gift Code</dt>
                            <dd style="margin-left: 0px;">' . $voucher['code'] . '</dd>
                            <dd style="margin-left: 0px;">Expires : ' . date('d/m/Y', strtotime($voucher['expiry'])) . '</dd>
                        </dl>
                    ';
                }
            }

            $return .= '
                    </td>
                    <td valign="top" align="left" style="font-size:12px;font-family:Georgia,\'Times New Roman\',Times,serif;color:#999999;padding:9px 10px;border-bottom:1px dotted #666">' . $item->getSku() . '</td>
                    <td valign="top" align="center" style="font-size:12px;padding:9px 10px;border-bottom:1px dotted #666;color:#999999;font-family:Georgia,\'Times New Roman\',Times,serif">' . number_format($item->getQtyOrdered()) . '</td>
                    <td valign="top" align="right" style="font-size:12px;padding:9px 10px;border-bottom:1px dotted #666;color:#999999;font-family:Georgia,\'Times New Roman\',Times,serif">
                        <span>' . Mage::helper('core')->formatPrice($item->getRowTotal(), false) . '</span>
                    </td>
                </tr>
            </tbody>
            ';
        }

        $return .= '
            <tbody style="border:0px solid #eaeaea;color:#999;font-family:Georgia,\'Times New Roman\',Times,serif!important">
                <tr>
                    <td align="right" style="padding:3px 9px" colspan="3">
                        Subtotal
                    </td>
                    <td align="right" style="padding:3px 9px">
                        <span>' . Mage::helper('core')->formatPrice($order->getBaseSubtotal(), false) . '</span>
                    </td>
                </tr>
                <tr>
                    <td align="right" style="padding:3px 9px" colspan="3">Shipping &amp; Handling</td>
                    <td align="right" style="padding:3px 9px">
                        <span>' . Mage::helper('core')->formatPrice($order->getShippingAmount(), false) . '</span>
                    </td>
                </tr>
        ';

        if ($order->getBaseDiscountAmount() != 0) {
            $return .= '
                <tr>
                    <td align="right" style="padding:3px 9px" colspan="3">Discount</td>
                    <td align="right" style="padding:3px 9px">
                        <span>' . Mage::helper('core')->formatPrice(($order->getBaseDiscountAmount() * -1), false) . '</span>
                    </td>
                </tr>
            ';
        }

        $return .= '
                <tr>
                    <td align="right" style="padding:3px 9px" colspan="3">
                        <strong>Total</strong>
                    </td>
                    <td align="right" style="padding:3px 9px">
                        <strong><span>' . Mage::helper('core')->formatPrice($order->getGrandTotal(), false) . '</span></strong>
                    </td>
                </tr>
            </tbody>
        </table>
        ';

        return $return;
    }

    protected function _sendNewOrderEmail($order, $vouchers, $campaignId)
    {
        $billingAddress = $order->getBillingAddress();

        $details = array();
        $details['CustomerName'] = $billingAddress->getFirstname() . ' ' . $billingAddress->getLastname();
        $details['supportemail'] = Mage::getStoreConfig('trans_email/ident_sales/email');
        $details['order_number'] = $order->getIncrementId();
        $details['ordernumber'] = $order->getIncrementId();
        $details['orderdate'] = date('l, jS F Y', strtotime($order->getCreatedAt()));
        $details['Address_Billing_HTML'] = $order->getBillingAddress()->format('html');
        $details['Payment_HTML'] = $this->_getPaymentHtml($order);
        $details['Address_Shipping_HTML'] = $order->getShippingAddress()->format('html');
        $details['Shipping_Description'] = $order->getShippingDescription();
        $details['Order_Items_HTML'] = $this->_getOrderItemsHtml($order, $vouchers);
        $details['Order_Total_HTML'] = ''; //$this->_getOrderTotalHtml($order);
        $details['Coupon_Code_HTML'] = ''; //$this->_getVoucherHtml($vouchers);
        $details['Customer_Note'] = $order->getEmailCustomerNote();

        $xmlData ='
    <XTMAILING>
        <CAMPAIGN_ID>' . $campaignId . '</CAMPAIGN_ID>
        <SHOW_ALL_SEND_DETAIL>true</SHOW_ALL_SEND_DETAIL>
        <SEND_AS_BATCH>false</SEND_AS_BATCH>
        <NO_RETRY_ON_FAILURE >false</NO_RETRY_ON_FAILURE>
        <SAVE_COLUMNS>
            <COLUMN_NAME>CustomerName</COLUMN_NAME>
            <COLUMN_NAME>ordernumber</COLUMN_NAME>
        </SAVE_COLUMNS>
        <RECIPIENT>
            <EMAIL>' . $order->getCustomerEmail() . '</EMAIL>
            <BODY_TYPE>HTML</BODY_TYPE>
        ';

        foreach ($details AS $key => $value) {
            $xmlData .= '
            <PERSONALIZATION>
                <TAG_NAME>' . $key . '</TAG_NAME>
                <VALUE><![CDATA[' . $value . ']]></VALUE>
            </PERSONALIZATION>
            ';
        }

        $xmlData .= '
        </RECIPIENT>
    </XTMAILING>';

        $this->_transactSendToSilverpop($xmlData);
    }

    public function sendGidtCardNewOrderEmail($order, $vouchers)
    {
        $this->_sendNewOrderEmail($order, $vouchers, $this->_giftCardCampaignId);
    }

    public function sendDealNewOrderEmail($order, $vouchers)
    {
        $this->_sendNewOrderEmail($order, $vouchers, $this->_giftCardCampaignId);
    }
}
