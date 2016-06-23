<?php
class As_Customfee_Block_Sales_Order_Customfee extends Mage_Core_Block_Template
{

    /**
     * Get order store object
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        return $this->getParentBlock()->getOrder();
    }

    /**
     * Get totals source object
     *
     * @return Mage_Sales_Model_Order
     */
    public function getSource()
    {
        return $this->getParentBlock()->getSource();
    }

    /**
     * Initialize fee totals
     *
     */
    public function initTotals()
    {
        if ((float) $this->getOrder()->getBaseCustomfeeAmount()) {
            $source = $this->getSource();
            $value  = $source->getCustomfeeAmount();

            $this->getParentBlock()->addTotal(new Varien_Object(array(
                'code'   => 'customfee',
                'strong' => false,
                'label'  => Mage::helper('customfee')->__('Skin Tool Offer'),
                'value'  => $value
            )));
        }

        return $this;
    }
}