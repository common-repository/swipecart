<?php

/**
 * @author Manthan Kanani
 */
class WPSC_REST_Extend_Args {

	const REST_POINT_ARGUMENT = 'point';

	/**
	 * Constructor to extend route parameter  
	 *
	 * @author Manthan Kanani	
	 * @since 2.8.0
	**/
	function __construct(){
		$this->namespace 		= REST_API_v1_Controller::NAMESPACE;
		$this->checkoutRoute 	= "/GenerateCheckout";

		$this->pointParam 		= self::REST_POINT_ARGUMENT;
	}


	/**
	 * Add hooks in order to enable functionality for checkout  
	 *
	 * @author Manthan Kanani	
	 * @since 2.8.0
	**/
	public function add_hooks() {
		add_action('swipecart_after_addtocart', array($this, 'before_checkout_initiate'));
		add_filter('rest_endpoints', array($this, 'rest_endpoints'));
		add_filter('rest_request_before_callbacks', array( $this, 'rest_request_before_callbacks'), 10, 3);
	}

	/**
	 * It will add required parameter for points.
	 *
	 * @param array $endpoints
	 * 
	 * @return array
	 *
	 * @since 2.8.0
	 */
	public function rest_endpoints($endpoints){
		foreach($endpoints as $route => &$endpoint){
			if(strpos($route, $this->namespace.$this->checkoutRoute)){
				foreach($endpoint as $key => &$data){
					if(is_numeric($key)){
						$data['args'][$this->pointParam] = array(
							'required'    			=> false,
							'type'        			=> "integer",
							'sanitize_callback'		=> "absint",
						);
					}
				}
			}
		}
		return $endpoints;
	}


	/**
	 * If generated URL is checkout, then set points into queryparameters.
	 *
	 * @param \WP_REST_Response|array|mixed $response
	 * @param \WP_REST_Server|array|mixed   $rest_server
	 * @param \WP_REST_Request              $request
	 *
	 * @return mixed
	 * 
	 * @since 2.8.0
	 */
	public function rest_request_before_callbacks($response, $rest_server, $request){
		$route = $request->get_route();
		
		if(strpos($route, $this->namespace.$this->checkoutRoute)){
			$applied_points 		= $request->get_param($this->pointParam);
			$pointParam 			= $this->pointParam;

			if($applied_points){
				add_filter('swipecart_checkout_query_arg', function($arg) use ($applied_points, $pointParam) {
					$arg["meta"][$pointParam] = $applied_points;
					return $arg;
				}, 9, 1);
			}
		}
		return $response;
	}


	/**
	 * set point before checkout initiated  
	 *
	 * @author Manthan Kanani	
	 * @since 2.8.0
	**/
	public function before_checkout_initiate($meta){
		if(!$meta) return;

		$point = $meta[$this->pointParam];

		$discount_amount = !empty($point) ? absint($point) : 0;
		if (is_user_logged_in() || is_admin()){
			WC()->session->set('wc_points_rewards_discount_amount', $discount_amount);
			$discount_code = WC_Points_Rewards_Discount::generate_discount_code();
			WC()->cart->add_discount($discount_code);
		}
	}
}
