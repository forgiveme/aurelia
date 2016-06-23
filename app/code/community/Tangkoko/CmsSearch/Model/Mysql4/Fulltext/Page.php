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
 * @copyright  Copyright (c) 2011 Tangkoko sarl (http://www.tangkoko.com)
 **/
class Tangkoko_CmsSearch_Model_Mysql4_Fulltext_Page extends Tangkoko_CmsSearch_Model_Mysql4_Fulltext_Abstract
{

	public function _construct()
    {
    	$this->_key = "page_id";
    	$this->_prefix  = "cmssearch";
    	$this->_FieldNameProcessed = "is_cmsprocessed";
    	$this->_tableResult = "cmssearch/result_page";
    	$this->_tableFulltext = "cmssearch/fulltext_page";
        $this->_init($this->_tableFulltext, $this->_key);
    }
    
//     public function getTLevel()
//     {
//     	return "554";
//     	return $this->_getWriteAdapter()->getTransactionLevel();
//     }
    
     /**
     * Retrieve searchable pages per store
     *
     * @param int $storeId
     * @param array|int $pageIds
     * @param int $lastPageId
     * @param int $limit
     * @return array
     */
    protected function _getSearchableElements($storeId, $pageIds = null, $lastPageId = 0, $limit = 100)
    {    	
    	$cmssearch_issearchable_table = Mage::getSingleton('core/resource')->getTableName('cmssearch_issearchable');
    	$select = $this->_getReadAdapter()->select()
    	->from(
	    	array('p' => $this->getTable('cms/page')),
	    	array('page_id', 'title', 'identifier', 'content', 'meta_keywords', 'meta_description', 'content_heading'))
    	->joinInner(
	    	array('store' => $this->getTable('cms/page_store')),
	    	$this->_getReadAdapter()->quoteInto('store.page_id=p.page_id AND (store.store_id=? OR store.store_id=0)', $storeId),
	    	array()
    	)
    	->where("p.page_id not in (SELECT entity_id FROM $cmssearch_issearchable_table WHERE entity='CMS')" );
    	
    	$select->where('p.is_active');
    	$select->where('p.page_id>?', $lastPageId)
    	->limit($limit)
    	->order('p.page_id');
    	
    	
        return $this->_getReadAdapter()->fetchAll($select);
    }
}