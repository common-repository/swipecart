<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/**
 * Modification Swipecart Remote User
 *
 * @package Swipecart
 * @author Manthan Kanani
 * @since 2.5.0
**/
class Swipecart_User {

	/**
	 * Constructor for Swipecart Migrations  
	 *
	 * @author Manthan Kanani   
	 * @since 2.5.0
	**/
	function __construct(){

	}

	/**
	 * Remote Swipecart create Auth
	 *
	 * @author Manthan Kanani
	 * @since 2.5.0
	**/
	public static function __register() {
		global $swipecart;

		$api 				= new APIClass();
		$webhook  			= new WC_SC_Webhook();
		$authorization 		= new SC_AuthGeneration();
		$body 				= $api->getSiteData(true);
		
		$firstAdminName    	= $body['admin'][0]['display_name'];
		$adminEmail    		= $body['site']['admin_email'];
		$siteShopURL    	= $body['site']['url'];
		$storeFrontToken   	= $body['store']['storeFrontToken'];
		$authSecret 		= $authorization->getAuth('AuthSecret') ?: "";

		$shopURL 			= str_replace(["https://","http://"], "", $siteShopURL);

		$swipecartVersion 	= GeneralUtility::__pluginVersion();

		$registrationData 	= array(
			"full_name" 	=> $firstAdminName,
			"email" 		=> $adminEmail,
			"shop" 			=> $shopURL,
			"woo_token"		=> $storeFrontToken,
			"woo_secret"	=> $authSecret,
			"version" 		=> $swipecartVersion
		);

		$resp = $webhook->__remoteCreateUser(array('body' => $registrationData));

		if($resp['success']){
			return $registrationData;
		}
		return false;
	}


	/**
	 * Enable swipecart Auto update by default
	 *
	 * @author Manthan Kanani
	 * @since 2.8.1
	**/
	public static function __auto_update($make_enable=true){
		global $swipecart;

		$auto_updates = (array) get_option("auto_update_plugins", array());

		if(!in_array($swipecart["basename"], $auto_updates) && $make_enable){
			$auto_updates[] = $swipecart["basename"];
		}
		if(in_array($swipecart["basename"], $auto_updates) && !$make_enable){
			unset($auto_updates[$swipecart["basename"]]);
		}
		update_option("auto_update_plugins", $auto_updates);
	}
}
