<?php

class Stardigital_Vouchers_Model_Silverpop extends Mage_Core_Model_Abstract
{
    // protected $_enabled;
    protected $_ftpHost;
    protected $_host;
    protected $_username;
    protected $_password;
    protected $_mainTableId;
    protected $_orderTableId;
    protected $_orderRelationTableId;
    protected $_orderItemsTableId;
    protected $_giftCardsTableId;

    protected $_monthlyDealsTableId;

    protected $_servlet = "XMLAPI";
    protected $_port = 80;
    protected $_timeout = 20;

    public function _construct() {
        parent::_construct();

        // $this->_enabled = Mage::getStoreConfig('silverpop/vouchers/enabled');
        $this->_ftpHost = 'transfer5.silverpop.com';
        $this->_host = Mage::getStoreConfig('silverpop/vouchers/spop_host');
        $this->_username = Mage::getStoreConfig('silverpop/vouchers/spop_username');
        $this->_password = Mage::getStoreConfig('silverpop/vouchers/spop_password');
        $this->_mainTableId = Mage::getStoreConfig('silverpop/silverpop/spop_user_table_id');
        $this->_orderTableId = Mage::getStoreConfig('silverpop/vouchers/order_table_id');
        $this->_orderRelationTableId = Mage::getStoreConfig('silverpop/vouchers/order_relation_table_id');
        $this->_orderItemsTableId = Mage::getStoreConfig('silverpop/vouchers/order_items_table_id');
        $this->_giftCardsTableId = Mage::getStoreConfig('silverpop/vouchers/gift_cards_table_id');

        $this->_monthlyDealsTableId = Mage::getStoreConfig('vouchers/monthly_deals/spop_table_id');

        $this->_init('vouchers/silverpop');
    }

    protected function _stripHeader($source)
    {
        list($header, $content) = explode("<Envelope>", $source);
        $content = "<Envelope>" . $content;
        list($content, $footer) = explode("</Envelope>", $content);
        $content .= "</Envelope>";

        return $content;
    }

    protected function _loginToSilverPop()
    {
        $sock = fsockopen($this->_host, $this->_port, $errno, $errstr, $this->_timeout);

        $data = "xml=<?xml version=\"1.0\"?>
        <Envelope>
            <Body>
                <Login>
                    <USERNAME>" . $this->_username . "</USERNAME>
                    <PASSWORD>" . $this->_password . "</PASSWORD>
                </Login>
            </Body>
        </Envelope>";

        if (!$sock) {
            return false;
        }

        $size = strlen($data);
        fputs($sock, "POST /servlet/" . $this->_servlet . " HTTP/1.1\n");
        fputs($sock, "Host: " . $this->_host . "\n");
        fputs($sock, "Content-type: application/x-www-form-urlencoded\n");
        fputs($sock, "Content-length: " . $size . "\n");
        fputs($sock, "Connection: close\n\n");
        fputs($sock, $data);
        $buffer = "";

        while (!feof($sock)) {
            $buffer .= fgets($sock);
        }

        fclose($sock);

        $buffer = $this->_stripHeader($buffer);

        try {
            $xml = simplexml_load_string($buffer);
            $sessionId = $xml->Body->RESULT->SESSIONID;
        }
        catch (Exception $e) {
            $sessionId = $this->_loginToSilverPop();
        }

        return $sessionId;
    }

    protected function _logoutFromSilverPop($sessionId)
    {
        $servlet = $this->_servlet . ";jsessionid=" . $sessionId;
        $sock = fsockopen($this->_host, $this->_port, $errno, $errstr, $this->_timeout);

        $data = "xml=<?xml version=\"1.0\"?>
        <Envelope>
            <Body>
                <Logout/>
            </Body>
        </Envelope>";

        if (!$sock) {
            return false;
        }

        $size = strlen ($data);
        fputs($sock, "POST /servlet/" . $servlet . " HTTP/1.1\n");
        fputs($sock, "Host: " . $this->_host . "\n");
        fputs($sock, "Content-type: application/x-www-form-urlencoded\n");
        fputs($sock, "Content-length: " . $size . "\n");
        fputs($sock, "Connection: close\n\n");
        fputs($sock, $data);
        $buffer = "";

        while (!feof($sock)) {
            $buffer .= fgets ($sock);
        }

        fclose ($sock);
    }

