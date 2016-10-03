<?php

class Cnc_Marketplace_Block_Adminhtml_Orders extends Mage_Adminhtml_Block_Template
{
    protected $helper;
    const STYLE_EXPRESS_OPTION = 'EXP';
    const STYLE_EXPRESS_DESCRIPTION = 'Style.com Shipping Method - Express';

    public function __construct()
    {
        parent::__construct();
        $this->setFormAction(Mage::getUrl('*/*/ordersubmit'));
        $this->setPaginationAction(Mage::getUrl('*/*/index'));
        $this->setOrderLoadingAction(Mage::getUrl('*/*/OrderLoading'));
        $this->setDownloadJsonAction(Mage::getUrl('*/*/downloadJson'));
        $this->setChangeUnreadOrderAction(Mage::getUrl('*/*/changeUnreadOrderStatus'));
        $this->setChangeUnreadIncidentAction(Mage::getUrl('*/*/changeUnreadIncidentStatus'));
        $this->setGetOrderMessagesAction(Mage::getUrl('*/*/GetOrderMessages'));
        $this->setOrderMessageAnswerAction(Mage::getUrl('*/*/OrderMessageAnswer'));
        $this->setorderLinesAction(Mage::getUrl('*/*/orderLinesGet'));
        $this->setorderLinesPostAction(Mage::getUrl('*/*/orderLinesPost'));
        $this->helper = Mage::helper('marketplace');
        $this->callApi = Mage::helper('marketplace/callapi');
    }

    public function getFiltersFromPost($filter_item)
    {
        if (is_array($filter_item) && isset($filter_item) && count($filter_item) > 0) {
            $filters = $filter_item;
            foreach ($filters as $value) {
                if ($value == "WAITING_DEBIT-WAITING_DEBIT_PAYMENT") {
                    $splitVal = explode("-", $value);
                }
            }
            $replaceKey = array_search("WAITING_DEBIT-WAITING_DEBIT_PAYMENT", $filters);
            if (trim($replaceKey) !== "") {
                unset($filters[$replaceKey]);
                $filters = array_merge($filters, $splitVal);
            }
        } else {
            if (isset($filter_item)) {
                $filters = json_decode($filter_item);
            }
        }
        return $filters;
    }

    public function getOrderDetails()
    {
        $post = Mage::app()->getRequest()->getPost('store');
        $unread_orders = isset($post['unread_orders']) ? $post['unread_orders'] : '';
        $post['filter_item'] = isset($post['filter_item']) ? $post['filter_item'] : '';
        if (!$unread_orders) {
            $filters = $this->getFiltersFromPost($post['filter_item']);
            $filter_exit = isset($post['filter_exit']) ? $post['filter_exit'] : '';
            if ($filters != '' || $filter_exit) {
                Mage::getSingleton('core/session')->setCmiOrderFilters($filters);
            }
            $filters = Mage::getSingleton('core/session')->getCmiOrderFilters();
        }
        $limitstart = isset($post['limitstart']) ? $post['limitstart'] : '1';
        $search_field = isset($post['search_field']) ? $post['search_field'] : '';
        $search_which_order = isset($post['search_which_order']) ? $post['search_which_order'] : '';
        $carriers = $this->callApi->getCarriers();
        $refund_reasons = $this->callApi->getRefundReasons();
        $order_acceptance = Mage::getStoreConfig('marketplace/acceptance_settings/automation_active', Mage::getSingleton('core/session')->getMiraklStoreId());
        $mirakle_data_full = $this->getAllOrders('1', true, $filters, '', '', $unread_orders);
        $mirakle_data_full_first = $this->getAllOrders('1', true, '', '', '', $unread_orders);
        $totalintable_first = count($mirakle_data_full_first);
        $this->storeMirakleOrdersToCustomTable($totalintable_first);
        if ($post['order_message_filter'])
            $order_message_filter = json_decode($post['order_message_filter']);
        $this->helper->saveOrderMessages();
        $orderMessages = $this->helper->getOrderOfferMessages();
        $mirakle_data = $this->getAllOrders($limitstart, false, $filters, $search_field, $search_which_order, $unread_orders, $order_message_filter);
        $block = $this->getLayout()->getBlock('orders');
        $block->setData('unread_orders', $unread_orders);
        $block->setData('search_field', $search_field);
        $block->setData('search_which_order', $search_which_order);
        $block->setData('limitstart', $limitstart);
        $block->setData('orderMessages', $orderMessages);
        $block->setData('carriers', $carriers);
        $block->setData('refund_reasons', $refund_reasons);
        $block->setData('order_acceptance', $order_acceptance);
        $block->setData('filters', json_encode($filters));
        $block->setData('totalintable', count($mirakle_data_full));
        $block->setData('mirakle_data', $mirakle_data);
    }

