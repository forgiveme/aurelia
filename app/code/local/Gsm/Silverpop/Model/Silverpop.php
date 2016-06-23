<?php

class Gsm_Silverpop_Model_Silverpop extends Mage_Core_Model_Abstract {
	public function _construct() {
		parent::_construct();
		$this->_init('silverpop/silverpop');
	}

	public function isSilverpopEnabled() {
		$return = False;

		$enabled = Mage::getStoreConfig('silverpop/silverpop/enabled');
		$host = Mage::getStoreConfig('silverpop/silverpop/spop_host');
		$username = Mage::getStoreConfig('silverpop/silverpop/spop_username');
		$password = Mage::getStoreConfig('silverpop/silverpop/spop_password');
		$user_table_id = Mage::getStoreConfig('silverpop/silverpop/spop_user_table_id');
		$order_table_id = Mage::getStoreConfig('silverpop/silverpop/spop_order_table_id');

		if ($enabled == '1' && $host !== '' && $username !== '' && $password !== '' && $user_table_id !== '' && $order_table_id !== '') {
			$return = True;
		}
		else {
			$return = False;
		}

		return $return;
	}

	/**
	 * Function to strip the HTML headers from the string.
	 *
	 * @return Contents minus the HTML headers.
	 * @author Masood Ahmed
	 **/
	protected function _stripHeader($source) {
		list($header, $content) = explode("<Envelope>", $source);
		$content = "<Envelope>" . $content;
		list($content, $footer) = explode("</Envelope>", $content);
		$content .= "</Envelope>";

		// $_filename = Mage::getBaseDir() . '/var/silverpop/header-' . time() . '.xml';
		// $fp = fopen($_filename, "w");
		// fwrite($fp, $content);
		// fclose($fp);

		return $content;
	}

	/**
	 * Function to login into silverpop.
	 *
	 * @return Session ID to be used in all subsequent Silverpop API calls.
	 * @author Masood Ahmed
	 **/
	protected function _loginToSilverPop ($host, $servlet, $username, $password) {
		$sock = fsockopen ($host, "80", $errno, $errstr, "20"); // open socket on port 80 w/ timeout of 20
		$data = "xml=<?xml version=\"1.0\"?>
		<Envelope>
			<Body>
				<Login>
					<USERNAME>".$username."</USERNAME>
					<PASSWORD>".$password."</PASSWORD>
				</Login>
			</Body>
		</Envelope>";

		if (!$sock) {
			print("Could not connect to host:". $errno . $errstr);
			return (false);
		}

		$size = strlen ($data);
		fputs ($sock, "POST /servlet/" . $servlet . " HTTP/1.1\n");
		fputs ($sock, "Host: " . $host . "\n");
		fputs ($sock, "Content-type: application/x-www-form-urlencoded\n");
		fputs ($sock, "Content-length: " . $size . "\n");
		fputs ($sock, "Connection: close\n\n");
		fputs ($sock, $data);
		$buffer = "";

		while (!feof ($sock)) {
			$buffer .= fgets ($sock);
		}

		fclose ($sock);

		$buffer = $this->_stripHeader($buffer);

		$xml = simplexml_load_string($buffer);

		// print_r($xml);
		$session_id = $xml->Body->RESULT->SESSIONID;

		return ($session_id);
	}

	/**
	 * Function to log out of silverpop
	 *
	 * @return void
	 * @author Masood Ahmed
	 **/
	protected function _logoutFromSilverPop ($host, $session_id, $servlet="XMLAPI", $port=80, $time_out=20) {
		$servlet = $servlet . ";jsessionid=" . $session_id;
		$sock = fsockopen ($host, "80", $errno, $errstr, "20"); // open socket on port 80 w/ timeout of 20
		$data = "xml=<?xml version=\"1.0\"?>
		<Envelope>
			<Body>
				<Logout/>
			</Body>
		</Envelope>";

		if (!$sock) {
			print("Could not connect to host:". $errno . $errstr);
			return (false);
		}

		$size = strlen ($data);
		fputs ($sock, "POST /servlet/" . $servlet . " HTTP/1.1\n");
		fputs ($sock, "Host: " . $host . "\n");
		fputs ($sock, "Content-type: application/x-www-form-urlencoded\n");
		fputs ($sock, "Content-length: " . $size . "\n");
		fputs ($sock, "Connection: close\n\n");
		fputs ($sock, $data);
		$buffer = "";
		$buffer = ""; while (!feof ($sock)) {
			$buffer .= fgets ($sock);
		}

		fclose ($sock);
	}

