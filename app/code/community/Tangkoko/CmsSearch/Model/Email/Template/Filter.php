<?php

class Tangkoko_CmsSearch_Model_Email_Template_Filter extends Mage_Core_Model_Email_Template_Filter
{
	const BLOCK_TYPE_PRODUCT_LIST = 'catalog/product_list';
	const BLOCK_TYPE_CMSSEARCH_PRODUCT_LIST = 'cmssearch/product_list';

    /**
     * Retrieve Block html directive
     *
     * @param array $construction
     * @return string
     */
    public function blockDirective($construction)
    {
    	$skipParams = array('type', 'id', 'output');
        $blockParameters = $this->_getIncludeParameters($construction[2]);
        $layout = Mage::app()->getLayout();

        if (isset($blockParameters['type'])) {
        	if($blockParameters['type'] == self::BLOCK_TYPE_PRODUCT_LIST)
        	{
        		$blockParameters['type'] = self::BLOCK_TYPE_CMSSEARCH_PRODUCT_LIST;
        	}
        	$type = $blockParameters['type'];
            $block = $layout->createBlock($type, null, $blockParameters);
        } elseif (isset($blockParameters['id'])) {
            $block = $layout->createBlock('cms/block');
            if ($block) {
                $block->setBlockId($blockParameters['id']);
            }
        }

        if ($block) {
        	$block->setArea('frontend');  //set frontend area to search templates in frontend instead of admin
            $block->setBlockParams($blockParameters);
            foreach ($blockParameters as $k => $v) {
                if (in_array($k, $skipParams)) {
                    continue;
                }
                $block->setDataUsingMethod($k, $v);
            }
        }

        if (!$block) {
            return '';
        }
        if (isset($blockParameters['output'])) {
            $method = $blockParameters['output'];
        }
        if (!isset($method) || !is_string($method) || !is_callable(array($block, $method))) {
            $method = 'toHtml';
        }
        return $block->$method();
    }

}
