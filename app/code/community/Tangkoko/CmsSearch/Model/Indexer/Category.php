<?php
class Tangkoko_CmsSearch_Model_Indexer_Category extends Tangkoko_CmsSearch_Model_Indexer_Abstract
{
	
	public function __construct()
	{
		
        parent::__construct();
		$this->_key = "entity_id";
        $this->_init('cmssearch/indexer_category');
    }
    
	protected function _getSearchableElements($storeId, $ids, $lastId, $blockId){
		return $this->getResource()->getSearchableElements($storeId, $ids, $lastId, $blockId);
	}
	
	public function getStoreId($model){
		return $model->getStoreId();
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
	    	
		                
			$lastId = 0;
		
	            $models = $this->_getSearchableElements($storeId, $ids, $lastId, $blockId);
			
	            if (!$models) {
	                return;
	            }
	
            $indexes = array();

            foreach ($models as $data) {
				
				$this->_cleanIndex($data['store_id'], $data["entity_id"], $blockId);
				$index = array();
                $lastId = $data[$this->_key];

            	if (!isset($data[$this->_key])) {
                    continue;
                }
                //categories or else cms pages
			
				if ($data['is_searchable'] != 0 && $data['store_id'] != 0)
					{
						$index = $this->_prepareData($data, $storeId);       
						$indexes[$data[$this->_key]][] =  $index;
				   }
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
	
	
	protected function _prepareData($data, $storeId)
	{
		
		$index = array();
		$helper = Mage::helper('cmssearch');
    	$helper->setStoreId($storeId[0]);
		if (isset($data['description'])) {
			$index['content']  = $this->dataTreatment($data['description'],$storeId);
		}else{
			$index['content'] ="";
		}
		if (isset($data['meta_keywords'])) {
			$index['meta-keyword'] = $this->dataTreatment($data['meta_keywords'],$storeId);
		}else{
			$index['meta-keyword'] ="";
		}
		if (isset($data['meta_description'])) {
			$index['meta-description'] = $this->dataTreatment($data['meta_description'],$storeId);
		}else{
			$index['meta-description'] ="";
		}
		if (isset($data['name'])) {
			$index['title'] = $this->dataTreatment($data['name'],$storeId);
		}else{
			$index['name'] ="";
		}
		if (isset($data['url_path'])) {
			$index['url'] = str_replace(Mage::getBaseUrl(), '', $data['url_path']);
		}else{
			$index['url'] ="";
		}
		
		if (isset($data['landing_page'])) {
			$block = mage::getModel("cms/block")->load($data['landing_page']);
			$index['content'] .= $block->getContent();
		}
		parent::_prepareGid($data, $index, $storeId);
		
		if (isset($data['store_id'])) {
			
			$index['store_id'] = $data['store_id'];
		}
		
		
		return $index;
	}
}
?>