    protected function _sendToSilverpop($data)
    {
        $sessionId = $this->_loginToSilverPop();

        $servlet = $this->_servlet . ";jsessionid=" . $sessionId;
        $sock = fsockopen($this->_host, $this->_port, $errno, $errstr, $this->_timeout);

        if (!$sock) {
            return false;
        }

        $size = strlen($data);
        fputs($sock, "POST /servlet/" . $servlet . " HTTP/1.1\n");
        fputs($sock, "Host: " . $this->_host . "\n");
        fputs($sock, "Content-type: application/x-www-form-urlencoded\n");
        fputs($sock, "Content-length: " . $size . "\n");
        fputs($sock, "Connection: close\n\n");
        fputs($sock, $data);

        $buffer = "";

        while (!feof($sock)) {
            $buffer .= fgets ($sock);
        }

        $this->_logoutFromSilverPop($sessionId);

        $buffer = $this->_stripHeader($buffer);

        return $buffer;
    }

    protected function _getAddressStreet($address)
    {
        $streetArray = array();
        $street1 = $address->getStreet(1);
        $street2 = $address->getStreet(2);

        if (!empty($street1)) {
            $streetArray[] = $street1;
        }

        if (!empty($street2)) {
            $streetArray[] = $street2;
        }

        $street = implode(", ", $streetArray);

        return $street;
    }

    public function createOrderRelationTable($orders)
    {
        $data = 'xml=<?xml version="1.0"?>
        <Envelope>
            <Body>
                <InsertUpdateRelationalTable>
                    <TABLE_ID>' . $this->_orderRelationTableId . '</TABLE_ID>
                    <ROWS>
        ';

        foreach ($orders AS $order) {
            $billingAddress = $order->getBillingAddress();
            $shippingAddress = $order->getShippingAddress();

            $data .= '
                        <ROW>
                            <COLUMN name="order_id"><![CDATA[' . $order->getIncrementId() . ']]></COLUMN>
                            <COLUMN name="order_created"><![CDATA[' . date("m/d/y H:i:s", strtotime($order->getCreatedAt())) . ']]></COLUMN>
                            <COLUMN name="order_total"><![CDATA[' . str_replace(",", "", number_format($order->getGrandTotal(), 2)) . ']]></COLUMN>
                            <COLUMN name="email_address"><![CDATA[' . $order->getCustomerEmail() . ']]></COLUMN>
                            <COLUMN name="billing_firstname"><![CDATA[' . str_replace("&", "and", $billingAddress->getFirstname()) . ']]></COLUMN>
                            <COLUMN name="billing_lastname"><![CDATA[' . str_replace("&", "and", $billingAddress->getLastname()) . ']]></COLUMN>
                            <COLUMN name="billing_street1"><![CDATA[' . str_replace("&", "and", $this->_getAddressStreet($billingAddress)) . ']]></COLUMN>
                            <COLUMN name="billing_city"><![CDATA[' . str_replace("&", "and", $billingAddress->getCity()) . ']]></COLUMN>
                            <COLUMN name="billing_state"><![CDATA[' . str_replace("&", "and", $billingAddress->getRegion()) . ']]></COLUMN>
                            <COLUMN name="billing_zipcode"><![CDATA[' . str_replace("&", "and", $billingAddress->getPostcode()) . ']]></COLUMN>
                            <COLUMN name="billing_country"><![CDATA[' . str_replace("&", "and", $billingAddress->getCountryId()) . ']]></COLUMN>
                            <COLUMN name="billing_telephone"><![CDATA[' . str_replace("&", "and", $billingAddress->getTelephone()) . ']]></COLUMN>
                            <COLUMN name="shipping_firstname"><![CDATA[' . str_replace("&", "and", $shippingAddress->getFirstname()) . ']]></COLUMN>
                            <COLUMN name="shipping_lastname"><![CDATA[' . str_replace("&", "and", $shippingAddress->getLastname()) . ']]></COLUMN>
                            <COLUMN name="shipping_street1"><![CDATA[' . str_replace("&", "and", $this->_getAddressStreet($shippingAddress)) . ']]></COLUMN>
                            <COLUMN name="shipping_city"><![CDATA[' . str_replace("&", "and", $shippingAddress->getCity()) . ']]></COLUMN>
                            <COLUMN name="shipping_state"><![CDATA[' . str_replace("&", "and", $shippingAddress->getRegion()) . ']]></COLUMN>
                            <COLUMN name="shipping_zipcode"><![CDATA[' . str_replace("&", "and", $shippingAddress->getPostcode()) . ']]></COLUMN>
                            <COLUMN name="shipping_country"><![CDATA[' . str_replace("&", "and", $shippingAddress->getCountryId()) . ']]></COLUMN>
                            <COLUMN name="shipping_telephone"><![CDATA[' . str_replace("&", "and", $shippingAddress->getTelephone()) . ']]></COLUMN>
                        </ROW>
            ';
        }

        $data .= '
                    </ROWS>
                </InsertUpdateRelationalTable>
            </Body>
        </Envelope>';

        $result = $this->_sendToSilverpop($data);

        return $result;
    }

