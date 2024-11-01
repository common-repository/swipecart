<?php

defined('ABSPATH') or die('No script kiddies please!');

/**
 * Swipecart Woocommerce Integraion View Generation
 *
 * @package Swipecart
 * @author Manthan Kanani
 * @since 1.0.0
**/
class WC_Swipecart_Integration extends WC_Integration {

	/**
	 * Init and hook in the integration  
	 *
	 * @author Manthan Kanani	
	 * @since 2.1.0
	**/
	public function __construct() {
		global $woocommerce;

		$auth_generation 			= new SC_AuthGeneration();
		$this->authCombo 			= $auth_generation->getAuth();
		
		$this->id                 	= 'sc-integration';
		$this->method_title       	= __('Swipecart Integration', 'swipecart');
		$this->method_description 	= __('This Storefront Token will be used for Swipecart Mobile API. Please, Possibly don\'t change this, Once you make an app Live.', 'swipecart');

		$this->init_settings();

		add_action('woocommerce_update_options_integration_' .  $this->id, array($this, 'process_admin_options'));
	}

	/**
	 * Unchangable Admin Option View
	 *
	 * @author Manthan Kanani	
	 * @since 1.0.0
	**/
	public function admin_options() {
		parent::admin_options();

		include 'views/html-sc-integration-admin-options.php';
	}
}

new WC_Swipecart_Integration(__FILE__);