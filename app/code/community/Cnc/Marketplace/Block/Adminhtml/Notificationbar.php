<?php

class Cnc_Marketplace_Block_Adminhtml_Notificationbar extends Mage_Adminhtml_Block_Template
{
    public function getMessage()
    {
        $model = Mage::getModel('marketplace/ordertable');
        $model_msg = Mage::getModel('marketplace/messagetable');
        $helper = Mage::helper('marketplace');
        $count = array();
        if ($helper->checkCredentials() == '1') {
            $waiting_acceptance = $model->getCollection()->getCountOrderStatus('WAITING_ACCEPTANCE')->getFirstItem()->getData();
            $unread_incidents = $model->getCollection()->getUnreadIncidentsCount()->getFirstItem()->getData();
            $unread_msg_order = $model_msg->getCollection()->getCountMessages('order')->getFirstItem()->getData();
            $unread_msg_offer = $model_msg->getCollection()->getCountMessages('offer')->getFirstItem()->getData();
            if ($helper->getConfigurationData('order_acceptance') == '1') {
                $unread_error_messages = $model->getCollection()->getUnreadErrorsCount()->getFirstItem()->getData();
                if (isset($unread_error_messages) && !empty($unread_error_messages)) {
                    $count['unread_error_messages'] = $unread_error_messages;
                }
            }
            if (isset($waiting_acceptance) && !empty($waiting_acceptance)) {
                $count['waiting_acceptance'] = $waiting_acceptance;
            }
            if (isset($unread_incidents) && !empty($unread_incidents)) {
                $count['unread_incidents'] = $unread_incidents;
            }
            if (isset($unread_msg_order) && !empty($unread_msg_order) && $unread_msg_order['counts'] > 0) {
                $count['unread_msg_order'] = $unread_msg_order;
                $count['order_message_ids'] = json_encode($unread_msg_order['order_offer_ids']);
            }
            if (isset($unread_msg_offer) && !empty($unread_msg_offer) && $unread_msg_offer['counts'] > 0) {
                $count['unread_msg_offer'] = $unread_msg_offer;
                $count['offer_message_ids'] = json_encode($unread_msg_offer['order_offer_ids']);
            }
        }
        return (!empty($count)) ? $count : '';
    }
}
