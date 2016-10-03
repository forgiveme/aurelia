<?php

/**
 * Used for pagination of the imports
 */
class Cnc_Marketplace_Helper_Imports extends Mage_Core_Helper_Abstract
{
    public function getPageImportIds($type, $page_nr)
    {
        $collection = $this->getCollection();
        $collection->getType($type);
        $collection->setCurPage($page_nr);

        $data = array();
        foreach ($collection as $item) {
            $data[] = $item->getData('import_id');
        }
        return $data;
    }

    public function getLastPageNr($type)
    {
        return $this->getCollection()->getType($type)->getLastPageNumber();
    }

    private function getCollection()
    {
        $fromDate = date('Y-m-d H:i:s', strtotime("-1 week"));
        $toDate = date('Y-m-d H:i:s', strtotime("now"));
        return Mage::getModel('marketplace/importtable')->getCollection()
            ->setOrder('date_created', 'DESC')
            ->addFieldToFilter('date_created', array('from' => $fromDate, 'to' => $toDate))
            ->setPageSize(10);
    }

}
