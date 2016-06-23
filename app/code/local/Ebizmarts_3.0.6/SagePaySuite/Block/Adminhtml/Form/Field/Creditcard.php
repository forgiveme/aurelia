<?php

class Ebizmarts_SagePaySuite_Block_Adminhtml_Form_Field_Creditcard extends Mage_Core_Block_Html_Select {

    /**
     * Credit cards cache
     *
     * @var array
     */
    private $_creditCards;

    /**
     * Flag whether to add cc all option or not
     *
     * @var bool
     */
    protected $_addCcAllOption = false;

    protected function _getCreditCards() {
        if (is_null($this->_creditCards)) {
            $this->_creditCards = Mage::getSingleton('sagepaysuite/config')->getCcTypesSagePayDirect();
        }
        return $this->_creditCards;
    }

    public function setInputName($value) {
        return $this->setName($value);
    }

    public function setColumnName($value) {
        return $this->setExtraParams('rel="#{creditcard}" style="width:120px"');
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    public function _toHtml() {
        if (!$this->getOptions()) {
            if ($this->_addCcAllOption) {
                $this->addOption('ALL', $this->__('ALL Credit Cards'));
            }
            foreach ($this->_getCreditCards() as $code => $label) {
                $this->addOption($code, $label);
            }
        }
        return parent::_toHtml();
    }

}