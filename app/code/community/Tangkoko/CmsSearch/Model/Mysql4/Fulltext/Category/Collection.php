<?php
/**
 * 
 * Tangkoko Cms Search Extension
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
 * @category Tangkoko
 * @package  CmsSearch
 * @author Nicolas RENAULT
 * @copyright  Copyright (c) 2011 Tangkoko sarl (http://www.tangkoko.com)
 **/
class Tangkoko_CmsSearch_Model_Mysql4_Fulltext_Category_Collection extends Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Collection
{
    /**
     * Retrieve query model object
     *
     * @return Mage_CatalogSearch_Model_Query
     */
    protected function _getQuery()
    {
        return Mage::helper('catalogSearch')->getQuery();
    }

    /**
     * Add search query filter
     *
     * @param   Mage_CatalogSearch_Model_Query $query
     * @return  Tangkoko_CmsSearch_Model_Mysql4_Fulltext_Category_Collection
     */
    public function addSearchFilter($query)
    {
        Mage::getSingleton('cmssearch/fulltext_category')->prepareResult();
        $this->getSelect()->joinInner(
            array('search_result' => $this->getTable('cmssearch/result_category')),
            $this->getConnection()->quoteInto(
                'search_result.category_id=e.entity_id AND search_result.query_id=?',
                $this->_getQuery()->getId()
            ),
            array('relevance' => 'relevance')
          
        );
        
      $categories = array();
        foreach ($this as $cat){
        	$category = Mage::getModel('catalog/category')->load($cat->getId());
        	$block = mage::getModel('cms/block')->load($category->getLandingPage());
        	$category->setBlock($block);
        	$categories[] = $category;
        }
        $this->_items = $categories;
        return $this;
    }
    /**
     * Set Order field
     *
     * @param string $attribute
     * @param string $dir
     * @return Tangkoko_CmsSearch_Model_Mysql4_Fulltext_Collection
     */
    public function setOrder($attribute, $dir='desc')
    {
        if ($attribute == 'relevance') {
            $this->getSelect()->order("relevance {$dir}");
        }
        else {
            parent::setOrder($attribute, $dir);
        }
        return $this;
    }
    
    public function addStoreFilter($store){
    	return $this;
    }

}