    public function createOrder($order)
    {
        $billingAddress = $order->getBillingAddress();
        $shippingAddress = $order->getShippingAddress();

        $data = "xml=<?xml version=\"1.0\"?>
        <Envelope>
            <Body>
                <AddRecipient>
                    <LIST_ID>" . $this->_orderTableId ."</LIST_ID>
                    <CREATED_FROM>2</CREATED_FROM>
                    <COLUMN>
                        <NAME>Email</NAME>
                        <VALUE><![CDATA[" . $order->getCustomerEmail() . "]]></VALUE>
                    </COLUMN>
                    <COLUMN>
                        <NAME>order_id</NAME>
                        <VALUE><![CDATA[" . $order->getIncrementId() . "]]></VALUE>
                    </COLUMN>
                    <COLUMN>
                        <NAME>order_created</NAME>
                        <VALUE><![CDATA[" . date("m/d/y H:i:s", strtotime($order->getCreatedAt())) . "]]></VALUE>
                    </COLUMN>
                    <COLUMN>
                        <NAME>billing_firstname</NAME>
                        <VALUE><![CDATA[" . str_replace("&", "and", $billingAddress->getFirstname()) . "]]></VALUE>
                    </COLUMN>
                    <COLUMN>
                        <NAME>billing_lastname</NAME>
                        <VALUE><![CDATA[" . str_replace("&", "and", $billingAddress->getLastname()) . "]]></VALUE>
                    </COLUMN>
                    <COLUMN>
                        <NAME>billing_street1</NAME>
                        <VALUE><![CDATA[" . str_replace("&", "and", $billingAddress->getStreet(1)) . "]]></VALUE>
                    </COLUMN>
                    <COLUMN>
                        <NAME>billing_street2</NAME>
                        <VALUE><![CDATA[" . str_replace("&", "and", $billingAddress->getStreet(2)) . "]]></VALUE>
                    </COLUMN>
                    <COLUMN>
                        <NAME>billing_city</NAME>
                        <VALUE><![CDATA[" . str_replace("&", "and", $billingAddress->getCity()) . "]]></VALUE>
                    </COLUMN>
                    <COLUMN>
                        <NAME>billing_state</NAME>
                        <VALUE><![CDATA[" . str_replace("&", "and", $billingAddress->getRegion()) . "]]></VALUE>
                    </COLUMN>
                    <COLUMN>
                        <NAME>billing_zipcode</NAME>
                        <VALUE><![CDATA[" . str_replace("&", "and", $billingAddress->getPostcode()) . "]]></VALUE>
                    </COLUMN>
                    <COLUMN>
                        <NAME>billing_country</NAME>
                        <VALUE><![CDATA[" . str_replace("&", "and", $billingAddress->getCountryId()) . "]]></VALUE>
                    </COLUMN>
                    <COLUMN>
                        <NAME>billing_telephone</NAME>
                        <VALUE><![CDATA[" . str_replace("&", "and", $billingAddress->getTelephone()) . "]]></VALUE>
                    </COLUMN>
                    <COLUMN>
                        <NAME>shipping_firstname</NAME>
                        <VALUE><![CDATA[" . str_replace("&", "and", $shippingAddress->getFirstname()) . "]]></VALUE>
                    </COLUMN>
                    <COLUMN>
                        <NAME>shipping_lastname</NAME>
                        <VALUE><![CDATA[" . str_replace("&", "and", $shippingAddress->getLastname()) . "]]></VALUE>
                    </COLUMN>
                    <COLUMN>
                        <NAME>shipping_street1</NAME>
                        <VALUE><![CDATA[" . str_replace("&", "and", $shippingAddress->getStreet(1)) . "]]></VALUE>
                    </COLUMN>
                    <COLUMN>
                        <NAME>shipping_street2</NAME>
                        <VALUE><![CDATA[" . str_replace("&", "and", $shippingAddress->getStreet(2)) . "]]></VALUE>
                    </COLUMN>
                    <COLUMN>
                        <NAME>shipping_city</NAME>
                        <VALUE><![CDATA[" . str_replace("&", "and", $shippingAddress->getCity()) . "]]></VALUE>
                    </COLUMN>
                    <COLUMN>
                        <NAME>shipping_state</NAME>
                        <VALUE><![CDATA[" . str_replace("&", "and", $shippingAddress->getRegion()) . "]]></VALUE>
                    </COLUMN>
                    <COLUMN>
                        <NAME>shipping_zipcode</NAME>
                        <VALUE><![CDATA[" . str_replace("&", "and", $shippingAddress->getPostcode()) . "]]></VALUE>
                    </COLUMN>
                    <COLUMN>
                        <NAME>shipping_country</NAME>
                        <VALUE><![CDATA[" . str_replace("&", "and", $shippingAddress->getCountryId()) . "]]></VALUE>
                    </COLUMN>
                    <COLUMN>
                        <NAME>shipping_telephone</NAME>
                        <VALUE><![CDATA[" . str_replace("&", "and", $shippingAddress->getTelephone()) . "]]></VALUE>
                    </COLUMN>
                    <COLUMN>
                        <NAME>order_total</NAME>
                        <VALUE><![CDATA[" . str_replace(",", "", number_format($order->getGrandTotal(), 2)) . "]]></VALUE>
                    </COLUMN>
                </AddRecipient>
            </Body>
        </Envelope>";

        $result = $this->_sendToSilverpop($data);

        $orders = array();
        $orders[] = $order;
        $this->createOrderRelationTable($orders);

        $this->_createOrderItems($order);

        return $result;
    }

