<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/**
 * Swipecart Points and Reward Integrations
 *
 * @package Swipecart
 * @author Manthan Kanani
 * @since 2.8.0
**/
class SC_RewardPoints {

	/**
	 * constructor for the integration  
	 *
	 * @author Manthan Kanani	
	 * @since 2.8.0
	**/
	function __construct(){
		$this->reward_plugins = ["woocommerce-points-and-rewards"];
	}


	/**
	 * check whether Reward & Points Plugins has found and add inclusion of very first related services
	 *
	 * @author Manthan Kanani	
	 * @since 2.8.0
	**/
	public function check_reward_plugin() {
		$i = 0;
		$available_reward_plugins = $this->installed_reward_plugins();
		foreach($available_reward_plugins as $plugin){
			$this->__include_files($plugin);
			$i++;
			break;
		}
		return $i ? true : false;
	}


	/**
	 * get available Reward & Points Plugins
	 *
	 * @author Manthan Kanani
	 * @since 2.8.0
	**/
	public function installed_reward_plugins() {
		$available_reward_plugins = array();

		foreach($this->reward_plugins as $plugin){
			if($this->is_active($plugin)) {
				$available_reward_plugins[] = $plugin;
			}
		}
		return $available_reward_plugins;
	}


	/**
	 * check if specific Reward Plugin is activated  
	 *
	 * @author Manthan Kanani	
	 * @since 2.8.0
	**/
	private function is_active($plugin_file) {
		if(!in_array($plugin_file, $this->reward_plugins)) return false;

		if($plugin_file == "woocommerce-points-and-rewards"){
			$active_plugins = (array) get_option('active_plugins', array());
			$plugin_path = 'woocommerce-points-and-rewards/woocommerce-points-and-rewards.php';

			if(class_exists('WC_Points_Rewards') || (!empty($active_plugins) && in_array($plugin_path, $active_plugins)) || (function_exists("is_plugin_active") && is_plugin_active($plugin_path))){
				return true;
			}
		}
		return false;
	}


	/**
	 * Inclusion of plugin files to work with Reward Plugin must have init.php in root 
	 *
	 * @author Manthan Kanani	
	 * @since 2.8.0
	**/
	private function __include_files($dirname){
		if(file_exists(dirname(__FILE__) .'/' . $dirname . '/init.php')){
			require_once(dirname(__FILE__) .'/' . $dirname . '/init.php');
		}
	}
}
