<?php
foreach (glob(dirname(__FILE__)."/validations/*.php") as $filename) {
    require_once($filename);
}

class ValidationsLoader
{
    public static function getValidators()
    {

        $validationClasses = array();

        foreach (get_declared_classes() as $class) {
            if (is_subclass_of($class, 'AbstractMagentoConfigValidation')) {
                array_push($validationClasses, new $class);
            }
        }

        return $validationClasses;
    }

}
