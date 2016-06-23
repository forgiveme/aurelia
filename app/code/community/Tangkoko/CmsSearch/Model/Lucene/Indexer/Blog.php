<?php

class Tangkoko_CmsSearch_Model_Lucene_Indexer_Blog extends Tangkoko_CmsSearch_Model_Lucene_Indexer_Abstract
{
	public function __construct()
	{
		$this->_indexSuffix = "blog";
	}
	
	
	
	protected function _prepareDocument($id,$info,$storeId, $doc)
	{		
		$doc = Zend_Search_Lucene_Document_Html::loadHTML($id);
		$doc->getField("title")->value = $info["title"];
		$config	= Mage::Helper("cmssearch");
		$weight = $this->_prepareWeight();
		
		if ($config->isInBlogFields('title'))
			$title = $doc->getField("title");
		else
			$title = Zend_Search_Lucene_Field::UnIndexed('title', $info['title']);
		
		if ($config->isInBlogFields('contents'))
			$contents = Zend_Search_Lucene_Field::Text("contents",$info["content"]);
		else
			$contents = Zend_Search_Lucene_Field::UnIndexed("contents",$info["content"]);
			
		if ($config->isInBlogFields('description'))
			$description = Zend_Search_Lucene_Field::Text("description", $info["meta_description"]);
		else
			$description = Zend_Search_Lucene_Field::UnIndexed("description", $info["meta_description"]);
		
		if ($config->isInBlogFields('meta_keywords'))
			$keywords = Zend_Search_Lucene_Field::Text("meta_keyword", $info["meta_keyword"]);
		else
			$keywords = Zend_Search_Lucene_Field::UnIndexed("meta_keyword", $info["meta_keyword"]);
			
		if ($config->isInBlogFields('identifier'))
			$identifier = Zend_Search_Lucene_Field::Text('identifier', $info["identifier"]);
		else
			$identifier = Zend_Search_Lucene_Field::UnIndexed('identifier', $info["identifier"]);
				
		$docId = Zend_Search_Lucene_Field::Text("docId", $id);
		
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
	
	
}
