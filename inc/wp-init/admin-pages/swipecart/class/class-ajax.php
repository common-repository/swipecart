<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/**
 * Swipecart Integration Initialization
 *
 * @package Swipecart
 * @author Manthan Kanani
 * @since 2.5.0
**/
class Swipecart_AdminAjax{
	/**
	 * Init and hook in the for Ajax  
	 *
	 * @author Manthan Kanani   
	 * @since 2.5.0
	**/
	function __construct(){
		add_action( 'wp_ajax_swipecart_reveal_tokens', [ $this, 'ajax_swipecart_reveal_tokens' ]);
	}

	/**
	 * Sends an ajax request to replace old URLs to new URLs. This method also updates strefront token and secret
	 * Fired by `wp_ajax_ajax_sc_reveal_tokens` action.
	 *
	 * @author Manthan Kanani   
	 * @since 2.5.0
	 */
	public function ajax_swipecart_reveal_tokens() {
		check_ajax_referer( 'swipecart_reveal_tokens', '_nonce' );

		$authGeneration = new SC_AuthGeneration();
		$authGeneration->updateAuthCombo();

		$resp = Swipecart_User::__register();

		if($resp) {
			$data = array(
				"success" => true, 
				"data" => array(
					"woo_token" => $resp['woo_token']
				)
			);
		} else {
			$data = array(
				"success" => false
			);
		}

		wp_send_json($data);
		wp_die();
	}
}
new Swipecart_AdminAjax();
