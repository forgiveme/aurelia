<?php

class Stardigital_Ordercountry_Block_Adminhtml_Order_Grid extends Mage_Adminhtml_Block_Sales_Order_Grid
{
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel($this->_getCollectionClass());
        $collection->join(
            array(
                'billing_address' => 'sales/order_address'
            ),
            'main_table.entity_id=billing_address.parent_id AND billing_address.address_type="billing"',
            'billing_address.country_id AS billing_country_id'
        );

        $collection->getSelect()->joinLeft(
            array(
                'shipping_address' => Mage::getSingleton('core/resource')->getTableName('sales/order_address'),
            ),
            'main_table.entity_id=shipping_address.parent_id AND shipping_address.address_type="shipping"',
            'shipping_address.country_id AS shipping_country_id'
        );

        $this->setCollection($collection);
        return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $res = parent::_prepareColumns();

        $baseGrandTotal = $this->_columns['base_grand_total'];
        $grandTotal = $this->_columns['grand_total'];
        $status = $this->_columns['status'];
        $action = $this->_columns['action'];

        $oldColumns = $this->_columns;
        $nextColumns = array();
        $unsetNextColumns = false;

        foreach ($oldColumns AS $key => $value) {
            if ($key == "shipping_name") {
                $unsetNextColumns = true;
                continue;
            }

            if ($unsetNextColumns) {
                $nextColumns[$key] = $value;
                unset($this->_columns[$key]);
            }
        }

        $this->addColumn(
            'billing_country_id',
            array(
                'header' => Mage::helper('sales')->__('Billing Country'),
                'index' => 'billing_country_id',
                'filter_index' => 'billing_address.country_id',
                'type' => 'country',
                'width' => '100px',
            )
        );

        $this->addColumn(
            'shipping_country_id',
            array(
                'header' => Mage::helper('sales')->__('Shipping Country'),
                'index' => 'shipping_country_id',
                'filter_index' => 'shipping_address.country_id',
                'type' => 'country',
                'width' => '100px',
            )
        );

        foreach ($nextColumns AS $key => $value) {
            $this->_columns[$key] = $value;
        }

        return $res;
    }
}
