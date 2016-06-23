<?php
class ChilliApple_Preferences_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
		if(!Mage::getSingleton('customer/session')->isLoggedIn()) 
		{
    	  	  $this->_redirect('customer/account/login');
    		  return;
		}
    	
		$this->_initLayoutMessages('customer/session');
		$customer=Mage::getSingleton('customer/session')->getCustomer();
		$customerId=$customer->getId();
		$preferences=Mage::getModel('preferences/preferences')->getCustomerPreferences($customerId);
		if(!$preferences->getAureliaFeedback() && !$preferences->getOtherBrands()) {
			$this->_redirect('preferences/index/edit');
    		return;
		}
		Mage::register('current_preferences',$preferences);
		$this->_initLayoutMessages('customer/session');
		$this->loadLayout();     
		$this->renderLayout();
    }
	
	public function editAction()
	{
		if(!Mage::getSingleton('customer/session')->isLoggedIn())
	    	{
	    	  $this->_redirect('customer/account/login');
	    		  return;
	    	}
	    	
	    	
		$this->_initLayoutMessages('customer/session');
		$customer=Mage::getSingleton('customer/session')->getCustomer();
		$customerId=$customer->getId();
		$preferences=Mage::getModel('preferences/preferences')->load($customerId,'customer_id');
		Mage::register('current_preferences',$preferences);
		$this->loadLayout();  
		$navigationBlock = $this->getLayout()->getBlock('customer_account_navigation');
        	if ($navigationBlock) {
            	   $navigationBlock->setActive('preferences/index');
        	}   
		$this->renderLayout();
	}
	
	public function postAction()
	{
		if(!Mage::getSingleton('customer/session')->isLoggedIn()) 
	    	{
	    	  $this->_redirect('customer/account/login');
	    		  return;
	    	}
		$this->_initLayoutMessages('customer/session');
		$request=$this->getRequest();
		
		if($request->isPost())
		{
			$customer=Mage::getSingleton('customer/session')->getCustomer();
			$customerId=$customer->getId();
			$preferences=Mage::getModel('preferences/preferences')->getCustomerPreferences($customerId);//->load($customerId,'customer_id');
			$primary=$request->getParam('primary_concern');
			$secondary=$request->getParam('secondary_concern');
			$skinCares=$request->getParam('skin_care');
			$otherBrands=$request->getParam('other_brands');
			$aureliaFeedback=$request->getParam('aurelia_feedback');
			$hasGlasses=(int)$request->getParam('has_glasses');
			$preferences->setCustomerId($customerId);
			$preferences->setPrimaryConcern($primary);
			$preferences->setSecondaryConcern($secondary);
			$preferences->setSkinCares($skinCares);
			$preferences->setotherBrands($otherBrands);
			$preferences->setAureliaFeedback($aureliaFeedback);
			$preferences->setHasGlasses($hasGlasses);
			$preferences->save();
			Mage::getSingleton('customer/session')->addSuccess(Mage::helper('promotion')->__('Preferences saved successfully.'));
			$this->_redirect('*/*/index');
			return;
			
			
		}
		$this->_redirect('*/*/edit');
	}
	public function exportskintoolAction()
    {
		//print_r($_POST);
		//exit;
		$resource = Mage::getSingleton('core/resource');
		$readConnection = $resource->getConnection('core_read');
        $fileName   = 'media/downloadable/skintool.csv';
		$seperator  = ";";
		$ln         = "\r\n";
		$csv_output = "";
		$_columns = array("Email address","Name","How does it all begin","Most mornings I'm feeling","Morning skincare regime","Usually happy with the results","What you don't love","I'd love to look","How old I look on a good day","How hydrated are you","At 5pm","My stress levels are..","What's in the beauty bag","What matters most","How does your day end at 10pm?","How do you feel at the end of the day?");
		$customersIds = $this->getRequest()->getParam('massexportskintool_ids');
        if(!$customersIds) {
             Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select customer(s).'));
        } else {
            try {
				$customersIdscombined = explode(",", $customersIds);
                foreach ($customersIdscombined as $customerId) {
                    $customer = Mage::getModel('customer/customer')->load($customerId);
                    $customeremail .= ",'".$customer->getEmail()."'";	
                }
				$customerEmails = trim($customeremail, ",");

				$query = "SELECT * FROM skintools_emails left join skintools_questionsdata on skintools_emails.questiondata_id = skintools_questionsdata.id WHERE skintools_emails.address IN ($customerEmails)";
				$collection = $readConnection->fetchAll($query);
				
				if(count($collection)>0) {
					/*foreach($collection as $skintoolData) {
						foreach($skintoolData as $key => $value){
							if(!in_array($key, array('id', 'questiondata_id', 'request_uri')))
							$_columns[] = $key;
						}
						$csv_output[] = $_columns;
						break;
					}*/
					$csv_output[] = $_columns;
					$i = 1;
					foreach($collection as $skintoolData) {
						$_data = array();
						//echo "<pre>";
						//print_r($skintoolData);
						foreach($skintoolData as $key => $value){
							
							if(!in_array($key, array('id', 'questiondata_id', 'request_uri'))) {
								if($key == 'question3') {
									$value = ($value=='1'?'Ready for bed!':($value=='2'?'Balanced':($value=='3'?'Ready for life!':'')));
								}
								if($key == 'question4') {
									$value = ($value=='regime1'?'I am yet to start one':($value=='regime2'?'Simple and quick':($value=='regime3'?'My tried and tested - cleanse, tone, moisturise':($value=='regime4'?'The complete routine':''))));
								}
								if($key == 'question8') {
									$value = ($value=='0'?'Twenties':($value=='1'?'Thirties':($value=='2'?'Forties':($value=='3'?'Older & Wiser':($value=='4'?'Teens':'')))));
								}
								if(in_array($key, array('question9', 'question11', 'question15'))) {
									$value = $value."%";
								}
								if($key == 'question12') {
									$answer12 = str_Replace("bag1", "All the basics", $value);
									$answer12 = str_Replace("bag2", "Ethical & fair trade", $answer12);
									$answer12 = str_Replace("bag3", "Organic / Biorganic", $answer12);
									$answer12 = str_Replace("bag4", "Anti-Ageing/New Science", $answer12);
									$answer12 = str_Replace("bag5", "The latest beauty recommendations", $answer12);
									$answer12 = str_Replace("bag6", "My go to classics", $answer12);
									$answer12 = str_Replace("bag7", "Mostly one brand, but all the products", $answer12);
									$value = trim($answer12, ",");
								}
								$_data[] = $value;
							}
						}
						$csv_output[] = $_data;
						
						$i++;
					}
				}
				//echo "<pre>";
				//print_r($csv_output);
				//exit;
				$mage_csv = new Varien_File_Csv(); //mage CSV
				$mage_csv->saveData($fileName, $csv_output);
				
				if (! is_file ( $fileName ) || ! is_readable ( $fileName )) {
					Mage::getSingleton('adminhtml/session')->addError("File cannot be created correctly, please try again.");
				} else {
					
					 $this->getResponse ()
						->setHttpResponseCode ( 200 )
						->setHeader ( 'Pragma', 'public', true )
						->setHeader ( 'Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true )
						->setHeader ( 'Content-type', 'application/force-download' )
						->setHeader ( 'Content-Length', filesize($fileName) )
						->setHeader ('Content-Disposition', 'inline' . '; filename=' . basename($fileName) );
					//$this->getResponse ()->clearBody ();
					$this->getResponse ()->sendHeaders ();
					readfile ( $fileName );
				}
				Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('File exported successfully.')
                );
				
				//$this->_redirectReferer();
                
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        //$this->_redirect('websitecontrol/customer/index');
    }
}
