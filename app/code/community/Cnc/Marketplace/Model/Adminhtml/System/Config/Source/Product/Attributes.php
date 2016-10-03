<?php
/**
 * Created by Daniel Rafique.
 * For: Style.com
 * Date: 31/08/2016
 * Time: 15:21
 * Copyright all rights reserved to author of this content.
 */
 class Cnc_Marketplace_Model_Adminhtml_System_Config_Source_Product_Attributes
 {
     public function toOptionArray()
     {

         $attributes = Mage::getModel('catalog/product')->getAttributes();
         $attributeArray = array();

         foreach($attributes as $attribute){

             foreach ($attribute->getEntityType()->getAttributeCodes() as $attributeName) {

                 $attributeArray[] = array(
                     'label' => $attributeName,
                     'value' => $attributeName
                 );
             }
             break;
         }
         return $attributeArray;
     }
 }
