<?php

class Cnc_Marketplace_Model_Actionobserver
{
    private static $appliedForControllersWithPrefix = 'Cnc_Marketplace_Adminhtml';

    public function indexPreDispatch(Varien_Event_Observer $observer)
    {
        if ($this->applied($observer)) {
            Mage::helper('marketplace/logger')->logActionStart($this->getActionInfo($observer));
        }
    }

    public function indexPostDispatch(Varien_Event_Observer $observer)
    {
        if ($this->applied($observer)) {
            Mage::helper('marketplace/logger')->logActionEnd($this->getActionInfo($observer));
        }
    }

    private function applied($observer)
    {
        if ($this->startsWith(get_class($observer->getEvent()->getControllerAction()), self::$appliedForControllersWithPrefix)) {
            return true;
        }
        return false;
    }

    private function getActionInfo($observer)
    {
        $controller = get_class($observer->getEvent()->getControllerAction());
        $actionName = $observer->getEvent()->getControllerAction()->getRequest()->getActionName();
        return $controller . ':' . $actionName;
    }

    private function startsWith($haystack, $needle)
    {
        return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== false;
    }
}
