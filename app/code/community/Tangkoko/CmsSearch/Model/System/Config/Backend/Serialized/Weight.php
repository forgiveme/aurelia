<?php

class Tangkoko_CmsSearch_Model_System_Config_Backend_Serialized_Weight extends Mage_Adminhtml_Model_System_Config_Backend_Serialized
{
    protected $_sourcesOptions = array();
    protected $_toSave = true;
    protected $_duplicateField = "Multiple weights are assigned to the same field.";
    protected $_emptyWeight = "All selected fields must have a corresponding weight.";
    protected $_invalidWeight = "Choosen weights must be numeric values.";

    protected function _checkValues($array, $key, $modelName, $internal = true, $isLoadAfter)
    {
        $modelName = 'cmssearch/system_config_source_' . $modelName;
        
        $options = Mage::getModel($modelName)->toOptionArray();
        $this->_sourcesOptions[$modelName] = array();
        
        foreach ($options as $data) {
            $this->_sourcesOptions[$modelName][] = $data['value'];
        }
        if ($isLoadAfter == false) {
            if (isset($array[$key]) && in_array($array[$key], $this->_sourcesOptions[$modelName]))
                return true;
            return false;
        } else {
            if (isset($key) && in_array($key, $this->_sourcesOptions[$modelName]))
                return true;
            return false;
        }
        return true;
    }

    protected function _afterLoad()
    {
        parent::_afterLoad();
        
        if (is_array($value = $this->getValue())) {
            foreach ($value as $key => $config) {
                if (! $this->_checkValues($value, $key, 'field_block', true, true)) {
                    unset($value[$key]);
                }
            }
        }
    }

    protected function _beforeSave()
    {
        $multiple = array();
        
        if (is_array($value = $this->getValue())) {
            foreach ($value as $key => $config) {
                if (! isset($config['attribute']) || empty($config['attribute']) || ! $this->_checkValues($config, 'attribute', 'field_block', true, false))
                    unset($value[$key]);
                if (! (empty($config['attribute'])) && ! (empty($config['weight'])))
                    array_push($multiple, $config['attribute']);
                else 
                    if (! empty($config['attribute']) && empty($config['weight']))
                        throw new Exception($this->_emptyWeight);
                if (! empty($config))
                    if (! (is_numeric($config['weight'])))
                        throw new Exception($this->_invalidWeight);
            }
        } else
            $value = array();
        
        $multiple = array_count_values($multiple);
        foreach ($multiple as $data) {
            if ($data > 1)
                $this->_toSave = false;
        }
        if ($this->_toSave) {
            $this->setValue($value);
            parent::_beforeSave();
        } else
            throw new Exception($this->_duplicateField);
    }
}
?>