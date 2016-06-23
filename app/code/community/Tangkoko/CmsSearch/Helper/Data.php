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
class Tangkoko_CmsSearch_Helper_Data extends Mage_Core_Helper_Abstract
{
	const CONFIG_PAGE_SEARCH = 'cmssearch/cmspage/page_search';
	const CONFIG_PAGE_CONTENT_HEADING = 'cmssearch/cmspage/page_content_heading';
	const CONFIG_PAGE_TITLE = 'cmssearch/cmspage/page_title';
	const CONFIG_PAGE_CONTENT = 'cmssearch/cmspage/page_content';
	const CONFIG_PAGE_METAKEYWORD = 'cmssearch/cmspage/page_metakeyword';
	const CONFIG_PAGE_METADESCRIPTION = 'cmssearch/cmspage/page_metadescription';
	const CONFIG_PAGE_URL = 'cmssearch/cmspage/page_url';
	
	const CONFIG_CATEGORIES_SEARCH = 'cmssearch/categories/cat_search';
	const CONFIG_CATEGORIES_NAME = 'cmssearch/categories/cat_name';
	const CONFIG_CATEGORIES_TITLE = 'cmssearch/categories/cat_title';
	const CONFIG_CATEGORIES_DESCRIPTION = 'cmssearch/categories/cat_description';
	const CONFIG_CATEGORIES_METAKEYWORD = 'cmssearch/categories/cat_metakeyword';
	const CONFIG_CATEGORIES_METADESCRIPTION = 'cmssearch/categories/cat_metadescription';
	const CONFIG_CATEGORIES_URL = 'cmssearch/categories/cat_url';
	const CONFIG_CATEGORIES_BLOCK_CONTENT = 'cmssearch/categories/cat_block_content';

	const CONFIG_FAQ_SEARCH = 'cmssearch/faq/faq_search';
	const CONFIG_BLOG_SEARCH = 'cmssearch/blogs/blog_search';
	const CONFIG_PAGE_FIELDS = 'cmssearch/cmspage/page_fields';
	const CONFIG_BLOG_FIELDS = 'cmssearch/blogs/blog_fields';
	const CONFIG_FAQ_FIELDS = 'cmssearch/faq/faq_fields';
	const CONFIG_CATEGORY_FIELDS = 'cmssearch/categories/cat_fields';
	
	const CONFIG_FUZZY_NUMBER = 'cmssearch/fuzzy/fuzzy_fields';
	const CONFIG_FUZZY_QUERY = 'cmssearch/fuzzy/fuzzy_search';
	const STORE = 'SiP04THn19Nc8aR';
	const GROUP = 'Wsnv4IB71ndI20Y';
	
	/**
	* Currenty selected store ID if applicable
	*
	* @var int
	*/
	protected $_storeId = null;
	
	/**
	* Set a specified store ID value
	*
	* @param int $store
	* @return Mage_Catalog_Helper_Data
	*/
	
	public function setStoreId($store)
	{
		$this->_storeId = $store;
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
        return Mage::getStoreConfig(Mage_CatalogSearch_Model_Fulltext::XML_PATH_CATALOG_SEARCH_TYPE, $storeId);
    }

	public function toAbstract($text)
	{	
		$search=array('@&lt;script.*?&gt;.*?&lt;/script&gt;@si', '@&lt;style.*?&gt;.*?&lt;/style&gt;@si', '/<script[^>]*?>.*?<\/script>/si', '/<style[^>]*?>.*?<\/style>/si');
		$replace=array('','','','');
		$text=trim(preg_replace($search,$replace,$text));

		preg_match('/^([^.!?\s]*[\.!?\s]+){0,30}/', trim(strip_tags($text)),$abstract);
		$result=trim(count($abstract) > 0 ? $abstract[0] : '').'...';
		return $result;
	} 
	
	public function array_extend($a, $b)
	{
		$finaltab=array();
		$temp=array();
		$alreadyexist=false;

		if(count($a) < count($b))
		{
			$tempa = $a;
			$a = $b;
			$b = $tempa;
		}

		foreach($a as $akey => $avalue)
		{
			foreach($b as $bkey => $bvalue)
			{
				if($avalue['category_id']==$bvalue['category_id'])
				{
					foreach($finaltab as $final)
					{
						if($final['category_id'] == $avalue['category_id'])
						{
							$alreadyexist=true;
						}
					}
					if(!$alreadyexist)
					{
						$temp = $avalue + $bvalue;
						$finaltab[] = $temp;
						$temp=array();
					}
					$alreadyexist=true;
				}
				else
				{
					foreach($finaltab as $final)
					{
						if($final['category_id'] == $avalue['category_id'])
						{
							$alreadyexist=true;
						}
					}
				}	
			}
			
			if(!$alreadyexist)
			{
				$finaltab[] = $avalue;
			}
			
			$alreadyexist = false;
		}
				
		return $finaltab;
	}
	
	public function getEnablePageSearch()
	{
		return (bool)Mage::getStoreConfig(self::CONFIG_PAGE_SEARCH, $this->_storeId);
	}
	
	public function getEnablePageContentheading()
	{
		return (bool)Mage::getStoreConfig(self::CONFIG_PAGE_CONTENT_HEADING, $this->_storeId);
	}
	
	public function getEnablePageTitle()
	{
		return (bool)Mage::getStoreConfig(self::CONFIG_PAGE_TITLE, $this->_storeId);
	}
	
	public function getEnablePageContent()
	{
		return (bool)Mage::getStoreConfig(self::CONFIG_PAGE_CONTENT, $this->_storeId);
	}
	
	public function getEnablePageMetakeyword()
	{
		return (bool)Mage::getStoreConfig(self::CONFIG_PAGE_METAKEYWORD, $this->_storeId);
	}
	
