<?php

class Tangkoko_CmsSearch_Model_Indexer_Faq extends Tangkoko_CmsSearch_Model_Indexer_Abstract
{
	
	public function __construct()
	{
        parent::__construct();
		$this->_key = "faq_id";
        $this->_init('cmssearch/indexer_faq');
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
		
		if (isset($data['faq_id'])) {
			$index['faq_id'] = $this->dataTreatment($data['faq_id'],$storeId);
		}else	{
			$index['faq_id'] = "";
		}
		
		if (isset($data['question'])) {
			$index['question'] = $this->dataTreatment($data['question'],$storeId);
		}else	{
			$index['question'] = "";
		}
		
		if (isset($data['answer'])) {
			$index['answer'] = $data['answer'];
		}else	{
			$index['answer'] = "";
		}
		
		$index['identifier'] = "faq/index/show/faq/".$data['faq_id'];
		
		if (isset($data['answer_html'])) {
			$index['answer_html'] = $this->dataTreatment($data['answer_html'],$storeId);
		}else	{
			$index['answer_html'] = "";
		}
		parent::_prepareGid($data, $index, $storeId);
		
		if(isset($data['store_id'])){
			$index['store_id'] = $data['store_id'];
		}
	
		
		return $index;
	}
	
	
}