    public function getAllOrders($limit, $full, $filters, $search_field, $search_which, $unread_orders = '', $order_message_filter = '')
    {
        $search_field = isset($search_field) ? trim($search_field) : '';
        $search_which = isset($search_which) ? $search_which : '';
        $filters = isset($filters) ? $filters : array();
        $limit = isset($limit) ? $limit : '0';
        $order_message_filter = isset($order_message_filter) ? $order_message_filter : '';
        $model = Mage::getModel('marketplace/ordertable');
        $collection = $model->getCollection()->setOrder('id', 'DESC');
        if ($full != 'true') {
            $collection->setPageSize(20);
        }
        if (isset($filters[0]) && trim($filters[0]) != '') {
            $filter = "'" . implode("','", $filters) . "'";
            $collection->addOrderFilter($filter);
        }
        if ($search_field || $unread_orders) {
            $collection->searchOrder($search_field, $search_which, $unread_orders);
        }
        if ($order_message_filter) {
            $collection->getMessageUnread($order_message_filter);
        }
        $collection->setCurPage($limit);
        $orders_data = array();
        foreach ($collection as $item) {
            $orders_data[] = $item->getData();
        }
        return $orders_data;
    }

    public function storeMirakleOrdersToCustomTable($total_count)
    {
        $offset = '';
        $fetch_limit = 100;
        if ($total_count > 0) {
            $count_offset = $this->saveMirakleOrders('', $fetch_limit, '');
        } else {
            do {
                $count_offset = $this->saveMirakleOrders($offset, $fetch_limit, 'recursive');
                $count_offset = explode('-', $count_offset);
                if (trim($count_offset[1]) == '') {
                    $offset = $fetch_limit;
                } else {
                    $offset = $count_offset[1] + $fetch_limit;
                }
                $total_count = $count_offset[0];
            } while ($total_count == $fetch_limit);
        }
    }

    public function saveMirakleOrders($offset, $max, $recursive)
    {
        $latest = $this->getLastestOrder('');
        $date_array = explode(' ', trim($latest['max(last_updated_date)']));
        $date = $date_array[0] . 'T' . $date_array[1] . 'Z';
        $mirakle_data_response = $this->callApi->getMirakleDataCurlResponse($offset, $max, $recursive, $date);
        $mirakle_data = json_decode($mirakle_data_response, true);
        foreach ($mirakle_data['orders'] as $key => $value) {
            $all_fields = json_encode($value);
            $this->insertorderData($value, $all_fields);
        }
        $automation = Mage::getStoreConfig('marketplace/acceptance_settings/automation_active');
        if ($automation) {
            $all_updated_fields = $this->getAllCustomOrderTable();
            foreach ($all_updated_fields as $key => $all_updated_field) {
                $all_updated_field = json_decode($all_updated_field['all_fields'], true);
                session_write_close();
                $this->setMagentoMirakleOrders($all_updated_field);
            }
        }
        $count_mo = count($mirakle_data['orders']);
        return $count_mo . '-' . $offset;
    }

