<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/**
 * Swipecart AuthGeneration and Activation Hook
 *
 * @package Swipecart
 * @author Manthan Kanani
 * @since 1.0.0
**/
class SC_AuthGeneration {

	/**
	 * Constructor for Swipecart Activation  
	 *
	 * @author Manthan Kanani	
	 * @since 2.0.0
	 * @version 2.4.0
	**/
	function __construct(){
		$this->storeFrontToken 			= NULL;
		$this->auth_option				= 'swipecart_auth';
		$this->options 					= ['swipecart_auth','swipecart_nonce','swipecart_webhooks'];
		$this->randomStringLength 		= 64;
	}

	/**
	 * create new Swipecart Secret Key
	 * 
	 * @author Manthan Kanani
	 * @since 2.0.0
	**/
	private function generateRandStr(){
		$utility 	= new Utility();
		return $utility->generateRandStr($this->randomStringLength);
	}

	/**
	 * Generate Auth Combo
	 *
	 * @author Manthan Kanani	
	 * @since 2.0.0
	 * @version 2.4.0
	**/
	public function createAuthCombo(){
		$this->storeFrontToken 	= $this->generateRandStr();
		$this->authSecret 		= $this->generateRandStr();

		$option_value = array(
			"StoreFrontToken"  	=> $this->storeFrontToken,
			"AuthSecret"		=> $this->authSecret
		);

		if(!get_option($this->auth_option)){
			update_option($this->auth_option, $option_value);
		}
	}

	/**
	 * Update AuthCombo
	 *
	 * @author Manthan Kanani	
	 * @since 2.0.0
	 * @version 2.5.0
	**/
	public function updateAuthCombo(){
		$this->storeFrontToken 	= $this->generateRandStr();
		$this->authSecret 		= $this->generateRandStr();

		$option_value = array(
			"StoreFrontToken"  	=> $this->storeFrontToken,
			"AuthSecret"		=> $this->authSecret
		);

		update_option($this->auth_option, $option_value);
	}

	/**
	 * Update Auth key value pair
	 *
	 * @author Manthan Kanani	
	 * @since 2.0.0
	**/
	public function updateAuth($option_value = array()){
		update_option($this->auth_option, $option_value);
	}

	/**
	 * Remove AuthCombo will run on deletion of plugin
	 *
	 * @author Manthan Kanani	
	 * @since 2.1.0
	**/
	public function removeAuthCombo(){
		foreach($this->options as $option){
			if(get_option($option)){
				delete_option($option);
			}
		}
	}

	/**
	 * Retrive AuthCombo
	 *
	 * @author Manthan Kanani	
	 * @since 2.0.0
	**/
	public function getAuth($value=''){
		$auth_combo = get_option($this->auth_option);
		if($auth_combo){
			if($value){
				return $auth_combo[$value] ?? false;
			} else {
				return $auth_combo;
			}
		}
		return false;
	}
}