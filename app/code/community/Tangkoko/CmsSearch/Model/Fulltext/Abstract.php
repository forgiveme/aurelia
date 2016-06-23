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
 * to license@tangkoko.com  and you will be sent
 * a copy immediately.
 *
 * @category   Tangkoko
 * @package    CmsSearch
 * @author     Vincent Decrombecque
 * @copyright  Copyright (c) 2012 Tangkoko sarl (http://www.tangkoko.com) 
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
abstract class Tangkoko_CmsSearch_Model_Fulltext_Abstract extends Mage_Core_Model_Abstract
{
    /**
     * Regenerate all Stores index
     *
     * Examples:
     * (null, null, null) => Regenerate index for all stores
     * (1, null, null)    => Regenerate index for store Id=1
     * (1, 2)       => Regenerate index for page or category Id=2 and its store view Id=1
     * (null, 2, null)    => Regenerate index for all store views of page or category Id=2
     * (null, null, 1) => Regenerate index for static block Id =1 for all store and all categories
     *
     * @param int $storeId Store View Id
     * @param int $id Page Entity Id or Category Entity Id
     * @return Tangkoko_CmsSearch_Model_Fulltext_Page or Tangkoko_CmsSearch_Model_Fulltext_Category
     */
    public function rebuildIndex($storeId = null, $id = null, $blockId=null)
    {
        $this->getResource()->rebuildIndex($storeId, $id, $blockId);
        return $this;
    }

    /**
     * Delete index data
     *
     * Examples:
     * (null, null, null) => Clean index of all stores
     * (1, null, null)    => Clean index of store Id=1
     * (1, 2, null)       => Clean index of page or category Id=2 and its store view Id=1
     * (null, 2, null)    => Clean index of all store views of page or category Id=2
     * (null, null, 1)    => Clean index of all store views of all categories for satic block Id = 1
     *
     * @param int $storeId Store View Id
     * @param int $pageId Page Entity Id
     * @return Tangkoko_CmsSearch_Model_Fulltext_Page or Tangkoko_CmsSearch_Model_Fulltext_Category  
     */
    public function cleanIndex($storeId = null, $id = null, $blockId=null)
    {
        $this->getResource()->cleanIndex($storeId, $id);
        return $this;
    }

    /**
     * Reset search results cache
     *
     * @return Mage_CatalogSearch_Model_Fulltext
     */
    public function resetSearchResults()
    {
        $this->getResource()->resetSearchResults();
        return $this;
    }

    /**
     * Prepare results for query
     *
     * @param Mage_CatalogSearch_Model_Query $query
     * @return Mage_CatalogSearch_Model_Fulltext
     */
    public function prepareResult($query = null)
    {
		if (!$query instanceof Mage_CatalogSearch_Model_Query) {
            $query = Mage::helper('catalogSearch')->getQuery();
        }
        $queryText = Mage::helper('catalogSearch')->getQueryText();
        if ($query->getSynonimFor()) {
            $queryText = $query->getSynonimFor();
        }
        $this->getResource()->prepareResult($this, $queryText, $query);
        return $this;
    }

    /**
     * Retrieve search type
     *
     * @param int $storeId
     * @return int
     */
    public function getSearchType($storeId = null)
    {
        return Mage::helper('cmssearch')->getSearchType($storeId);
    }

}