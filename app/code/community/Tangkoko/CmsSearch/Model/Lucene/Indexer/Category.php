<?php

class Tangkoko_CmsSearch_Model_Lucene_Indexer_Category extends Tangkoko_CmsSearch_Model_Lucene_Indexer_Abstract
{
	public function __construct()
	{
		$this->_indexSuffix = "category";
	}
		
	protected function _prepareDocument($id,$info,$storeId, $doc)	{
		$weight = $this->_prepareWeight();
		$config = Mage::Helper('cmssearch');
		
		$doc = Zend_Search_Lucene_Document_Html::loadHTML($id);
		$doc->getField("title")->value = $info["title"];
		
		if ($config->IsInCatFields('title'))
			$title = $doc->getField("title");
		else
			$title = Zend_Search_Lucene_Field::UnIndexed('title', $info['title']);
		
		if ($config->IsInCatFields('contents'))
			$contents = Zend_Search_Lucene_Field::Text("contents",$info["content"]);
		else 
			$contents = Zend_Search_Lucene_Field::UnIndexed('contents',$info["content"]);
			
		if ($config->IsInCatFields('description'))
			$description = Zend_Search_Lucene_Field::Text("description", $info["meta-description"]);
		else
			$description = Zend_Search_Lucene_Field::UnIndexed("description", $info["meta-description"]);
	
		if ($config->IsInCatFields('meta_keywords'))
			$keywords = Zend_Search_Lucene_Field::Text("meta_keyword", $info["meta-keyword"]);
		else
			$keywords = Zend_Search_Lucene_Field::UnIndexed("meta_keyword", $info["meta-keyword"]);
		
		if ($config->IsInCatFields('identifier'))
			$identifier = Zend_Search_Lucene_Field::Text('identifier', $info["url"]);
		else
			$identifier = Zend_Search_Lucene_Field::UnIndexed('identifier', $info["url"]);
		
		$docId = Zend_Search_Lucene_Field::Keyword("docId", $id);
		
		$title->boost = isset($weight['title']) ? (int)$weight['title'] : 1;
		$contents->boost = isset($weight['contents']) ? (int)$weight['contents'] : 1;
		$description->boost = isset($weight['description']) ? (int)$weight['description'] : 1;
		$keywords->boost = isset($weight['meta_keywords']) ? (int)$weight['meta_keywords'] : 1;
		$identifier->boost = isset($weight['identifier']) ? (int)$weight['identifier'] : 1;

		$Fields = array($title, $contents, $description, $keywords, $identifier, $docId);
		
		foreach ($Fields as $data)
			$doc->addField($data, "UTF-8");	
	
		parent::_indexMultipleFields($info['store_id'], "store_id", $doc);
		parent::_indexMultipleFields($info['customer_group_id'], "customer_group_id", $doc);
		return $doc;
	}
	
	public function saveIndexes($storeId, $data){
		$doc = null;
		$index = $this->_getIndex();

		foreach($data as $id => $infos){
			foreach($infos as $info){
				$doc = $this->_prepareDocument($id,$info,$storeId, $doc);
				$index->addDocument($doc);
			}
		}
		$index->commit();
		$index->optimize();
		// mage::log("Il reste ".$index->numDocs()." documents $this->_indexSuffix.");
	}
	
	protected function _removeExistingIndex($id, $index, $storeIds=null)
	{
		if(!$storeIds){
			$storeIds = array();
			foreach(mage::app()->getWebsites() as $website)	{
				foreach($website->getStores() as $store){
					$storeIds[]= $store->getStoreId();
				}
			}	
		}else{
			$storeIds = array($storeIds);
		}

		foreach($storeIds as $store_id){
			if($id)
			{
				Zend_Search_Lucene_Analysis_Analyzer::setDefault(new Zend_Search_Lucene_Analysis_Analyzer_Common_TextNum_CaseInsensitive());
				$query = new Zend_Search_Lucene_Search_Query_Boolean();
			
				$doc = Zend_Search_Lucene_Search_QueryParser::parse("docId:".$id."");
				$store = Zend_Search_Lucene_Search_QueryParser::parse("store_id:".Tangkoko_CmsSearch_Helper_Data::STORE.$store_id."");
			
				$query->addSubquery($doc, true);
				$query->addSubquery($store, true);
				
				$hits = $index->find($query);
				foreach ($hits as $hit) {
					$index->delete($hit->id);
				}
			}
		}
	}
}
