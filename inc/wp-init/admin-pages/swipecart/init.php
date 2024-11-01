<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/**
 * Swipecart Integration Initialization
 *
 * @package Swipecart
 * @author Manthan Kanani
 * @since 1.0.0
**/
class WC_SC_Integrations{

	/**
	 * constructor for the integration  
	 *
	 * @author Manthan Kanani	
	 * @since 1.0.0
	 * @version 2.6.10
	**/
	public function __construct(){
		global $swipecart;
		
		$this->plugin_basename 			= $swipecart['basename'];

		$this->registerHooks();
	}


	/**
	 * Register Hooks for upgradation
	 *
	 * @author Manthan Kanani   
	 * @since 2.6.10
	**/
	private function registerHooks(){
		add_action('admin_enqueue_scripts', array($this, 'enqueueAdminScripts'));
		add_action('wp_enqueue_scripts', array($this, 'enqueueUserScripts'));
		add_action('plugins_loaded', array($this, 'wcIntegrations'));

		add_filter('plugin_action_links_'.$this->plugin_basename, [$this, '__createPluginActionLink']);
	}


	/**
	 * Initialize the plugin  
	 *
	 * @author Manthan Kanani	
	 * @since 1.0.0
	 * @version 2.5.0
	**/
	public function wcIntegrations() {
		if(class_exists('WC_Integration')){
			require_once('class/class-ajax.php');
			require_once('class/class-integrationPage.php');
			add_filter('woocommerce_integrations', array($this, 'addSwipecartIntegrationTab'));
		} else {
			return;
		}
	}

	/**
	 * Swipecart Add secondary tab  
	 *
	 * @author Manthan Kanani	
	 * @since 1.0.0
	**/
	public function addSwipecartIntegrationTab($integrations){
		$integrations[] 	= 'WC_Swipecart_Integration';
		return $integrations;
	}


	/**
	 * Create Plugin Action Link
	 *
	 * @author Manthan Kanani   
	 * @since 2.0.1
	 * @version 2.6.10
	**/
	public function __createPluginActionLink($actions) {
		$settingURL = '<a href="'. esc_url(get_admin_url(null, 'admin.php?page=wc-settings&tab=integration&section=sc-integration')) .'">'.esc_html__('Settings','swipecart').'</a>';
		array_unshift($actions, $settingURL);
		return $actions;
	}


	/**
	 * Swipecart Register Admin Scripts
	 *
	 * @author Manthan Kanani	
	 * @since 2.5.0
	**/
	public function enqueueAdminScripts(){
		wp_register_script('swipecart-admin', plugin_dir_url( __FILE__ ). 'assets/js/swipecart-admin'.GeneralUtility::__scriptMinify().'.js', [], GeneralUtility::__styleVersion());

		wp_localize_script('swipecart-admin', 'swipecart', array(
				'ajax_url' => admin_url('admin-ajax.php'),
				'site_url' => get_site_url()
			)
		);
	}

	/**
	 * Swipecart Register User Scripts
	 *
	 * @author Manthan Kanani	
	 * @since 2.5.0
	**/
	public function enqueueUserScripts(){
		wp_register_script('swipecart', plugin_dir_url( __FILE__ ). 'assets/js/swipecart'.GeneralUtility::__scriptMinify().'.js', [], GeneralUtility::__styleVersion());

		wp_localize_script('swipecart', 'swipecart', array(
				'ajax_url' => admin_url('admin-ajax.php'),
				'site_url' => get_site_url()
			)
		);
	}
}

new WC_SC_Integrations();