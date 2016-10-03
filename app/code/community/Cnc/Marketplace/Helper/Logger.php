<?php
class Cnc_Marketplace_Helper_Logger extends Mage_Core_Helper_Abstract
{

    private $reqId = '';
    private $reqStartTime = 0;
    private $log_to_file_flag = 0;

    public function __construct() {
        $this->cloudLoggingToken = '391d8d39-fcd6-4ab5-a09f-c37dade1a5fd';
        $this->cloudLoggingBaseURL = "http://logs-01.loggly.com/bulk/" . $this->cloudLoggingToken . "/tag/";
        $this->helper = Mage::helper( 'marketplace' );
        $this->log_to_file_flag = Mage::helper('marketplace/data')->getConfigurationData('config_log_data');
    }

    public function log($text, $value=null)
    {
        $value = isset( $value ) ? $value : '';
        if ( $value ) {
            $json_text = json_encode( $value );
        } else {
            $json_text = '';
        }

        $servertime = $this->getTimestamp();
        $shop_info = $this->helper->getConfigurationData( 'shop_info' );
        if (!$shop_info || trim($shop_info) == '') {
            $shop_info = 'NO_SHOP_INFO';
        }
        $calling_function = debug_backtrace()[1]['function'];
        $msg = "shop_info: $shop_info; req_guid: $this->reqId; svr_time: $servertime; func: $calling_function; $text: $json_text";

        // log out
        $this->logLogfile($msg);
        $this->logCloud($msg);
    }

    public function logActionStart($action) {
        $this->reqStartTime = microtime(true);
        $this->reqId = $this->generateRandomString(10);
        $pl_ver = Mage::helper('marketplace/util')->getCncMarketplacePluginVersion();
        $mag_ver = Mage::getVersion();
        $this->log("Start request $action; plugin_ver: $pl_ver; magento_ver: $mag_ver;", '');
    }

    public function logActionEnd($action) {
        $reqTime = sprintf('%0.3f', microtime(true) - $this->reqStartTime );
        $this->log("End request $action, Time: $reqTime s", '');
        $this->reqId = '';
        $this->reqStartTime=0;
    }

    private function logLogfile($msg)
    {
        // Only log to file if this is configured on in DB settings
        if ($this->log_to_file_flag == 1) {
            Mage::log($msg, null, 'cnc_marketplace.'.date('Y-m-d').'.log');
        }
    }

    private function logCloud($msg) {
        $request = curl_init($this->cloudLoggingBaseURL . $this->getVendorUnique() . "/");
        curl_setopt($request, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($request, CURLOPT_HTTPHEADER, array('Content-Type: text/plain'));
        curl_setopt($request, CURLOPT_POST, true);
        curl_setopt($request, CURLOPT_POSTFIELDS, $msg);
        $response = curl_exec( $request );
        if ( curl_errno( $request ) ) {
            // log errors normally?
        }
        curl_close( $request );
    }


    private function getVendorUnique() {
        // the vendor's domain name should be unique
        return parse_url(Mage::getBaseUrl(), PHP_URL_HOST);
    }

    private function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    private function getTimestamp() {
        $microtime = microtime(true);
        $microtimeformat = $this->containsDecimal($microtime) ? 'U.u' : 'U';
        return DateTime::createFromFormat($microtimeformat, $microtime)->format("Y-m-d H:i:s.u");
    }

    private function containsDecimal( $value ) {
        if ( strpos( $value, "." ) !== false ) {
            return true;
        }
        return false;
    }



}
