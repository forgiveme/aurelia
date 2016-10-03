<?php

/**
 * Created by Daniel Rafique.
 * Date: 15/07/2016
 * Time: 16:59
 */
class Cnc_Marketplace_Model_Observer
{

    /**
     * @param Varien_Event_Observer $observer
     * @throws Mage_Core_Exception
     */
    public function adminhtmlBlockHtmlBefore(Varien_Event_Observer $observer)
    {

        try {
            $event = $observer->getEvent();
            if ($event->getBlock() instanceof Mage_Adminhtml_Block_Catalog_Product_Grid) {
                $this->_grid = $event->getBlock();
                $this->_collection = $this->_grid->getCollection();
                $this->_collection->addAttributeToSelect('display_style_com');

                $event->getBlock()->addColumnAfter('display_style_com', array(
                    'header' => Mage::helper('marketplace')->__('Visible in style.com'),
                    'width' => '20px',
                    'index' => 'display_style_com',
                    'type' => 'options',
                    'options' => array(
                        '1' => 'Yes',
                        '0' => 'No'
                    )
                ), 'entity_id');
                $this->_grid->addColumnsOrder('display_style_com', 'entity_id')->sortColumnsByOrder();

                // rebuild the filters
                $filter = $this->_grid->getParam($this->_grid->getVarNameFilter());
                if (is_null($filter)) {

                    $this->_setFilterValues('');

                }
                $this->_collection->clear();

                if (is_string($filter)) {
                    $data = $this->_grid->helper('adminhtml')->prepareFilterString($filter);
                    $this->_setFilterValues($data);
                } else {
                    if ($filter && is_array($filter)) {
                        $this->_setFilterValues($filter);
                    }
                }

                $this->_grid->getCollection()->addWebsiteNamesToResult();

                // force a reload of the collection
                $this->_collection->load();
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }

        return $this;
    }

    protected function _setFilterValues($data)
    {
        foreach ($this->_grid->getColumns() as $columnId => $column) {
            if (isset($data[$columnId]) && (!empty($data[$columnId]) || strlen($data[$columnId]) > 0)
                && $column->getFilter()
            ) {
                $column->getFilter()->setValue($data[$columnId]);
                $this->_addColumnFilterToCollection($column);
            }
        }

        return $this;
    }

    protected function _addColumnFilterToCollection($column)
    {
        if ($this->_collection) {
            $field = ($column->getFilterIndex()) ? $column->getFilterIndex() : $column->getIndex();
            if ($column->getFilterConditionCallback()) {
                call_user_func($column->getFilterConditionCallback(), $column);
            } else {
                $cond = $column->getFilter()->getCondition();
                if ($field && isset($cond)) {
                    $this->_collection->addFieldToFilter($field, $cond);
                }
            }
        }

        return $this;
    }

    /**
     * @param $observer
     */
    public function addButtonDownload($observer)
    {
        $block = $observer->getEvent()->getBlock();

        if ($block instanceof Mage_Adminhtml_Block_Catalog_Product) {
            if ($this->_isModuleActive()) {
                $currentStoreId = Mage::app()->getRequest()->getParam('store');
                $block->addButton('download', array(
                    'label' => Mage::helper('catalog')->__('Download Mirakl Product CSV'),
                    'onclick' => 'setLocation(\'' . $block->getUrl('adminhtml/marketplace_productsupdate/export', array('store' => empty($currentStoreId) ? Mage::app()->getStore()->getStoreId() : $currentStoreId)) . '\')',
                    'class' => 'go'
                ));
            }
        }
    }

    /**
     * @param Varien_Event_Observer $observer
     * @throws Mage_Core_Exception
     */
    public function addMassAction(Varien_Event_Observer $observer)
    {

        $block = $observer->getEvent()->getBlock();

        if ($block instanceof Mage_Adminhtml_Block_Catalog_Product_Grid) {
            if ($this->_isModuleActive()) {
                // Add Mass action
                $block->setMassactionIdField('entity_id');
                $block->getMassactionBlock()->setFormFieldName('product');
                $block->getMassactionBlock()->addItem('addtostyle', array(
                    'label' => Mage::helper('catalog')->__('Add Products to style.com'),
                    'url' => $block->getUrl('adminhtml/marketplace_productsupdate/massAddStyle')
                ));

                $block->getMassactionBlock()->addItem('removetostyle', array(
                    'label' => Mage::helper('catalog')->__('Remove Products from style.com'),
                    'url' => $block->getUrl('adminhtml/marketplace_productsupdate/massRemoveStyle')
                ));

                $block->getMassactionBlock()->addItem('exportCsv', array(
                    'label' => Mage::helper('catalog')->__('Export selection to MIRAKL CSV (style.com)'),
                    'url' => $block->getUrl('adminhtml/marketplace_productsupdate/exportSelection', array('store' => empty($currentStoreId) ? Mage::app()->getStore()->getStoreId() : $currentStoreId))
                ));
            }
        }

    }

    public function _isModuleActive()
    {
        $config = Mage::getStoreConfigFlag('marketplace/configuration/active');
        return $config;
    }

    public function createInvoiceForMiraklOrder(Varien_Event_Observer $observer)
    {
        /** @var $order Mage_Sales_Model_Order */
        $order = $observer->getEvent()->getOrder();
        if ($order->canInvoice()) {
            try {
                $invoice = $order->prepareInvoice();
                $invoice->setRequestedCaptureCase(Mage_Sales_Model_Order_Invoice::NOT_CAPTURE);
                $invoice->register()->pay();
                Mage::getModel('core/resource_transaction')
                    ->addObject($invoice)
                    ->addObject($invoice->getOrder())
                    ->save();
                $order->setState(
                    Mage_Sales_Model_Order::STATE_PROCESSING,
                    true,
                    'Invoice ' . $invoice->getIncrementId() . ' created automatically'
                );
            } catch (Mage_Core_Exception $e) {
                Mage::logException($e);
                $order->addStatusHistoryComment('Error whilst creating an invoice automatically');
            }
        } else {
            Mage::log('Style.com order ' . $order->getIncrementId() . ' cannot create invoice');
            $order->addStatusHistoryComment('Cannot create invoice automatically');
        }
        $order->save();
    }
}
