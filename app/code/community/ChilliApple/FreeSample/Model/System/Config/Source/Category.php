<?php 
/**
* 
*/
class ChilliApple_FreeSample_Model_System_Config_Source_Category extends Mage_Adminhtml_Model_System_Config_Source_Category
{
	
	public function toOptionArray($addEmpty = true)
    {
        $tree = Mage::getResourceModel('catalog/category_tree');

        $collection = Mage::getResourceModel('catalog/category_collection');

        $collection->addAttributeToSelect('name')
            //->addRootLevelFilter()
        	->addAttributeToFilter('level',array('eq' => 2 ))
            ->load();

        $options = array();

        if ($addEmpty) {
            $options[] = array(
                'label' => Mage::helper('adminhtml')->__('-- Please Select a Category --'),
                'value' => ''
            );
        }
        foreach ($collection as $category) {
            $options[] = array(
               'label' => $category->getName(),
               'value' => $category->getId()
            );
        }

        return $options;
    }
}