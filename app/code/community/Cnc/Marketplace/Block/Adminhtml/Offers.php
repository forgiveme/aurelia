<?php

class Cnc_Marketplace_Block_Adminhtml_Offers extends Mage_Adminhtml_Block_Template
{
    protected $helper;
    protected $importshelper;

    public function __construct()
    {
        parent::__construct();
        $this->setFormAction(Mage::getUrl('*/*/productUpload'));
        $this->setDownloadAction(Mage::getUrl('*/*/downloadError'));
        $this->setListAction(Mage::getUrl('*/*/getList'));
        $this->setofferEditSaveAction(Mage::getUrl('*/*/offerEditSave'));
        $this->setmessageOfferAnswerAction(Mage::getUrl('*/*/messageOfferAnswer'));
        $this->setmessageOfferGetAction(Mage::getUrl('*/*/messageOfferGet'));
        $this->setmessageOfferGetAction(Mage::getUrl('*/*/messageOfferGet'));
        $this->setofferSingleAction(Mage::getUrl('*/*/offerSingle'));
        $this->helper = Mage::helper('marketplace');
        $this->callApi = Mage::helper('marketplace/callapi');
        $this->importshelper = Mage::helper('marketplace/imports');
    }

    public function getProductsMapDetails()
    {
        $post = Mage::app()->getRequest()->getPost();
        $limitstart = isset($post['limitstart']) ? $post['limitstart'] : '1';
        if ($post['offer_message_filter'])
            $offer_message_filter = $post['offer_message_filter'];
        if ($post['offer_mapping_go'])
            $this->helper->getOffersToUpload($cron);
        $this->helper->saveOrderMessages();
        $offerMessages = $this->helper->getOrderOfferMessages();
        $this->storeOffersList($limitstart);
        $offersList = $this->getOffersList($limitstart, '', $offer_message_filter);
        $totalintable = $this->getOffersList($limitstart, 1, '');
        $field_attributes = $this->helper->getDefaultProductAttributes('offer');
        $mapped_fields_data = $this->helper->getConfigurationData('product_map');
        $block = $this->getLayout()->getBlock('offers');
        $block->setData('offersList', $offersList);
        $block->setData('limitstart', $limitstart);
        $block->setData('offerMessages', $offerMessages);
        $block->setData('totalintable', $totalintable);
        $block->setData('field_attributes', $field_attributes);
        $block->setData('mapped_fields_data', $mapped_fields_data);

        $block->setData('offer_import_total_pages', $this->importshelper->getLastPageNr('offer'));

        $offer_imports_page_nr = isset($post['offer_imports_page_nr']) ? $post['offer_imports_page_nr'] : 1;
        $offer_import_ids = $this->importshelper->getPageImportIds('offer', $offer_imports_page_nr);
        $block->setData('offer_imports_page_nr', $offer_imports_page_nr);
        $block->setData('offer_import_ids', $offer_import_ids);
        $block->setData('imports_navigated', isset($post['offer_imports_page_nr']));
    }

    public function storeOffersList()
    {
        $offers_data = $this->callApi->getAllOffers();
        $offers = json_decode($offers_data);
        $this->deleteExistingOffers($offers->offers);
        $this->storeOfferMessages($offers->offers);
        Mage::helper('marketplace/logger')->log('storeOffersList - offers_data: ', $offers_data);
    }

    public function deleteExistingOffers($offers)
    {
        $api_offers = array();
        $check_del = Mage::getModel('marketplace/offertable')->getCollection()->getData();
        foreach ($offers as $offer) {
            $api_offers[] = $offer->shop_sku;
        }
        $errors = array();
        foreach ($check_del as $del) {
            if (!in_array($del['offer_sku'], $api_offers)) {
                $id = $del['id'];
                $model = Mage::getModel('marketplace/offertable');
                try {
                    $model->setId($id)->delete();
                } catch (Exception $e) {
                    Mage::getSingleton('core/session')->addError($e->getMessage());
                    array_push($errors, array($id => $e->getMessage()));
                    session_write_close();
                }
            }
        }

        if (count($errors) > 0) {
            Mage::helper('marketplace/logger')->log('deleteExistingOffers - Errors: ', $errors);
        }
    }