	/**
	 * Function to send data (xml) to silverpop. In this function we first login to silverpop,
	 * then we send the data (xml) to silverpop, after that we logout from silverpop.
	 *
	 * @return Response we get when we send data (xml) to silverpop.
	 * @author Masood Ahmed
	 **/
	public function sendToSilverpop($data) {
		$servlet = "XMLAPI";
		$host = Mage::getStoreConfig('silverpop/silverpop/spop_host');
		$username = Mage::getStoreConfig('silverpop/silverpop/spop_username');
		$password = Mage::getStoreConfig('silverpop/silverpop/spop_password');
		$port = 80;
		$time_out=20;

		$session_id = $this->_loginToSilverPop($host, $servlet, $username, $password);

		$servlet = $servlet . ";jsessionid=" . $session_id;
		$sock = fsockopen ($host, $port, $errno, $errstr, $time_out);

		if (!$sock) {
			print("Could not connect to host:". $errno . $errstr);
			return (false);
		}

		$size = strlen ($data);
		fputs ($sock, "POST /servlet/" . $servlet . " HTTP/1.1\n");
		fputs ($sock, "Host: " . $host . "\n");
		fputs ($sock, "Content-type: application/x-www-form-urlencoded\n");
		fputs ($sock, "Content-length: " . $size . "\n");
		fputs ($sock, "Connection: close\n\n");
		fputs ($sock, $data);
		$buffer = "";
		while (!feof ($sock)) {
			$buffer .= fgets ($sock);
		}

		fclose($sock);

		$this->_logoutFromSilverPop($host, $session_id, $servlet, "80", "20");

		// $_filename = Mage::getBaseDir() . '/var/silverpop/debug-' . time() . '.xml';
		// $fp = fopen($_filename, "w");
		// fwrite($fp, $buffer);
		// fclose($fp);

		$buffer = $this->_stripHeader($buffer);

		return $buffer;
	}

	/**
	 * Function to add user entry into silverpop user database.
	 *
	 * @return Response from silverpop.
	 * @author Masood Ahmed
	 **/
	protected function _createUser($cust_data) {
		if ($cust_data == null) {
			return;
		}

		$data = "xml=<?xml version=\"1.0\"?>
		<Envelope>
			<Body>
				<AddRecipient>
					<LIST_ID>" . Mage::getStoreConfig('silverpop/silverpop/spop_user_table_id') ."</LIST_ID>
					<CREATED_FROM>2</CREATED_FROM>
					<COLUMN>
						<NAME>Email</NAME>
						<VALUE>" . $cust_data['Email'] . "</VALUE>
					</COLUMN>
					<COLUMN>
						<NAME>Forename</NAME>
						<VALUE>" . $cust_data['First Name'] . "</VALUE>
					</COLUMN>
					<COLUMN>
						<NAME>Created On</NAME>
						<VALUE>" . date('m/d/Y H:i:s') . "</VALUE>
					</COLUMN>
					<COLUMN>
						<NAME>Guest</NAME>
						<VALUE>" . $cust_data['Guest'] . "</VALUE>
					</COLUMN>
					<COLUMN>
						<NAME>Newsletter</NAME>
						<VALUE>" . $cust_data['Newsletter'] . "</VALUE>
					</COLUMN>
					<COLUMN>
						<NAME>Telephone</NAME>
						<VALUE>" . $cust_data["Phone"] . "</VALUE>
					</COLUMN>
					<COLUMN>
						<NAME>Registered</NAME>
						<VALUE>" . $cust_data["Registered"] . "</VALUE>
					</COLUMN>
					<COLUMN>
						<NAME>Surname</NAME>
						<VALUE>" . $cust_data["Surname"] . "</VALUE>
					</COLUMN>
					<COLUMN>
						<NAME>Updated On</NAME>
						<VALUE>" . date('m/d/Y H:i:s') . "</VALUE>
					</COLUMN>
				</AddRecipient>
			</Body>
		</Envelope>";

		$result = $this->sendToSilverpop($data);

		return $result;
	}

