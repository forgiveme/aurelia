<?php

class Stardigital_Vouchers_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $_voucherLength = 8;

    protected function _generateUniqueVoucherCode($length = null)
    {
        if (empty($length)) {
            $length = $this->_voucherLength;
        }

        $rndId = crypt(uniqid(rand(),1));
        $rndId = strip_tags(stripslashes($rndId));
        $rndId = str_replace(array(".", "$"),"",$rndId);
        $rndId = strrev(str_replace("/","",$rndId));

        if (!is_null($rndId)) {
            $voucherCode = strtoupper(substr($rndId, 0, $length));
        } else {
            $voucherCode = strtoupper($rndId);
        }

        return $voucherCode;
    }

    protected function _voucherAlreadyExists($voucherCode)
    {
        $voucher = Mage::getModel('vouchers/vouchers')
            ->getCollection()
            ->addFieldToFilter(
                'code',
                array(
                    'eq' => $voucherCode
                )
            )
            ->getFirstItem();

        if ($voucher->getId()) {
            return true;
        }

        return false;
    }

    protected function _generateVoucherModel($voucherCode, $email, $orderId, $voucherPrice, $voucherFromDate, $voucherToDate, $voucherType, $ruleId, $productId = null)
    {
        $voucher = Mage::getModel('vouchers/vouchers');
        $voucher->setCode($voucherCode);
        $voucher->setCustomerEmail($email);
        $voucher->setOrderId($orderId);
        $voucher->setValue($voucherPrice);
        $voucher->setFromDate($voucherFromDate);
        $voucher->setToDate($voucherToDate);
        $voucher->setType($voucherType);
        $voucher->setRuleId($ruleId);
        $voucher->setProductId($productId);
        $voucher->save();

        return $voucher;
    }

    protected function _generateVoucher($order, $item, $voucherPrice, $voucherCode, $voucherType, $counter)
    {
        $customerGroups = Mage::getModel('customer/group')->getCollection();
        $customerGroupIds = array();

        foreach ($customerGroups AS $customerGroup) {
            $customerGroupIds[] = $customerGroup->getId();
        }

        $voucherName = $order->getIncrementId() . ' - ' . $item->getSku() . ' - ' . ($counter + 1);

        $description = 'Coupon for voucher purchased in order : ' . $order->getIncrementId() . '. Value of the voucher is : ' . $voucherPrice;

        $voucherFromDate = date('Y-m-d');
        $voucherToDate = date('Y-m-d', strtotime("+12 months"));

        $model = Mage::getModel('salesrule/rule');
        $model->setName($voucherName);
        $model->setDescription($description);
        $model->setUsesPerCoupon(1);
        $model->setUsesPerCustomer(1);
        $model->setCustomerGroupIds(implode(',', $customerGroupIds));
        $model->setIsActive(1);
        $model->setStopRulesProcessing(0);
        $model->setIsAdvanced(1);
        $model->setSortOrder('0');
        $model->setSimpleAction('cart_fixed');
        $model->setDiscountAmount($voucherPrice);
        $model->setDiscountStep(0);
        $model->setSimpleFreeShipping(0);
        $model->setApplyToShipping(1);
        $model->setCouponType(2);
        $model->setCouponCode($voucherCode);
        $model->setUsesPerCoupon(1);
        $model->setTimesUsed(0);
        $model->setIsRss(0);
        $model->setWebsiteIds('1');
        $model->setFromDate($voucherFromDate);
        $model->setToDate($voucherToDate);
        $model->save();

        $voucher = $this->_generateVoucherModel(
            $voucherCode,
            $order->getCustomerEmail(),
            $order->getId(),
            $voucherPrice,
            $voucherFromDate,
            $voucherToDate,
            $voucherType,
            $model->getId(),
            $item->getProductId()
        );

        return array(
            'couponCode' => $voucherCode,
            'ruleName' => $voucherName,
            'fromDate' => $voucherFromDate,
            'toDate' => $voucherToDate,
        );
    }

    public function generateVouchers($order)
    {
        if ($order->getVoucherGenerated() == 1) {
            return false;
        }

        $voucherGenerated = false;

        $giftCardDetails = array();

        $voucherSkus = array(
            'GIFTCARD35',
            'GIFTCARD50',
            'GIFTCARD100',
        );

        foreach ($order->getAllItems() AS $item) {
            if (in_array($item->getSku(), $voucherSkus)) {
                $product = Mage::getModel('catalog/product')->load($item->getProductId());
                $voucherPrice = $product->getPrice();

                for($counter = 0; $counter < $item->getQtyOrdered(); $counter++) {
                    $voucherType = 'virtual_and_physical';

                    $voucherCode = $this->_generateUniqueVoucherCode($this->_voucherLength);

                    while($this->_voucherAlreadyExists($voucherCode)) {
                        $voucherCode = $this->_generateUniqueVoucherCode($this->_voucherLength);
                    }

                    $giftCardDetails[] = $this->_generateVoucher(
                        $order,
                        $item,
                        $voucherPrice,
                        $voucherCode,
                        $voucherType,
                        $counter
                    );

                    $voucherGenerated = true;
                }
            }
        }

        if ($voucherGenerated) {
            $order->setVoucherGenerated(1);
            $order->save();

            Mage::getModel('vouchers/silverpop')->createGiftCard($order, $giftCardDetails);

            return true;
        }

        return false;
    }

    public function getOrderVouchers($order)
    {
        $vouchers = array();

        $collection = Mage::getModel('vouchers/vouchers')
            ->getCollection()
            ->addFieldToFilter(
                'order_id',
                array(
                    'eq' => $order->getId(),
                )
            );

        foreach ($collection AS $record) {
            $rule = Mage::getModel('salesrule/rule')->load($record->getRuleId());
            $temp = array();
            $temp['code'] = $record->getCode();
            $temp['value'] = $rule->getDiscountAmount();
            $temp['expiry'] = $record->getToDate();
            $temp['product_id'] = $record->getProductId();

            $vouchers[] = $temp;
        }

        return $vouchers;
    }

    public function generateCustomerVouchers($rule)
    {
        $rule->setCouponType(2);
        $rule->setUseAutoGeneration(1);
        $rule->save();

        $vouchers = array();
        $emails = array();

        $orders = Mage::getResourceModel('sales/order_collection')
            ->addAttributeToSelect('customer_email');

        foreach ($orders AS $order) {
            $emails[$order->getCustomerEmail()] = $order->getCustomerEmail();
        }

        foreach ($emails AS $email) {
            $voucherType = 'virtual_and_physical';
            $voucherCode = $this->_generateUniqueVoucherCode($this->_voucherLength);

            while($this->_voucherAlreadyExists($voucherCode)) {
                $voucherCode = $this->_generateUniqueVoucherCode($this->_voucherLength);
            }

            $coupon = Mage::getModel('salesrule/coupon');
            $coupon->setId(null)
                ->setRuleId($rule->getId())
                ->setUsageLimit(1)
                ->setUsagePerCustomer(1)
                ->setExpirationDate($rule->getToDate())
                ->setCreatedAt(Mage::getSingleton('core/date')->gmtTimestamp())
                ->setType(Mage_SalesRule_Helper_Coupon::COUPON_TYPE_SPECIFIC_AUTOGENERATED)
                ->setCode($voucherCode)
                ->save();

            $temp = array();
            $temp['email'] = $email;
            $temp['code'] = $voucherCode;

            $vouchers[] = $temp;

            $voucher = $this->_generateVoucherModel(
                $voucherCode,
                $email,
                0,
                $rule->getDiscountAmount(),
                $rule->getFromDate(),
                $rule->getToDate(),
                $voucherType,
                $rule->getId()
            );
        }

        return $vouchers;
    }

    public function isSilverpopVouchersEnabled()
    {
        if (Mage::getStoreConfig('silverpop/vouchers/enabled') == '1') {
            return true;
        }

        return false;
    }

    public function isSilverpopTransactEnabled()
    {
        if (Mage::getStoreConfig('silverpop/transact/enabled') == '1') {
            return true;
        }

        return false;
    }

    public function deleteVouchers($vouchers)
    {
        foreach ($vouchers AS $voucher) {
            $coupons = Mage::getModel('salesrule/coupon')
                ->getCollection()
                ->addFieldToFilter(
                    'code',
                    array(
                        'eq' => $voucher['code'],
                    )
                );

            foreach ($coupons AS $coupon) {
                $coupon->delete();
            }

            $coupons = Mage::getModel('vouchers/vouchers')
                ->getCollection()
                ->addFieldToFilter(
                    'code',
                    array(
                        'eq' => $voucher['code'],
                    )
                );

            foreach ($coupons AS $coupon) {
                $coupon->delete();
            }
        }
    }
}
