<?php
/**
 * Silverpop Customer account controller overrides
 *
 * @category	 Silverpop
 * @package		 Gsm_Silverpop
 * @author		 Masood Ahmed
 */
require_once('Mage/Customer/controllers/AccountController.php');

class Gsm_Silverpop_Customer_AccountController extends Mage_Customer_AccountController {
	/**
	 * Change customer password action
	 */
	public function editPostAction() {
		if (Mage::getModel('silverpop/silverpop')->isSilverpopEnabled()) {
			if (!$this->_validateFormKey()) {
				return $this->_redirect('*/*/edit');
			}

			if ($this->getRequest()->isPost()) {
				/** @var $customer Mage_Customer_Model_Customer */
				$customer = $this->_getSession()->getCustomer();

				/** @var $customerForm Mage_Customer_Model_Form */
				$customerForm = Mage::getModel('customer/form');
				$customerForm->setFormCode('customer_account_edit')
					->setEntity($customer);

				$customerData = $customerForm->extractData($this->getRequest());

				$errors = array();
				$customerErrors = $customerForm->validateData($customerData);
				if ($customerErrors !== true) {
					$errors = array_merge($customerErrors, $errors);
				} else {
					$customerForm->compactData($customerData);
					$errors = array();

					// If password change was requested then add it to common validation scheme
					if ($this->getRequest()->getParam('change_password')) {
						$currPass		= $this->getRequest()->getPost('current_password');
						$newPass		= $this->getRequest()->getPost('password');
						$confPass		= $this->getRequest()->getPost('confirmation');

						$oldPass = $this->_getSession()->getCustomer()->getPasswordHash();
						if (Mage::helper('core/string')->strpos($oldPass, ':')) {
							list($_salt, $salt) = explode(':', $oldPass);
						} else {
							$salt = false;
						}

						if ($customer->hashPassword($currPass, $salt) == $oldPass) {
							if (strlen($newPass)) {
								/**
								 * Set entered password and its confirmation - they
								 * will be validated later to match each other and be of right length
								 */
								$customer->setPassword($newPass);
								$customer->setConfirmation($confPass);
							} else {
								$errors[] = $this->__('New password field cannot be empty.');
							}
						} else {
							$errors[] = $this->__('Invalid current password');
						}
					}

					// Validate account and compose list of errors if any
					$customerErrors = $customer->validate();
					if (is_array($customerErrors)) {
						$errors = array_merge($errors, $customerErrors);
					}
				}

				if (!empty($errors)) {
				$this->_getSession()->setCustomerFormData($this->getRequest()->getPost());
				foreach ($errors as $message) {
					$this->_getSession()->addError($message);
				}
				$this->_redirect('*/*/edit');
				return $this;
			}

			try {
				$customer->setConfirmation(null);
				$customer->save();

				$silverpop = Mage::getModel('silverpop/silverpop');
				$silverpop->updateRegisteredAccount($customer, $_POST['email']);

				$this->_getSession()->setCustomer($customer)
					->addSuccess($this->__('The account information has been saved.'));

				$this->_redirect('customer/account');
				return;
				} catch (Mage_Core_Exception $e) {
				$this->_getSession()->setCustomerFormData($this->getRequest()->getPost())
					->addError($e->getMessage());
				} catch (Exception $e) {
				$this->_getSession()->setCustomerFormData($this->getRequest()->getPost())
					->addException($e, $this->__('Cannot save the customer.'));
				}
			}

			$this->_redirect('*/*/edit');
		}
		else {
			return parent::editPostAction();
		}
	}
}
