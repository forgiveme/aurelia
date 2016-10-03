<?php
/**
 * Created by PhpStorm.
 * User: drafique
 * Date: 18/07/2016
 * Time: 10:14
 */

class Cnc_Marketplace_Model_Adminhtml_System_Config_Source_Offer_Options {


    public function toOptionArray()
    {
        return array(
            '' => 'Never',
            '*/1 * * * *'  =>  'Every 1 minute',
            '*/5 * * * *'  =>  'Every 5 minutes',
            '*/10 * * * *' => 'Every 10 minutes',
            '*/30 * * * *' => 'Every 30 minutes',
            '*/60 * * * *' => 'Every 1 hour'
        );
    }
}
