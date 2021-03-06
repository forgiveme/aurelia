<?php
class Skint_Reports_Adminhtml_SkintreportsController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Return some checking result
     *
     * @return void
     */
    public function skintoolsexportAction()
    {
		
		if ($data = $this->getRequest()->getPost()) {
			
			$postData = $this->getRequest()->getPost();
			
			if($postData['groups']['skintool_group']['fields']) {
				
				if($postData['groups']['skintool_group']['fields']['skintools_start_date']['value']=='' || $postData['groups']['skintool_group']['fields']['skintools_end_date']['value']=='') {
					
					$result = Mage::helper('skintreports/data')->__("Please select dates to generate the report.");
					Mage::app()->getResponse()->setBody($result);
					return;
				
				}
			
				$modelObj = Mage::getModel('skintreports/reports');
				
				$sktData = $modelObj->getskintoolsData($postData['groups']['skintool_group']['fields']);
				
				if($sktData) {
					
					$fileName   = 'media/downloadable/skintoolcomplete.csv';
					$seperator  = ";";
					$ln         = "\r\n";
					$completeData = Mage::helper('skintreports/data')->getskintoolsCsvdata($sktData);
					$mage_csv = new Varien_File_Csv(); //mage CSV
					$mage_csv->saveData($fileName, $completeData);
					
					try {
					
						if (! is_file ( $fileName ) || ! is_readable ( $fileName )) {
							
							$result = 3;
							Mage::app()->getResponse()->setBody($result);
						
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
							//readfile ( $fileName );
							
							$result = 1;
							Mage::app()->getResponse()->setBody($result);
							
						}
					
					} catch (Exception $e) {
						
						$result = Mage::helper('skintreports/data')->__($e);
						Mage::app()->getResponse()->setBody($result);
						
					}
					
				} else {
					$result = Mage::helper('skintreports/data')->__("No data found for the requested dates.");
					Mage::app()->getResponse()->setBody($result);
				}
			} else {
				$result = Mage::helper('skintreports/data')->__("The request cannot be sent successfully, please try again.");
				Mage::app()->getResponse()->setBody($result);
			}
		}
    }
	
	public function skintoolsexportorderAction()
    {
		
		if ($data = $this->getRequest()->getPost()) {
			
			$postData = $this->getRequest()->getPost();
			
			if($postData['groups']['skintool_group']['fields']) {
				
				if($postData['groups']['skintool_group']['fields']['skintools_start_date']['value']=='' || $postData['groups']['skintool_group']['fields']['skintools_end_date']['value']=='') {
					
					$result = Mage::helper('skintreports/data')->__("Please select dates to generate the report.");
					Mage::app()->getResponse()->setBody($result);
					return;
				
				}
			
				$modelObj = Mage::getModel('skintreports/reports');
				
				$sktData = $modelObj->getskintoolsorderData($postData['groups']['skintool_group']['fields']);
				
				if($sktData) {
					
					$fileName   = 'media/downloadable/skintoolordercomplete.csv';
					$seperator  = ";";
					$ln         = "\r\n";
					$completeData = Mage::helper('skintreports/data')->getskintoolsorderCsvdata($sktData);
					$mage_csv = new Varien_File_Csv(); //mage CSV
					$mage_csv->saveData($fileName, $completeData);
					
					try {
					
						if (! is_file ( $fileName ) || ! is_readable ( $fileName )) {
							
							$result = 3;
							Mage::app()->getResponse()->setBody($result);
						
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
							//readfile ( $fileName );
							
							$result = 1;
							Mage::app()->getResponse()->setBody($result);
							
						}
					
					} catch (Exception $e) {
						
						$result = Mage::helper('skintreports/data')->__($e);
						Mage::app()->getResponse()->setBody($result);
						
					}
					
				} else {
					$result = Mage::helper('skintreports/data')->__("No data found for the requested dates.");
					Mage::app()->getResponse()->setBody($result);
				}
			} else {
				$result = Mage::helper('skintreports/data')->__("The request cannot be sent successfully, please try again.");
				Mage::app()->getResponse()->setBody($result);
			}
		}
    }
	
	public function skintoolsexpcustomerorproductsAction() {
		
		if ($data = $this->getRequest()->getPost()) {
			
			$postData = $this->getRequest()->getPost();
			
			if($postData['groups']['customer_groups']['fields']) {
				
				if(!isset($postData['groups']['customer_groups']['fields']['select_customers']) && !isset($postData['groups']['customer_groups']['fields']['select_products'])) {
					
					$result = Mage::helper('skintreports/data')->__("Please select atleast one customer or product to generate report.");
					Mage::app()->getResponse()->setBody($result);
					return;
				
				}
				if($postData['groups']['customer_groups']['fields']['customer_group_start_date']['value']=='' || $postData['groups']['customer_groups']['fields']['customer_group_end_date']['value']=='') {
					
					$result = Mage::helper('skintreports/data')->__("Please select dates to generate the report.");
					Mage::app()->getResponse()->setBody($result);
					return;
				
				}
				
				$modelObj = Mage::getModel('skintreports/reports');
				
				$sktData = $modelObj->getcustomerProductData($postData['groups']['customer_groups']['fields']);
				
				if($sktData) {
					
					$fileName   = 'media/downloadable/customersandproducts.csv';
					$seperator  = ";";
					$ln         = "\r\n";
					$completeData = Mage::helper('skintreports/data')->getcustomerproductsCsvdata($sktData);
					$mage_csv = new Varien_File_Csv(); //mage CSV
					$mage_csv->saveData($fileName, $completeData);
					
					try {
					
						if (! is_file ( $fileName ) || ! is_readable ( $fileName )) {
							
							$result = 3;
							Mage::app()->getResponse()->setBody($result);
						
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
							//readfile ( $fileName );
							
							$result = 1;
							Mage::app()->getResponse()->setBody($result);
							
						}
					
					} catch (Exception $e) {
						
						$result = Mage::helper('skintreports/data')->__($e);
						Mage::app()->getResponse()->setBody($result);
						
					}
					
				} else {
					$result = Mage::helper('skintreports/data')->__("No data found for the requested dates.");
					Mage::app()->getResponse()->setBody($result);
				}
				
			} else {
				$result = Mage::helper('skintreports/data')->__("The request cannot be sent successfully, please try again.");
				Mage::app()->getResponse()->setBody($result);
			}
		}
	}
	
	public function skintoolsexportguestsAction() {
		
		if ($data = $this->getRequest()->getPost()) {
			
			$postData = $this->getRequest()->getPost();
			
			if($postData['groups']['customer_groups']['fields']) {
				
				if(!isset($postData['groups']['customer_groups']['fields']['select_products'])) {
					
					$result = Mage::helper('skintreports/data')->__("Please select atleast one product to generate report.");
					Mage::app()->getResponse()->setBody($result);
					return;
				
				}
				if($postData['groups']['customer_groups']['fields']['customer_group_start_date']['value']=='' || $postData['groups']['customer_groups']['fields']['customer_group_end_date']['value']=='') {
					
					$result = Mage::helper('skintreports/data')->__("Please select dates to generate the report.");
					Mage::app()->getResponse()->setBody($result);
					return;
				
				}
				
				$modelObj = Mage::getModel('skintreports/reports');
				
				$sktData = $modelObj->getguestcustomerProductData($postData['groups']['customer_groups']['fields']);
				
				if($sktData) {
					
					$fileName   = 'media/downloadable/guestsproducts.csv';
					$seperator  = ";";
					$ln         = "\r\n";
					$completeData = Mage::helper('skintreports/data')->getguestproductsCsvdata($sktData);
					$mage_csv = new Varien_File_Csv(); //mage CSV
					$mage_csv->saveData($fileName, $completeData);
					
					try {
					
						if (! is_file ( $fileName ) || ! is_readable ( $fileName )) {
							
							$result = 3;
							Mage::app()->getResponse()->setBody($result);
						
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
							//readfile ( $fileName );
							
							$result = 1;
							Mage::app()->getResponse()->setBody($result);
							
						}
					
					} catch (Exception $e) {
						
						$result = Mage::helper('skintreports/data')->__($e);
						Mage::app()->getResponse()->setBody($result);
						
					}
					
				} else {
					$result = Mage::helper('skintreports/data')->__("No data found for the requested dates.");
					Mage::app()->getResponse()->setBody($result);
				}
				
			} else {
				$result = Mage::helper('skintreports/data')->__("The request cannot be sent successfully, please try again.");
				Mage::app()->getResponse()->setBody($result);
			}
		}
	}
	
	public function skintoolsexpcustomerdumpAction() {
		
		if ($data = $this->getRequest()->getPost()) {
			
			$postData = $this->getRequest()->getPost();
			
			if($postData['groups']['skintool_group']['fields']) {
			
				$modelObj = Mage::getModel('skintreports/reports');
				
				$sktData = $modelObj->getcustomerDumpData($postData['groups']['skintool_group']['fields']);
				
				if($sktData) {
					
					$fileName   = 'media/downloadable/customersdump.csv';
					$seperator  = ";";
					$ln         = "\r\n";
					$completeData = Mage::helper('skintreports/data')->getcustomerDumpCsvdata($sktData);
					
					//print_r($completeData);
					//exit;
					
					$mage_csv = new Varien_File_Csv(); //mage CSV
					$mage_csv->saveData($fileName, $completeData);
					
					try {
					
						if (! is_file ( $fileName ) || ! is_readable ( $fileName )) {
							
							$result = 3;
							Mage::app()->getResponse()->setBody($result);
						
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
							//readfile ( $fileName );
							
							$result = 1;
							Mage::app()->getResponse()->setBody($result);
							
						}
					
					} catch (Exception $e) {
						
						$result = Mage::helper('skintreports/data')->__($e);
						Mage::app()->getResponse()->setBody($result);
						
					}
					
				} else {
					$result = Mage::helper('skintreports/data')->__("No data found for the requested dates.");
					Mage::app()->getResponse()->setBody($result);
				}
			} else {
				$result = Mage::helper('skintreports/data')->__("The request cannot be sent successfully, please try again.");
				Mage::app()->getResponse()->setBody($result);
			}
		}
		
	}
}