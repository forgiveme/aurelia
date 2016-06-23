<?php
/**
 * Tangkoko Cms Search Extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@tangkoko.com and you will be sent
 * a copy immediately.
 *
 * @category   Tangkoko
 * @package    CmsSearch
 * @author     Vincent Decrombecque
 * @copyright  Copyright (c) 2012 Tangkoko sarl (http://www.tangkoko.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Tangkoko_CmsSearch_Block_Result extends Mage_CatalogSearch_Block_Result
{
	/**
	 * Cms Page collection
	 *
	 * @var Tangkoko_CmsSearch_Model_Mysql4_Fulltext_Collection
	 */
	protected $_otherCollection;
	
	/**
	 * Retrieve search result count
	 *
	 * @return string
	 */
	public function getOtherResultCount()
	{
		if (!$this->getData('other_result_count')) {
			$size = count($this->_getOtherCollection());
			$this->setOtherResultCount($size);
		}
		return $this->getData('other_result_count');
	}

	/**
	 * Retrieve cms page collection
	 *
	 * @return Tangkoko_CmsSearch_Model_Mysql4_Fulltext_Collection
	 */
	protected function _getOtherCollection()
	{
		if (is_null($this->_otherCollection)) {
			$this->_otherCollection = Mage::getModel('cmssearch/lucene_search')->search(Mage::app()->getStore(), Mage::helper('catalogSearch')->getEscapedQueryText());
		}
		return $this->_otherCollection;
	}

	protected function _toAbstract($content)
	{
		return Mage::helper('cmssearch')->toAbstract($this->_toHtmlContent($content));
	}
	
	protected function _toHtmlContent($html)
	{
		//generate block
		$helper = Mage::helper('cms');
		$processor = $helper->getBlockTemplateProcessor();
		
		$search=array('@&lt;script.*?&gt;.*?&lt;/script&gt;@si', '@&lt;style.*?&gt;.*?&lt;/style&gt;@si', '/<script[^>]*?>.*?<\/script>/si', '/<style[^>]*?>.*?<\/style>/si');
		$replace=array('','','','');
		$html=trim(preg_replace($search,$replace,$html));
		//escape html tags
		$html = strip_tags($html);

		return $html;
	}
	
	
	public function getCategoryResultCount()
	{
		if (!$this->getData('category_result_count')) {
			$size = $this->_getCategoryCollection()->getSize();
			$this->setCategoryResultCount($size);
		}
		return $this->getData('category_result_count');
	}

	/**
	 * Retrieve cms page collection
	 *
	 * @return Tangkoko_CmsSearch_Model_Mysql4_Fulltext_Collection
	 */
	protected function _getCategoryCollection()
	{
		if (is_null($this->_categoryCollection)) {
			$this->_categoryCollection = Mage::getResourceModel('cmssearch/fulltext_category_collection');
			$this->_categoryCollection->addSearchFilter(Mage::helper('catalogSearch')->getEscapedQueryText())
			->addStoreFilter(Mage::app()->getStore())->addNameToResult()->addUrlRewriteToResult();
		}
		
		return $this->_categoryCollection;
	}

}