<?php

defined('ABSPATH') or die('No script kiddies please!');

/**
 * Swipecart Checkout Initialization
 *
 * @package Swipecart
 * @author Manthan Kanani
 * @since 1.0.0
**/
class Swipecart_Checkout{

	const ORDERMETA = '_swipecart_order';

	/**
	 * Constructor cart and checkout after redirection  
	 *
	 * @author Manthan Kanani	
	 * @since 2.0.0
	 * @since 2.8.0
	**/
	function __construct(){	
		$this->mobileOrderMeta = self::ORDERMETA;

		add_action('wp', [ $this, '__before_cart' ], 10,1);
		add_filter('woocommerce_after_order_notes', [ $this, '__after_notes_fields'], 10, 1);
		add_action('woocommerce_checkout_update_order_meta', [ $this, '__after_checkout'], 10, 1);
	}

	/**
	 * before checkout redirection  
	 *
	 * @author Manthan Kanani	
	 * @since 2.0.4
	 * @version 2.8.0
	**/
	public function __before_cart(){
		$utility  		= new Utility();
		$api 			= new APIClass();
		$meta 			= array();
		$cart_count 	= 0;

		if((is_checkout() || is_cart()) && isset($_GET['items'])){
			if(isset($_GET["meta"]) && !empty($_GET["meta"])){
				$meta = $utility->encrypt_decrypt($_GET['meta'], 'decrypt');
				$meta = json_decode($meta, true);
			}
			if(!is_user_logged_in() && isset($_GET['auth']) && $authToken = $utility->encrypt_decrypt($_GET['auth'], 'decrypt')){
				if($user = $api->tokenValidation($authToken)){
					wp_clear_auth_cookie();
					wp_set_current_user($user->ID, $user->user_login);
					wp_set_auth_cookie($user->ID, true);
					do_action('wp_login', $user->user_login, $user);
				}
			}

			$line_items 	= $utility->encrypt_decrypt($_GET['items'], 'decrypt');
			WC()->cart->empty_cart();

			do_action('swipecart_before_addtocart', $meta);

			$body = sanitize_text_field($line_items);
			$datas = json_decode($body, TRUE);

			if (json_last_error() === 0) {
				foreach($datas as $key => $data){
					$product_cart_id = WC()->cart->generate_cart_id( $data['id'] );
					
					if(!WC()->cart->find_product_in_cart( $product_cart_id )) {  
						WC()->cart->add_to_cart($data['id'], $data['quantity'], $data['variation_id']??null, $data['variation']??null);
					}
					$cart_count++;
			    }
			}

			do_action('swipecart_after_addtocart', $meta);
			if(!is_checkout() && $cart_count){
				$this->ordermeta = $_GET['ordermeta'];
				add_action('template_redirect', [$this, 'redirect_to_checkout']);
			}
		}
	}

	/**
	 * after checkout update order meta  
	 *
	 * @author Manthan Kanani	
	 * @since 2.0.0
	**/
	public function __after_checkout($order_id){
		if(isset($_POST[$this->mobileOrderMeta])) {
			update_post_meta( $order_id, $this->mobileOrderMeta, sanitize_text_field($_POST[$this->mobileOrderMeta]) );
		}
	}

	/**
	 * redirect to checkout page  
	 *
	 * @author Manthan Kanani	
	 * @since 2.8.0
	**/
	public function redirect_to_checkout(){
		$url = add_query_arg(array(
			'ordermeta'	 => $this->ordermeta
		), wc_get_checkout_url());
		wp_redirect($url);
	}


	/**
	 * get order by order meta  
	 *
	 * @author Manthan Kanani	
	 * @since 2.0.0
	**/
	public function __getOrderByMeta($ordermeta){
		$args = array(
			'orderby' 		=> 'id',
			'order' 		=> 'desc',
			'return' 		=> 'ids',
			'limit' 		=> 1,
			'paged' 		=> 1,
			'meta_key'     	=> $this->mobileOrderMeta,
			'meta_compare' 	=> '=',
			'meta_value' 	=> $ordermeta,
		);
		$order = wc_get_orders($args);
		return (count($order)) ? wc_get_order($order[0]) : false ;
	}

	/**
	 * after checkout note field  
	 *
	 * @author Manthan Kanani	
	 * @since 2.0.0
	**/
	public function __after_notes_fields($checkout){
		if(isset($_GET['ordermeta']) && !empty($_GET['ordermeta'])){
			echo '<input type="hidden" class="input-hidden" name="'.$this->mobileOrderMeta.'" value="'.$_GET['ordermeta'].'">';
		}
	}

	/**
	 * encryption of the checkout  
	 *
	 * @author Manthan Kanani	
	 * @since 2.0.5
	 * @version 2.8.0
	**/
	public function __checkout_query_arg($checkout_data){
		$utility  		= new Utility();

		$checkout_data = apply_filters('swipecart_checkout_query_arg', $checkout_data);
		$checkout_data = array_filter($checkout_data, function($val){return $val;});

		$result = array_map(function($v) use ($utility) {
			if(gettype($v) == "array") $v = json_encode($v);
			return $utility->encrypt_decrypt($v, 'encrypt');
		}, $checkout_data);

		$checkout_url 	= add_query_arg($result, wc_get_cart_url());
		$ordermetaid 	= $result['ordermeta'];

		$checkout_query_arg = array(
			'url' 		=> $checkout_url,
			'id'		=> $ordermetaid
		);

		$checkout_query_arg = apply_filters('swipecart_checkout_return_params', $checkout_query_arg);

		return $checkout_query_arg;
	}
}
