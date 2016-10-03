<?php

class Cnc_Marketplace_Adminhtml_Marketplace_OrdersController extends Mage_Adminhtml_Controller_Action
{
    public function _initAction()
    {
        Mage::getSingleton('core/session')->getMessages(true);
    }

    public function indexAction()
    {
        $helper = Mage::helper('marketplace');
        $check = $helper->configCheckerAll();
        if (!$check)
            $this->_redirect('adminhtml/system_config');
        $block = $this->getLayout()->getBlockSingleton('marketplace/adminhtml_orders');
        Mage::getSingleton('core/session')->setMiraklStoreId(Mage::app()->getRequest()->getParam('store'));
        $this->loadLayout()->_setActiveMenu('marketplace');
        $block->getOrderDetails();
        $this->_title($this->__("Style.com/Orders"));
        $this->renderLayout();
    }

    public function ordersubmitAction()
    {
        $helper = Mage::helper('marketplace');
        $put_data = $helper->setMirakleOrders('', '', '');
        echo $put_data;
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('marketplace/orders');
    }

    public function downloadJsonAction()
    {
        $block = $this->getLayout()->getBlockSingleton('marketplace/adminhtml_orders');
        $block->downloadJson();
    }

    public function changeUnreadOrderStatusAction()
    {
        $block = $this->getLayout()->getBlockSingleton('marketplace/adminhtml_orders');
        $block->changeOrderReadStatus();
        $this->_redirectReferer();
    }

    public function GetOrderMessagesAction()
    {
        $post = Mage::app()->getRequest()->getPost();
        $helper = Mage::helper('marketplace');
        $helper->getInduvidualOrderMessage($post['orderId'], 'orders');
    }

    public function OrderMessageAnswerAction()
    {
        $block = $this->getLayout()->getBlockSingleton('marketplace/adminhtml_orders');
        echo $block->sendNewOrderMessage();
    }

    public function changeUnreadIncidentStatusAction()
    {
        $block = $this->getLayout()->getBlockSingleton('marketplace/adminhtml_orders');
        $block->changeIncidentReadStatus();
    }

    public function orderLinesGetAction()
    {
        $block = $this->getLayout()->getBlockSingleton('marketplace/adminhtml_orders');
        $block->orderLinesGetInfo();
    }

    public function orderLinesPostAction()
    {
        $block = $this->getLayout()->getBlockSingleton('marketplace/adminhtml_orders');
        $block->orderLinesPostData();
        $this->_redirectReferer();
    }
}
