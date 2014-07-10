<?php
class PayItEasy_Payment_Model_System_Config_Source_Dropdown_PaymentoptionsELV
{
	public function toOptionArray()
	{
		return array(
				array(
						'value' => 'accountholder',
						'label' => 'accountholder',
				),
				array(
						'value' => 'optionalaccountholder',
						'label' => 'optionalaccountholder',
				),
				array(
						'value' => 'checklist',
						'label' => 'checklist',
				),
		);
	}
}

?>