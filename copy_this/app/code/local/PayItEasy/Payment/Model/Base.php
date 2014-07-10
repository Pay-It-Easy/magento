<?php

class PayItEasy_Payment_Model_Base extends Mage_Payment_Model_Method_Abstract {

		/////////////////


	protected $_code = 'PayItEasy_payment_base';

	protected $_isInitializeNeeded      = true;
	protected $_canUseForMultishipping  = false;
	protected $_canUseInternal          = false;

	protected $_order;





	protected $prefix = PayItEasy_Payment_Helper_PayItEasyCore::PREFIX_CC;


	//common transaction fields
	protected $paymentmethod;

	protected $logger;


	/**
	 * constructor
	 */
	public function __construct(){
		$this->logger=new PayItEasy_Payment_Helper_SimpleLogger($this->getLoggerFileName(),$this->getLoggerLevel());
	}


	public function getOrderPlaceRedirectUrl() {
		return Mage::getUrl('PayItEasy_payment/payment/redirect', array('_secure' => true));
	}


	public function getPaymentMethod(){
		return $this->paymentmethod;
	}

	public function isLiveMode(){
		return !$this->getValueforKey(PayItEasy_Payment_Helper_PayItEasyCore::TEST_MODE);
	}

	function getSecret(){
		return $this->getValueforCommonKey(PayItEasy_Payment_Helper_PayItEasyCore::SECRET);
	}

	function getPrefix(){
		return $this->prefix;
	}

	function getSSLMerchant(){
		return $this->getValueforCommonKey(PayItEasy_Payment_Helper_PayItEasyCore::SSLMERCHANT);
	}

	function getTransactiontype(){
		return $this->getValueforKey(PayItEasy_Payment_Helper_PayItEasyCore::TRANSACTIONTYPE);
	}

	/**
	 * Instantiate state and set it to state object
	 * @param string $paymentAction
	 * @param Varien_Object
	 */
	public function initialize($paymentAction, $stateObject)
	{
		$state = Mage_Sales_Model_Order::STATE_PENDING_PAYMENT;
		$stateObject->setState($state);
		$stateObject->setStatus('pending_payment');
		$stateObject->setIsNotified(false);
	}




	function init(){}


	function getLoggerLevel(){
		$debug=$this->getValueforCommonKey(PayItEasy_Payment_Helper_PayItEasyCore::DEBUG);

		if($debug =='debug')
		{
			return 'DEBUG';
		}

		if($debug =='info')
		{
			return 'info';
		}

		return 'NONE';
	}

	function getLoggerFileName(){
		$debug=$this->getValueforCommonKey(PayItEasy_Payment_Helper_PayItEasyCore::DEBUG_FILE_PATH);
		return $debug;
	}

	function getCssurl()
	{
		if(defined('CSS_URL') && !is_null(CSS_URL))
		{
			return CSS_URL;
		}

		return null;
	}



	function getOrderid(){
		return $this->_order->getId();

	}


	function getPaymentGatewayURL() {
		return $this->getValueforKey(PayItEasy_Payment_Helper_PayItEasyCore::PAYMENT_GATEWAY_URL);
	}

	function getPayment_options() {
		return $this->getValueforKey(PayItEasy_Payment_Helper_PayItEasyCore::PAYMENTOPTIONS);
	}



	/* (non-PHPdoc)
	 * @see Core#setAdditionalParamsforPayPal($params)
	 */
	function setAdditionalParamsforPayPal(array &$params){
		$line_item_no = 0;

		$items = $this->_order->getAllItems();
		$itemcount=count($items);

		$name=array();
		$unitPrice=array();
		$sku=array();
		$ids=array();
		$qty=array();

		foreach ($items as $itemId => $item)
		{
			$sku[]=$item->getSku();
		}

		$params['basket_shipping_costs']=$this->formatAmount($this->_order->getShippingAmount());

		foreach ($items as $itemId => $item) {
			$params['basketitem_amount' . $line_item_no] = $this->formatAmount($item->getPrice());
			$params['basketitem_name' . $line_item_no] =substr($item->getName(),0,32);
			$params['basketitem_desc' . $line_item_no] =substr($product['name'],0,50);
			$params['basketitem_number' . $line_item_no] = urlencode(substr($ids[]=$item->getProductId(),0,32));
			$params['basketitem_qty' . $line_item_no] = $item->getQtyToInvoice();

			$line_item_no++;
		}

		return $params;
	}



