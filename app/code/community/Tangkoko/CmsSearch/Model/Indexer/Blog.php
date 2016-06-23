<?php

class Tangkoko_CmsSearch_Model_Indexer_Blog extends Tangkoko_CmsSearch_Model_Indexer_Abstract
{
	
	public function __construct()
	{
		
        parent::__construct();
		$this->_key = "post_id";
        $this->_init('cmssearch/indexer_blog');
    }
	
	protected function _getSearchableElements($storeId, $ids, $lastId, $blockId){
		return $this->getResource()->getSearchableElements($storeId, $ids, $lastId, $blockId);
	}
	
	public function getStoreId($model){
		if (!$model->getData('stores')) {
			return array('0');
		}else{
			return implode(",",$model->getData('stores'));
		}
	}
    
	protected function _prepareData($data, $storeId)
	{
		$index = array();
		$helper = Mage::helper('cmssearch');
    	$helper->setStoreId($storeId[0]);
		if (isset($data['post_content'])) {
			$index['content'] = $this->dataTreatment($data['post_content'],$storeId);
		}
		if (isset($data['meta_keywords'])) {
			$index['meta_keyword'] = $this->dataTreatment($data['meta_keywords'],$storeId);
		}
		if (isset($data['meta_description'])) {
			$index['meta_description'] = $this->dataTreatment($data['meta_description'],$storeId);
		}
		if (isset($data['content_heading'])) {
			$index['content_heading'] = $this->dataTreatment($data['content_heading'],$storeId);
		}
		if (isset($data['identifier'])) {
			$url = mage::helper("blog")->getRoute()."/".$data['identifier'];
			$index['identifier'] = $url;
		}
		if (isset($data['title'])) {
			$index['title'] = $this->dataTreatment($data['title'],$storeId);
		}
		parent::_prepareGid($data, $index, $storeId);
		if(isset($data['store_id'])){
			$index['store_id'] = $data['store_id'];
		}
		
		return $index;
	}
	
	
}