    public function getLastestOrder($orderid)
    {
        $connection = Mage::getSingleton('core/resource')->getConnection('core_read');
        $prefix = Mage::getConfig()->getTablePrefix();
        $select = $connection->select()->from($prefix . 'cn_cmi_orders', array(
            'max(last_updated_date)'
        ));
        $rowArray = $connection->fetchRow($select);
        return $rowArray;
    }

    public function insertorderData($value, $output)
    {
        $check = $this->helper->getSingleOrder($value['order_id'], '');
        if ($check['orderid']) {
            $id = $check['id'];
            $data = array(
                'orderid' => $value['order_id'],
                'last_updated_date' => $value['last_updated_date'],
                'total_price' => $value['total_price'],
                'order_state' => $value['order_state'],
                'all_fields' => $output,
                'is_mirakl_order' => '1'
            );
            $model = Mage::getModel('marketplace/ordertable')->load($id)->addData($data);
            try {
                $model->setId($id)->save();
            } catch (Exception $e) {
                $logMessages = array(
                    'value' => $value,
                    'output' => $output,
                    'Error' => $e->getMessage()
                );
                Mage::helper('marketplace/logger')->log('insertorderData update', $logMessages);
                Mage::getSingleton('core/session')->addError($e->getMessage());
                session_write_close();
            }
        } else {
            $has_incident = (isset($value['has_incident']) && !empty($value['has_incident'])) ? '1' : '0';
            $data = array(
                'orderid' => $value['order_id'],
                'last_updated_date' => $value['last_updated_date'],
                'total_price' => $value['total_price'],
                'order_state' => $value['order_state'],
                'all_fields' => $output,
                'order_read' => '1',
                'has_incident' => $has_incident,
                'incident_read' => '1',
                'is_mirakl_order' => '1',
            );
            $model = Mage::getModel('marketplace/ordertable')->setData($data);
            try {
                $model->save()->getId();
            } catch (Exception $e) {
                $logMessages = array(
                    'value' => $value,
                    'output' => $output,
                    'Error' => $e->getMessage()
                );
                Mage::helper('marketplace/logger')->log('insertorderData insert', $logMessages);
                Mage::getSingleton('core/session')->addError($e->getMessage());
                session_write_close();
            }
        }
    }

    public static function getAllCustomOrderTable()
    {
        $model = Mage::getModel('marketplace/ordertable');
        $collection = $model->getCollection();
        $array = array();
        foreach ($collection as $item) {
            $array[] = $item->getData();
        }
        return $array;
    }

