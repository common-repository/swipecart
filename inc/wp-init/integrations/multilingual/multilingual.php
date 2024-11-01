<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/**
 * Swipecart Multilingual
 *
 * @package Swipecart
 * @author Manthan Kanani
 * @since 2.6.1
**/
class SC_Multilingual {

	/**
	 * constructor for the integration  
	 *
	 * @author Manthan Kanani	
	 * @since 2.6.1
	**/
	function __construct(){
		$this->multilingual_plugins = ["wpml"];
	}


	/**
	 * check whether multilingual plugin has found and add inclusion of related services
	 *
	 * @author Manthan Kanani	
	 * @since 2.6.1
	**/
	public function check_multilingual() {
		$i = 0;
		$available_multilingual = $this->installed_ml_plugins();
		foreach($available_multilingual as $plugin){
			$this->__include_files($plugin);
			$i++;
			break;
		}
		return $i ? true : false;
	}


	/**
	 * get available multilingual Plugins  
	 *
	 * @author Manthan Kanani	
	 * @since 2.6.1
	**/
	public function installed_ml_plugins() {
		$available_multilinguals = array();

		foreach($this->multilingual_plugins as $plugin){
			if($this->is_active($plugin)) {
				$available_multilinguals[] = $plugin;
			}
		}
		return $available_multilinguals;
	}


	/**
	 * check if WPML activated  
	 *
	 * @author Manthan Kanani	
	 * @since 2.6.1
	**/
	private function is_active($plugin_file) {
		if(!in_array($plugin_file, $this->multilingual_plugins)) return false;

		if($plugin_file == "wpml"){
			$languages = apply_filters('wpml_active_languages', NULL, '');
			if(!empty($languages) && is_array($languages) && count($languages)){
				return true;
			}
		}
		return false;
	}


	/**
	 * Inclusion of files to work with multilingual must have init.php exists 
	 *
	 * @author Manthan Kanani	
	 * @since 2.6.1
	**/
	private function __include_files($dirname){
		if(file_exists(dirname(__FILE__) .'/' . $dirname . '/init.php')){
			require_once(dirname(__FILE__) .'/' . $dirname . '/init.php');
		}
	}
}