<?php

class Stardigital_Vouchers_Model_Observer
{
    public function handleAdminSystemConfigChangedSection()
    {
        if (Mage::getStoreConfig('silverpop/deals/enabled') == '1' && Mage::getStoreConfig('silverpop/deals/generate_codes') == '1') {
            if (Mage::getModel('silverpop/silverpop')->isSilverpopEnabled()) {
                $columnName = Mage::getStoreConfig('silverpop/deals/column_name');
                Mage::getModel('vouchers/silverpop')->createMainTableColumn($columnName);

                $ruleId = Mage::getStoreConfig('silverpop/deals/rule_id');
                $rule = Mage::getModel('salesrule/rule')->load($ruleId);

                if ($rule->getId()) {
                    if ($rule->getIsActive() == 1) {
                        $vouchers = Mage::helper('vouchers')->generateCustomerVouchers($rule);
                        $csvFilePath = Mage::getModel('vouchers/silverpop')->generateDealCsvFile($vouchers);
                        $xmlMapFilePath = Mage::getModel('vouchers/silverpop')->generateDealXmlMapFile($columnName);

                        if (Mage::getModel('vouchers/silverpop')->uploadDealFilesToSilverpop($csvFilePath, $xmlMapFilePath)) {
                            Mage::getModel('vouchers/silverpop')->processDealJob();
                            Mage::getSingleton('adminhtml/session')->addSuccess('Vouchers generated and sent to silverpop successfully');
                        } else {
                            Mage::helper('vouchers')->deleteVouchers($vouchers);
                            Mage::getSingleton('adminhtml/session')->addError('Could not connect to Silverpop. No vouchers were generated');
                        }

                        Mage::getModel('core/config')->deleteConfig('silverpop/deals/generate_codes');

                        unlink($csvFilePath);
                        unlink($xmlMapFilePath);
                    }
                }
            }
        }
    }
}