    public function setMagentoMirakleOrders($mirakle_orders)
    {
        $morders = isset($mirakle_orders) ? $mirakle_orders : '';
        if ($morders['order_state'] == 'WAITING_ACCEPTANCE' || $morders['order_state'] == 'SHIPPING') {
            list($is_there_a_product, $is_product_instock, $is_prod_in_stylecom, $prod_store_sku) = $this->getProductStockExists($morders['order_lines']);
            $logMessages = array(
                'is_there_a_product' => $is_there_a_product,
                'is_prod_in_stylecom' => $is_prod_in_stylecom,
                'is_product_instock' => $is_product_instock,
                'prod_store_sku' => $prod_store_sku
            );
            Mage::helper('marketplace/logger')->log('setMagentoMirakleOrders', $logMessages);
            if (trim(array_search("", $is_there_a_product)) == '' && trim(array_search("0", $is_prod_in_stylecom)) == '' && trim(array_search("", $is_prod_in_stylecom)) == '' && trim(array_search('0', $is_product_instock)) == '') {
                if ($morders['order_state'] == 'WAITING_ACCEPTANCE') {
                    $order_lines_pass = array();
                    foreach ($morders['order_lines'] as $order_lines) {
                        $order_lines_pass[] = json_encode(array(
                            'accept' => $order_lines['order_line_id']
                        ));
                    }
                    $this->helper->setMirakleOrders('confirm_multiple', $morders['order_id'], $order_lines_pass);
                    $this->helper->updateCustomOrderTable($morders['order_id'], '', '');
                }
                $mcustomer = $morders['customer'];
                $customer_details = $this->getValidatedCustomerDetails($mcustomer);
                list($BilladdressData, $ShipaddressData) = $this->formatCustomerAddr($customer_details);
                $check_order_id = $this->helper->getSingleOrder($morders['order_id'], '');
                $create_new_order = false;
                $new_order_id = '';
                if (trim($check_order_id['m_order_id']) == "" && $morders['order_state'] == 'SHIPPING') {
                    $new_order_id = $this->createMagentoOrder($morders, $customer_details['email'], $BilladdressData, $ShipaddressData);
                    $create_new_order = true;
                } else if (trim($check_order_id['m_order_id']) != "" && $morders['order_state'] != 'SHIPPING') {
                    $new_order_id = $check_order_id['m_order_id'];
                }
                Mage::helper('marketplace/logger')->log('setMagentoMirakleOrders - new_order_id: ', $new_order_id);
                if ($new_order_id) {
                    $this->changeOrdersInMagento($new_order_id, $morders['order_state'], $morders['shipping_price'], $ShipaddressData, $BilladdressData, $create_new_order, $morders['order_id']);
                    $this->helper->updateCustomOrderTable($morders['order_id'], $new_order_id, '');
                }
            } else {
                $exists_go = array();
                foreach ($is_there_a_product as $kkey => $exists) {
                    if ($exists == '')
                        $exists_go[] = $prod_store_sku[$kkey];
                }
                $not_exists_sku = implode(',', array_unique($exists_go));
                $exists_instylecom_go = array();
                foreach ($is_prod_in_stylecom as $kcey => $exists_instylecom) {
                    if ($exists_instylecom == '' || $exists_instylecom == 0)
                        $exists_instylecom_go[] = $prod_store_sku[$kcey];
                }
                $not_exists_stylecom_sku = implode(',', array_unique($exists_instylecom_go));
                $not_in_stock_go = array();
                foreach ($is_product_instock as $skey => $not_in_stock) {
                    if ($not_in_stock == 0)
                        $not_in_stock_go[] = $prod_store_sku[$skey];
                }
                $not_instock_sku = implode(',', array_unique($not_in_stock_go));
                $check_order_id = $this->helper->getSingleOrder($morders['order_id'], '');
                if ($not_exists_sku && trim($check_order_id['m_order_id']) == "") {
                    $this->helper->updateCustomOrderTable($morders['order_id'], '', 'Product with sku(s): ' . $not_exists_sku . ' does not exist');
                } elseif ($not_instock_sku && trim($check_order_id['m_order_id']) == "") {
                    $this->helper->updateCustomOrderTable($morders['order_id'], '', 'Product with sku(s): ' . $not_instock_sku . ' is out of stock');
                } elseif ($not_exists_stylecom_sku && trim($check_order_id['m_order_id']) == "") {
                    $this->helper->updateCustomOrderTable($morders['order_id'], '', 'Product with sku(s): ' . $not_exists_stylecom_sku . ' is not added to style.com');
                }
            }
        } else if ($morders['order_state'] == 'RECEIVED' || $morders['order_state'] == 'CLOSED' || $morders['order_state'] == 'CANCELED') {
            $full_orders = Mage::getModel('marketplace/ordertable')->getCollection()->searchOrder($morders['order_id'], 1, '')->getData();
            $new_order_id = $full_orders[0]['m_order_id'];
            if (isset($new_order_id) && trim($new_order_id) != '')
                $this->changeOrdersInMagento($new_order_id, $morders['order_state'], '', '', '', false, $morders['order_id']);
        }
    }

