<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/**
 * Swipecart Reward Plugin Integration
 *
 * @package Swipecart
 * @author Manthan Kanani
 * @since 2.8.0
**/
class SC_RewardPlugin{

	/**
	 * constructor for the woocommerce points and rewards Integrations  
	 * 
	 * @author Manthan Kanani	
	 * @since 2.8.0
	**/
	function __construct(){
		$this->__include_files();

		add_action('woocommerce_init', [$this, '__WC_init']);
	}

	/**
	 * inclusion of fo the file
	 * 
	 * @since 2.8.0
	**/
	private function __include_files(){
		require_once(dirname(__FILE__) .'/class-pointnreward.php');
	}

	/** 
	 * Woocommerce initialization 
	 *
	 * @author Manthan Kanani
	 * @since 2.8.0
	**/
	public function __WC_init(){
		$generalUtility 	= new GeneralUtility();

		if($generalUtility->swipecartCan()){
			$this->__init();
		}
	}

	/**
	 * inclusion of fo the files and services
	 * 
	 * @since 2.8.0
	**/
	public function __init(){
		require_once(dirname(__FILE__) .'/API/class-webservices.php');
		require_once(dirname(__FILE__) .'/API/class-rest-extend.php');

		if(class_exists("WPSC_REST_Extend_Args")){
			$pointRewards  = new WPSC_REST_Extend_Args();
			$pointRewards->add_hooks();
		}
	}
}

new SC_RewardPlugin();