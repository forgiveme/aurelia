<?php
/**
 * Created by Daniel Rafique.
 * For: Style.com
 * Date: 01/08/2016
 * Time: 16:02
 * Copyright all rights reserved to author of this content.
 */

class Cnc_Marketplace_Model_Adminhtml_System_Config_Source_Carriers_Options
{
    public function toOptionArray()
    {
        $methods = $this->getCarriers();
        $options = array();
        foreach ($methods->carriers as $method) {
            $options[] = array(
                'value' => $method->code,
                'label' => $method->label
            );
        }
        if (!$isMultiSelect) {
            array_unshift($options, array(
                'value' => '',
                'label' => Mage::helper('adminhtml')->__('--Please Select--')
            ));
        }
        return $options;
    }

    public function getCarriers($check = '')
    {
        $api = $this->_apiCredentials();
        $url = $api->apiUrl();
        $apiKey = $api->apiKey();
        if ($url && $apiKey) {
            $request = curl_init($url . "/api/shipping/carriers?api_key=" . $apiKey);
            curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($request, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($request, CURLOPT_SSL_VERIFYHOST, FALSE);
            $res = curl_exec($request);
            if ($check) {
                $result = json_decode($res);
                $value = isset($result->carriers[0]->code) ? $result->carriers[0]->code : '';
                if (curl_errno($request) || $value == '') {
                    return 0;
                } else {
                    return 1;
                }
            } else {
                if (curl_errno($request)) {
                    $error = 'Curl error: ' . curl_error($request);
                    Mage::getSingleton('core/session')->addError("Please check your internet connection and the credentials entered in your config. " . $error);
                }
                return json_decode($res);
            }
            curl_close($request);
        }
    }

    private function _apiCredentials()
    {
        $api = Mage::getModel('marketplace/marketplace_api', $this->getCurrentStoreId());
        return $api;
    }

    protected function getCurrentStoreId()
    {
        $store_id = Mage::app()->getRequest()->getParam('store', 0);
        return $store_id;
    }

}
