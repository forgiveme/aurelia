<?php 

class Fontis_CampaignMonitor_NotifyController extends Mage_Core_Controller_Front_Action {

   public function newAction() {
   

   $email = $this->getRequest()->getParam('c_email');
  $name=  $this->getRequest()->getParam('name');

	 $session   = Mage::getSingleton('core/session');
   
        if (  $email !="" && $name !="" ) {
           
          
            $apiKey ="748091197600da830bca086216264f56b1ecac88c86a37de";
            $listID = "90869be08731fe1c81cebb508e4e2818";
        
            if($apiKey && $listID) {
                try {
                    $client = new SoapClient("http://api.createsend.com/api/api.asmx?wsdl", array("trace" => true));
                } catch(Exception $e) {
                    Mage::log("Fontis_CampaignMonitor: Error connecting to CampaignMonitor server: ".$e->getMessage());
                    $session->addException($e, $this->__('There was a problem with the subscription'));
                    $this->_redirectReferer();
                }

                    // otherwise if nobody's logged in, ignore the custom
                    // attributes and just set the name to '(Guest)'
                    try {
                        $result = $client->AddAndResubscribe(array(
                                "ApiKey" => $apiKey,
                                "ListID" => $listID,
                                "Email" => $email,
                                "Name" => $name));
                    } catch (Exception $e) {
                        Mage::log("Fontis_CampaignMonitor: Error in CampaignMonitor SOAP call: ".$e->getMessage());
                        $session->addException($e, $this->__('There was a problem with the subscription'));
                        $this->_redirectReferer();
                    }
                $session->addSuccess($this->__('Thank you for your subscription.'));
				
				  $this->_redirect('botanical-cream-deodorant/');
                    return;
				
            } else {
                Mage::log("Fontis_CampaignMonitor: Error: Campaign Monitor API key and/or list ID not set ");
            }
        }else{
		
		$session->addError($this->__('Please complete all of the required fields to hear about this exclusive launch')); 
		  $this->_redirect('botanical-cream-deodorant/');
                    return;
		
		}

    
    }

    


}