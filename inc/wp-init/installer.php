<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/**
 * Migrate Plugins
 *
 * @package Swipecart
 * @author Manthan Kanani
 * @since 2.0.0
**/
class SC_Installer {

	/**
	 * Constructor for Swipecart Migrations  
	 *
	 * @author Manthan Kanani   
	 * @since 2.0.0
	 * @version 2.6.10
	**/
	function __construct(){
		global $swipecart;
		
		$this->plugin_basename 		= $swipecart['basename'];
		$this->transientID 			= array(
			"install" 		=> 'swipecart_installed',
			"update" 		=> 'swipecart_updated'
		);

		$this->registerHooks();
	}


	/**
	 * Register Hooks for upgradation
	 *
	 * @author Manthan Kanani   
	 * @since 2.0.0
	 * @version 2.4.0
	**/
	private function registerHooks() {
		global $swipecart;
		register_activation_hook($swipecart['dir_file'], [ $this, '__activatePlugin']);
		register_deactivation_hook($swipecart['dir_file'], [ $this, '__deactivatePlugin']);
		register_uninstall_hook($swipecart['dir_file'], [ __CLASS__, '__uninstallPlugin'] );

		add_action('upgrader_process_complete', [$this, '__upgraderProcess'], 10, 2);
		add_action('plugins_loaded', [$this, '__redirectToUpdatePlugin']);
	}


	/**
	 * Register Hooks for activate plugin
	 *
	 * @author Manthan Kanani   
	 * @since 2.1.0
	 * @version 2.5.0
	**/
	public function __activatePlugin() {
		$authorization 	= new SC_AuthGeneration();
		$authorization->createAuthCombo();

		set_transient($this->transientID["install"], 1);
	}


	/**
	 * Register Hooks for deactivation
	 *
	 * @author Manthan Kanani   
	 * @since 2.1.0
	 * @version 2.7.0
	**/
	public function __deactivatePlugin() {
		if(class_exists('WC_SC_Webhook')){
			$webhook  		= new WC_SC_Webhook();
			$webhook->__remotePluginAction(array(
				"action" => "deactivate"
			));
		}
	}


	/** 
	 * Register Hooks for uninstallation
	 *
	 * @author Manthan Kanani
	 * @since 2.4.0
	 * @version 2.6.10
	**/
	static function __uninstallPlugin(){
		if(class_exists('WC_SC_Webhook')){
			$webhook  		= new WC_SC_Webhook();
			$webhook->__remotePluginAction(array(
				"action" => "uninstall"
			));
		}

		$authGeneration = new SC_AuthGeneration();
		$authGeneration->removeAuthCombo();

		delete_transient("swipecart_installed");
		delete_transient("swipecart_updated");
	}


	/**
	 * API Callback on activation hook
	 *
	 * @author Manthan Kanani   
	 * @since 2.1.0
	 * @version 2.4.1
	**/
	public function __remoteSetAuth() {
		$authGeneration = new SC_AuthGeneration();
		$authGeneration->updateAuthCombo();

		Swipecart_User::__register();
	}


	/**
	 * Process while upgrading plugin
	 *
	 * @author Manthan Kanani   
	 * @since 2.0.0
	 * @version 2.4.0
	**/
	public function __redirectToUpdatePlugin() {
		if (get_transient($this->transientID["update"]) && current_user_can('update_plugins')) {
			$this->__onUpgradePlugin();
			delete_transient($this->transientID["update"]);
		}
	}


	/**
	 * update Auth Combo while ugrading
	 *
	 * @author Manthan Kanani	
	 * @since 2.0.0
	 * @version 2.5.7
	**/
	public function __onUpgradePlugin(){
		$authorization 			= new SC_AuthGeneration();
		$oldAuthToken 			= $authorization->getAuth('StoreFrontToken') ?: "";
		$oldAuthSecret 			= $authorization->getAuth('AuthSecret') ?: "";

		$newAuthToken 			= $oldAuthToken ?: $authorization->generateRandStr();
		$newAuthSecret 			= $oldAuthSecret ?: $authorization->generateRandStr();

		if($oldAuthToken!=$newAuthToken || $oldAuthSecret!=$newAuthSecret){
			$authorization->updateAuth(array(
				"StoreFrontToken"	=> $newAuthToken,
				"AuthSecret"		=> $newAuthSecret
			));
			
			$this->__remoteSetAuth();
		}
	}


	/**
	 * migrations from 1.0.1 to 2.0.0 
	 *
	 * @author Manthan Kanani	
	 * @since 2.0.0
	**/
	public function __upgraderProcess(\WP_Upgrader $upgrader, array $hook_extra){
		if(is_array($hook_extra) && array_key_exists('action', $hook_extra) && array_key_exists('type', $hook_extra) && array_key_exists('plugins', $hook_extra)) {
			if($hook_extra['action']=='update' && $hook_extra['type']=='plugin' && is_array($hook_extra['plugins']) && !empty($hook_extra['plugins'])) {
				foreach ($hook_extra['plugins'] as $key => $plugin) {
					if ($this->plugin_basename == $plugin) {
						set_transient($this->transientID["update"], 1);
						break;
					}
				}
				unset($key, $plugin, $this->plugin_basename);
			}
		}
	}
}
