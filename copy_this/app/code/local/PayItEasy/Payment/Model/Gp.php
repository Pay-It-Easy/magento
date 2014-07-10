<?php
class PayItEasy_Payment_Model_Gp extends PayItEasy_Payment_Model_Base {
	protected $_code = 'PayItEasy_payment_gp';
	protected $paymentmethod='banktransfer';
	protected $prefix = PayItEasy_Payment_Helper_PayItEasyCore::PREFIX_GP;

	function getLabel0($param='') {
		return $this->getValueforKey(PayItEasy_Payment_Helper_PayItEasyCore::LABEL0);
	}
	function getLabel1($param='') {
		return $this->getValueforKey(PayItEasy_Payment_Helper_PayItEasyCore::LABEL1);
	}
	function getLabel2($param='') {
		return $this->getValueforKey(PayItEasy_Payment_Helper_PayItEasyCore::LABEL2);
	}
	function getLabel3($param='') {
		return $this->getValueforKey(PayItEasy_Payment_Helper_PayItEasyCore::LABEL3);
	}
	function getLabel4($param='') {
		return $this->getValueforKey(PayItEasy_Payment_Helper_PayItEasyCore::LABEL4);
	}

	function getText0($param='') {
		return $this->getValueforKey(PayItEasy_Payment_Helper_PayItEasyCore::TEXT0);
	}
	function gettext1($param='') {
		return $this->getValueforKey(PayItEasy_Payment_Helper_PayItEasyCore::TEXT1);
	}
	function getText2($param='') {
		return $this->getValueforKey(PayItEasy_Payment_Helper_PayItEasyCore::TEXT2);
	}
	function getText3($param='') {
		return $this->getValueforKey(PayItEasy_Payment_Helper_PayItEasyCore::TEXT3);
	}
	function getText4($param='') {
		return $this->getValueforKey(PayItEasy_Payment_Helper_PayItEasyCore::TEXT4);
	}

	function getAccountNumber(){
		return '';
	}

	function getBankCode(){
		if(!$this->isLiveMode())
		return '12345679';
		else
			return '';
	}

	function getPayment_options(){
		$paymentoptions='';
		if($this->getValueforKey('ageverification'))
			$paymentoptions= 'avsopen';

		return $paymentoptions ;
	}

	function getBic(){
		if($this->isLiveMode())
		return '';
		else
		return 'TESTDETT421';
	}

	function getIban(){
		return '';
	}

}
?>