<?php

class Cnc_Marketplace_Adminhtml_Marketplace_ProductsupdateController extends Mage_Adminhtml_Controller_Action
{
    public function massAddStyleAction()
    {
        $productIds = $this->getRequest()->getParam('product');
        $count = count($productIds);
        $product = Mage::getModel('catalog/product');

        if ($count > 0) {
            foreach ($productIds as $productId) {
                $_product = $product->load($productId);
                $product->load($_product->getEntityId());
                $product->setData('display_style_com', 1)->getResource()->saveAttribute($product, 'display_style_com');
            }
            Mage::getSingleton('core/session')->addSuccess('Succesfully updated products to style.com attribute');
            session_write_close();
        }
        $this->_redirect('adminhtml/catalog_product');
    }

    public function massRemoveStyleAction()
    {
        $productIds = $this->getRequest()->getParam('product');
        $count = count($productIds);
        $product = Mage::getModel('catalog/product');
        if ($count > 0) {
            foreach ($productIds as $productId) {
                $_product = $product->load($productId);
                $product->load($_product->getEntityId());
                $product->setData('display_style_com', 0)->getResource()->saveAttribute($product, 'display_style_com');
            }
            Mage::getSingleton('core/session')->addSuccess('Succesfully removed products from style.com attribute');
            session_write_close();
        }
        $this->_redirect('adminhtml/catalog_product');
    }

    /**
     * @throws Zend_Controller_Response_Exception
     */
    public function exportAction()
    {
        $has_products = Mage::helper('marketplace')->createProductsCSV();
        if ($has_products) {
            $filepath = Mage::helper('marketplace/util')->getProductExportFilename();
            $this->getResponse()
                ->setHttpResponseCode(200)
                ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
                ->setHeader('Pragma', 'public', true)
                ->setHeader('Content-type', 'text/csv')
                ->setHeader('Content-Length', filesize($filepath))
                ->setHeader('Content-disposition', 'attachment' . '; filename=' . basename($filepath));
            $this->getResponse()->clearBody();
            $this->getResponse()->sendHeaders();
            readfile($filepath);
            exit();
        } else {
            Mage::getSingleton('core/session')->addError("Please add products to style.com first");
            session_write_close();
            $this->_redirect('adminhtml/catalog_product');
        }
    }

    /**
     * @throws Zend_Controller_Response_Exception
     */
    public function exportSelectionAction()
    {
        $request = Mage::app()->getRequest();
        $has_products = Mage::helper('marketplace')->createCSVFromProductSelection($request->getParam('product'));
        if ($has_products) {
            $filepath = Mage::helper('marketplace/util')->getProductExportFilename();
            $this->getResponse()
                ->setHttpResponseCode(200)
                ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
                ->setHeader('Pragma', 'public', true)
                ->setHeader('Content-type', 'text/csv')
                ->setHeader('Content-Length', filesize($filepath))
                ->setHeader('Content-disposition', 'attachment' . '; filename=' . basename($filepath));
            $this->getResponse()->clearBody();
            $this->getResponse()->sendHeaders();
            readfile($filepath);
            Mage::getSingleton('core/session')->addSuccess("Selected products added to CSV");
            exit();
        } else {
            Mage::getSingleton('core/session')->addError("Please select product first");
            session_write_close();
            $this->_redirect('adminhtml/catalog_product');
        }
    }
}
