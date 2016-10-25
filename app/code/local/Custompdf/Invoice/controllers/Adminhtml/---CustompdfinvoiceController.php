<?php
include('lib/Mpdf/mpdf.php');

class Custompdf_Invoice_Adminhtml_CustompdfinvoiceController extends Mage_Adminhtml_Controller_Action
{
	public function printpdfAction() {
		
		if ($data = $this->getRequest()->getParams()) {
			
			$params = $this->getRequest()->getParams();
			
			if($params['order_id']) {
				
				$order = Mage::getModel('sales/order')->load($params['order_id']);
				$Incrementid = $order->getIncrementId();
				
				$block = $this->getLayout()->createBlock('custompdfinvoice/adminhtml_custompdf');
				$block->setTemplate('custompdf/invoice/data.phtml');

				// Render the template to the browser
				$html = $block->toHtml();
				
				//echo $html;
				//exit;
				
				$mpdf=new mPDF('', '', '', 'AvenirLT', 8,8,8,8);
				
				$mpdf->SetDisplayMode('fullpage');
				
				$mpdf->WriteHTML($html);
				
				if($params['view_type'] == '1') {
					echo $html;
				} else if($params['view_type'] == '2') {
					$mpdf->Output();
				} else {
					$mpdf->Output('Orderinvoice-'.$Incrementid.'.pdf', 'D');
				}
				//$mpdf->Output('Orderinvoice.pdf', 'D');
				//$mpdf->Output();
				
				exit;
			}
		}
	}
}