    protected function _createOrderItems($order)
    {
        $data = 'xml=<?xml version="1.0"?>
        <Envelope>
            <Body>
                <InsertUpdateRelationalTable>
                    <TABLE_ID>' . $this->_orderItemsTableId . '</TABLE_ID>
                    <ROWS>
                    ';

        foreach ($order->getAllItems() AS $item) {
            $data .= '
                        <ROW>
                            <COLUMN name="order_id"><![CDATA[' . $order->getIncrementId() . ']]></COLUMN>
                            <COLUMN name="item_sku"><![CDATA[' . str_replace("&", "and", $item->getSku()) . ']]></COLUMN>
                            <COLUMN name="item_name"><![CDATA[' . str_replace("&", "and", $item->getName()) . ']]></COLUMN>
                            <COLUMN name="item_qty"><![CDATA[' . str_replace(",", "", number_format($item->getQtyOrdered(), 2)) . ']]></COLUMN>
                            <COLUMN name="item_price"><![CDATA[' . str_replace(",", "", number_format($item->getPrice(), 2)) . ']]></COLUMN>
                            <COLUMN name="row_total"><![CDATA[' . str_replace(",", "", number_format($item->getRowTotal(), 2)) . ']]></COLUMN>
                            <COLUMN name="jTime"><![CDATA[' . $order->getIncrementId() . '-' . $item->getSku() . ']]></COLUMN>
                        </ROW>
                    ';
        }

        $data .= '
                    </ROWS>
                </InsertUpdateRelationalTable>
            </Body>
        </Envelope>';

        $result = $this->_sendToSilverpop($data);

        return $result;
    }

    public function createGiftCard($order, $giftCardDetails)
    {
        $data = 'xml=<?xml version="1.0"?>
        <Envelope>
            <Body>
                <InsertUpdateRelationalTable>
                    <TABLE_ID>' . $this->_giftCardsTableId . '</TABLE_ID>
                    <ROWS>
                    ';

        foreach ($giftCardDetails AS $giftCardDetail) {
            $data .= '
                        <ROW>
                            <COLUMN name="order_id"><![CDATA[' . $order->getIncrementId() . ']]></COLUMN>
                            <COLUMN name="coupon_code"><![CDATA[' . $giftCardDetail['couponCode'] . ']]></COLUMN>
                            <COLUMN name="rule_name"><![CDATA[' . str_replace("&", "and", $giftCardDetail['ruleName']) . ']]></COLUMN>
                            <COLUMN name="from_date"><![CDATA[' . date("m/d/Y", strtotime($giftCardDetail['fromDate'])) . ']]></COLUMN>
                            <COLUMN name="to_date"><![CDATA[' . date("m/d/Y", strtotime($giftCardDetail['toDate'])) . ']]></COLUMN>
                        </ROW>
                    ';
        }

        $data .= '
                    </ROWS>
                </InsertUpdateRelationalTable>
            </Body>
        </Envelope>';

        $result = $this->_sendToSilverpop($data);

        return $result;
    }

