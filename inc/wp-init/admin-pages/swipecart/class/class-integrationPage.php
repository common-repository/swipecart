<?php

defined('ABSPATH') or die('No script kiddies please!');

/**
 * Swipecart Woocommerce Integraion View Generation
 *
 * @package Swipecart
 * @author Manthan Kanani
 * @since 1.0.0
 * @version 2.5.0
**/
class WC_Swipecart_Integration extends WC_Integration {

	/**
	 * Init and hook in the integration  
	 *
	 * @author Manthan Kanani	
	 * @since 2.1.0
	**/
	function __construct() {
		global $woocommerce;

		$this->id                 	= 'sc-integration';
		$this->method_title       	= __('Swipecart Integration', 'swipecart');
		$this->method_description 	= __('This Storefront Token will be used for Swipecart Mobile API. Please, Frequent changes may affect the performance of the app. So, possibly don\'t change this, Once you make an app Live.', 'swipecart');

		$this->init_settings();

		add_action('admin_enqueue_scripts', [ $this, '__page_styling' ]);
		add_action('woocommerce_update_options_integration_' .  $this->id, array($this, 'process_admin_options'));
	}

	/**
	 * AuthCombo retrive  
	 *
	 * @author Manthan Kanani	
	 * @since 2.6.4
	**/
	private function getAuthCombo(){
		$auth_generation 		= new SC_AuthGeneration();
		return $auth_generation->getAuth();
	}

	/**
	 * Unchangable Admin Option View
	 *
	 * @author Manthan Kanani	
	 * @since 1.0.0
	 * @version 2.6.4
	**/
	public function admin_options() {
		parent::admin_options();

		$authCombo = $this->getAuthCombo();

		include 'views/html-sc-integration-admin-options.php';
	}

	/**
	 * Add styling for specific page 
	 *
	 * @author Manthan Kanani	
	 * @since 2.5.0
	**/
	public function __page_styling($hook) {
		wp_register_style('swipecart-admin-integration', plugin_dir_url( __DIR__ ).'assets/css/style.css', false, GeneralUtility::__styleVersion());
		wp_register_script('swipecart-admin-integration-ajax', plugin_dir_url( __DIR__ ).'assets/js/ajax'.GeneralUtility::__scriptMinify().'.js', ['swipecart-admin','jquery'], GeneralUtility::__styleVersion(), true);
		
		if($hook=="woocommerce_page_wc-settings"){
			wp_enqueue_style('swipecart-admin-integration');
			wp_enqueue_script('swipecart-admin-integration-ajax');
		}
	}


}

new WC_Swipecart_Integration(__FILE__);