	/**
	 * Function to check whether the user exists.
	 *
	 * @return True if user exists, false if not present.
	 * @author Masood Ahmed
	 **/
	public function _checkUserExists($email) {
		$data = "xml=<?xml version=\"1.0\"?>
		<Envelope>
			<Body>
				<SelectRecipientData>
					<LIST_ID>" . Mage::getStoreConfig('silverpop/silverpop/spop_user_table_id') . "</LIST_ID>
					<EMAIL>" . $email . "</EMAIL>
				</SelectRecipientData>
			</Body>
		</Envelope>";

		$result = $this->sendToSilverpop($data);
		$xml = simplexml_load_string($result);

		$result = (string)$xml->Body->RESULT->SUCCESS;
		$result = strtolower($result);

		if ($result == "true") {
			$return = true;
		}
		else {
			$return = false;
		}

		return $return;
	}

	public function checkUserExists($email) {
		return $this->_checkUserExists($email);
	}

	/**
	 * Function to update the user.
	 *
	 * @return Response from silverpop.
	 * @author Masood Ahmed
	 **/
	protected function _updateUser($cust_data) {
		if (!isset($cust_data['OLD_EMAIL'])) {
			$cust_data['OLD_EMAIL'] = $cust_data['Email'];
		}

		$data = "xml=<?xml version=\"1.0\"?>
		<Envelope>
			<Body>
				<UpdateRecipient>
					<LIST_ID>" . Mage::getStoreConfig('silverpop/silverpop/spop_user_table_id') ."</LIST_ID>
					<CREATED_FROM>2</CREATED_FROM>
					<OLD_EMAIL>" . $cust_data['OLD_EMAIL'] . "</OLD_EMAIL>
					<COLUMN>
						<NAME>Email</NAME>
						<VALUE>" . $cust_data['Email'] . "</VALUE>
					</COLUMN>
					<COLUMN>
						<NAME>Forename</NAME>
						<VALUE>" . $cust_data['First Name'] . "</VALUE>
					</COLUMN>
					<COLUMN>
						<NAME>Guest</NAME>
						<VALUE>" . $cust_data['Guest'] . "</VALUE>
					</COLUMN>
					<COLUMN>
						<NAME>Telephone</NAME>
						<VALUE>" . $cust_data["Phone"] . "</VALUE>
					</COLUMN>
					<COLUMN>
						<NAME>Registered</NAME>
						<VALUE>" . $cust_data["Registered"] . "</VALUE>
					</COLUMN>
					<COLUMN>
						<NAME>Surname</NAME>
						<VALUE>" . $cust_data["Surname"] . "</VALUE>
					</COLUMN>
					<COLUMN>
						<NAME>Updated On</NAME>
						<VALUE>" . date('m/d/Y H:i:s') . "</VALUE>
					</COLUMN>
				</UpdateRecipient>
			</Body>
		</Envelope>";

		$result = $this->sendToSilverpop($data);

		return $result;
	}

	/**
	 * Function to update silverpop entry for registered user. In this function we gather
	 * required data from the customer for silverpop and call updateUser function. If old_email
	 * is specified then we update the email address entry in the silverpop also.
	 *
	 * @return Response from silverpop
	 * @author Masood Ahmed
	 **/
	public function updateRegisteredAccount($_customer, $old_email=null) {
		$customer = Mage::getModel('customer/customer')->load($_customer->getId());
		$cust_array = $customer->toArray();

		$cust_data['Email'] = $cust_array['email'];
		$cust_data['First Name'] = ucwords($cust_array['firstname']);
		$cust_data['Guest'] = 'No';
		$cust_data["Registered"] = 'Yes';
		$cust_data["Surname"] = ucwords($cust_array['lastname']);

		$_address_id = $cust_array['default_billing'];

		if ($_address_id == NULL) {
			$_address_id = $cust_array['default_shipping'];
		}

		if ($_address_id == NULL) {
			$cust_data["Phone"] = '';
		}
		else {
			$_address = Mage::getModel('customer/address')->load($_address_id);
			$_address_array = $_address->toArray();
			$cust_data["Phone"] = $_address_array['telephone'];
		}

		if ($old_email !== $cust_data['Email'] && $old_email != null) {
			$cust_data['OLD_EMAIL'] = $old_email;
		}

		$result[0] = $this->_updateUser($cust_data);

		return $result;
	}

