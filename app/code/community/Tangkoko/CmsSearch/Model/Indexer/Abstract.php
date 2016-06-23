<?php
abstract class Tangkoko_CmsSearch_Model_Indexer_Abstract extends Mage_Core_Model_Abstract
{
	protected $_key;
	protected $_engine;
	public function __construct()
	{
        parent::__construct();
    }
	
	abstract protected function _prepareData($data,$storeId);

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
    public function rebuildIndex($storeId = null, $id = null, $blockId=null, $engine="lucene")
    {	
		if ($this->isActive())
		{
			$this->setEngine($engine);
        	$this->_rebuildStoreIndex($storeId, $id, $blockId, $engine);
        }
        return $this;
    }
	
	protected function _cleanIndex($storeId, $ids, $blockId)
	{
		
		$this->_engine = mage::getSingleton("cmssearch/".$this->getEngine()."_indexer_".$this->getSuffix());
		
		$this->_engine->cleanIndex($storeId, $ids, $blockId);
	}
	
	public function cleanIndex($storeId, $ids, $blockId=null, $engine="lucene")
	{
		$this->setEngine($engine);
		$this->_cleanIndex($storeId, $ids, $blockId);
	}
	
	
	protected function _saveIndexes($storeId, $indexes)
	{
		$this->_engine->saveIndexes($storeId, $indexes);
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
		try
		{
	    	$this->_cleanIndex($storeId, $ids, $blockId);
		                
			$lastId = 0;
	            $models = $this->_getSearchableElements($storeId, $ids, $lastId, $blockId);
	            if (count($models)<1) {
	                return;
	            }
	
            $indexes = array();

            foreach ($models as $data)
            {
				$index = array();
                $lastId = $data[$this->_key];

            	if (!isset($data[$this->_key])) {
                    continue;
                }
                //categories or else cms pages
			
               $index =  $this->_prepareData($data, $storeId);       
			   $indexes[$data[$this->_key]] =  $index;
            }  
			$this->_saveIndexes($storeId, $indexes);
			//$this->resetSearchResults();
		}
		catch (Exception $e)
		{
			Mage::logException($e);
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
    		//$html = strip_tags($html); //escape html tags

    		return $html;
    	}
    	return "";
    }
	
	public function _prepareGid($data, &$index, $storeId)
	{
		if (isset($data['customer_group_id']))
			$index['customer_group_id'] =  $this->dataTreatment($data['customer_group_id'], $storeId);
		else
			$index['customer_group_id'] = "";
	}
	
	public function isActive()
	{
		return Mage::helper('core')->isModuleEnabled($this->getModuleName());
	}
}