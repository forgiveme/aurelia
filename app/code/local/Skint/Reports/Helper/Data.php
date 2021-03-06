<?php
class Skint_Reports_Helper_Data extends Mage_Core_Helper_Abstract
{
	
	public function getskintoolsCsvdata($sktData) {
		
		$excludedArray = array('entity_id', 'entity_type_id', 'attribute_set_id', 'website_id', 'group_id', 'increment_id', 'store_id', 'is_active', 'disable_auto_group_change', 'id', 'address', 'questiondata_id', 'request_uri');
		
		$csv_output = "";
		$_columns = array("Email address","Customer Since","Updated at","Name","How does it all begin","Most mornings I'm feeling","Morning skincare regime","Usually happy with the results","What you don't love","I'd love to look","How old I look on a good day","How hydrated are you","At 5pm","My stress levels are..","What's in the beauty bag","What matters most","How does your day end at 10pm?","How do you feel at the end of the day?", "Must Have Product", "Recommended Product", "First name", "Last name", "DOB");
		$csv_output[] = $_columns;
		$i = 1;
		
		foreach($sktData as $skintoolDatas) {
			$skintoolData = $skintoolDatas->getData();
			//echo "<pre>";
			//print_r($skintoolData);
			//exit;
			$_data = array();
			foreach($skintoolData as $key => $value){
				if(!in_array($key, $excludedArray)) {
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
					if($key == 'dob') {
						$value = date("Y-m-d", strtotime($value));
					}
					if($key == 'musthave_product') {
						$value = html_entity_decode($value);
					}
					if($key == 'recommended_product') {
						$value = html_entity_decode($value);
					}
					$_data[] = $value;
				}
			}
			$csv_output[] = $_data;
			//echo "<pre>";
			//print_r($csv_output);
			//exit;
			$i++;
		}
		return $csv_output;		
	}
	
	public function getskintoolsorderCsvdata($sktData) {
		
		$excludedArray = array('entity_id', 'entity_type_id', 'attribute_set_id', 'website_id', 'group_id', 'increment_id', 'store_id', 'is_active', 'disable_auto_group_change', 'id', 'address', 'questiondata_id', 'request_uri','orderId', 'customfee_amount');
		
		$csv_output = "";
		$_columns = array("Email address","Customer Since","Updated at","Name","How does it all begin","Most mornings I'm feeling","Morning skincare regime","Usually happy with the results","What you don't love","I'd love to look","How old I look on a good day","How hydrated are you","At 5pm","My stress levels are..","What's in the beauty bag","What matters most","How does your day end at 10pm?","How do you feel at the end of the day?", "Must Have Product", "Recommended Product", "First name", "Last name", "DOB", "Product Ordered");
		$csv_output[] = $_columns;
		$i = 1;
		//echo "<pre>";
		//print_r($sktData);
		//exit;
		foreach($sktData as $skintoolDatas) {
			$productsOrdered = "";
			$skintoolData = $skintoolDatas->getData();
			$customerId = $skintoolData['entity_id'];
			$_data = array();
			
			$orders = Mage::getResourceModel('sales/order_collection')
				->addFieldToSelect('*')
				->addFieldToFilter('customer_id', $customerId)
				->setOrder('created_at', 'desc');
			foreach ($orders as $order) {
				$customFeeAmount = 0;
				$customFeeAmount = $order->getcustomfeeAmount();
				if($customFeeAmount != '0') {
					$orderItems = $order->getItemsCollection();
					foreach ($orderItems as $item){
						$product_name = $item->getName();
						$productsOrdered .= $product_name.",";
					}
				}
				//echo "<br>----".$order->getRealOrderId().'&nbsp;at&nbsp;'.$order->getCreatedAtStoreDate().'&nbsp;('.$order->formatPrice($order->getGrandTotal()).')';
			}
			if(!isset($skintoolData['dob'])) {
				$skintoolData['dob'] = "N/A";
			}
			$skintoolData['products_ordered'] = trim($productsOrdered,',');
			
			foreach($skintoolData as $key => $value){
				if(!in_array($key, $excludedArray)) {
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
					if($key == 'dob') {
						if($value!='N/A')
							$value = date("Y-m-d", strtotime($value));
					}
					if($key == 'musthave_product') {
						$value = html_entity_decode($value);
					}
					if($key == 'recommended_product') {
						$value = html_entity_decode($value);
					}
					$_data[] = $value;
				}
			}
			$csv_output[] = $_data;
			
			$i++;
		}
		//echo "<pre>";
		//print_r($csv_output);
		//exit;
		return $csv_output;		
	}
	
