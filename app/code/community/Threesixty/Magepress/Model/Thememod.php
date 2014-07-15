<?php

class Threesixty_Magepress_Model_Thememod extends Fishpig_Wordpress_Model_Abstract
{
	public function _construct()
	{
		$this->_init('wordpress/option');
	}
	
	/**
	 * Loads an thememod based on its name
	 *
	 * $param string $name
	 * @return $this
	 */
	public function loadByName($name)
	{
		$theme 	= $this->load('current_theme', 'option_name');
		$mods 	= 'theme_mods_' . strtolower( $theme->getOptionValue() );
		$mods 	= $this->load( $mods, 'option_name');
		$mods 	= unserialize( $mods->getOptionValue() );

		return isset($mods[$name]) ? $mods[$name] : null;
	}
}