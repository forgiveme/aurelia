<?php
require_once "Zend/Search/Lucene/MultiSearcher.php";
class Tangkoko_CmsSearch_Model_Lucene_Multisearcher extends Zend_Search_Lucene_Interface_MultiSearcher 
{
	public function find($query)
	{
		
		if (count($this->_indices) == 0) {
			return array();
		}

		$hitsList = array();
		
		foreach ($this->_indices as $index) {
			
			$hits = $index->find($query);
			$hitsList = $this->merge($hits, $hitsList);
		}

		return $hitsList;
	}
	
	protected function merge(&$leftList, &$rightList)
	{
		$results = array();
		while(0 < count($leftList) && 0 < count($rightList)) {
			if($leftList[0]->score < $rightList[0]->score) {
				$results[] = array_shift($leftList);
			} else {
				$results[] = array_shift($rightList);
			}
		}

		$results = count($leftList) > count($rightList) 
			? array_merge($results, $leftList) : array_merge($results, $rightList);

		return $results;
	}

}