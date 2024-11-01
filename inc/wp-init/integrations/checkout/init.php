<?php

defined('ABSPATH') or die('No script kiddies please!');

/**
 * Swipecart Checkout Initialization
 *
 * @package Swipecart
 * @author Manthan Kanani
 * @since 2.4.2
**/
class SC_Checkout{

	/**
	 * Constructor cart and checkout after redirection  
	 *
	 * @author Manthan Kanani	
	 * @since 2.4.2
	**/
	function __construct(){	
		add_action( 'woocommerce_init', [ $this, '__WC_init' ] );
	}

	/** 
	 * Woocommerce initialization
	 *
	 * @author Manthan Kanani
	 * @since 2.4.2
	**/
	public function __WC_init(){
		global $swipecart;

		$generalUtility 	= new GeneralUtility();

		if($generalUtility->swipecartCan()){
			require_once('class-checkout.php');

			new Swipecart_Checkout();
		}
	}
}

new SC_Checkout();
