<?php
/**
 * Created by PhpStorm.
 * User: drafique
 * Date: 18/07/2016
 * Time: 10:14
 */

class Cnc_Marketplace_Model_Adminhtml_System_Config_Source_Product_Options {

    public function toOptionArray() {

        return array(
            '' => 'Never',
            '* 0 * * * ' => '12 midnight Everyday',
            '* */12 * * *' => 'Every 12 hours'
        );
    }
}
