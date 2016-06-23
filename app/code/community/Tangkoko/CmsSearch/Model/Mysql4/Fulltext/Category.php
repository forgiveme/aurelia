<?php
/**
 * 
 * Tangkoko Cms Search Extension
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@tangkoko.com  and you will be sent
 * a copy immediately.
 * 
 * @category Tangkoko
 * @package  CmsSearch
 * @author Nicolas RENAULT
 * @copyright  Copyright (c) 2012 Tangkoko sarl (http://www.tangkoko.com)
 **/
class Tangkoko_CmsSearch_Model_Mysql4_Fulltext_Category extends Tangkoko_CmsSearch_Model_Mysql4_Fulltext_Abstract
{

	public function _construct()
    {
    	$this->_key = "category_id";
    	$this->_prefix  = "categorysearch";
    	$this->_FieldNameProcessed = "is_categoryprocessed";
    	$this->_tableResult = "cmssearch/result_category";
    	$this->_tableFulltext = "cmssearch/fulltext_category";
        $this->_init($this->_tableFulltext, $this->_key);
    }
    

    
     /**
     * Retrieve searchable categories
     *
     * @param int $storeId
     * @param array|int $pageIds
     * @param int $lastPageId
     * @param int $limit
     * @return array
     */
    protected function _getSearchableElements($storeId, $pageIds = null, $lastPageId = 0, $limit = 100)
    {
    	$helper = Mage::helper('cmssearch');
    	$result = array();
    	
    	//get attributes ID
    	$tab_attributes = $this->getAttributesIdByCode('catalog_category',array('name', 'description', 'meta_keywords', 'meta_description', 'is_active', 'url_key', 'meta_title', 'landing_page'));
    	
    	//name selection
    	$result = $this->getSelection('text', $tab_attributes['name'], 'name', $tab_attributes['is_active']);
    	//meta keywords selection
     	$result = $helper->array_extend($result,$this->getSelection('text', $tab_attributes['meta_keywords'], 'meta-keyword', $tab_attributes['is_active']));
    	//meta description selection
    	$result = $helper->array_extend($result,$this->getSelection('text', $tab_attributes['meta_description'], 'meta-description', $tab_attributes['is_active']));
    	//description selection
    	$result = $helper->array_extend($result,$this->getSelection('text', $tab_attributes['description'], 'description', $tab_attributes['is_active']));
    	//url selection
    	$result = $helper->array_extend($result,$this->getSelection('varchar', $tab_attributes['url_key'], 'url', $tab_attributes['is_active']));
    	//title selection
    	$result = $helper->array_extend($result,$this->getSelection('varchar', $tab_attributes['meta_title'], 'title', $tab_attributes['is_active']));
    	
    	//join static block
    	$result = $helper->array_extend($result,$this->getStaticBlock($storeId, $tab_attributes));
    	   	
    	return $result;
    }
    
    /**
     * Retrieve catalog_category attributes Id
     * 
     * @param int $entityTypeId
     * @param array $attributesToSelect
     * @return array
     */
    public function getAttributesIdByCode($entityTypeCode, $attributesToSelect)
    {
    	$tab_attributes = array();
    	
    	//loading attributes
    	$attributes = Mage::getModel('eav/mysql4_entity_attribute_collection')->setEntityTypeFilter(Mage::getModel('eav/entity_type')->loadByCode($entityTypeCode)); 

    	//record needed attributes in array
    	foreach($attributes as $_attribute)
    	{
    		foreach($attributesToSelect as $value)
    		{
				if($value==$_attribute->getAttributeCode())
				{
					$tab_attributes[$value] = $_attribute->getAttributeId();
				}    			
    		}
    	}
    	return $tab_attributes;
    }
    
    
    /**
    * Retrieve selection
    *
    * @param string $eavType
    * @param int $attribute_id
    * @param string $selection
    * @return array
    */
    public function getSelection($eavType, $attribute_id, $selection, $IdIsActiveAttribute)
    {
      	$cmssearch_issearchable_table = Mage::getSingleton('core/resource')->getTableName('cmssearch_issearchable');
      	$table_prefix = Mage::getConfig()->getTablePrefix();
    	
    	$select = $this->_getWriteAdapter()->select()
    	->from(
	    	array('e' => $this->getTable('catalog/category')),
	    	array('category_id' => 'entity_id', 'entity_type_id')
    	)
    	->join(
	    	array($eavType => Mage::getSingleton('core/resource')->getTableName('catalog_category_entity_'.$eavType)),
	    		$eavType.'.entity_id=e.entity_id',
	    	array($selection => 'value')
    	)
    	->join(
	    	array('int' => Mage::getSingleton('core/resource')->getTableName('catalog_category_entity_int')),
	    		'int.entity_id=e.entity_id',
	    	array()
    	)
    	->where("e.entity_id not in (SELECT entity_id FROM $cmssearch_issearchable_table WHERE entity='CAT')" )
    	->where($eavType.'.value IS NOT NULL')
    	->where($eavType.'.attribute_id=?', $attribute_id)
    	//select only active categories
    	->where('int.attribute_id=?',$IdIsActiveAttribute)
    	->where('int.value=?',1);

    	$select->order('e.entity_id');
    	$result = $this->_getWriteAdapter()->fetchAll($select);
    	return $result;
    }
    
    public function getStaticBlock($storeId, $tab_attributes)
    {
    	$helper = Mage::helper('cmssearch');
    	$block = array();
    	
    	//active categories
    	$select = $this->_getWriteAdapter()->select()
    	->from(
	    	array('e' => $this->getTable('catalog/category')),
	    	array('category_id' => 'entity_id')
    	)
    	->join(
	    	array('int' => Mage::getSingleton('core/resource')->getTableName('catalog_category_entity_int')),
	    	'int.entity_id=e.entity_id',
	    	array()
    	)
    	->where('int.value=?',1)
    	->where('int.attribute_id=?',$tab_attributes['is_active']);
    	
    	$activeCat = $this->_getWriteAdapter()->fetchAll($select);
    	
    	//joined block 
    	$select = $this->_getWriteAdapter()->select()
    	->from(
	    	array('e' => $this->getTable('catalog/category')),
	    	array('category_id' => 'entity_id')
    	)
    	->join(
    		array('int' => Mage::getSingleton('core/resource')->getTableName('catalog_category_entity_int')),
    		'int.entity_id=e.entity_id',
    		array('landing_page' => 'value')
    	)
    	->where('int.value IS NOT NULL')
    	->where('int.attribute_id=?',$tab_attributes['landing_page']);
    	
    	$joinedBlock = $this->_getWriteAdapter()->fetchAll($select);
    	$result = $helper->array_extend($activeCat,$joinedBlock);
    	    	
    	//get block content
    	foreach($result as $rkey => $r)
    	{
    		if(isset($r['landing_page']))
    		{
    			$select = $this->_getWriteAdapter()->select()
	    			->from(
	    			array('b' => $this->getTable('cms/block')),
	    			array('content')
    				)
    				->joinInner(
		    			array('store' => $this->getTable('cms/block_store')),
		    			$this->_getReadAdapter()->quoteInto('store.block_id=b.block_id AND (store.store_id=? OR store.store_id=0)', $storeId),
		    			array()
    				)
    				->where('b.block_id=?', $r['landing_page']);
    			
    			$temp = $this->_getWriteAdapter()->fetchAll($select);
    			if(isset($temp[0]))
    			{
    				$block[] = $result[$rkey]+$temp[0];
    			}
    		}
    	}
    	return $block;
    }
 	
}