<?php

defined('ABSPATH') or die('No script kiddies please!');

/**
 * General Utility
 * 
 * @package Swipecart
 * @author Manthan Kanani
 * @since 2.2.0
**/
class GeneralUtility{
	
	/**
	 * set styling version
	 *
	 * @package Swipecart
	 * @author Manthan Kanani
	 * @since 2.2.0
	**/
	public static function __styleVersion(){
		if(self::__staging())
			return time();
		return false;
	}

	/**
	 * set styling and scripting minification
	 *
	 * @package Swipecart
	 * @author Manthan Kanani
	 * @since 2.2.0
	 * @version 2.4.2
	**/
	public static function __scriptMinify(){
		if(self::__staging())
			return "";
		return ".min";
	}

	/**
	 * set styling version
	 *
	 * @package Swipecart
	 * @author Manthan Kanani
	 * @since 2.2.1
	**/
	public static function __getServerURL($isBackend=false){
		global $swipecart;
		$urlScheme = "https://";

		if($swipecart['environment'] == 'development'){
			$url[] = "dev";
		} elseif($swipecart['environment'] == 'staging'){
			$url[] = "staging";
		}

		$url[] = ($isBackend) ? "api" : "swipecart" ; 
		$url[] = "rentechdigital.com";

		$serverURL = implode('.', $url);
		return $urlScheme . $serverURL;
	}

	/**
	 * verify if staging
	 * 
	 * @package Swipecart
	 * @author Manthan Kanani
	 * @since 2.2.0 
	**/
	public static function __staging(){
		global $swipecart;
		if(!in_array($swipecart['environment'], ['development','staging'])){
			$siteHost = preg_replace('/(^\w+:|^)\/\//', '', get_site_url());
			$siteHost = preg_replace('/\/$/', '', $siteHost);
			if(str_contains($siteHost, '.rentechdigital.com')) return true;
		}
		return in_array($swipecart['environment'], ['development','staging']);
	}

	/**
	 * get Plugin version
	 *
	 * @package Swipecart
	 * @author Manthan Kanani
	 * @since 2.5.0
	**/
	public static function __pluginVersion(){
		global $swipecart;

		if(!function_exists('get_plugin_data'))
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

		$plugin_data = get_plugin_data($swipecart['dir_file'], false);

		return ($plugin_data && $plugin_data['Version']) ? $plugin_data['Version'] : '2.5.0';
	}

	/**
	 * Check Plugin Installed or not
	 * 
	 * @package Swipecart
	 * @author Manthan Kanani
	 * @since 2.2.5
	**/
	public static function isPluginActivated($pluginFile){
		if(in_array($pluginFile, apply_filters('active_plugins', get_option('active_plugins'))))
			return true;
		return false;
	}

	/**
	 * manage has capability to access
	 *
	 * @package Swipecart
	 * @author Manthan Kanani
	 * @since 2.4.2
	**/
	public static function swipecartCan(){
		if(is_ssl()){
			return true;
		} else {
			return (self::__staging()) ? true : false ;
		}
	}
}