	public function getEnablePageMetadescription()
	{
		return (bool)Mage::getStoreConfig(self::CONFIG_PAGE_METADESCRIPTION, $this->_storeId);
	}
	
	public function getEnablePageUrl()
	{
		return (bool)Mage::getStoreConfig(self::CONFIG_PAGE_URL, $this->_storeId);
	}
	
	public function getFuzzyNumber()
	{
		return (Mage::getStoreConfig(self::CONFIG_FUZZY_NUMBER, $this->_storeId));
	}
	
	public function isFuzzy()
	{
		return (bool)Mage::getStoreConfig(self::CONFIG_FUZZY_QUERY, $this->_storeId);
	}
		
	public function getEnableCategorySearch()
	{
		return (bool)Mage::getStoreConfig(self::CONFIG_CATEGORIES_SEARCH, $this->_storeId);
	}
	
	public function getEnableCategoryName()
	{
		return (bool)Mage::getStoreConfig(self::CONFIG_CATEGORIES_NAME, $this->_storeId);
	}
	
	public function getEnableCategoryTitle()
	{
		return (bool)Mage::getStoreConfig(self::CONFIG_CATEGORIES_TITLE, $this->_storeId);
	}
	
	public function getEnableCategoryDescription()
	{
		return (bool)Mage::getStoreConfig(self::CONFIG_CATEGORIES_DESCRIPTION, $this->_storeId);
	}
	
	public function getEnableCategoryMetakeyword()
	{
		return (bool)Mage::getStoreConfig(self::CONFIG_CATEGORIES_METAKEYWORD, $this->_storeId);
	}
	
	public function getEnableCategoryMetadescription()
	{
		return (bool)Mage::getStoreConfig(self::CONFIG_CATEGORIES_METADESCRIPTION, $this->_storeId);
	}
	
	public function getEnableCategoryUrl()
	{
		return (bool)Mage::getStoreConfig(self::CONFIG_CATEGORIES_URL, $this->_storeId);
	}
	
	public function getEnableCategoryBlockContent()
	{
		return (bool)Mage::getStoreConfig(self::CONFIG_CATEGORIES_BLOCK_CONTENT, $this->_storeId);
	}
	
	public function getEnableFaqSearch()
	{
		return (bool)Mage::getStoreConfig(self::CONFIG_FAQ_SEARCH, $this->_storeId);
	}
	
	public function getEnableBlogSearch()
	{
		return (bool)Mage::getStoreConfig(self::CONFIG_BLOG_SEARCH, $this->_storeId);
	}
	
	public function isFaqSearchable()
	{
		$config = mage::getSingleton("cmssearch/config");
		return (bool)($this->getEnableFaqSearch() && $config->isActive("Flagbit_Faq"));
	}
	
	public function cleverCmsIsActive()
	{
		$config = mage::getSingleton("cmssearch/config");
		return $config->isActive("Clever_Cms");
		
	}
	
	public function isCategorySearchable()
	{
		$config = mage::getSingleton("cmssearch/config");
		return (bool)($this->getEnableCategorySearch());
	}
	
	public function isPageSearchable()
	{
		$config = mage::getSingleton("cmssearch/config");
		return (bool)($this->getEnablePageSearch() && $config->isActive("Mage_Cms"));
	}
	
	public function isInPageFields($value)
	{
		$config = mage::getStoreConfig(self::CONFIG_PAGE_FIELDS, $this->_storeId);
		return (in_array($value, explode(',', $config)));
	}
	
	public function isInCatFields($value)
	{
		$config = mage::getStoreConfig(self::CONFIG_CATEGORY_FIELDS, $this->_storeId);
		return (in_array($value, explode(',', $config)));
	}
	
	public function isInBlogFields($value)
	{
		$config = mage::getStoreConfig(self::CONFIG_BLOG_FIELDS, $this->_storeId);
		return (in_array($value, explode(',', $config)));
	}
	
	public function isInFaqFields($value)
	{
		$config = mage::getStoreConfig(self::CONFIG_FAQ_FIELDS, $this->_storeId);
		return (in_array($value, explode(',', $config)));
	}
	
	public function isBlogSearchable()
	{
		$config = mage::getSingleton("cmssearch/config");
		return (bool) ($this->getEnableBlogSearch() && $config->isActive("AW_Blog"));
	}
	
	public function getConfig()
	{
		return Mage::getSingleton("cmssearch/config");
	}
	
	public function getSearchableTypes()
	{
		return $this->getConfig()->getSearchableTypes();
	}
	
	public function getSearchableTypeCodes()
	{
		$codes =array();
		$types = $this->getSearchableTypes();
		foreach ($types as $type)
			$codes[] = $type['suffix'];
		$codes = array_unique($codes);
		return $codes;
	}
	
	public function isInSearchableEntityTypes($class)
	{
		$types = $this->getSearchableTypes();
		return isset($types[$class]);
	}
	
	public function getEntityTypeCode($class)
	{
		$type = $this->getSearchableType($class);
		return $type["suffix"];
	}
	
	public function getSearchableType($class)
	{
		$ret = '';
		$types = $this->getSearchableTypes();
		if (!is_string($class))
			$key = get_class($class);
		else
			$key = $class;
		if (array_key_exists($key, $types))
			$ret = $types[$key];
		return $ret;
	}
	
	public function getIndexer($class)
	{
		$type = $this->getSearchableType($class);
		$indexer = Mage::getModel('cmssearch/indexer_'.$type["suffix"]);
		$indexer->setModuleName($type["module_name"]);
		$indexer->setSuffix($type["suffix"]);
		return $indexer;
	}
}