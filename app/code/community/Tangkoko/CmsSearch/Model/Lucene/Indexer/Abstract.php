<?php

abstract class Tangkoko_CmsSearch_Model_Lucene_Indexer_Abstract extends Mage_Core_Model_Abstract
{
	
	
	protected $_indexSuffix;
	
	public function cleanIndex($storeId, $id)
	{
		$index = $this->_getIndex();
		$this->_removeExistingIndex($id, $index, $storeId);
		$index->commit();
		$index->optimize();
	}
	
	abstract protected function _prepareDocument($id,$info,$storeId, $doc);
	
	protected function _getIndexPath()
	{
		return Mage::getBaseDir("var").DS."cmssearch".DS.$this->_indexSuffix;
	}
	
	public function saveIndexes($storeId, $data)
	{
		$doc = null;
		$index = $this->_getIndex();
		foreach($data as $id => $info)
		{
			$this->_removeExistingIndex($id, $index);
			
			Zend_Search_Lucene_Analysis_Analyzer::setDefault(new Zend_Search_Lucene_Analysis_Analyzer_Common_TextNum_CaseInsensitive());
			$doc = $this->_prepareDocument($id,$info,$storeId, $doc);
			$index->addDocument($doc);
		}
		$index->commit();
		$index->optimize();
		// mage::log("Il reste ".$index->numDocs()." documents $this->_indexSuffix.");
	}
	
	protected function _removeExistingIndex($id, $index, $store_id=null)
	{
		//mage::log($id);
		$hits = $index->find("docId:$id");
		foreach ($hits as $hit) {
			$index->delete($hit->id);
		}
	}
	
	
	protected function _createIndex()
	{
		$index = Zend_Search_Lucene::create($this->_getIndexPath());
	}
	
	protected function _getIndex()
	{
		try
		{
			$index = Zend_Search_Lucene::open($this->_getIndexPath());
		}
		catch (Zend_Search_Lucene_Exception $e)
		{
			$index = Zend_Search_Lucene::create($this->_getIndexPath());
		}
		return  $index;
	}
	
	public function _indexMultipleFields($tab, $index, &$doc)
	{
		$array = explode(',', $tab);
		$value = "";
		
		switch ($index)	{
			case "store_id":
				$prefix = Tangkoko_CmsSearch_Helper_Data::STORE;
			break;
			case "customer_group_id":
				$prefix = Tangkoko_CmsSearch_Helper_Data::GROUP;
			break;
			default: return;
		}
			
		foreach ($array as $data)	{
			$value .= $prefix.$data.',';
		}
		$value = trim($value, ',');
		$doc->addField(Zend_Search_Lucene_Field::Text($index, $value), 'utf-8');
	}

	protected function _prepareWeight()
	{
		$weight = unserialize(mage::getStoreConfig("cmssearch/weight/attributes"));
		$weightTab = array();
		
		foreach ($weight as $key => $value)
			$weightTab[$value['attribute']] = $value['weight'];
		return $weightTab;
	}
}
