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
class Tangkoko_CmsSearch_Model_Mysql4_Indexer_Blog extends Tangkoko_CmsSearch_Model_Mysql4_Indexer_Abstract
{

	public function _construct()
    {
    	$this->_key = "post_id";
    	$this->_prefix  = "blog";
    }
    
//     public function getTLevel()
//     {
//     	return "554";
//     	return $this->_getWriteAdapter()->getTransactionLevel();
//     }
    
     /**
     * Retrieve searchable pages per store
     *
     * @param int $storeId
     * @param array|int $pageIds
     * @param int $lastPageId
     * @param int $limit
     * @return array
     */
    public function getSearchableElements($storeId, $postIds = null, $lastPageId = 0, $limit = 100)
    {   
		$collection = mage::getResourceModel("blog/post_collection")->addStatusFilter(AW_Blog_Model_Status::STATUS_ENABLED);
		$collection->getSelect()->joinLeft(
                            array('store_table' => $collection->getTable('blog/store')), 'main_table.post_id = store_table.post_id', array()
                    )
					->columns('CAST(GROUP_CONCAT(DISTINCT store_table.store_id ORDER BY store_table.store_id DESC SEPARATOR ",")AS char( 255 ) ) as store_id');
					if($storeId){
						$collection->getSelect()->where('store_table.store_id in (?)', array_merge(array("0"), explode(',', $storeId)));
					}
		if($postIds){
			$collection->getSelect()->where("main_table.post_id = ?", $postIds);
		}
		$collection->getSelect()->group('main_table.post_id');
		$collection->getSelect()->columns(new Zend_Db_Expr(Tangkoko_CmsSearch_Model_Lucene_Search::ALL_GROUP_ID." as customer_group_id"));
		$result = $collection->toArray();
		return $result["items"];
    }
}