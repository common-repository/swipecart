<?php
/**
 *
 * Plugin Name: Swipecart
 * Plugin URI: https://rentechdigital.com/swipecart
 * Description: Launch a world-class mobile app for your brand within minutes, without codes. Ready-to-market feature-rich app for your e-commerce store instantly.
 * Version: 2.8.6
 * Requires at least: 4.9
 * Requires PHP: 7.4
 * Author: Rentech Digital
 * Author URI: https://rentechdigital.com/swipecart
 * Text Domain: swipecart
 * License: GPL v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
**/

defined('ABSPATH') or die('No script kiddies please!');

/**
 * Intial Class for Swipecart
 *
 * @package Swipecart
 * @author Manthan Kanani
 * @since 1.0.0
**/
Class Swipecart {

	/** 
	 * Construct Development
	 *
	 * @author Manthan Kanani
	 * @since 2.2.0 
	 * @version 2.8.0
	**/
	function __construct(){
		$this->__globals();
		$this->__include_files();
		$this->__init();

		add_action( 'tgmpa_register', [ $this, '_required_plugins' ] );
	}


	/** 
	 * Define Globals
	 *
	 * @author Manthan Kanani
	 * @since 2.2.0 
	 * @version 2.5.0 
	**/
	private function __globals(){
		global $swipecart;

		$swipecart 			= array(
			"basename"		=> plugin_basename( __FILE__ ),
			"dir_url"		=> plugin_dir_url(__FILE__),
			"dir_path"		=> dirname(__FILE__),
			"dir_file"		=> __FILE__,
			"environment"	=> 'production',   // staging, development, production
		);
	}


	/** 
	 * Inclusion of files
	 *
	 * @author Manthan Kanani
	 * @since 2.2.6
	 * @version 2.8.0
	**/
	private function __include_files(){
		global $swipecart;

		require_once($swipecart['dir_path'] . '/inc/Utility.php');
		require_once($swipecart['dir_path'] . '/inc/class/class-general.php');
		require_once($swipecart['dir_path'] . '/inc/class/class-entry.php');
		require_once($swipecart['dir_path'] . '/inc/class/class-events.php');

		require_once($swipecart['dir_path'] . '/inc/wp-init/init-authorization.php');
		require_once($swipecart['dir_path'] . '/inc/wp-init/installer.php');

		add_action('woocommerce_init', [$this, '__WC_init']);

		require_once($swipecart['dir_path'] . '/inc/wp-init/admin-pages/integrations.php');
		require_once($swipecart['dir_path'] . '/inc/wp-init/integrations/integrations.php');

		require_once($swipecart['dir_path'] . '/inc/wp-init/plugins/tgmpa.php');

		require_once($swipecart['dir_path'] . '/inc/class/class-icons.php');
		require_once($swipecart['dir_path'] . '/inc/class/class-api.php');
	}


	/** 
	 * First Run of Application
	 *
	 * @author Manthan Kanani
	 * @since 2.0.1
	 * @version 2.8.1
	**/
	private function __init(){
		new SC_Installer();
		new SwipecartEvents();
	}


	/** 
	 * Woocommerce initialization
	 *
	 * @author Manthan Kanani
	 * @since 2.2.6
	 * @version 2.4.2
	**/
	public function __WC_init(){
		global $swipecart;

		$generalUtility 	= new GeneralUtility();

		if($generalUtility->swipecartCan()){
			require_once($swipecart['dir_path'] . '/inc/class/class-webservices.php');
		}
	}


	/** 
	 * Install Required Plugin for Swipecart
	 *
	 * @author Manthan Kanani
	 * @since 1.0.0 
	**/
	public function _required_plugins() {
		$plugins = array(
			array(
				'name'      			=> 'WooCommerce',
				'slug'      			=> 'woocommerce',
				'required'  			=> true
			),
		);

		$config = array(
			'id'           => 'swipecart',
			'default_path' => '', 
			'menu'         => 'tgmpa-install-plugins',
			'parent_slug'  => 'plugins.php',
			'capability'   => 'manage_options',
			'has_notices'  => true,
			'dismissable'  => false,
			'dismiss_msg'  => '',
			'is_automatic' => false,
			'message'      => '',
		);
		tgmpa( $plugins, $config );
	}
}
new Swipecart();