	/**
	 * Function to create silverpop entry for registered user. In this function we gather
	 * silverpop required data from the customer and call createUser function. If the user exists
	 * (as guest users), then we update the user instead of creating.
	 *
	 * @return Response from silverpop.
	 * @author Masood Ahmed
	 **/
	public function createRegisteredAccount($_customer, $opt=false) {
		$customer = Mage::getModel('customer/customer')->load($_customer->getId());
		$cust_array = $customer->toArray();

		$cust_data['Email'] = $cust_array['email'];
		$cust_data['First Name'] = ucwords($cust_array['firstname']);
		$cust_data['Guest'] = 'No';
		$cust_data["Registered"] = 'Yes';
		$cust_data["Surname"] = ucwords($cust_array['lastname']);

		if ($opt == NULL) {
			$newsletter_subscriber = Mage::getModel('newsletter/subscriber')->loadByEmail($cust_array['email']);

			if ($newsletter_subscriber->getSubscriberStatus() == 1) {
				$opt = true;
			}
			else {
				$opt = false;
			}
		}

		if ($opt) {
			$cust_data['Newsletter'] = 'Yes';
		}
		else {
			$cust_data['Newsletter'] = 'No';
		}

		$_address_id = $cust_array['default_billing'];

		if ($_address_id == NULL) {
			$_address_id = $cust_array['default_shipping'];
		}

		if ($_address_id == NULL) {
					$cust_data["Phone"] = '';
		}
		else {
			$_address = Mage::getModel('customer/address')->load($_address_id);
			$_address_array = $_address->toArray();
			$cust_data["Phone"] = $_address_array['telephone'];
		}

		if ($this->_checkUserExists($cust_data['Email'])) {
			$result[0] = $this->_updateUser($cust_data);

			if ($opt) {
				$this->setSubscribed($opt, $cust_data['Email']);
			}
			else {
				$this->setSubscribed($opt, $cust_data['Email']);
			}
		}
		else {
			$result[0] = $this->_createUser($cust_data);
		}

		return $result;
	}

	/**
	 * Function to create order entry in silverpop.
	 *
	 * @return Response from silverpop.
	 * @author Masood Ahmed
	 **/
	protected function _createOrder($_orders) {
		$data = 'xml=<?xml version="1.0"?>
			<Envelope>
				<Body>
					<InsertUpdateRelationalTable>
						<TABLE_ID>' . Mage::getStoreConfig('silverpop/silverpop/spop_order_table_id') . '</TABLE_ID>
						<ROWS>
						';

		foreach($_orders AS $_order) {
			$item_name = str_replace("&", "and", $_order['Item Name']);
			$item_name = str_replace("%", "percent", $item_name);
			$item_name = str_replace("!", "exclamation", $item_name);
			$item_name = str_replace("@", "at", $item_name);
			$item_name = str_replace("#", "hash", $item_name);
			$item_name = str_replace("$", "dollor", $item_name);
			$item_name = str_replace("^", "caret", $item_name);
			$item_name = str_replace("*", "star", $item_name);
			$item_name = str_replace("(", "bracket", $item_name);
			$item_name = str_replace(")", "bracket", $item_name);

			$data .= '
							<ROW>
								<COLUMN name="Created On"><![CDATA[' . date('m/d/Y H:i:s') . ']]></COLUMN>
								<COLUMN name="Customer ID"><![CDATA[' . $_order['Customer ID'] . ']]></COLUMN>
								<COLUMN name="Email Address"><![CDATA[' . $_order['Email Address']. ']]></COLUMN>
								<COLUMN name="Item Name"><![CDATA[' . $item_name . ']]></COLUMN>
								<COLUMN name="Item Price"><![CDATA[' . $_order['Item Price'] . ']]></COLUMN>
								<COLUMN name="Item Quantity"><![CDATA[' . $_order['Item Quantity'] . ']]></COLUMN>
								<COLUMN name="Item SKU"><![CDATA[' . $_order['Item SKU'] . ']]></COLUMN>
								<COLUMN name="jTime"><![CDATA[' . $_order['jTime'] . ']]></COLUMN>
								<COLUMN name="Order Date"><![CDATA[' . date('m/d/Y', strtotime($_order['Order Date'])) . ']]></COLUMN>
								<COLUMN name="Order ID"><![CDATA[' . $_order['Order ID'] . ']]></COLUMN>
								<COLUMN name="Updated On"><![CDATA[' . date('m/d/Y H:i:s') . ']]></COLUMN>
							</ROW>
							';
		}

		$data .= '
						</ROWS>
					</InsertUpdateRelationalTable>
				</Body>
			</Envelope>
		';

		$result = $data . "\n\n";
		$result .= $this->sendToSilverpop($data);

		return $result;
	}