	/**
	 * @param unknown_type $order_id
	 */
	function processOnError($order_id,$msg){
		$_order = new Mage_Sales_Model_Order();
		$_order = Mage::getModel('sales/order')	->load($order_id);
		$_order->cancel();
		$_order->setStatus(Mage_Sales_Model_Order::STATE_PENDING_PAYMENT,Mage_Sales_Model_Order::STATE_PENDING_PAYMENT, $msg);
		$_order->save();
		return Mage::getUrl('checkout/onepage/failure');
	}


	function processOnCancel($order_id){
		$_order = new Mage_Sales_Model_Order();
		$_order = Mage::getModel('sales/order')	->load($order_id);
		$_order->cancel();
		$_order->setStatus(Mage_Sales_Model_Order::STATE_CANCELED);
		$_order->save();
		return 	Mage::getUrl('PayItEasy_payment/Notification/reorder?order_id='.$order_id);
	}


	function processOnOk($order_id,$amount,$currency){
		$_order = new Mage_Sales_Model_Order();
		$_order = Mage::getModel('sales/order')->load($order_id);
		$vmsg=$this->validateOrderAmount($_order,$amount,$currency);
		if(''!=$vmsg)
			return $this->processOnError($order_id,$vmsg);
		$_order->setState(Mage_Sales_Model_Order::STATE_PENDING_PAYMENT, Mage_Sales_Model_Order::STATE_COMPLETE)->save();
		$_order->sendNewOrderEmail();
		return Mage::getUrl('checkout/onepage/success');
	}

	function validateOrderAmount(&$order,$amount,$currency){
		$this->logDebug("validate()->start() amount:".$amount.' currency:'.$currency);
		if(is_null($order))
		{
			$this->logTransaction('validate()->can`t find order!');
			return 'cant find order!';
		}

		if(!is_null($currency) && $order->getOrderCurrencyCode()!=$currency){
			$this->logTransaction('validate()->invalid currency order currency:'.$order->getOrderCurrencyCode().' gateway currency:'.$currency);
			return 'invalid currency order currency:'.$order->getBaseCurrencyCode().' gateway currency:'.$currency;
		}

		$orderAmount =$order->getGrandTotal();
		$orderAmount= $this->formatAmount($orderAmount);
		if($orderAmount!= $amount){
			$this->logTransaction('validate()->invalid amount order amount:'.$orderAmount.' gateway amount:'.$amount);
			return 'invalid amount order amount:'.$orderAmount.' gateway amount:'.$amount;
		}
		$this->logTransaction('validate()->ok');
		return '';
	}




	/* (non-PHPdoc)
	 * @see Core::getAmount()
	 */
	function getAmount(){
		$amount =$this->_order->getGrandTotal();
		return $this->formatAmount($amount);
	}

	/**
	 * Enter description here ...
	 * @return Ambigous <NULL, mixed>
	 */
	function getForm_label_submit(){
		return $this->getValueforCommonKey(PayItEasy_Payment_Helper_PayItEasyCore::FORM_LABEL_SUBMIT);
	}
	/**
	 * Enter description here ...
	 * @return Ambigous <NULL, mixed>
	 */
	function getForm_label_cancel(){
		return $this->getValueforCommonKey(PayItEasy_Payment_Helper_PayItEasyCore::FORM_LABEL_CANCEL);
	}

	/**
	 * Enter description here ...
	 * @return Ambigous <NULL, mixed>
	 */
	function getDeliverycountryrejectmessage(){
		return $this->getValueforCommonKey(PayItEasy_Payment_Helper_PayItEasyCore::DELIVERYCOUNTRY_REJECT_MESSAGE);
	}

	/**
	 * Enter description here ...
	 * @return Ambigous <NULL, mixed>
	 */
	function getForm_merchantref(){
		return $this->getValueforKey(PayItEasy_Payment_Helper_PayItEasyCore::FORM_MERCHANTREF);
	}




	/**
	 * Enter description here ...
	 * @return Ambigous <NULL, mixed>
	 */
	function  getDeliverycountryaction(){
		return $this->getValueforKey(PayItEasy_Payment_Helper_PayItEasyCore::DELIVERYCOUNTRY_ACTION);
	}


