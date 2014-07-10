<?php
class PayItEasy_Payment_NotificationController extends Mage_Core_Controller_Front_Action {

	/**
	 * Instantiate IPN model and pass IPN request to it
	 */
	public function indexAction() {
		try {
			if (! $this->getRequest ()->isPost ()) {
				return;
			}
			$data = $this->getRequest ()->getPost ();
			$dStatus = Mage::getModel ( 'PayItEasy_payment/Notification' )->processIpnRequest ( $data );
			echo $dStatus ['redirecturl'];
		} catch ( Exception $e ) {
			Mage::logException ( $e );
		}
	}
	public function reorderAction() {
		if (! is_null ( $_REQUEST ['order_id'] ))
			$this->_redirectUrl ( Mage::getUrl ( 'sales/order/reorder/order_id/' . $_REQUEST ['order_id'] ) );
		else
			$this->_redirectUrl ( Mage::getUrl ( '*/*/' ) );
	}
}