	public function getcustomerproductsCsvdata($sktData) {
		
		$maxOrdercount = $sktData['max_order_count'];
		unset($sktData['max_order_count']);
		
		$csv_output = "";
		$_columns = array("Customer Id", "Customer Name", "DOB", "Date( of first order)", "Date(of most recent order)", "Total No of Orders", "Total Order Amount");
		for($ii = 1; $ii <= $maxOrdercount; $ii++) {
			$_columns[] = "Products on Order ".$ii;
		}
		$_columnspro = array("Product Id", "Product Name", "Customers Ordered");
		//echo "<pre>";
		//print_r($sktData);
		//exit;
		if(isset($sktData[0]['product_id'])) {
			$csv_output[] = $_columnspro;
		} else {
			$csv_output[] = $_columns;
		}
		$i = 1;
		foreach($sktData as $skintoolDatas) {
			$_data = array();
			foreach($skintoolDatas as $key => $value){
				if(is_array($value)) {
					foreach($value as $keyData => $valData) {
						$_data[] = $valData;
					}
				} else {
					
					if($key == 'dob' || $key == 'date_first_order' || $key == 'date_most_recent_order') {
						if($value!='N/A' && $value!='')
							$value = date("Y-m-d", strtotime($value));
					}
					
					$_data[] = $value;
				}
			}
			$csv_output[] = $_data;
			
			$i++;
		}
		return $csv_output;	
		
	}
	public function getguestproductsCsvdata($sktData) {
		
		$csv_output = "";
		$_columns = array("Product Id", "Product Name", "Customers Ordered");
		$csv_output[] = $_columns;
		$i = 1;
		//echo "<pre>";
		//print_r($sktData);
		//exit;
		foreach($sktData as $skintoolDatas) {
			$_data = array();
			foreach($skintoolDatas as $key => $value){
				$_data[] = $value;
			}
			$csv_output[] = $_data;
			
			$i++;
		}
		return $csv_output;
	}
	
