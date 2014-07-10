<?php
class PayItEasy_Payment_Model_System_Config_Source_Dropdown_Paymentoption
{
	public function toOptionArray()
	{
		return array(
				array(
						'value' => 'cardholder',
						'label' => 'cardholder',
				),
				array(
						'value' => 'optionalcardholder',
						'label' => 'optionalcardholder',
				),
				array(
						'value' => 'optionalcardholder',
						'label' => 'optionalcardholder',
				),
				array(
						'value' => 'sslifvisaenrolledu',
						'label' => 'sslifvisaenrolledu',
				),
				array(
						'value' => 'amexavs',
						'label' => 'amexavs',
				),
				array(
						'value' => 'accountholder',
						'label' => 'accountholder',
				),
		);
	}
}

?>