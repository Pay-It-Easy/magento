<?php

class PayItEasy_Payment_Model_System_Config_Source_Dropdown_Transactiontype
{
	public function toOptionArray()
	{
		return array(
				array(
						'value' => 'authorization',
						'label' => 'authorization',
				),
				array(
						'value' => 'preauthorization',
						'label' => 'preauthorization',
				),
		);
	}
}

?>