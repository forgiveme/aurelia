<?php
/**
 * Created by Daniel Rafique.
 * For: Style.com
 * Date: 17/08/2016
 * Time: 13:44
 * Copyright all rights reserved to author of this content.
 */
 class Cnc_Marketplace_Block_Adminhtml_Sales_Order_Grid extends Mage_Adminhtml_Block_Sales_Order_Grid
 {
     protected function _prepareColumns()
     {
         parent::_prepareColumns();

         $this->addColumnAfter('is_mirakl_order', array(
             'header' => Mage::helper('marketplace')->__('Style.com Order'),
             'index' => 'is_mirakl_order',
             'type'  => 'options',
             'width' => '20px',
             'renderer' => 'marketplace/adminhtml_grid_column_renderer_order_status',
             'options' => array('1'=>'Yes', '0'=>'No'),
             'separator' => '<br/>'
         ),'status');

         $this->sortColumnsByOrder();
         return $this;
     }
 }
