<?php
class PayItEasy_Payment_Model_Notification {

	public function processIpnRequest(array $data){
		return $helber= Mage::getModel('PayItEasy_payment/base')->processIpnRequest($data);
	}
}
?>