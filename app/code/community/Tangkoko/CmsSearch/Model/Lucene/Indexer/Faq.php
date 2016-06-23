<?php
class Tangkoko_CmsSearch_Model_Lucene_Indexer_Faq extends Tangkoko_CmsSearch_Model_Lucene_Indexer_Abstract
{
	public function __construct()
	{
		$this->_indexSuffix = "faq";
	}
	
	protected function _prepareDocument($id,$info,$storeId, $doc){
		$weight = $this->_prepareWeight();
		$config	= Mage::Helper("cmssearch");

		$doc = Zend_Search_Lucene_Document_Html::loadHTML($id);
		$doc->getField("title")->value = $info["question"];
		
		if ($config->IsInFaqFields('title'))
			$title = $doc->getField("title");
		else
			$title = Zend_Search_Lucene_Field::UnIndexed('title', $info['question']);

		if ($config->IsInFaqFields('contents'))
			$contents = Zend_Search_Lucene_Field::Text('contents',$info["answer"]);
		else 
			$contents = Zend_Search_Lucene_Field::UnIndexed('contents',$info["answer"]);
	
		if ($config->IsInFaqFields('identifier'))
			$identifier = Zend_Search_Lucene_Field::Text('identifier', $info["identifier"]);
		else	
			$identifier = Zend_Search_Lucene_Field::UnIndexed('identifier', $info["identifier"]);
		
		$docId = Zend_Search_Lucene_Field::Keyword("docId", $id);

		$title->boost = isset($weight['title']) ? (int)$weight['title'] : 1;
		$contents->boost = isset($weight['contents']) ? (int)$weight['contents'] : 1;
	    $identifier->boost = isset($weight['identifier']) ? (int)$weight['identifier'] : 1;
		
		$Fields = array($title, $contents, $identifier, $docId);
			
		foreach ($Fields as $data)
			$doc->addField($data, "UTF-8");
		parent::_indexMultipleFields($info['store_id'], "store_id", $doc);
		parent::_indexMultipleFields($info['customer_group_id'], "customer_group_id", $doc);
		return $doc;
	}
}
?>