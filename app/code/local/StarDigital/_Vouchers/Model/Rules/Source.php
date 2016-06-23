<?php

class Stardigital_Vouchers_Model_Rules_Source
{
    public function toOptionArray()
    {
        $options = array();
        $options[] = array(
            'value' => '',
            'label' => '-- Please Select --',
        );

        $rules = Mage::getModel('salesrule/rule')
            ->getCollection()
            ->addFieldToFilter(
                'is_active',
                array(
                    'eq' => 1
                )
            );

        foreach ($rules AS $rule) {
            $options[] = array(
                'value' => $rule->getId(),
                'label' => $rule->getName(),
            );
        }

        return $options;
    }
}
