<?php 
class As_Brand_Model_Attribute_Source_Brand extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{

	protected $_options=null;
   
	public function getAllOptions()
	{
	   if (is_null($this->_options)) 
	   {
	            $this->_options = array();
	            $collection=Mage::getSingleton('brand/brand')->getCollection();

	            foreach ($collection as $row) {
	            	$this->_options[]=array('value' => $row['brand_id'],'label'=>$row['title'] );
	            }
	   }
	   return $this->_options;
	   
	}
   
   
    public function toOptionArray()
    {
        return $this->getAllOptions();
    }
}