	/**
	 * Enter description here ...
	 * @return Ambigous <NULL, mixed>
	 */
	function getAutocapture(){
		return $this->getValueforKey(PayItEasy_Payment_Helper_PayItEasyCore::AUTOCAPTURE);
	}

	/**
	 * Enter description here ...
	 * @return Ambigous <NULL, mixed>
	 */
	function getCountryrejectmessage(){
		return $this->getValueforCommonKey(PayItEasy_Payment_Helper_PayItEasyCore::COUNTRYREJECTMESSAGE);
	}

	/**
	 * Enter description here ...
	 * @return Ambigous <NULL, mixed>
	 */
	function getForm_merchantname(){
		return $this->getValueforCommonKey(PayItEasy_Payment_Helper_PayItEasyCore::FORM_MERCHANTNAME);
	}

	/* (non-PHPdoc)
	 * @see Core::getCurrency()
	 */
	function getCurrency(){
		return  Mage::app()->getStore()->getCurrentCurrencyCode();
	}

	/* (non-PHPdoc)
	 * @see Core::getLocale()
	 */
	function getLocale(){
		$locale = Mage::app()->getLocale()->getLocaleCode();

		$str = strtolower($locale);
		if(strlen($str)<2)
		return "de";
		else
		return substr($str,0,2);
	}

	/* (non-PHPdoc)
	 * @see Core::getSessionid()
	 */
	function getSessionid(){
		return Mage::getSingleton("core/session")->getEncryptedSessionId();
	}


	/* (non-PHPdoc)
	 * @see Core::getBasketid()
	 */
	function getBasketid(){
		return $this->prefix.$this->_order->getQuoteId();
	}

	/* (non-PHPdoc)
	 * @see Core::getNotifyurl()
	 */
	function getNotifyurl(){
		return Mage::getUrl('PayItEasy_payment/Notification/');
		return $this->getValueforCommonKey('NOTIFICATION_URL');
	}

	/* (non-PHPdoc)
	 * @see Core::getNotificationfailedurl()
	 */
	function getNotificationfailedurl(){
		//return Mage::getUrl('PayItEasy_payment/Notification/');
		return $this->getValueforCommonKey(PayItEasy_Payment_Helper_PayItEasyCore::NOTIFICATION_FAILED_URL);
	}




	#****************************** helber functions ***************

	/**
	 * Enter description here ...
	 * @param unknown_type $amount
	 * @return string
	 */
	private function formatAmount($amount){
		// set the amount
		$tstr = number_format($amount, 2, ',', '');
		$tstr = substr( $tstr, 0,strpos($tstr,',')+3);
		return $tstr;
	}



	/**
	 * Enter description here ...
	 */
	function getZone(){
		$this->getValueforKey('ZONE');
	}

	/**
	 * Enter description here ...
	 * @param unknown_type $Key
	 * @return mixed|NULL
	 */
	function getValueforKey($Key){
		$configKey='payment/'.$this->getCode().'/'.$Key;
		$value=$this->getConfigData($Key);
		//$this->logDebug("value for ".$configKey." is:".$value);
		return $value;
	}

	/**
	 * Enter description here ...
	 * @param unknown_type $Key
	 * @return mixed|NULL
	 */
	function getValueforCommonKey($Key){
		$configKey='payment/PayItEasy_payment/'.$Key;
		$value=mage::getStoreConfig($configKey);
		//$this->logDebug("value for ".$configKey." is:".$value);
		return $value;
	}


	function getTransactionRedirect($order){
		$this->_order=$order;
		$helper=new PayItEasy_Payment_Helper_PayItEasyCore();
		return $helper->getTransactionRedirect($this,__('REDIRECT'));
	}

	public function processIpnRequest(array $data){
		$helper=new PayItEasy_Payment_Helper_PayItEasyCore();
		return $helper-> processPaymentGatewayNotification($data, $this);
	}

	/**
	 * @param unknown $param
	 */
	function logDebug($param) {
		$this->logger->debug($param);
	}

	/**
	 * @param unknown $param
	 */
	function logTransaction($param) {
		$this->logger->info($param);
	}

	/**
	 * @param unknown $param
	 */
	function logError($param) {
		$this->logger->error($param);
	}

}
?>