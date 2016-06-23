<?php

class Star_DpkProdIntegration_Model_System_Config_Source_Dropdown_DaysOfWeek
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => '1',
                'label' => 'Monday',
            ),
            array(
                'value' => '2',
                'label' => 'Tuesday',
            ),
			array(
                'value' => '3',
                'label' => 'Wednesday',
            ),
			array(
                'value' => '4',
                'label' => 'Thursday',
            ),
			array(
                'value' => '5',
                'label' => 'Friday',
            ),
			array(
                'value' => '6',
                'label' => 'Saturday',
            ),
			array(
                'value' => '7',
                'label' => 'Sunday',
            ),
        );
    }
}