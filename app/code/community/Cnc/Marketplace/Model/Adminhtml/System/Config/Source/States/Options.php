<?php
/**
 * Created by Daniel Rafique.
 * For: Style.com
 * Date: 01/08/2016
 * Time: 16:02
 * Copyright all rights reserved to author of this content.
 */

class Cnc_Marketplace_Model_Adminhtml_System_Config_Source_States_Options

{

    /**
     * Retrieve Status options as array
     *
     * @return array
     */
    // set null to enable all possible
    protected $_stateStatuses = null;

    public function toOptionArray()
    {
        if ($this->_stateStatuses) {
            $statuses = Mage::getSingleton('sales/order_config')->getStateStatuses($this->_stateStatuses);
        }
        else {
            $statuses = Mage::getSingleton('sales/order_config')->getStatuses();
        }
        $options = array();
        $options[] = array(
            'value' => '',
            'label' => Mage::helper('adminhtml')->__('-- Please Select --')
        );
        foreach ($statuses as $code=>$label) {
            $options[] = array(
                'value' => $code,
                'label' => $label
            );
        }
        return $options;
    }
}
