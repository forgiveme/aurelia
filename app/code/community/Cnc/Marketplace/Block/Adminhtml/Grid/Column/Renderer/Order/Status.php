<?php
/**
 * Created by Daniel Rafique.
 * For: Style.com
 * Date: 17/08/2016
 * Time: 13:50
 * Copyright all rights reserved to author of this content.
 */

class Cnc_Marketplace_Block_Adminhtml_Grid_Column_Renderer_Order_Status extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $dataArr = [];
        $isStyleComOrder = $row->getData($this->getColumn()->getIndex());
        if ($isStyleComOrder) {
            $dataArr[] = Mage::helper('marketplace')->__('Yes');
        } else {
            $dataArr[] = Mage::helper('marketplace')->__('No');
        }
        $data = join('<br/>', $dataArr);
        return $data;
    }
}
