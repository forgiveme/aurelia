<?php
class Tangkoko_CmsSearch_Model_Indexer_Abstract extends Mage_Core_Model_Abstract
{
	public function __construct()
	{
        parent::__construct();
    }
	
	abstract protected function _prepareData($data);
	abstract protected function _getSearchableElements($storeId, $ids, $lastId, $blockId);

	/**
     * Regenerate all Stores index
     *
     * Examples:
     * (null, null, null) => Regenerate index for all stores
     * (1, null, null)    => Regenerate index for store Id=1
     * (1, 2)       => Regenerate index for page or category Id=2 and its store view Id=1
     * (null, 2, null)    => Regenerate index for all store views of page or category Id=2
     * (null, null, 1) => Regenerate index for static block Id =1 for all store and all categories
     *
     * @param int $storeId Store View Id
     * @param int $id Page Entity Id or Category Entity Id
     * @return Tangkoko_CmsSearch_Model_Fulltext_Page or Tangkoko_CmsSearch_Model_Fulltext_Category
     */
    public function rebuildIndex($storeId = null, $id = null, $blockId=null, $engine="lucene", $suffix)
    {
		$this->setSuffix($suffix);
		$this->setEngine($engine);
        $this->_rebuildStoreIndex($storeId, $id, $blockId);
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
	protected function _rebuildStoreIndex($storeId, $ids = null, $blockId=null, $engine)
    {
    	$helper = Mage::helper('cmssearch');
    	$helper->setStoreId($storeId[0]);
    	
		try
		{
	    	$this->cleanIndex($storeId, $ids, $blockId, $engine);
		                
			$lastId = 0;
	            $models = $this->_getSearchableElements($storeId, $ids, $lastId, $blockId);
	            if (!$models) {
	                return;
	            }
	
            $indexes = array();

            foreach ($models as $data) {
                $lastId = $data[$this->_key];

            	if (!isset($data[$this->_key])) {
                    continue;
                }

                $index = array();

                //categories or else cms pages
                $this->_prepareData($data);       
            }            
            $this->_saveIndexes($storeId, $indexes);

	        $this->resetSearchResults();
		}
		catch (Exception $e)
		{
			Mage::logEception($e);
			throw $e;
		}
        return $this;
    }
}