<?php
class PayItEasy_Payment_Model_Cc extends PayItEasy_Payment_Model_Base {
	protected $_code = 'PayItEasy_payment_cc';

	protected $paymentmethod='creditcard';
	protected $prefix = PayItEasy_Payment_Helper_PayItEasyCore::PREFIX_CC;

	function getAcceptcountries(){
		return $this->getValueforKey(PayItEasy_Payment_Helper_PayItEasyCore::ACCEPTCOUNTRIES);
	}

	function getRejectcountries(){
		return $this->getValueforKey(PayItEasy_Payment_Helper_PayItEasyCore::REJECTCOUNTRIES);
	}

	function getCustomer_addr_city(){
		return $this->_order->getShippingaddress()->getCity();
	}

	function getCustomer_addr_street(){
		return $this->_order->getShippingaddress()->getData('street');
	}

	function getCustomer_addr_zip(){
		return $this->_order->getShippingaddress()->getZip();
	}

	function getCustomer_addr_number(){
		global $order;
		return '';
	}

	function getDeliverycountry(){
		return $this->_order->getShippingaddress()->getCountryCode();
	}
}
?>