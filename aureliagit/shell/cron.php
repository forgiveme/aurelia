<?php

require_once 'abstract.php';

class As_Cron extends Mage_Shell_Abstract {
	
	protected $_model=null;
	
	protected function _getModel()
	{
		
	   if (is_null($this->_model)) {
            $this->_model = Mage::getModel('promotion/cron');
           }
           return $this->_model;
	
	}
		/**
	 * Run script
	 * 
	 * @return void
	 */
	public function run() {
		$method = $this->getArg('method');
		
		if (empty($method)) {
			echo $this->usageHelp();
		} else {
			$this->_getModel();
			if (method_exists($this->_model, $method)) {
				$this->_model->$method();
			} else {
				echo "Method $method not found!\n";
				echo $this->usageHelp();
				exit(1);
			}
		}
	}
	
	public function usageHelp() {
		$this->_getModel();
		$help = 'Available Methods: ' . "\n";
		$methods = get_class_methods($this->_model);
		foreach ($methods as $method) {
			if (substr($method, 1) !== '_') {
				$help .= '    -method ' . $method;
				$help .= "\n";
			}
		}
		return $help;
	}
}

$shell= new As_Cron();
$shell->run();