    public function formatCustomerAddr($customer_details)
    {
        $BilladdressData = array(
            'prefix' => $customer_details['btitle'],
            'firstname' => $customer_details['bfname'],
            'lastname' => $customer_details['blname'],
            'street' => $customer_details['bstreet1'] . ' ' . $customer_details['bstreet2'],
            'city' => $customer_details['bcity'],
            'postcode' => $customer_details['bzip'],
            'telephone' => $customer_details['bphone'],
            'country_id' => $customer_details['bcountry'],
            'region_id' => $customer_details['bstate']
        );
        $ShipaddressData = array(
            'prefix' => $customer_details['stitle'],
            'firstname' => $customer_details['sfname'],
            'lastname' => $customer_details['slname'],
            'street' => $customer_details['sstreet1'] . ' ' . $customer_details['sstreet2'],
            'city' => $customer_details['scity'],
            'postcode' => $customer_details['szip'],
            'telephone' => $customer_details['sphone'],
            'country_id' => $customer_details['scountry'],
            'region_id' => $customer_details['sstate']
        );
        return array(
            $BilladdressData,
            $ShipaddressData
        );
    }

    public function getFinalStatus()
    {
       $magento_value = Mage::getStoreConfig('marketplace/acceptance_settings/order_shipped');
        return $magento_value;
    }

    public function getMagentoOrderState($status)
    {
        $order_states = Mage::getModel('sales/order_status')->getCollection()->joinStates()->getData();
        $state = array();
        foreach ($order_states as $order_state) {
            if ($order_state['status'] == $status)
                $state[] = $order_state['state'];
        }
        return $state[0];
    }

    public function setOrderState($order_real, $final_state, $final_status)
    {
        switch ($final_state) {
            case 'new':
                $order_real->setState(Mage_Sales_Model_Order::STATE_NEW, $final_status, '', false);
                break;
            case 'processing':
                $order_real->setState(Mage_Sales_Model_Order::STATE_PROCESSING, $final_status, '', false);
                break;
            case 'closed':
                $order_real->setState(Mage_Sales_Model_Order::STATE_CLOSED, $final_status, '', false);
                break;
            case 'canceled':
                $order_real->setState(Mage_Sales_Model_Order::STATE_CANCELED, $final_status, '', false);
                break;
            case 'holded':
                $order_real->setState(Mage_Sales_Model_Order::STATE_HOLDED, $final_status, '', false);
                break;
            case 'payment_review':
                $order_real->setState(Mage_Sales_Model_Order::STATE_PAYMENT_REVIEW, $final_status, '', false);
                break;
            case 'pending_payment':
                $order_real->setState(Mage_Sales_Model_Order::STATE_PENDING_PAYMENT, $final_status, '', false);
                break;
            default:
                $order_real->setState(Mage_Sales_Model_Order::STATE_NEW, $final_status, '', false);
                break;
        }

        $logMessages = array(
            'order_real' => $order_real,
            'final_state' => $final_state,
            'final_status' => $final_status
        );
        Mage::helper('marketplace/logger')->log('setOrderState', $logMessages);
    }

    public function saveNewMagentoOrderFromMirakle($new_order_id, $final_status, $shippingprice, $mirakl_order_id)
    {
        $order_real = Mage::getModel('sales/order')->loadByIncrementId($new_order_id);
        $final_state = $this->getMagentoOrderState($final_status);
        if (isset($final_status) && $final_status != '') {
            try {
                $this->setOrderState($order_real, $final_state, $final_status);
                $this->helper->updateCustomOrderTable($mirakl_order_id, $new_order_id, '');
            } catch (Exception $e) {
                $error = $e->getMessage();
                $this->helper->updateCustomOrderTable($mirakl_order_id, $new_order_id, 'Magento status cannot evolve in such a manner. Error from magento:' . $error);
            }
        }
        Mage::helper('marketplace/logger')->log('saveNewMagentoOrderFromMirakle - shippingprice: ', $shippingprice);
        if (trim($shippingprice) != '') {
            $order_real->setShippingAmount($shippingprice);
            $order_real->setBaseShippingAmount($shippingprice);
            $price = $order_real->getSubtotal();
            $order_real->setBaseGrandTotal($price);
            try {
                $order_real->setidentityStyleCom(1);
            } catch (Exception $e) {
                echo $error = $e->getMessage();
                $this->helper->updateCustomOrderTable($mirakl_order_id, $new_order_id, 'Error: Could not set identifier as style.com order. Error from magento:' . $error);
                exit;
            }
            $order_real->setGrandTotal($price + $shippingprice);
            $order_real->setData('is_mirakl_order', 1);
            Mage::helper('marketplace/logger')->log('saveNewMagentoOrderFromMirakle - setGrandTotal: ', $price + $shippingprice);
            $order_real->save();
        }
    }

