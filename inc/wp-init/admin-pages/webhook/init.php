<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/**
 * Swipecart Webhook implementation
 *
 * @package Swipecart
 * @author Manthan Kanani
 * @since 2.4.0
**/
class WC_SC_Webhook{

	/**
	 * constructor for the integration  
	 *
	 * @author Manthan Kanani	
	 * @since 2.4.0
	 * @version 2.6.10
	**/
	function __construct(){
		$this->webhookString		= 'swipecart_webhooks';

		$this->webhooks 			= array(
			"qty" 				=> array(
				"up" 			=> "wp-qty_up",
				"down" 			=> "wp-qty_down",
			),
			"plugin_action" 	=> "wp-plugin_action"
		); 
	}


	/**
	 * Apply Webhook
	 *
	 * @author Manthan Kanani	
	 * @since 2.1.0
	 * @version 2.5.0
	**/
	public function __remoteCreateUser($data){
		$entry 		= new SC_Entry();
		$url 		= $entry->getURL("remote_create_user");

		$response 	= wp_remote_post($url, $data);

		if(is_wp_error($response)){
			return array(
				"success" 	=> false,
				"data" 		=> $response->get_error_message()
			);
		} else {
			return array(
				"success" 	=> true,
				"data" 		=> $response
			);
		}
	}


	/**
	 * remote send plugin action whether its deactivated or uninstalled
	 * 
	 * @param $data string|array {
	 * 		@type  $body  json array of action 
	 * }
	 * @return json
	 *
	 * @author Manthan Kanani
	 * @since 2.6.10
	 * @since 2.7.2
	**/
	public function __remotePluginAction($data){
		$request 		= array(
			"method" 		=> "POST",
			"headers" 		=> array(
				"Content-Type" 	=> 'application/json'
			),
			"body" 			=> json_encode($data)
		);

		return $this->__sendRemoteEvent($request, $this->webhooks["plugin_action"]);
	}


	/**
	 * remote send plugin qty action whether its added or removed from inventory
	 * 
	 * @param $data string|array {
	 * 		@type  $body  json array of action and qty 
	 * }
	 * @return json
	 *
	 * @author Manthan Kanani
	 * @since 2.7.0
	 * @since 2.7.2
	**/
	public function __remoteProductQtyAction($data, $event){
		if(isset($this->webhooks["qty"][$event])){
			$request 		= array(
				"method" 		=> "POST",
				"headers" 		=> array(
					"Content-Type" 	=> 'application/json'
				),
				"body" 			=> json_encode($data)
			);

			return $this->__sendRemoteEvent($request, $this->webhooks["qty"][$event]);
		} 
		return false;
	}



	/**
	 * to check whether the user has activated swipecart event or not
	 *
	 * @author Manthan Kanani	
	 * @since 2.4.0
	**/
	public function isActionActivated($topic){
		$webhooks 			= get_option($this->webhookString);
		
		if(is_array($webhooks) && isset($webhooks[$topic]) && isset($webhooks[$topic]["url"])){
			return $webhooks[$topic]["url"];
		}
		return false;
	}


	/**
	 * to check whether the user has activated swipecart event or not
	 *
	 * @author Manthan Kanani	
	 * @since 2.4.0
	**/
	public function isValidSecret($key=""){
		if(is_string($key) && !empty($key)){
			$authorization 		= new SC_AuthGeneration();
			$authSecret 		= $authorization->getAuth('AuthSecret') ?: "";
			if($key == $authSecret){
				return true;
			}
		}
		return false;
	}


	/**
	 * to check whether the user has activated swipecart event or not
	 *
	 * @author Manthan Kanani	
	 * @since 2.4.0
	**/
	public function getAuthSecret(){
		$authorization 		= new SC_AuthGeneration();

		return $authorization->getAuth('AuthSecret') ?: false;
	}


	/**
	 * Send Event to Rentech.
	 *
	 * @param $data string|array {
	 * 		@type  $headers  contains event in x-swipecart-topic and shopdomain in x-swipecart-shop-domain
	 * }
	 * @param $event (wp-qty_up|qty_down|plugin_action)
	 * @return json 
	 * 
	 * @author Manthan Kanani
	 * @since 2.4.0
	**/
	public function __sendRemoteEvent($data, $event){
		$siteURL 	= get_site_url();
		$siteURL 	= str_replace(["https://","http://"], "", $siteURL);

		if($url = $this->isActionActivated($event)){
			$data['headers']['x-swipecart-topic'] 			= $event;
			$data['headers']['x-swipecart-shop-domain'] 	= $siteURL;
			$data['headers']['x-swipecart-auth-secret'] 	= $this->getAuthSecret();
			$data['timeout']								= 5;

			return wp_remote_post($url, $data);
		}
		return false;
	}
}