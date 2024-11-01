<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/**
 * Swipecart Reward Plugin Integration
 *
 * @package Swipecart
 * @author Manthan Kanani
 * @since 2.8.0
**/
class Swipecart_PointsnRewards{

	/**
	 * constructor for the woocommerce points and rewards Integrations  
	 * 
	 * @author Manthan Kanani	
	 * @since 2.8.0
	**/
	function __construct(){

	}


	/**
	 * get points by user_id
	 *
	 * @author Manthan Kanani	
	 * @since 2.8.0
	**/
	public function get_rewardpoints($user_id){
		if(class_exists("WC_Points_Rewards_Manager")){
			return WC_Points_Rewards_Manager::get_users_points($user_id);
		}
		return false;
	}


	/**
	 * get reward point history by type
	 *
	 * @author Manthan Kanani	
	 * @since 2.8.0
	**/
	public function get_rewardpoints_history($user_id, $order='desc', $orderBy='date', $page=1, $pp=10, $type=[]){
		if(!class_exists("WC_Points_Rewards_Manager")) return false;
		
		$args = array(
			'calc_found_rows' => true,
			'orderby' => array(
				'field' => $orderBy,
				'order' => $order,
			),
			'per_page' => $pp,
			'paged'    => $page,
			'user'     => $user_id
		);
		$moments = WC_Points_Rewards_Points_Log::get_points_log_entries($args);

		$result = array_map(function($moment){
			return array(
				'id'  				=> $moment->id,
				'points'			=> $moment->points,
				'type'				=> $moment->type,
				'order_id'			=> (string) $moment->order_id ?: "" ,
				'date'				=> date("Y-m-d\TH:i:s\Z", strtotime($moment->date)),
				'description'		=> $moment->description
			);
		}, $moments);

		return $result;
	}



