<?php

class Tangkoko_CmsSearch_Model_Indexer_Page extends Tangkoko_CmsSearch_Model_Indexer_Abstract
{
	
	public function __construct()
	{
		
        parent::__construct();
		$this->_key = "page_id";
        $this->_init('cmssearch/indexer_page');
    }
	
	protected function _getSearchableElements($storeId, $ids, $lastId, $blockId){
		return $this->getResource()->getSearchableElements($storeId, $ids, $lastId, $blockId);
	}
	
	public function getStoreId($model){
		$newStores = (array)$model->getStores();
        if (empty($newStores)) {
            $newStores = (array)$model->getStoreId();
        }
		return $newStores;
	}
    
	protected function _prepareData($data, $storeId)
	{
		$index = array();
		$helper = Mage::helper('cmssearch');
    	$helper->setStoreId($storeId[0]);
		if (isset($data['content'])) {
			$index['content'] = $this->dataTreatment($data['content'],$storeId);
		}else{
			$index['content']="";
		}
		if (isset($data['meta_keyword'])) {
			$index['meta_keyword'] = $this->dataTreatment($data['meta_keyword'],$storeId);
		}else{
			$index['meta_keyword'] ="";
		}
		if (isset($data['meta_description'])) {
			$index['meta_description'] = $this->dataTreatment($data['meta_description'],$storeId);
		}else{
		$index['meta_description'] = "";
		}
		if (isset($data['content_heading'])) {
			$index['content_heading'] = $this->dataTreatment($data['content_heading'],$storeId);
		}else{
		$index['content_heading'] = "";
		}
		if (isset($data['identifier'])) {
			$index['identifier'] = $data['identifier'];
		}else{
			$index['identifier'] = "";
		}
		if (isset($data['title'])) {
			$index['title'] = $this->dataTreatment($data['title'],$storeId);
		}else{
			$index['title'] = "";
		}
		parent::_prepareGid($data, $index, $storeId);

		if(isset($data['store_id'])){
			$index['store_id'] = $data['store_id'];
		}
		return $index;
	}
	
	
}