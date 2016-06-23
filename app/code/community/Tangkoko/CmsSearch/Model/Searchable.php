<?php

class Tangkoko_CmsSearch_Model_Searchable extends Mage_Core_Model_Abstract
{
	public function __construct()
	{
        parent::__construct();
        $this->_init('cmssearch/searchable');
    }
    
	/**
	* look if entity is searchable by entity type (CMS or CAT) and entity_id
	*
	* @param   varchar $entity_type
	* @param   int $entity_id
	* @return  Tangkoko_CmsSearch_Model_Mysql4_Searchable
	*/
	public function isNotSearchable($entity_type, $entity_id)
	{
		$collection = Mage::getModel('cmssearch/searchable')->getCollection();
		 
		foreach ($collection as $searchable) {
			if($searchable->getEntity() == $entity_type && $searchable->getEntityId() == $entity_id) {
				return $searchable;
			}
		}
		return false;
		
	}
	
	public function saveSearchable($entity)
	{		
		$entity_type_code = Mage::helper("cmssearch")->getEntityTypeCode($entity);
		$entity->setSuffix($entity_type_code);
		$entity_id = $entity->getId();
		$choice_searchable = $entity->getIsSearchable();	
		$actual_searchable = Mage::getModel('cmssearch/searchable')->isNotSearchable($entity_type_code, $entity_id);

		if($choice_searchable && $actual_searchable)
		{
			$actual_searchable->delete();
		}
		
		if(!$choice_searchable && !$actual_searchable)
		{
			$searchable = Mage::getModel('cmssearch/searchable');
			$searchable->setEntity($entity_type_code);
			$searchable->setEntityId($entity_id);
			
			try {
				$searchable->save();
			} catch (Exception $e) {
				Mage::logException($e);
			}
		}
	}
	
	/**
	* Prepare page's searchable statuses.
	*
	* @return array
	*/
	public function getSearchableStatuses()
	{
		$statuses = new Varien_Object(array(
		0 => Mage::helper('cmssearch')->__('Yes'),
		1 => Mage::helper('cmssearch')->__('No'),
		));
		
		return $statuses->getData();
	}
	
}