<?php
/**
 * Tangkoko Cms Search Extension
 *
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
 * @category   Tangkoko
 * @package    CmsSearch
 * @author     Vincent Decrombecque
 * @copyright  Copyright (c) 2012 Tangkoko sarl (http://www.tangkoko.com) 
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Tangkoko_CmsSearch_Model_Observer
{
    public function onModelSaveAfter($observer)
    {
		$entity = $observer->getEvent()->getObject();
		if (Mage::helper("cmssearch")->isInSearchableEntityTypes(get_class($entity)))
			$this->_refreshModelIndex($entity);
	}

    protected function _refreshModelIndex($entity)
    {
		$indexer = Mage::helper("cmssearch")->getIndexer(get_class($entity));
		$id = $entity->getId();
		$store_id = $indexer->getStoreId($entity);
		if ($entity instanceof Clever_Cms_Model_Mysql4_Page_Permission)
		{
			$id = null;
			$store_id = null;
		}
		elseif ($entity instanceof Flagbit_Faq_Model_Faq)
		{
			$id = $entity->getFaqId();
		}
		$indexer->rebuildIndex($store_id, $id, null,"lucene");
        return $this;
    }
    
    
    /**
     * Remove Model in index
     *
     * @param   Varien_Event_Observer $observer
     * @return  Tangkoko_Cmsseach_Model_Observer
     */

    public function cleanModelIndex($observer)
    {
		$entity = $observer->getEvent()->getObject();
		if (Mage::helper("cmssearch")->isInSearchableEntityTypes(get_class($entity)))
		{
			$entity = $observer->getEvent()->getObject();
			$id = $entity->getId();
			$indexer = Mage::helper("cmssearch")->getIndexer(get_class($entity));
			$store_id = $indexer->getStoreId($entity);
			if ($entity instanceof Flagbit_Faq_Model_Faq)
			{
				$id = $entity->getFaqId();
			}
			
			$indexer->cleanIndex($store_id, $id, null, "lucene");
		}
        return $this;
    }
    
 	
    /**
     * Refresh fulltext index
     *
     * @param   Varien_Event_Observer $observer
     * @return  Tangkoko_Cmsseach_Model_Observer
     */
    public function refreshFullIndex($observer)
    {	
		$types = Mage::helper("cmssearch")->getSearchableTypes();
		$refreshed = array();
		foreach($types as $class => $type)
		{	
			if (!in_array($type["suffix"], $refreshed))
			{
				$refreshed[] = $type["suffix"];
				$indexer = Mage::helper("cmssearch")->getIndexer($class);
				$indexer->rebuildIndex(null, null, null, "lucene");
			}
		}
        return $this;
    }
    
    /**
     * Adding field "is searchable" when editing CMS page
     * 
     * @param  Varien_Event_Observer $observer 
     */
    public function cmsField($observer)
    {
    	$model = Mage::registry('cms_page');
    	$bIsNotSearchable = Mage::getModel('cmssearch/searchable')->isNotSearchable('page',$model->getId());
    	if(empty($bIsNotSearchable) || !$bIsNotSearchable){
    		$value = '1';
    	} else {
    		$value = '0';
    	}
    	
    	$form = $observer->getForm();
    	$fieldset = $form->addFieldset('cmssearch_content_fieldset', array('legend'=>Mage::helper('cmssearch')->__('CMS Search'),'class'=>'fieldset-wide'));
    	$fieldset->addField('is_searchable', 'select', array(
                'name'      => 'is_searchable',
                'label'     => Mage::helper('cmssearch')->__('Is searchable'),
                'title'     => Mage::helper('cmssearch')->__('Is searchable'),
                'disabled'  => false,
    			'options'   => array(
    	                        '1' => Mage::helper('cms')->__('Yes'),
    	                        '0' => Mage::helper('reports')->__('No'),
    			),
                'value'     => $value
    	));
    }
    
    /**
    * Adding field "is searchable" when editing Category page
    *
    * @param  Varien_Event_Observer $observer
    */
    public function categoryField($observer)
    {
    	$model = Mage::registry('cms_page');
    	$bIsNotSearchable = Mage::getModel('cmssearch/searchable')->isNotSearchable('category',$model->getId());
    	if(!$bIsNotSearchable){
    		$value = '1';
    	} else {
    		$value = '0';
    	}
    	 
    	$form = $observer->getForm();
    	$fieldset = $form->addFieldset('cmssearch_content_fieldset', array('legend'=>Mage::helper('cmssearch')->__('CMS Search'),'class'=>'fieldset-wide'));
    	$fieldset->addField('is_searchable', 'select', array(
                    'name'      => 'is_searchable',
                    'label'     => Mage::helper('cmssearch')->__('Is searchable'),
                    'title'     => Mage::helper('cmssearch')->__('Is searchable'),
                    'disabled'  => false,
        			'options'   => array(
        	                        '1' => Mage::helper('cms')->__('Yes'),
        	                        '0' => Mage::helper('reports')->__('No'),
    	),
                    'value'     => $value
    	));
    }
}