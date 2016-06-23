<?php


class Tangkoko_CmsSearch_Model_Lucene_Search extends Mage_Core_Model_Abstract 
{

	Const ALL_GROUP_ID = 999999999;
	protected $_searcher;
	
	public function __construct()
	{
		$this->_searcher = mage::getModel("cmssearch/lucene_multisearcher");
	}

	protected function _openIndex($indexSuffix)
	{
		try
		{
			$index = Zend_Search_Lucene::open($this->_getIndexPath($indexSuffix));
			$this->_searcher->addIndex($index);
		}
		catch (Zend_Search_Lucene_Exception $e)
		{
			mage::logException($e);
		}
	}
	
	protected function _openIndexes($codes)
	{
		foreach($codes as $code)
			$this->_openIndex($code);
	}

	protected function _getIndexPath($index)
	{
		return Mage::getBaseDir("var")."/cmssearch/".$index;
	}

	protected function _prepareQuery()
	{
		
	}

	public function search($store, $queryText)
	{
		$session = mage::getSingleton("customer/session");
		$fuzzy = Mage::getStoreConfig("cmssearch/fuzzy/fuzzy_fields");
		$helper = Mage::helper('cmssearch');
		$codes = $helper->getSearchableTypeCodes();
		
		if (empty($codes))
			return null;
		if ($helper->isFuzzy())
			$queryText.= '~'.$helper->getFuzzyNumber();
	
		$this->_openIndexes($codes);
		Zend_Search_Lucene_Analysis_Analyzer::setDefault(new Zend_Search_Lucene_Analysis_Analyzer_Common_TextNum_CaseInsensitive());

		$query = new Zend_Search_Lucene_Search_Query_Boolean();
		
		$customer_group_id = $session->getCustomerGroupId();
		$store_id = $store->getStoreId();

		
		$userQuery = Zend_Search_Lucene_Search_QueryParser::parse("customer_group_id:".Tangkoko_CmsSearch_Helper_Data::GROUP.$customer_group_id." OR customer_group_id:".Tangkoko_CmsSearch_Helper_Data::GROUP.self::ALL_GROUP_ID."");
		$storeQuery = Zend_Search_Lucene_Search_QueryParser::parse("store_id:".Tangkoko_CmsSearch_Helper_Data::STORE.$store_id." OR store_id:".Tangkoko_CmsSearch_Helper_Data::STORE."0");
		$wordsQuery = Zend_Search_Lucene_Search_QueryParser::parse(str_replace(" "," OR ",$queryText));
				
		$query->addSubquery($userQuery, true);
		$query->addSubquery($storeQuery, true);
		$query->addSubquery($wordsQuery, true);
		
		$hits = $this->_searcher->find($query);
		return $hits;
	}

}