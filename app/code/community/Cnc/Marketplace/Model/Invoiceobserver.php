<?php

class Cnc_Marketplace_Model_Invoiceobserver
{
    public function logUpdate(Varien_Event_Observer $event)
    {
        $helper = Mage::helper('marketplace');
        $callApi = Mage::helper('marketplace/callapi');
        $orderId = $event->getOrder()->getIncrementId();
        $mirakle_data = $helper->getSingleOrder($orderId, 1);
        if (count($mirakle_data) > 0 && isset($mirakle_data['orderid']) && $mirakle_data['orderid']) {
            Mage::helper('marketplace/logger')->log('logUpdate - orderid', $mirakle_data['orderid']);
            $order = $event->getOrder()->getData();
            $orderdata = $event->getOrder();
            $status = $orderdata->getStatus();
            $order_states = Mage::getModel('sales/order_status')->getResourceCollection()->joinStates()->getData();
            $state_array = array();
            foreach ($order_states as $order_state) {
                if ($order_state['status'] == $status && $order_state['is_default'] == 1) {
                    $state_array[] = $order_state['state'];
                }
            }
            $count_statearray = count($state_array);
            if ($count_statearray > 0)
                $final_state = $state_array[0];
            $mappedstate = json_decode($helper->getConfigurationData('state_mapping'));
            Mage::helper('marketplace/logger')->log('logUpdate - final_state', array('final_state' => $final_state, 'ship_state_mapping' => $mappedstate->ship_state_mapping));
            $final_state = isset($final_state) ? $final_state : '';
            if ($mappedstate->ship_state_mapping == $final_state) {
                $result = $helper->setMirakleOrders('shipped', $mirakle_data['orderid']);
                $res = json_decode($result);
                if (isset($res->message) && $res->message != '')
                    $helper->updateCustomOrderTable($mirakle_data['orderid'], $orderId, $res->message);
            }
            if ($mappedstate->track_state_mapping == $final_state) {
                $order_real = Mage::getModel('sales/order')->loadByIncrementId($orderId);
                $shipmentCollection = $order_real->getShipmentsCollection();
                foreach ($shipmentCollection as $shipment) {
                    $shipmentIncrementId = $shipment->getIncrementId();
                    $shipment = Mage::getModel('sales/order_shipment')->loadByIncrementId($shipmentIncrementId);
                    foreach ($shipment->getAllTracks() as $track) {
                        $tracking_details = $track->getData();
                        $flatrate = Mage::getStoreConfig('marketplace/acceptance_settings/flatrate');
                        $fedex = Mage::getStoreConfig('marketplace/acceptance_settings/fedex');
                        $freeshipping = Mage::getStoreConfig('marketplace/acceptance_settings/freeshipping');
                        $ups = Mage::getStoreConfig('marketplace/acceptance_settings/ups');

                        $carrier_mapped = array($flatrate, $fedex, $freeshipping, $ups);
                        $tracking_code = array();
                        foreach ($carrier_mapped as $carrier_map) {
                            if (isset($carrier_map['value']) && $carrier_map['value'])
                                $tracking_code[] = '';
                        }
                        $tracking_label = array();
                        $ship_col = $callApi->getCarriers();
                        foreach ($ship_col->carriers as $sc) {
                            if ($sc->code == $tracking_code[0])
                                $tracking_label[] = $sc->label;
                        }

                        $carrierCode = isset($tracking_code[0]) ?
                            $tracking_code[0] : 'custom';
                        $courier = isset($tracking_label[0]) ?
                            $tracking_label[0] : $tracking_details['title'];

                        if ($tracking_details['track_number']) {
                            $fields_selected = array(
                                'orderId' => $mirakle_data['orderid'],
                                'carriercode' => $carrierCode,
                                'courier' => $courier,
                                'trackingNumber' => $tracking_details['track_number']
                            );

                            $result = $helper->setMirakleOrders('tracking', $order ,$fields_selected);
                            $res = json_decode($result);
                            if (isset($res->message) && $res->message != '')
                                $helper->updateCustomOrderTable($mirakle_data['orderid'], $orderId, 'Tracking: ' . $res->message);
                        }
                    }
                }
            }
        }
    }
}