    public function storeOfferMessages($offers)
    {
        foreach ($offers as $offer) {
            $all_fields = json_encode($offer);
            $check = Mage::getModel('marketplace/offertable')->getCollection()->getOfferById($offer->shop_sku)->getData();
            if (isset($check[0]['id']) && $check[0]['id']) {
                $this->updateOfferMessages($check[0]['id'], $offer->shop_sku, $offer->offer_id, $all_fields);
                Mage::helper('marketplace/logger')->log('storeOfferMessages - updateOfferMessages: ', $offer);
            } else {
                Mage::helper('marketplace/logger')->log('storeOfferMessages - insertOfferMessages: ', $offer);
                $this->insertOfferMessages($offer->shop_sku, $offer->offer_id, $all_fields);
            }
        }
    }

    public function updateOfferMessages($id, $offer_sku, $offer_id, $all_fields)
    {
        $data = array(
            'offer_sku' => $offer_sku,
            'offer_id' => $offer_id,
            'all_fields' => $all_fields
        );
        $model = Mage::getModel('marketplace/offertable')->load($id)->addData($data);
        try {
            $model->setId($id)->save();
        } catch (Exception $e) {
            Mage::getSingleton('core/session')->addError($e->getMessage());
            Mage::helper('marketplace/logger')->log('updateOfferMessages - Error: ', $e->getMessage());
            session_write_close();
        }
    }

    public function insertOfferMessages($shop_sku, $offer_id, $all_fields)
    {
        $data = array(
            'offer_sku' => $shop_sku,
            'offer_id' => $offer_id,
            'all_fields' => $all_fields
        );
        $model = Mage::getModel('marketplace/offertable')->setData($data);
        try {
            $model->save()->getId();
        } catch (Exception $e) {
            Mage::getSingleton('core/session')->addError($e->getMessage());
            Mage::helper('marketplace/logger')->log('insertOfferMessages - Error: ', $e->getMessage());
            session_write_close();
        }
    }

    public function getOffersList($limitstart, $full, $offer_message_filter)
    {
        $full = isset($full) ? $full : '0';
        $offer_message_filter = isset($offer_message_filter) ? $offer_message_filter : '';
        $collections = Mage::getModel('marketplace/offertable')->getCollection();
        if ($offer_message_filter) {
            $collections->getMessageUnread($offer_message_filter);
        }
        if (!$full) {
            $collections->setPageSize(10);
            $collections->setCurPage($limitstart);
        }
        $result = array();
        foreach ($collections as $key => $collection) {
            $result_get = $collection->getData();
            $result[$key] = json_decode($result_get['all_fields']);
        }
        if ($full) {
            return count($result);
        } else {
            return $result;
        }
    }

    public function deleteBulkOffer($offer_id)
    {
        $offer_data = Mage::app()->getRequest()->getPost();
        $response = $this->callApi->deleteBulkOffersApi($offer_data);
        if ($response) {
            $this->helper->setImportData($response, 'offer');
        }
        Mage::helper('marketplace/logger')->log('deleteBulkOffer - offer_data: ', $offer_data);
    }

    public function offerSingleGeTAjax()
    {
        $post = Mage::app()->getRequest()->getPost();
        $offer_full = Mage::getModel('marketplace/offertable')->getCollection()->getOfferById($post['offer_id_ajax'])->getData();
        Mage::helper('marketplace/logger')->log('offerSingleGeTAjax - offer_full: ', $offer_full);
        return $offer_full[0]['all_fields'];
    }

    public function saveSingleOffer()
    {
        $response = $this->callApi->updateOffer();
        Mage::helper('marketplace/logger')->log('saveSingleOffer - response: ', $response);
        if ($response) {
            $this->helper->setImportData($response, 'offer');
        }
    }

    public function answerMesssages($offerID, $body)
    {
        $offerID = isset($offerID) ? $offerID : '';
        $body = isset($body) ? $body : '';
        $msg_ids = Mage::getModel('marketplace/messagetable')->getCollection()->getByOrderOfferID($offerID)->getData();
        $response = $this->callApi->answerOfferMessages($msg_ids, $body, $offerID);
        $logMessages = array(
            'response' => $response,
            'offerID' => $offerID,
            'body' => $body
        );
        Mage::helper('marketplace/logger')->log('answerMesssages ', $logMessages);
        echo $response;
    }
}
