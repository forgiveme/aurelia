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
abstract class Tangkoko_CmsSearch_Model_Mysql4_Indexer_Abstract extends Mage_Core_Model_Mysql4_Abstract
{
	/**
	 * Retrieve searchable Elements per store
	 * @param int $storeId Store View Id
	 * @param array|int ids (Page Entity Id or category Entity Id)
	 *  @param int $block_Id block Entity Id
	 */
    abstract public function getSearchableElements($storeId, $ids = null, $lastId = 0, $limit = 100);
	
    /**
     * Index values separator
     *
     * @var string
     */
    protected $_separator = ' ';
	protected $_key;
	protected $_tableResult;
    protected $_tableFulltext;
	protected $_prefix;
	protected $_FieldNameProcessed;
    
   
	
    /**
     * Regenerate search index for store(s)
     *
     * @param int $storeId Store View Id
     * @param int|array $pageIds Page Entity Id(s)
     * @return Tangkoko_CmsSearch_Model_Mysql4_Fulltext
     */
    public function rebuildIndex($storeId = null, $ids = null, $blockId=null)
    {
    	if (is_null($storeId)) {
            foreach (Mage::app()->getStores(false) as $store) {
                $this->_rebuildStoreIndex($store->getId(), $ids, $blockId);
            }
        } else {
            $this->_rebuildStoreIndex($storeId, $ids, $blockId);
        }
        return $this;
    }

    
      /**
     * Regenerate search index for specific store
     *
     * @param int $storeId Store View Id
     * @param int|array $CategoryIds Category Entity Id or $PageIds Page Entity Id
     * @param int $blockId Block Entity Id
     * @return Tangkoko_CmsSearch_Model_Mysql4_Fulltext_Category
     */
	protected function _rebuildStoreIndex($storeId, $ids = null, $blockId=null)
    {
    	$helper = Mage::helper('cmssearch');
    	$helper->setStoreId($storeId);
    	
		try
		{
	    	$this->cleanIndex($storeId, $ids, $blockId);
		                
			$lastId = 0;
	            $categories = $this->_getSearchableElements($storeId, $ids, $lastId, $blockId);
	            if (!$categories) {
	                return;
	            }
	
            $indexes = array();

            foreach ($categories as $data) {
            
                $lastId = $data[$this->_key];

            	if (!isset($data[$this->_key])) {
                    continue;
                }

                $index = array();

                //categories or else cms pages
                if($this->_key == "category_id")
                {         
                	if (isset($data['description']) && $helper->getEnableCategoryDescription()) {
                		$index[] = $this->dataTreatment($data['description'],$storeId);
                	}
                	if (isset($data['meta-keyword']) && $helper->getEnableCategoryMetakeyword()) {
                		$index[] = $this->dataTreatment($data['meta-keyword'],$storeId);
                	}
                	if (isset($data['meta-description']) && $helper->getEnableCategoryMetadescription()) {
                		$index[] = $this->dataTreatment($data['meta-description'],$storeId);
                	}
                	if (isset($data['name']) && $helper->getEnableCategoryName()) {
                		$index[] = $this->dataTreatment($data['name'],$storeId);
                	}
                	if (isset($data['url']) && $helper->getEnableCategoryUrl()) {
                		$index[] = $this->dataTreatment($data['url'],$storeId);
                	}
                	if (isset($data['title']) && $helper->getEnableCategoryTitle()) {
                		$index[] = $this->dataTreatment($data['title'],$storeId);
                	}
                	if (isset($data['content']) && $helper->getEnableCategoryBlockContent()) {
                		$index[] = $this->dataTreatment($data['content'],$storeId);
                	}
                	
                	$indexes[$data[$this->_key]] = join($this->_separator, $index);
                }
                else
                {
                	if (isset($data['content']) && $helper->getEnablePageContent()) {
                		$index[] = $this->dataTreatment($data['content'],$storeId);
                	}
                	if (isset($data['meta_keyword']) && $helper->getEnablePageMetakeyword()) {
                		$index[] = $this->dataTreatment($data['meta_keyword'],$storeId);
                	}
                	if (isset($data['meta_description']) && $helper->getEnablePageMetadescription()) {
                		$index[] = $this->dataTreatment($data['meta_description'],$storeId);
                	}
                	if (isset($data['content_heading']) && $helper->getEnablePageContentheading()) {
                		$index[] = $this->dataTreatment($data['content_heading'],$storeId);
                	}
                	if (isset($data['identifier']) && $helper->getEnablePageUrl()) {
                		$index[] = $this->dataTreatment($data['identifier'],$storeId);
                	}
                	if (isset($data['title']) && $helper->getEnablePageTitle()) {
                		$index[] = $this->dataTreatment($data['title'],$storeId);
                	}
                	
                	$indexes[$data[$this->_key]] = join($this->_separator, $index);
                }
                
            }            
            $this->_saveIndexes($storeId, $indexes);

	        $this->resetSearchResults();
		}
		catch (Exception $e)
		{
			Mage::log($e);
			throw $e;
		}
        return $this;
    }
    
    public function dataTreatment($data, $storeId)
    {
    	if(!empty($data))
    	{
    		//generate block
    		//$helper = Mage::helper('cms');
    		//$processor =  $helper->getBlockTemplateProcessor();
			$processor =  Mage::getModel('cmssearch/email_template_filter');
    		$html = $processor->filter($data);
    		$html = strip_tags($html); //escape html tags

    		return $html;
    	}
    	return "";
    }
   
