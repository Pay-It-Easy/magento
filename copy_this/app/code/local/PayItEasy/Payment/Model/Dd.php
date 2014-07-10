<?php
class PayItEasy_Payment_Model_Dd extends PayItEasy_Payment_Model_Base {
	protected $_code = 'PayItEasy_payment_dd';
	protected $prefix = PayItEasy_Payment_Helper_PayItEasyCore::PREFIX_DD;

	protected $paymentmethod='directdebit';
	function getAcceptcountries(){
		return 'DE';
	}

	function getMandateid() {
		return $this->getValueforKey(PayItEasy_Payment_Helper_PayItEasyCore::MANDATEPREFIX).'-'.$this->getOrderid();
	}
	function getMandatename() {
		return $this->getValueforKey(PayItEasy_Payment_Helper_PayItEasyCore::MANDATENAME);
	}

	function getMandatesigned() {
		return date('Ymd');
	}

	function getSequencetype() {
		return $this->getValueforKey(PayItEasy_Payment_Helper_PayItEasyCore::SEQUENCETYPE);
	}
}
?>