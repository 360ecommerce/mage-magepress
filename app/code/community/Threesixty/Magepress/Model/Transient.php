<?php

class Threesixty_Magepress_Model_Transient extends Fishpig_Wordpress_Model_Abstract
{
	public function _construct()
	{
		$this->_init('wordpress/option');
	}
	
	/**
	 * Loads an transient based on its name
	 *
	 * $param string $name
	 * @return $this
	 */
	public function loadByName($name)
	{
		$theme 		= $this->load('_transient_' . $name, 'option_name');
		$transient 	= $theme->getOptionValue();

		return $transient;
	}

	public function setTransient($data)
	{
		$model = Mage::getModel('wordpress/option');
		$model->setOptionName('blablabal');
		$model->setOptionValue('fhfhfdhdf');
		$model->save();
	}
}