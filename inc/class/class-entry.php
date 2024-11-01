<?php

defined('ABSPATH') or die('No script kiddies please!');

/**
 * List URLs that will be used in whole app.
 *
 * @package Swipecart
 * @author Manthan Kanani
 * @since 2.4.1
**/
class SC_Entry {

	/**
	 * Constructor for Swipecart Migrations  
	 *
	 * @author Manthan Kanani   
	 * @since 2.4.1
	**/
	function __construct(){
		$this->serverURL = GeneralUtility::__getServerURL(true);
		$this->clientURL = GeneralUtility::__getServerURL();
	}

	/**
	 * Constructor for Swipecart Migrations  
	 *
	 * @author Manthan Kanani   
	 * @since 2.4.1
	**/
	public function getURL($key=null){
		$urls = array(
			"remote_create_user" 	=> $this->serverURL . "/swipecart/ecommerce/add-woocommerce-user",
			"cdn_popup"				=> "https://cdn.rentechdigital.com/swipecart/mobile.app.script.min.js"
		);

		if(!$key) return $urls;
		return isset($urls[$key])? $urls[$key] : false ;
	}
}