	public function getcustomerDumpCsvdata($sktData) {
		
		$excludedArray = array('entity_id', 'entity_type_id', 'attribute_set_id', 'website_id', 'group_id', 'increment_id', 'store_id', 'is_active', 'disable_auto_group_change', 'id', 'address', 'questiondata_id', 'request_uri');
		
		$csv_output = "";
		$_columns = array("Email address","Customer Since","Updated at","Name","How does it all begin","Most mornings I'm feeling","Morning skincare regime","Usually happy with the results","What you don't love","I'd love to look","How old I look on a good day","How hydrated are you","At 5pm","My stress levels are..","What's in the beauty bag","What matters most","How does your day end at 10pm?","How do you feel at the end of the day?", "Must Have Product", "Recommended Product", "Feedback", "Other Brands", "Has Glasses", "First name", "Last name", "Used First Order Discount", "DOB", "Gender", "Company", "Street Address", "City", "Country", "Region", "Postcode");
		$csv_output[] = $_columns;
		$i = 1;

		foreach($sktData as $skintoolDatas) {
						
			$model = Mage::getModel('customer/customer')->load($skintoolDatas->getentityId());
			$customerAddress = $model->getPrimaryBillingAddress();
			$skintoolData = $skintoolDatas->getData();
			if(!isset($skintoolData['dob'])) {
				$skintoolData['dob'] = "N/A";
			}
			if(!isset($skintoolData['gender'])) {
				$skintoolData['gender'] = "N/A";
			}
			//echo "<pre>";
			//print_r($skintoolData);
			//exit;
			$_data = array();
			$_data[0] = $skintoolData['email'];
			$_data[1] = $skintoolData['created_at'];
			$_data[2] = $skintoolData['updated_at'];
			$_data[3] = $skintoolData['question1'];
			$_data[4] = $skintoolData['question2'];
			$_data[5] = ($skintoolData['question3']=='1'?'Ready for bed!':($skintoolData['question3']=='2'?'Balanced':($skintoolData['question3']=='3'?'Ready for life!':'N/A')));
			$_data[6] = ($skintoolData['question4']=='regime1'?'I am yet to start one':($skintoolData['question4']=='regime2'?'Simple and quick':($skintoolData['question4']=='regime3'?'My tried and tested - cleanse, tone, moisturise':($skintoolData['question4']=='regime4'?'The complete routine':'N/A'))));
			$_data[7] = $skintoolData['question5'];
			$_data[8] = $skintoolData['question6'];
			$_data[9] = $skintoolData['question7'];
			$_data[10] = ($skintoolData['question8']=='0'?'Twenties':($skintoolData['question8']=='1'?'Thirties':($skintoolData['question8']=='2'?'Forties':($skintoolData['question8']=='3'?'Older & Wiser':($skintoolData['question8']=='4'?'Teens':'N/A')))));
			$_data[11] = ($skintoolData['question9']!=''?$skintoolData['question9']."%":"N/A");
			$_data[12] = $skintoolData['question10'];
			$_data[13] = ($skintoolData['question11']!=''?$skintoolData['question11']."%":"N/A");
			
			$answer12 = str_Replace("bag1", "All the basics", $skintoolData['question12']);
			$answer12 = str_Replace("bag2", "Ethical & fair trade", $answer12);
			$answer12 = str_Replace("bag3", "Organic / Biorganic", $answer12);
			$answer12 = str_Replace("bag4", "Anti-Ageing/New Science", $answer12);
			$answer12 = str_Replace("bag5", "The latest beauty recommendations", $answer12);
			$answer12 = str_Replace("bag6", "My go to classics", $answer12);
			$answer12 = str_Replace("bag7", "Mostly one brand, but all the products", $answer12);
			$value = trim($answer12, ",");
			
			$_data[14] = $answer12;
			$_data[15] = $skintoolData['question13'];
			$_data[16] = $skintoolData['question14'];
			$_data[17] = ($skintoolData['question15']!=''?$skintoolData['question15']."%":"N/A");
			$_data[18] = ($skintoolData['musthave_product']!=''?html_entity_decode($skintoolData['musthave_product']):"N/A");
			$_data[19] = ($skintoolData['recommended_product']!=''?html_entity_decode($skintoolData['recommended_product']):"N/A");
			$_data[20] = $skintoolData['aurelia_feedback'];
			$_data[21] = $skintoolData['other_brands'];
			$_data[22] = ($skintoolData['has_glasses']=='1'?'Yes':($skintoolData['has_glasses']=='0'?'No':'N/A'));
			$_data[23] = $skintoolData['firstname'];
			$_data[24] = $skintoolData['lastname'];
			$_data[25] = ($skintoolData['used_first_order_discount']=='4'?'Yes':'No');
			$_data[26] = ($skintoolData['dob']!='N/A'? date("Y-m-d", strtotime($skintoolData['dob'])):'N/A');
			$_data[27] = ($skintoolData['gender']=='1'?'Male':($skintoolData['gender']=='2'?'Female':'N/A'));
			if($customerAddress) {
				$_data[28] = ($customerAddress->getCompany()!=''?$customerAddress->getCompany():"N/A");
				
				$streetvalue = "";
				if($customerAddress->getStreet()) {
					foreach($customerAddress->getStreet() as $skey => $sval) {
						$streetvalue .= $sval." ";
					}
				} else {
					$streetvalue = "N/A";
				}
				
				$_data[29] = $streetvalue;
				$_data[30] = ($customerAddress->getCity()?$customerAddress->getCity():"N/A");
				
				$countryId = ($customerAddress->getcountryId()!=''?$customerAddress->getcountryId():"N/A");
				if($countryId != 'N/A') {
					$countryName = Mage::app()->getLocale()->getCountryTranslation($countryId);
				}
				$_data[31] = $countryName;
				$_data[32] = ($customerAddress->getRegion()!=''?$customerAddress->getRegion():"N/A");;
				$_data[33] = ($customerAddress->getPostcode()!=''?$customerAddress->getPostcode():"N/A");
			}
			/*foreach($skintoolData as $key => $value){
				if(!in_array($key, $excludedArray)) {
					if($key == 'question3') {
						$value = ($value=='1'?'Ready for bed!':($value=='2'?'Balanced':($value=='3'?'Ready for life!':'N/A')));
					}
					if($key == 'question4') {
						$value = ($value=='regime1'?'I am yet to start one':($value=='regime2'?'Simple and quick':($value=='regime3'?'My tried and tested - cleanse, tone, moisturise':($value=='regime4'?'The complete routine':'N/A'))));
					}
					if($key == 'question8') {
						$value = ($value=='0'?'Twenties':($value=='1'?'Thirties':($value=='2'?'Forties':($value=='3'?'Older & Wiser':($value=='4'?'Teens':'N/A')))));
					}
					if(in_array($key, array('question9', 'question11', 'question15'))) {
						$value = ($value!=''?$value."%":"N/A");
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
					if($key == 'dob') {
						if($skintoolData['dob']!='N/A')
							$value = date("Y-m-d", strtotime($skintoolData['dob']));
					}
					if($key == 'has_glasses') {
						$value = ($value=='1'?'Yes':($value=='0'?'No':'N/A'));
					}
					if($key == 'gender') {
						$value = ($skintoolData['gender']=='1'?'Male':($skintoolData['gender']=='2'?'Female':'N/A'));
					}
					if($key == 'used_first_order_discount') {
						$value = ($skintoolData['used_first_order_discount']=='4'?'Yes':'No');
					}
					if($key == 'street') {
						$tempval = "";
						foreach($value as $skey => $sval) {
							$tempval .= $sval." ";
						}
						$value = $tempval;
					}
					if($key == 'country' && $value != 'N/A') {
						$value = Mage::app()->getLocale()->getCountryTranslation($value);
					}
					if($key == 'company' && $value == '') {
						$value = 'N/A';
					}
					$_data[] = $value;
				}
			}*/
			$csv_output[] = $_data;
			//echo "<pre>";
			//print_r($csv_output);
			//exit;
			$i++;
		}
		return $csv_output;
		
	}
}