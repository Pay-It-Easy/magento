<?php
class PayItEasy_Payment_Model_Pp extends PayItEasy_Payment_Model_Base {
	protected $_code = 'PayItEasy_payment_pp';
	protected $paymentmethod='paypal';
	protected $prefix = PayItEasy_Payment_Helper_PayItEasyCore::PREFIX_PP;
}
?>