	/**
	 * get discounted item and points
	 * 
	 * @author Manthan Kanani
	 * @since 2.8.0
	**/
	public function get_discount_and_redeeming_points($cart, $user_id){
		global $wc_points_rewards;
		$available_user_discount = WC_Points_Rewards_Manager::get_users_points_value($user_id);

		if($available_user_discount <= 0){
			return false;
		}

		if('yes' === get_option('wc_points_rewards_partial_redemption_enabled')){
			$requested_user_discount = WC_Points_Rewards_Manager::calculate_points_value("");
			if($requested_user_discount > 0 && $requested_user_discount < $available_user_discount ) {
				$available_user_discount = $requested_user_discount;
			}
		}

		$minimum_discount = get_option('wc_points_rewards_cart_min_discount', '');
		if($minimum_discount > $available_user_discount){
			return false;
		}

		$discount_applied = 0;

		$api 	= new APIClass();
		$lines 	= $api->generateCart($cart);

		foreach($lines["cart"] as $item){
			$product =  $item['post_type'] == "product" ? wc_get_product($item["id"]) : new WC_Product_Variation($item["id"]);
			$max_discount = WC_Points_Rewards_Product::get_maximum_points_discount_for_product($product);

			if(is_numeric($max_discount)){
				$discount 	=  $max_discount * $item['quantity'];
			} else {
				if(function_exists('wc_get_price_excluding_tax')){
					$max_discount = wc_get_price_excluding_tax( $product, array('qty' => $item['quantity']));
				} elseif (method_exists( $product, 'get_price_excluding_tax' ) ) {
					$max_discount = $product->get_price_excluding_tax( $item['quantity'] );
				} else {
					$max_discount = $product->get_price( 'edit' ) * $item['quantity'];
				}
				$discount 	=  $max_discount;
			}

			$discount 	= ($available_user_discount <= $discount) ? $available_user_discount : $discount;
			$discount_applied += $discount;
			$available_user_discount -= $discount;
		}
		$discount_applied = max(0, min($discount_applied, $lines["pricing"]["subtotal"]));
		
		$max_discount = get_option('wc_points_rewards_cart_max_discount');
		if(false !== strpos($max_discount, '%')){
			$percentage = str_replace('%', '', $max_discount) / 100;
			$discount 	= $cart["pricing"]["subtotal"];
			$max_discount = $percentage * $discount;
		}

		$max_discount = filter_var( $max_discount, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
		if($max_discount && $max_discount < $discount_applied){
			$discount_applied = $max_discount;
		}

		$discount_available = filter_var($discount_applied, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

		if(!$discount_available){
			return false;
		}
		$points  = WC_Points_Rewards_Manager::calculate_points_for_discount($discount_available);

		// Message generation method
		$message 	= get_option('wc_points_rewards_redeem_points_message');
		$message 	= str_replace('{points}', number_format_i18n($points), $message);
		$message 	= str_replace('{points_value}', wc_price($discount_available), $message);
		$message 	= str_replace('{points_label}', $wc_points_rewards->get_points_label($points), $message);

		return array(
			"point" 		=> $points,
			"points_value" 	=> $discount_available,
			"subtotal"		=> $lines["pricing"]["subtotal"],
			"message"		=> $message
		);
	}


	/**
	 * check point conversions for rewards
	 *
	 * @author Manthan Kanani	
	 * @since 2.8.0
	**/
	public function earn_points_ratio(){
		$default 			= '1:1';
		$earning_ratio 		= get_option('wc_points_rewards_earn_points_ratio', $default);

		list($points, $monetary_value) = explode(':', $earning_ratio);
		return array(
			"points" 			=> $points,
			"monetary_value" 	=> $monetary_value,
		);
	}


	/**
	 * check point conversions when he redeem
	 *
	 * @author Manthan Kanani	
	 * @since 2.8.0
	**/
	public function redeem_points_ratio(){
		$default 			= '100:1';
		$redeem_ratio 		= get_option('wc_points_rewards_redeem_points_ratio', $default);

		list($points, $monetary_value) = explode(':', $redeem_ratio);
		return array(
			"points" 			=> $points,
			"monetary_value" 	=> $monetary_value,
		);
	}


	/**
	 * check point conversions when he redeem
	 *
	 * @author Manthan Kanani	
	 * @since 2.8.0
	**/
	public function point_label(){
		$default 			= 'Point:Points';
		$points_label 		= get_option('wc_points_rewards_points_label', $default );

		list( $singular, $plural ) = explode( ':', $points_label );
		return array(
			"singular" 		=> $singular,
			"plural" 		=> $plural,
		);
	}


	/**
	 * earning points rounding
	 *
	 * @author Manthan Kanani	
	 * @since 2.8.0
	**/
	public function earn_points_rounding(){
		$default 			= 'round'; 
		$rounding_option 	= get_option('wc_points_rewards_earn_points_rounding', $default);
		if(in_array($rounding_option, ['round','ceil','floor'])){
			return $rounding_option;
		}
		return $default;
	}


	/**
	 * partial redemption enabled
	 *
	 * @author Manthan Kanani	
	 * @since 2.8.0
	**/
	public function partial_redemption_enabled(){
		$default 			= 'no';
		$partial_redemption = get_option('wc_points_rewards_partial_redemption_enabled', $default);

		return($partial_redemption == 'yes') ? true : false;
	}


	/**
	 * get minimum discount points
	 * get maximum discount points or % based on cart total
	 * 
	 * @author Manthan Kanani	
	 * @since 2.8.0
	**/
	public function minmax_discount(){
		$min_discount 		= get_option('wc_points_rewards_cart_min_discount', '');
		$max_discount 		= get_option('wc_points_rewards_cart_max_discount', '');

		return array(
			'min' 		=> $min_discount,
			'max' 		=> $max_discount,
		);
	}


	/**
	 * get maximum discount product points or % based on per product
	 * 
	 * @author Manthan Kanani	
	 * @since 2.8.0
	**/
	public function max_product_discount(){
		$max_discount 	= get_option('wc_points_rewards_max_discount');

		return array(
			'max' 	=> $max_discount
		);
	}
}