<?php

class PayItEasy_Payment_Model_System_Config_Source_Dropdown_Sequencetype
{
	public function toOptionArray()
	{
		return array(
		array(
						'value' => 'oneoff',
						'label' => 'oneoff',
		),
		array(
						'value' => 'first',
						'label' => 'first',
		),
		array(
						'value' => 'reccuring',
						'label' => 'reccuring',
		),
		array(
						'value' => 'final',
						'label' => 'final',
		)
		);
	}
}

?>