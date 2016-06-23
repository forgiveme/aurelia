<?php

class Stardigital_Productsalesreport_Model_Productsalesreport extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('productsalesreport/productsalesreport');
    }

    public function getFilter($var)
    {
        return Mage::app()->getRequest()->getParam($var, '');
    }

    public function getReportCollection()
    {
        $from = $this->getFilter('report_from');
        $sku = $this->getFilter('product_report_sku');
        $to = $this->getFilter('report_to');

        $todaysDate = date('Y-m-d');

        if ( ! is_null($from) &&  ! is_null($to) ) {
            $reportFrom = $from . ' 00:00:00';
            $reportTo = $to . ' 23:59:59';
        }

        $productId = Mage::getModel('catalog/product')->getIdBySku($sku);

        $product = Mage::getModel('catalog/product')->load($productId);

        $orderIds = array();
        $reportCollection = array();

        if ($product->getId()) {
            $orderItems = Mage::getModel('sales/order_item')
                ->getCollection()
                ->addFieldToFilter('product_id', array(
                    'eq' => $productId,
                ));

            foreach ($orderItems AS $orderItem) {
                $order = Mage::getModel('sales/order')
                    ->getCollection()
                    ->addFieldToFilter('entity_id', array(
                        'eq' => $orderItem->getOrderId(),
                    ))
                    ->addFieldToFilter('status', array(
                        'eq' => 'complete'
                    ));

                if (is_null($from) || is_null($to)) {
                    $order->addFieldToFilter('created_at', array(
                        'eq' => $todaysDate,
                    ));
                }
                else {
                    $order->addFieldToFilter('created_at', array(
                        'from' => $reportFrom,
                    ));
                    $order->addFieldToFilter('created_at', array(
                        'to' => $reportTo,
                    ));
                }

                $_order = $order->getFirstItem();

                if ($_order->getId()) {
                    $createdAt = explode(" ", $_order->getCreatedAt());

                    $currencySymbol = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();

                    $temp = array();
                    $temp['sku'] = $sku;
                    $temp['increment_id'] = $_order->getIncrementId();
                    $temp['payment_method'] = $_order->getPayment()->getMethodInstance()->getTitle();
                    $temp['date'] = date("d-m-Y", strtotime($createdAt[0]));
                    $temp['qty'] = number_format($orderItem->getQtyOrdered(), 0);
                    $temp['shippingCountry'] = $_order->getShippingAddress()->getCountryId();
                    $temp['row_total'] = $currencySymbol . ' ' . number_format($orderItem->getRowTotal(), 2);
                    $temp['tax'] = $currencySymbol . ' ' . number_format($orderItem->getTaxAmount(), 2);
                    $temp['row_total_incl_tax'] = $currencySymbol . ' ' . number_format($orderItem->getRowTotalInclTax(), 2);

                    $reportCollection[] = $temp;
                }
            }

            return $reportCollection;
        }
    }

    public function getTotals($collection)
    {
        $return = null;

        $currencySymbol = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();

        $qty_total = 0;
        $sales_total = 0;
        $sales_incl_vat_total = 0;

        foreach ($collection AS $record) {

            $rowTotal = explode(' ', $record['row_total']);
            $rotTotalInclVat = explode(' ', $record['row_total_incl_tax']);

            $qty_total = $qty_total + $record['qty'];
            $sales_total = $sales_total + $rowTotal[1];
            $sales_incl_vat_total = $sales_incl_vat_total + $rotTotalInclVat[1];
        }

        $qtyTotal = number_format($qty_total, 0);
        $salesTotal = number_format($sales_total, 2);
        $salesInclVatTotal = number_format($sales_incl_vat_total, 2);

        $temp = array();

        $temp['sku'] = 'Total';
        $temp['increment_id'] = '';
        $temp['payment_method'] = '';
        $temp['date'] = '';
        $temp['qty'] = $qty_total;
        $temp['shippingCountry'] = '';
        $temp['row_total'] = $currencySymbol . ' ' . $salesTotal;
        $temp['tax'] = '';
        $temp['row_total_incl_tax'] = $currencySymbol . ' ' . $salesInclVatTotal;

        $return = $temp;

        return $return;
    }
}
