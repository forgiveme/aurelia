<?php

class Stardigital_Productsalesreport_Adminhtml_ProductsalesreportController extends Mage_Adminhtml_Controller_action
{
    protected function _initAction()
    {
        $this->loadLayout()->_setActiveMenu('productsalesreport/items')->_addBreadcrumb(
            Mage::helper('adminhtml')->__('Items Manager'),
            Mage::helper('adminhtml')->__('Item Manager')
        );

        return $this;
    }

    public function indexAction()
    {
        $this->_initAction()->renderLayout();
    }

    public function exportCsvAction()
    {
        $header = array(
            'SKU',
            'Order #',
            'Payment Method',
            'Date of Order',
            'Quantity (within that order)',
            'Country of shipping',
            'Total value of item (excl VAT)',
            'VAT',
            'Total (incl VAT)'
        );

        $model = Mage::getModel('productsalesreport/productsalesreport');
        $collection = $model->getReportCollection();
        $totals = $model->getTotals($collection);

        $content = array();

        foreach($collection AS $record) {
            $content[] = $record;
        }

        $content[] = $totals;

        $csv = $this->_generateCsv($header, $content);
        $filename = 'productsalesreport.csv';
        $this->_sendUploadResponse($filename, $csv);
    }

    public function exportXmlAction()
    {
        $fileName   = 'productsalesreport.xml';
        $content    = $this->getLayout()->createBlock('productsalesreport/adminhtml_productsalesreport_grid')
            ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }

    protected function _generateCsv($header, $content)
    {
        $return = null;

        $fp = fopen('php://memory', 'w+');

        fputcsv($fp, $header);

        foreach($content AS $row) {
            fputcsv($fp, $row);
        }

        rewind($fp);
        $return = stream_get_contents($fp);
        fclose($fp);

        return $return;
    }
}
