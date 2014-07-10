<?php

class PayItEasy_Payment_Model_System_Config_Source_Dropdown_Deliverycountryaction
{
	public function toOptionArray()
	{
		return array(
				array(
						'value' => 'notify',
						'label' => 'notify',
				),
				array(
						'value' => 'reject',
						'label' => 'reject',
				),
		);
	}
}

?>