    public function sendMonthlyDealsDetails($incrementId, $voucherCode, $ruleName, $fromDate, $toDate)
    {
        $data = 'xml=<?xml version="1.0"?>
        <Envelope>
            <Body>
                <InsertUpdateRelationalTable>
                    <TABLE_ID>' . $this->_monthlyDealsTableId . '</TABLE_ID>
                    <ROWS>
                        <ROW>
                            <COLUMN name="order_id"><![CDATA[' . $incrementId . ']]></COLUMN>
                            <COLUMN name="coupon_code"><![CDATA[' . $voucherCode . ']]></COLUMN>
                            <COLUMN name="rule_name"><![CDATA[' . str_replace("&", "and", $ruleName) . ']]></COLUMN>
                            <COLUMN name="from_date"><![CDATA[' . date("m/d/Y", strtotime($fromDate)) . ']]></COLUMN>
                            <COLUMN name="to_date"><![CDATA[' . date("m/d/Y", strtotime($toDate)) . ']]></COLUMN>
                        </ROW>
                    </ROWS>
                </InsertUpdateRelationalTable>
            </Body>
        </Envelope>';
        $result = $this->_sendToSilverpop($data);

        return $result;
    }

    public function createMainTableColumn($columnName)
    {
        $data ='xml=<?xml version="1.0"?>
        <Envelope>
            <Body>
                <AddListColumn>
                    <LIST_ID>' . $this->_mainTableId . '</LIST_ID>
                    <COLUMN_NAME><![CDATA[' . $columnName . ']]></COLUMN_NAME>
                    <COLUMN_TYPE>0</COLUMN_TYPE>
                    <DEFAULT><![CDATA[]]></DEFAULT>
                </AddListColumn>
            </Body>
        </Envelope>';

        $result = $this->_sendToSilverpop($data);

        return $result;
    }

    public function generateDealCsvFile($vouchers)
    {
        $filePath = Mage::getBaseDir('var') . DS . 'spopVouchers.csv';

        $handle = fopen($filePath, 'w');

        foreach ($vouchers AS $row) {
            fputcsv($handle, $row);
        }

        fclose($handle);

        return $filePath;
    }

    public function generateDealXmlMapFile($columnName)
    {
        $xml ='<LIST_IMPORT>
    <LIST_INFO>
        <ACTION>UPDATE_ONLY</ACTION>
        <LIST_ID>' . $this->_mainTableId . '</LIST_ID>
        <LIST_VISIBILITY>0</LIST_VISIBILITY>
        <FILE_TYPE>0</FILE_TYPE>
        <HASHEADERS>false</HASHEADERS>
    </LIST_INFO>
    <MAPPING>
        <COLUMN>
            <INDEX>1</INDEX>
            <NAME>Email</NAME>
            <INCLUDE>true</INCLUDE>
        </COLUMN>
        <COLUMN>
            <INDEX>2</INDEX>
            <NAME>' . $columnName . '</NAME>
            <INCLUDE>true</INCLUDE>
        </COLUMN>
    </MAPPING>
</LIST_IMPORT>';

        $fileName = Mage::getBaseDir('var') . DS . 'spopVouchersMap.xml';
        $fp = fopen($fileName, 'w');
        fwrite($fp, $xml);
        fclose($fp);

        return $fileName;
    }

    public function uploadDealFilesToSilverpop($csvFilePath, $xmlMapFilePath)
    {
        $ftpCsvPath = '/upload/spopVouchers.csv';
        $ftpMapFilePath = '/upload/spopVouchersMap.xml';

        $ftpConn = ftp_connect($this->_ftpHost, 21);

        if (@ftp_login($ftpConn, $this->_username, $this->_password)) {
            ftp_pasv($ftpConn, true);
            ftp_put($ftpConn, $ftpCsvPath, $csvFilePath, FTP_ASCII);
            ftp_put($ftpConn, $ftpMapFilePath, $xmlMapFilePath, FTP_ASCII);
            ftp_close($ftpConn);

            return true;
        }

        return false;
    }

    public function processDealJob()
    {
        $data = 'xml=<?xml version="1.0"?>
        <Envelope>
            <Body>
                <ImportList>
                    <MAP_FILE>spopVouchersMap.xml</MAP_FILE>
                    <SOURCE_FILE>spopVouchers.csv</SOURCE_FILE>
                </ImportList>
            </Body>
        </Envelope>';

        $result = $this->_sendToSilverpop($data);

        return $result;
    }
}
