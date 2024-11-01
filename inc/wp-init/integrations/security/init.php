<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/**
 * Swipecart Security Setup
 *
 * @package Swipecart
 * @author Manthan Kanani
 * @since 2.5.2
**/
class SC_Security{

	/**
	 * constructor for the integration  
	 *
	 * @author Manthan Kanani	
	 * @since 2.5.2
	 * @version 2.6.5
	**/
	function __construct(){
		$this->allowed_endpoints = '/wp-json/sc';

		$this->init();
	}

	
	/**
	 * initializatin for rest security.  
	 *
	 * @author Manthan Kanani	
	 * @since 2.5.2
	**/
	private function init(){
		add_filter('rest_pre_dispatch', array($this, 'require_auth_for_all_endpoints'), 10,3);
	}


	/**
	 * JWT unblock and Allow Headers for our REST Path
	 *
	 * @author Manthan Kanani	
	 * @since 2.5.2
	 * @version 2.7.3
	**/
	public function require_auth_for_all_endpoints($result, $server, $request) {
		global $wp;
		$current_url 		= wp_parse_url(home_url(add_query_arg(array(), $wp->request)));
		$site_base 			= wp_parse_url(home_url());

		$current_url_path 	= $current_url['path'] ?? "";
		$home_url_path 		= $site_base['path'] ?? "";

		if(strpos($current_url_path, $home_url_path.$this->allowed_endpoints) === 0){
			header('Access-Control-Allow-Headers: *');
			header('Access-Control-Allow-Origin: *');
		}

		$allowed_endpoints[] = $this->allowed_endpoints;
		$allowed_endpoints = apply_filters('reqauth/allowed_endpoints', $allowed_endpoints);
	}

}
new SC_Security();