	public function createUser($cust_data) {
		return $this->_createUser($cust_data);
	}

	/**
	 * Function to process guest order. We parse the order and call the function to create
	 * guest user entry and order entry in silverpop
	 *
	 * @return void
	 * @author Masood Ahmed
	 **/
	public function processGuestOrder($order, $opt=false, $order_push=true) {
		$billing_address = $order->getBillingAddress();
		$billing_array = $billing_address->toArray();

		$cust_data['Email'] = $billing_array['email'];
		$cust_data['First Name'] = ucwords($billing_array['firstname']);
		$cust_data['Guest'] = 'Yes';
		$cust_data["Registered"] = 'No';
		$cust_data["Surname"] = ucwords($billing_array['lastname']);
		$cust_data["Phone"] = $billing_array['telephone'];

		$newsletter_subscriber = Mage::getModel('newsletter/subscriber')->loadByEmail($billing_array['email']);

		if ($newsletter_subscriber->getSubscriberStatus() == 1) {
			$opt = true;
		}
		else {
			$opt = false;
		}

		if ($opt) {
			$cust_data['Newsletter'] = 'Yes';
		}
		else {
			$cust_data['Newsletter'] = 'No';
		}

		if ($this->_checkUserExists($cust_data['Email'])) {
			$result[0] = $this->_updateUser($cust_data);

			if ($opt) {
				$this->setSubscribed($opt, $cust_data['Email']);
			}
			else {
				$this->setSubscribed($opt, $cust_data['Email']);
			}
		}
		else {
			$result[0] = $this->_createUser($cust_data);
		}

		if (!$order_push) {
			return $result;
		}

		$_order_array = $order->toArray();
		$_items = $order->getAllItems();

		$i = 0;
		$_orders = null;

		foreach ($_items as $itemId => $item) {
			if ($item->getParentItem()) {
				continue;
			}

			$_orders[$i]['Customer ID'] = "Guest";
			$_orders[$i]['Email Address'] = $billing_array['email'];
			$_orders[$i]['Item Name'] = (string)$item->getName();
			$_orders[$i]['Item Price'] = number_format($item->getPriceInclTax(), 2);
			$_orders[$i]['Item Quantity'] = number_format($item->getQtyOrdered(), 0);
			$_orders[$i]['Item SKU'] = (string)$item->getSku();
			$_orders[$i]['jTime'] = $_order_array['increment_id'] . '-' . (string)$item->getSku();
			$_orders[$i]['Order Date'] = $_order_array['created_at'];
			$_orders[$i]['Order ID'] = $_order_array['increment_id'];

			// $product_options = $item->getProductOptions();
			//
			// if (isset($product_options['attributes_info'])) {
			// 	foreach($product_options['attributes_info'] AS $attr_info) {
			// 		$_orders[$i]['Item Name'] .= '<br /><strong>' . $attr_info['label'] . ": </strong>" . $attr_info['value'];
			// 	}
			// }

			$i++;
		}

		$result[2] = $this->_createOrder($_orders);

		// $filename = Mage::getBaseDir() .'/debug.xml';
		// $fh = fopen($filename, 'w');
		// fwrite($fh, $result[2]);
		// fclose($fh);

		return $result;
	}