    public function updateExistingMagentoOrderFromMirakle($new_order_id, $final_status, $mirakl_order_id)
    {
        $order_real = Mage::getModel('sales/order')->loadByIncrementId($new_order_id);
        $current_status = $order_real->getStatus();
        $final_state = $this->getMagentoOrderState($final_status);
        if ($current_status != $final_status) {
            try {
                $this->setOrderState($order_real, $final_state, $final_status);
                $this->helper->updateCustomOrderTable($mirakl_order_id, $new_order_id, '');
            } catch (Exception $e) {
                $error = $e->getMessage();
                $this->helper->updateCustomOrderTable($mirakl_order_id, $new_order_id, 'Magento status cannot evolve in such a manner. Error from magento:' . $error);
            }
        }
        $order_real->save();
    }

    public function sendNewOrderMessage()
    {
        $message_details = Mage::app()->getRequest()->getPost();
        $orderId = isset($message_details['order_msg_order_id']) ? $message_details['order_msg_order_id'] : '';
        return $this->callApi->answerOrderMessages($orderId, $message_details);
    }

    public function changeOrdersInMagento($new_order_id, $order_state, $shipping_price, $ShipaddressData, $BilladdressData, $create_new_order, $mirakl_order_id)
    {
        $lists = Mage::getStoreConfig('marketplace/acceptance_settings/order_shipped');
        $mirakle_statuses = json_decode($this->helper->getConfigurationData('mirakle_status'));
        foreach ($mirakle_statuses as $mirakle_status) {
            $final_status = $this->getFinalStatus($lists, $mirakle_status);
            if ($order_state == $mirakle_status && $create_new_order) {
                $this->saveNewMagentoOrderFromMirakle($new_order_id, $final_status, $shipping_price, $mirakl_order_id);
            } else if ($order_state == $mirakle_status && !$create_new_order) {
                $this->updateExistingMagentoOrderFromMirakle($new_order_id, $final_status, $mirakl_order_id);
            }
        }
    }

    public function createMagentoOrder($morders, $customer_email, $BilladdressData, $ShipaddressData)
    {
        $firstname = $morders['customer']['firstname'];
        $lastname = $morders['customer']['lastname'];

        $cart = Mage::getModel('sales/quote')->setStoreId(Mage::app()->getStore()->getId());
        $cart->setCustomerEmail($customer_email);
        $cart->setCustomerFirstname($firstname);
        $cart->setCustomerLastname($lastname);
        $cart->setData("is_mirakl_order", 1);


        $order_lines = $morders['order_lines'];

        foreach ($order_lines as $mproducts) {
            $product = Mage::getModel('catalog/product');
            $qty = $mproducts['quantity'];
            $prod_sku = $mproducts['offer_sku'];
            $price = $mproducts['price_unit'];
            $id = Mage::getModel('catalog/product')->getResource()->getIdBySku($prod_sku);
            if ($id) {
                $product->load($id);
            }
            $inStock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product)->getIsInStock();
            if ($inStock) {
                $buyInfo = array(
                    'qty' => $qty
                );
                $cart->addProduct($product, new Varien_Object($buyInfo));
                $quoteItem = $cart->getItemByProduct($product);
                $quoteItem->setCustomPrice($price);
                $quoteItem->setOriginalCustomPrice($price);
                $quoteItem->getProduct()->setIsSuperMode(true);
            }
        }
        $cart->getBillingAddress()->addData($BilladdressData);
        $shippingAddress = $cart->getShippingAddress()->addData($ShipaddressData);
        $shippingAddress->setCollectShippingRates(true)->collectShippingRates()->setShippingMethod(Mage::getStoreConfig('marketplace/acceptance_settings/flatrate_flaterate'))->setPaymentMethod('checkmo');
        $cart->getPayment()->importData(array(
            'method' => 'checkmo'
        ));

