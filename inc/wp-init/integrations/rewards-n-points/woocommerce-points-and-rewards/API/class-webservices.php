<?php

defined('ABSPATH') or die('No script kiddies please!');

/**
 * REST API Route List with functions...
 *
 * @package Swipecart
 * @author Manthan Kanani
 * @since 2.8.0
**/
class REST_API_v1_Controller_RewardPoints extends REST_API_v1_Controller {
	/**
	 * Constructor for common items
	 *
	 * @author Manthan Kanani	
	 * @since 2.8.0
	**/
	function __construct() {
		
	}

	/**
	 * Get points by user_id
	 *
	 * @author Manthan Kanani	
	 * @since 2.8.0
	**/
	public function getPointsByUser($request){
		$api 				= new Swipecart_PointsnRewards();
		$user 				= $request->get_param('loggedin_data');

		$result 			= $api->get_rewardpoints($user->ID);

		if(is_numeric($result)) {
			$string 		= $api->point_label(); 
			$arr = array(
				"success" 	=> true, 
				"message" 	=> "success",
				"data"		=> array(
					"count"  => $result,
					"text"	 => ($result>1)?$string["plural"]:$string["singular"]
				)
			);
		} else {
			$arr = array("success" => false, "message" => __("Something went wrong.", "swipecart"));
			return new WP_REST_Response($arr, 400);
		}
		return $arr;
	}

	/**
	 * Get points Hostory by user_id
	 *
	 * @author Manthan Kanani	
	 * @since 2.8.0
	 * @version 2.8.4
	**/
	public function getPointsHistoryByUser($request){
		$api 				= new Swipecart_PointsnRewards();
		$order 				= $request->get_param('order');
		$orderBy 			= $request->get_param('orderBy');
		$page 				= $request->get_param('page');
		$pp 				= $request->get_param('pp');
		$type 				= $request->get_param('type');

		$user 				= $request->get_param('loggedin_data');

		if($result = $api->get_rewardpoints_history($user->ID, $order, $orderBy, $page, $pp, $type)) {
			$arr = array(
				"success" 	=> true, 
				"message" 	=> "success",
				"data" 		=> array("pointsHistory" => $result)
			);
		} else {
			$arr = array("success" => false, "message" => __("Something went wrong.", "swipecart"));
			return new WP_REST_Response($arr, 400);
		}
		return $arr;
	}


	/**
	 * get available points on cart
	 *
	 * @author Manthan Kanani	
	 * @since 2.8.0
	**/
	public function availablePointsForCart($request){
		$api 				= new Swipecart_PointsnRewards();
		$cart 				= $request->get_param('lineItems');

		$user 				= $request->get_param('loggedin_data');

		if($result = $api->get_discount_and_redeeming_points($cart, $user->ID)) {
			$arr = array(
				"success" 	=> true, 
				"message" 	=> "success",
				"data" 		=> $result
			);
		} else {
			$arr = array("success" => false, "message" => __("Something went wrong.", "swipecart"));
			return new WP_REST_Response($arr, 400);
		}
		return $arr;
	}





	/*************************************************************
	**************************************************************
	** double curly bracket {{field}} is required field  
	** single curly bracket {field} is not mendetory each time
	** Intitial url : /wp-json/
	**
	** @header StoreFrontToken
	** @author Manthan Kanani
	**/
	public function register_routes() {
		
		/**
		 * Get Product Categories of product
		 * sc/v1/getPointsByUser
		 *
		 * @return json
		 */
		register_rest_route($this->namespace, 'getPointsByUser', [
			'methods' 				=> 'POST',
			'callback' 				=> array( $this, 'getPointsByUser'),
			'permission_callback' 	=> array( $this, 'verifyAccessTokenPermissionCallback' ),
		]);

		/**
		 * Get Product Categories of product
		 * sc/v1/getPointsByUser
		 *
		 * @param (str) {order}		=> ('asc'||'desc')
		 * @param (str) {orderBy}	=> ('id'||'name'||'popularity'||'date')
		 * @param (int) {page}		=> ({}>0)
		 * @param (int) {pp}		=> ({}>=0)
		 *
		 * @return json
		 */
		register_rest_route($this->namespace, 'getPointsHistoryByUser', [
			'methods' 				=> 'POST',
			'callback' 				=> array( $this, 'getPointsHistoryByUser'),
			'permission_callback' 	=> array( $this, 'verifyAccessTokenPermissionCallback' ),
			'args'					=> array(
				'order' => array(
					'required' 			=> false,
					'default'			=> 'desc',
					'sanitize_callback'	=> array($this, 'orderSanitizeCallback'),
					'validate_callback'	=> array($this, 'orderValidateCallback'),
				),
				'orderBy' => array(
					'required' 			=> false,
					'default'			=> 'date',
					'sanitize_callback'	=> array($this, 'orderBySanitizeCallback'),
					'validate_callback'	=> array($this, 'orderByValidateCallback'),
				),
				'page' => array(
					'default' 			=> 1,
					'sanitize_callback'	=> 'absint',
					'validate_callback'	=> array($this, 'numericPositiveValidateCallback'),
				),
				'pp' => array(
					'default' 			=> 10,
					'sanitize_callback'	=> 'absint',
					'validate_callback'	=> 'rest_is_integer',
				)
			)
		]);


		/**
		 * Get Product Categories of product
		 * sc/v1/getPointsByUser
		 *
		 * @param (str) {order}		=> ('asc'||'desc')
		 * @param (str) {orderBy}	=> ('id'||'name'||'popularity'||'date')
		 * @param (int) {page}		=> ({}>0)
		 * @param (int) {pp}		=> ({}>=0)
		 *
		 * @return json
		 */
		register_rest_route($this->namespace, 'availablePointsForCart', [
			'methods' 				=> 'POST',
			'callback' 				=> array( $this, 'availablePointsForCart'),
			'permission_callback' 	=> array( $this, 'verifyAccessTokenPermissionCallback' ),
		]);
	}


}

/**
 * Initialize Rest API Route  
 *
 * @author Manthan Kanani	
 * @since 2.8.0
**/
add_action('rest_api_init', function(){
	$controller = new REST_API_v1_Controller_RewardPoints();
	$controller->register_routes();
});
