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
class Tangkoko_CmsSearch_Model_Mysql4_Indexer_Page extends Tangkoko_CmsSearch_Model_Mysql4_Indexer_Abstract
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
    public function getSearchableElements($storeId, $pageIds = null, $lastPageId = 0, $limit = 100)
    {    	
		$config = mage::getSingleton("cmssearch/config");
		$cmssearch_issearchable_table = Mage::getSingleton('core/resource')->getTableName('cmssearch_issearchable');
		
		if ($config->isActive("Clever_Cms"))	{
			$resource = Mage::getSingleton('core/resource');
			$select = $this->_getReadAdapter()->select()
				->from(
					array('p' => $resource->getTableName('cms_page_tree')),
					array('page_id', 'title', 'identifier', 'content', 'meta_keywords', 'meta_description', 'content_heading'));
			$select->joinLeft(
				array('pp' => $this->getTable('cms/page_permission')),
					'pp.page_id=p.page_id',
				''
			);
		}else{
			$select = $this->_getReadAdapter()->select()
				->from(
					array('p' => $this->getTable('cms/page')),
					array('page_id', 'title', 'identifier', 'content', 'meta_keywords', 'meta_description', 'content_heading'));
			if($storeId){
				$select->joinInner(
					array('store' => $this->getTable('cms/page_store')),
					$this->_getReadAdapter()->quoteInto('store.page_id=p.page_id AND (store.store_id in(?) OR store.store_id=0)', $storeId),
					array()
				);
			}else{
					$select->joinInner(
					array('store' => $this->getTable('cms/page_store')),
						'store.page_id=p.page_id',
					array()
				);
			}
		}

		$select->where("p.is_searchable = 1" );

		if ($config->isActive('Clever_Cms'))
			$select->columns('pp.store_id AS store_id');
		else
			$select->columns('CAST(GROUP_CONCAT(DISTINCT store.store_id ORDER BY store.store_id DESC SEPARATOR ",")AS char( 255 ) ) as store_id');

		$select->where('p.is_active');
    	if($pageIds)
			$select->where('p.page_id = ?', $pageIds);

    	$select->limit($limit)
		->group('p.page_id')
    	->order('p.page_id');
		
		if($config->isActive("Clever_Cms"))
			$select->columns('CAST(GROUP_CONCAT(DISTINCT pp.customer_group_id ORDER BY pp.customer_group_id DESC SEPARATOR ",")AS char( 255 ) ) as customer_group_id');
		else
			$select->columns(new Zend_Db_Expr(Tangkoko_CmsSearch_Model_Lucene_Search::ALL_GROUP_ID." as customer_group_id"));
		return $this->_getReadAdapter()->fetchAll($select);
    }
}