<?php

class PayItEasy_Payment_Model_System_Config_Source_Dropdown_Debuglevel
{
	public function toOptionArray()
	{
		return array(
				array(
						'value' => 'off',
						'label' => 'off',
				),
				array(
						'value' => 'info',
						'label' => 'Transaction',
				),
				array(
						'value' => 'debug',
						'label' => 'Development',
				)
		);
	}
}

?>