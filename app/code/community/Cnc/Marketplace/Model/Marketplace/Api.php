<?php
/**
 * Created by Daniel Rafique.
 * For: Style.com
 * Date: 20/07/2016
 * Time: 09:40
 * Copyright all rights reserved to author of this content.
 */

class Cnc_Marketplace_Model_Marketplace_Api 
{
    public function getCurrentStoreId()
    {
        $storeId = (int) Mage::app()->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }

    public function apiUrl()
    {
        $api_url = Mage::getStoreConfig('marketplace/configuration/api_url', $this->getCurrentStoreId());
        return $api_url;
    }

    public function apiKey()
    {
        $api_key = Mage::getStoreConfig('marketplace/configuration/api_key', $this->getCurrentStoreId());
        return $api_key;
    }

    public function getShopId()
    {
        $shop_id = Mage::getStoreConfig('marketplace/configuration/shop_id', $this->getCurrentStoreId());
        return $shop_id;
    }

    public function getShopName()
    {
        $shop_name = Mage::getStoreConfig('marketplace/configuration/shop_name', $this->getCurrentStoreId());
        return $shop_name;
    }

    public function getStatusMap()
    {
        $status = Mage::getStoreConfig('marketplace/acceptance_settings/automation_active', $this->getCurrentStoreId());
        return $status;
    }

}