    /**
     * Save Multiply Page indexes
     *
     * @param int $storeId
     * @param array $pageIndexes
     * @return Tangkoko_CmsSearch_Model_Mysql4_Fulltext
     */
    protected function _saveIndexes($storeId, $indexes)
    {
        $values = array();
        $bind   = array();
        foreach ($indexes as $id => &$index) {
        	//if($index!="")
        	//{
        		$values[] = sprintf('(%s,%s,%s)',
        		$this->_getWriteAdapter()->quoteInto('?', $id),
        		$this->_getWriteAdapter()->quoteInto('?', $storeId),
        		                '?'
        		);
        		$bind[] = $index;
        	//}
        }
		
        if ($values) {
            $sql = "REPLACE INTO `{$this->getMainTable()}` VALUES"
                . join(',', $values);
               
            $this->_getWriteAdapter()->query($sql, $bind);
        }

        return $this;
    }

    /**
     * Delete search index data for store
     * Default clean index fonction
     * @param int $storeId Store View Id
     * @param int $pageId Page Entity Id
     * @return Tangkoko_CmsSearch_Model_Mysql4_Fulltext
     */
    public function cleanIndex($storeId = null, $id = null, $blockId=null)
    {
        $where = array();

        if (!is_null($storeId)) {
            $where[] = $this->_getWriteAdapter()->quoteInto('store_id=?', $storeId);
        }
        if (!is_null($id)) {
            $where[] = $this->_getWriteAdapter()->quoteInto($this->_key.' IN(?)', $id);
        }

        $this->_getWriteAdapter()->delete($this->getMainTable(), $where);
        return $this;
    }

   

     /**
     * Prepare results for query
     *
     * @param Tangkoko_CmsSearch_Model_Fulltext $object
     * @param string $queryText
     * @param Mage_CatalogSearch_Model_Query $query
     * @return Tangkoko_CmsSearch_Model_Mysql4_Fulltext
     */
    public function prepareResult($object, $queryText, $query)
    {	
    
    	if (!$query->getData($this->_FieldNameProcessed)) {
            $searchType = $object->getSearchType($query->getStoreId());

            $stringHelper = Mage::helper('core/string');
            /* @var $stringHelper Mage_Core_Helper_String */

            $bind = array(
                ':query'     => $queryText
            );
            $like = array();
            $unLike = array();

            $fulltextCond   = '';
            $likeCond       = '';
            $separateCond   = '';

            if ($searchType == Mage_CatalogSearch_Model_Fulltext::SEARCH_TYPE_LIKE
                || $searchType == Mage_CatalogSearch_Model_Fulltext::SEARCH_TYPE_COMBINE) {
                $words = $stringHelper->splitWords($queryText, true, $query->getMaxQueryWords());
                $likeI = 0;
                foreach ($words as $word) {
                    $like[] = '`data_index` LIKE :likew' . $likeI;
                    $bind[':likew' . $likeI] = '%' . $word . '%';    
                    $likeI ++;
                }
                if ($like) {
                    $likeCond = '(' . join(' AND ',$like) . ')';
                }
            }
            if ($searchType == Mage_CatalogSearch_Model_Fulltext::SEARCH_TYPE_FULLTEXT
                || $searchType == Mage_CatalogSearch_Model_Fulltext::SEARCH_TYPE_COMBINE) {
                $fulltextCond = 'MATCH (`data_index`) AGAINST (:query IN BOOLEAN MODE)';
            }
            if ($searchType == Mage_CatalogSearch_Model_Fulltext::SEARCH_TYPE_COMBINE && $likeCond) {
                $separateCond = ' OR ';
            }

            $sql = sprintf("REPLACE INTO `{$this->getTable($this->getTableResult())}` "
                . "(SELECT '%d', $this->_key, MATCH (`data_index`) AGAINST (:query IN BOOLEAN MODE) "
                . "FROM `{$this->getMainTable()}` WHERE (%s%s%s) AND `store_id`='%d')",
                $query->getId(),
                $fulltextCond,
                $separateCond,
                $likeCond,
                $query->getStoreId()
            );
            $this->_getWriteAdapter()->query($sql, $bind);
            $query->setData($this->_FieldNameProcessed,1);
        }

        return $this;
    }

    	public function getKey(){
    		return $this->_key;
    	}
    	
	/**
     * Reset search results
     *
     * @return Tangkoko_CmsSearch_Model_Mysql4_Fulltext
     * @TODO
     */
    public function resetSearchResults()
    {
        $this->beginTransaction();
        try {
            $this->_getWriteAdapter()->update($this->getTable('catalogsearch/search_query'), array($this->_FieldNameProcessed => 0));
         
           $this->_getWriteAdapter()->query("DELETE FROM {$this->getTable($this->getTableResult())}");
		
            $this->commit();
        }
        catch (Exception $e) {
            $this->rollBack();
            Mage::logException($e);
            throw $e;
        }

        Mage::dispatchEvent($this->_prefix.'_reset_search_result');

        return $this;
	}
	
	public function getTableResult(){
		return $this->_tableResult;
	}
	
	public function getTableFulltext(){
		return $this->_tableFulltext;
	}	
}