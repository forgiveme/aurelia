<?php

class Gsm_Silverpop_Block_Silverpop extends Mage_Core_Block_Template {
	public function _prepareLayout() {
		return parent::_prepareLayout();
	}

	public function getSilverpop() {
		if (!$this->hasData('silverpop')) {
			$this->setData('silverpop', Mage::registry('silverpop'));
		}

		return $this->getData('silverpop');
	}
}
