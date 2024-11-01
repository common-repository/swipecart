<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/**
 * Swipecart Integration Initialization
 *
 * @package Swipecart
 * @author Manthan Kanani
 * @since 2.2.0
**/
class SC_Popup{

	/**
	 * constructor for the integration  
	 *
	 * @author Manthan Kanani	
	 * @since 2.2.0
	**/
	public function __construct(){
		add_action('wp_enqueue_scripts', array($this, 'enqueueScripts'));
	}

	/**
	 * Enqueue Scripts  
	 *
	 * @author Manthan Kanani	
	 * @since 2.2.0
	 * @version 2.6.1
	**/
	public function enqueueScripts() {
		if(!is_admin()){
			$entry 		= new SC_Entry();
			$popup_cdn 	= $entry->getURL("cdn_popup");

			$siteURL 	= get_site_url();
			$siteURL 	= str_replace(["https://","http://"], "", $siteURL);

			$popup_url 	= add_query_arg(array(
				"shop" => $siteURL
			), $popup_cdn);
			
			wp_enqueue_script('sc-popup', $popup_url, ['swipecart'], GeneralUtility::__styleVersion());
		}
	}

}
new SC_Popup();