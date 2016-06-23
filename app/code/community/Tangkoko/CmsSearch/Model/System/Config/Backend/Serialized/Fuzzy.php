<?php
	class Tangkoko_CmsSearch_Model_System_Config_Backend_Serialized_Fuzzy extends Mage_Core_Model_Config_Data
	{
		private	$nan = "Chosen number must be a numeric value.";
		private $limits = "Similarity number must be between 0 and 1.";
	
		public function save()
			{
				$fuzzy = $this->getValue();
							
				if (!(is_numeric($fuzzy)))
					throw new Exception($this->nan);
				if ($fuzzy < 0 || $fuzzy > 1)
					throw new Exception($this->limits);
				return parent::save();
			}
	}
?>