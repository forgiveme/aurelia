<?php

/**
 * Created by Daniel Rafique.
 * For: Style.com
 * Date: 25/08/2016
 * Time: 16:23
 * Copyright all rights reserved to author of this content.
 */
class Cnc_Marketplace_Model_Adminhtml_System_Config_Source_Category_Options
{

    public function toOptionArray()
    {
        $rootCatId = Mage::app()->getStore()->getRootCategoryId();
        $options = array();
        $options[] = array(
            'label' => '-- ' . Mage::helper('adminhtml')->__('None') . ' --',
            'value' => ''
        );
        $this->getTreeCategories($rootCatId, $options);
        return $options;
    }

    public function getTreeCategories($parentId, &$option)
    {
        $allCats = Mage::getModel('catalog/category')->getCollection()
            ->addAttributeToSelect(array('name', 'level'))
            ->addAttributeToFilter('parent_id', array('eq' => $parentId))
            ->addAttributeToSort('position', 'asc');

        foreach ($allCats as $category) {
            $prefix = '';
            $sub = $category->getChildren();

            for ($index = 0; $index < $category->getLevel() - 2; $index++) {
                $prefix .= '. . . . . ';
            }

            if ($category->getLevel() == 2 && count($sub) > 0) {
                $prefix .= '(+) ';
            }

            if ($category->getLevel() > 1) {
                $option[] = array('value' => $category->getId(), 'label' => $prefix . $category->getName());
            }

            if ($sub) {
                $this->getTreeCategories($category->getId(), $option);
            }
        }
    }

    public function getAllOptions()
    {
        return $this->toOptionArray();
    }

}