        $cart->collectTotals()->save();
        $service = Mage::getModel('sales/service_quote', $cart);
        $service->submitAll();
        $order = $service->getOrder();

        Mage::dispatchEvent('create_invoice_for_mirakl_order', array('order' => $order));

        // Add visual indication of express order
        if ($morders['shipping_type_code'] == self::STYLE_EXPRESS_OPTION) {
            $order->setData('shipping_description', self::STYLE_EXPRESS_DESCRIPTION);
            $order->save();
        }

        if ($order) {
            $new_order_id = $order->getIncrementId();
        } else {
            $new_order_id = Mage::getSingleton('checkout/session')->getLastRealOrderId();
        }
        Mage::helper('marketplace/logger')->log('createMagentoOrder - new_order_id: ', $new_order_id);
        return $new_order_id;
    }

    public function getValidatedCustomerDetails($mcustomer)
    {
        $valid_cust['title'] = isset($mcustomer['civility']) ? $mcustomer['civility'] : '';
        $valid_cust['first_name'] = isset($mcustomer['firstname']) ? $mcustomer['firstname'] : 'CN First name';
        $valid_cust['last_name'] = isset($mcustomer['lastname']) ? $mcustomer['lastname'] : 'CN Last name';
        $valid_cust['email'] = isset($mcustomer['email']) ? $mcustomer['email'] : '';
        $billing_address = $mcustomer['billing_address'];
        $shipping_address = $mcustomer['shipping_address'];
        $valid_cust['btitle'] = isset($billing_address['civility']) ? $billing_address['civility'] : '';
        $valid_cust['bfname'] = isset($billing_address['firstname']) ? $billing_address['firstname'] : $valid_cust['first_name'];
        $valid_cust['blname'] = isset($billing_address['lastname']) ? $billing_address['lastname'] : $valid_cust['last_name'];
        $valid_cust['bstreet1'] = isset($billing_address['street_1']) ? $billing_address['street_1'] : '';
        $valid_cust['bstreet2'] = isset($billing_address['street_2']) ? $billing_address['street_2'] : '';
        $valid_cust['bcity'] = isset($billing_address['city']) ? $billing_address['city'] : 'CN city';
        $valid_cust['bzip'] = isset($billing_address['zip_code']) ? $billing_address['zip_code'] : 'CN zip_code';
        $valid_cust['bcountry'] = isset($billing_address['country']) ? $billing_address['country'] : '';
        $valid_cust['bphone'] = isset($billing_address['phone']) ? $billing_address['phone'] : '000';
        $valid_cust['bstate'] = isset($billing_address['state']) ? $billing_address['state'] : 'CN State';
        $valid_cust['stitle'] = isset($shipping_address['civility']) ? $shipping_address['civility'] : '';
        $valid_cust['sfname'] = isset($shipping_address['firstname']) ? $shipping_address['firstname'] : $valid_cust['first_name'];
        $valid_cust['slname'] = isset($shipping_address['lastname']) ? $shipping_address['lastname'] : $valid_cust['last_name'];
        $valid_cust['sstreet1'] = isset($shipping_address['street_1']) ? $shipping_address['street_1'] : 'CN street_1';
        $valid_cust['sstreet2'] = isset($shipping_address['street_2']) ? $shipping_address['street_2'] : '';
        $valid_cust['scity'] = isset($shipping_address['city']) ? $shipping_address['city'] : 'CN city';
        $valid_cust['szip'] = isset($shipping_address['zip_code']) ? $shipping_address['zip_code'] : 'CN zip_code';
        $valid_cust['scountry'] = isset($shipping_address['country']) ? $shipping_address['country'] : '';
        $valid_cust['sphone'] = isset($shipping_address['phone']) ? $shipping_address['phone'] : '000';
        $valid_cust['sstate'] = isset($shipping_address['state']) ? $shipping_address['state'] : 'CN State';
        return $valid_cust;
    }

    public function getProductStockExists($order_lines)
    {
        foreach ($order_lines as $mproducts) {
            $product = Mage::getModel('catalog/product');
            $qty = $mproducts['quantity'];
            $prod_sku = $mproducts['offer_sku'];
            $prod_store_sku[] = $prod_sku;
            $id = Mage::getModel('catalog/product')->getResource()->getIdBySku($prod_sku);
            if ($id) {
                $product->load($id);
            }
            $is_prod_in_stylecom[] = $product->getData('display_style_com');
            $is_there_a_product[] = $id;
            $inStock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product)->getIsInStock();
            $is_product_instock[] = $inStock;
        }
        return array(
            $is_there_a_product,
            $is_product_instock,
            $is_prod_in_stylecom,
            $prod_store_sku
        );
    }

    public function downloadJson()
    {
        $order = Mage::app()->getRequest()->getPost();
        if ($order['order_idd_down_bulk'] && isset($order['order_idd_down_bulk'])) {
            header('Content-disposition: attachment; filename=Orders.json');
            header('Content-type: application/json');
            $order_idd_down_bulk = json_decode($order['order_idd_down_bulk']);
            foreach ($order_idd_down_bulk as $orderID) {
                $orderID;
                $order_details = $this->helper->getSingleOrder($orderID, '');
                echo $order_details['all_fields'];
            }
        } else {
            $order_details = $this->helper->getSingleOrder($order['order_idd_down'], '');
            header('Content-disposition: attachment; filename=' . $order['order_idd_down'] . '.json');
            header('Content-type: application/json');
            echo $order_details['all_fields'];
        }
        exit;
    }

    public function changeOrderReadStatus()
    {
        $order = Mage::app()->getRequest()->getPost();
        if ($order['order_unread_bulk'] && isset($order['order_unread_bulk'])) {
            $update_data = array(
                'order_read' => '0',
                'message_read' => '0'
            );
            $order_unread_bulk = json_decode($order['order_unread_bulk']);
            foreach ($order_unread_bulk as $orderID) {
                $order_details = $this->helper->getSingleOrder($orderID, '');
                $model = Mage::getModel('marketplace/ordertable')->load($order_details['id'])->addData($update_data);
                try {
                    $model->setId($order_details['id'])->save();
                } catch (Exception $e) {
                    echo $e->getMessage();
                }
            }
        }
    }

    public function orderLinesGetInfo()
    {
        $order = Mage::app()->getRequest()->getPost();
        $all = Mage::getModel('marketplace/ordertable')->getCollection()->searchOrder($order['orderId_lines'], 1, '')->getData();
        $orderlines_decode = json_decode($all[0]['all_fields']);
        $orderlines_all = $orderlines_decode->order_lines;
        $orderlines_sort = array();
        foreach ($orderlines_all as $items_list) {
            $orderline_sort[] = $items_list->order_line_id;
        }
        array_multisort($orderline_sort, SORT_ASC, $orderlines_all);
        $orderlines_decode->order_lines = $orderlines_all;
        $order_line_encode = json_encode($orderlines_decode);
        echo $order_line_encode;
    }

    public function orderLinesPostData()
    {
        $order = Mage::app()->getRequest()->getPost();
        $this->helper->setMirakleOrders('confirm_multiple', $order['order_id_lines'], $order['order_line']);
    }

    public function changeIncidentReadStatus()
    {
        $order = Mage::app()->getRequest()->getPost();
        if ($order['incident_orderId']) {
            $update_data = array(
                'incident_read' => '0',
                'order_read' => '0'
            );
            $incident_read_orderId = $order['incident_orderId'];
            $order_details = $this->helper->getSingleOrder($incident_read_orderId, '');
            $model = Mage::getModel('marketplace/ordertable')->load($order_details['id'])->addData($update_data);
            try {
                $model->setId($order_details['id'])->save();
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        }
    }
}