	/**
	 * Function to process registered order. We parse the order and call the function to create
	 * registered user entry and order entry in silverpop
	 *
	 * @return void
	 * @author Masood Ahmed
	 **/
	public function processRegisteredOrder($order, $opt=NULL, $order_push=true) {
		$_order_array = $order->toArray();

		$_customer = Mage::getModel('customer/customer')->load($_order_array['customer_id']);

		$result[0] = $this->createRegisteredAccount($_customer, $opt);

		if (!$order_push) {
			return $result;
		}

		$_items = $order->getAllItems();

		$i = 0;
		$_orders = null;

		foreach ($_items as $itemId => $item) {
			if ($item->getParentItem()) {
				continue;
			}

			$_orders[$i]['Customer ID'] = (string)$_customer->getId();
			$_orders[$i]['Email Address'] = (string)$_customer->getEmail();
			$_orders[$i]['Item Name'] = (string)$item->getName();
			$_orders[$i]['Item Price'] = number_format($item->getPriceInclTax(), 2);
			$_orders[$i]['Item Quantity'] = number_format($item->getQtyOrdered(), 0);
			$_orders[$i]['Item SKU'] = (string)$item->getSku();
			$_orders[$i]['jTime'] = $_order_array['increment_id'] . '-' . (string)$item->getSku();
			$_orders[$i]['Order Date'] = $_order_array['created_at'];
			$_orders[$i]['Order ID'] = $_order_array['increment_id'];

			$i++;
		}

		$result[2] = $this->_createOrder($_orders);

		// $filename = Mage::getBaseDir() .'/debug.xml';
		// $fh = fopen($filename, 'w');
		// fwrite($fh, $result[2]);
		// fclose($fh);

		return $result;
	}

	/**
	 * Function to check whether the user is subscribed to newletter or not.
	 *
	 * @return True if he is subscribed or else false.
	 * @author Masood Ahmed
	 **/
	public function getIsSubscribed()
	{
		$return = false;

		$_email = Mage::getSingleton('customer/session')->getCustomer()->getEmail();

		$data = "xml=<?xml version=\"1.0\"?>
		<Envelope>
			<Body>
				<SelectRecipientData>
					<LIST_ID>" . Mage::getStoreConfig('silverpop/silverpop/spop_user_table_id') . "</LIST_ID>
					<EMAIL>" . $_email . "</EMAIL>
				</SelectRecipientData>
			</Body>
		</Envelope>";

		$result = $this->sendToSilverpop($data);
		$xml = simplexml_load_string($result);

		$_columns = $xml->Body->RESULT->COLUMNS;

		foreach($_columns->COLUMN AS $_column) {
			if ((string)$_column->NAME === "Newsletter") {
				$_value = (string)$_column->VALUE;

				if ($_value === "Yes") {
					$return = true;
				}
				else {
					$return = false;
				}
			}
		}

		return $return;
	}

	/**
	 * Function to set the subscription status in silverpop.
	 *
	 * @return Response from silverpop.
	 * @author Masood Ahmed
	 **/
	public function setSubscribed($opt=false, $email=NULL) {
		if ($email == NULL) {
			$_email = Mage::getSingleton('customer/session')->getCustomer()->getEmail();
		}
		else {
			$_email = $email;
		}

		if ($opt) {
			$_value = "Yes";
		}
		else {
			$_value = "No";
		}

		$data = "xml=<?xml version=\"1.0\"?>
		<Envelope>
			<Body>
				<UpdateRecipient>
					<LIST_ID>" . Mage::getStoreConfig('silverpop/silverpop/spop_user_table_id') ."</LIST_ID>
					<CREATED_FROM>2</CREATED_FROM>
					<OLD_EMAIL>" . $_email . "</OLD_EMAIL>
					<COLUMN>
						<NAME>Newsletter</NAME>
						<VALUE>" . $_value . "</VALUE>
					</COLUMN>
				</UpdateRecipient>
			</Body>
		</Envelope>";

		$result = $this->sendToSilverpop($data);

		return $result;
	}
}
