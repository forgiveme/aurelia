<?php

class Stardigital_Productsalesreport_Block_Adminhtml_Productsalesreport extends Mage_Adminhtml_Block_Template
{
    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    public function getReportCollection()
    {
        return Mage::getModel('productsalesreport/productsalesreport')->getReportCollection();
    }

    public function getTotals($collection)
    {
        return Mage::getModel('productsalesreport/productsalesreport')->getTotals($collection);
    }

    public function getGridHeader()
    {
        return "Product Sales Report";
    }

    public function getGridId()
    {
        return "gridProductReport";
    }

    public function getGridUrl()
    {
        return Mage::getUrl('productsalesreport/adminhtml_productsalesreport/index');
    }

    public function getCsvUrl()
    {
        return Mage::getUrl('productsalesreport/adminhtml_productsalesreport/exportcsv/');
    }

    public function getFilter($var)
    {
        return Mage::app()->getRequest()->getParam($var, '');
    }
}
