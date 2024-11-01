<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/**
 * Swipecart Back in Stock Integration Initialization
 *
 * @package Swipecart
 * @author Manthan Kanani
 * @since 2.4.0
**/
class SC_BackInStock{

	/**
	 * constructor for the integration  
	 *
	 * @author Manthan Kanani	
	 * @since 2.4.0
	**/
	function __construct(){
		$this->__prePostUpdateForProduct();
	}

	/**
	 * constructor for the integration 
	 * 
	 * @param $data string|array {
	 * 		@type  $body  json array of id and qty 
	 * }
	 * @return json 
	 *
	 * @author Manthan Kanani	
	 * @since 2.4.0
	 * @since 2.7.0
	**/
	public function __sendStockEvent($data, $event){
		if(class_exists('WC_SC_Webhook')){
			$webhook  		= new WC_SC_Webhook();
			return $webhook->__remoteProductQtyAction($data, $event);
		}
		return false;
	}

	/**
	 * pre Post update filter.  
	 *
	 * @author Manthan Kanani	
	 * @since 2.4.0
	**/
	private function __prePostUpdateForProduct(){
		add_action('pre_post_update', [$this, "__checkNonVariableStockDiff"]);
		add_action('woocommerce_update_product_variation', [$this, "__checkVariableStockDiff"]);
	}

	/**
	 * Check difference between stocks.  
	 *
	 * @author Manthan Kanani	
	 * @since 2.4.0
	 * @version 2.7.0
	**/
	public function __checkNonVariableStockDiff($post){
		global $post;
		if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
		if(get_post_type($post)!=='product') return;

		$product 				= wc_get_product($post);
		$request_qtyUp 			= array();

		if($product->is_type('variable')){
			$stockfluctuation 		= 0;

			$before_stock_qty		= $product->get_stock_quantity();
			$before_manage_stock 	= $product->managing_stock();
			$after_manage_stock 	= isset($_POST['_manage_stock'])&&$_POST['_manage_stock']=="yes"? true : false ;

			$poduct_var 				= array();

			foreach($product->get_available_variations() as $i){
				$variation_id 		= $i['variation_id'];
				$variation 			= new WC_Product_variation($variation_id);

				if($variation->managing_stock()!== "parent") continue;

				$poduct_var[] 	= array(
					"id" 	=> $variation_id,
					"qty" 	=> $_POST['_stock'],
				);
			}

			if($before_manage_stock === $after_manage_stock){
				if($before_manage_stock){
					$before_stock_qty	= $product->get_stock_quantity();
					$after_stock_qty 	= $_POST['_stock'];

					if($after_stock_qty > $before_stock_qty){
						$stockfluctuation 	= 1;

						// before 0 then send notification to variation
						if($before_stock_qty == 0){
							$request_qtyUp = $poduct_var;
						}
						// notification array generated
					} elseif($after_stock_qty < $before_stock_qty) {
						$stockfluctuation 	= -1;
					}
				}
			} else {
				if($after_manage_stock){
					$before_stock_qty	= 0;  // undefined stocks defined as 0
					$after_stock_qty 	= $_POST['_stock'];

					if($after_stock_qty > $before_stock_qty){
						$stockfluctuation 	= 1;

						// before undefined then send notification to variation
						if($before_stock_qty == 0){
							$request_qtyUp = $poduct_var;
						}
						// notification array generated
					}
				}
			}
		} else {
			$stockfluctuation 		= 0;

			$before_manage_stock 	= $product->managing_stock()? "yes" : "no" ;
			$after_manage_stock 	= isset($_POST['_manage_stock'])&&$_POST['_manage_stock']=="yes"? "yes" : "no" ;

			if($before_manage_stock == $after_manage_stock){
				if($before_manage_stock == "no"){
					$before_stock_status	= $product->get_stock_status();
					$after_stock_status 	= $_POST['_stock_status'];

					if($before_stock_status !== "instock" && $after_stock_status == "instock"){
						$stockfluctuation 	= 1;
						
						// always send notification product
						$request_qtyUp[] 	= array(
							"id" 	=> $product->get_id(),
							"qty" 	=> $after_stock_status
						);
						// notification array generated
					} elseif($before_stock_status == "instock" && $after_stock_status !== "instock") {
						$stockfluctuation 	= -1;
					}
				} elseif($before_manage_stock == "yes") {
					$before_stock_qty	= $product->get_stock_quantity();
					$after_stock_qty 	= $_POST['_stock'];

					if($after_stock_qty > $before_stock_qty){
						$stockfluctuation 	= 1;

						// before 0 then send notification product
						if($before_stock_qty == 0){
							$request_qtyUp[] 	= array(
								"id" 	=> $product->get_id(),
								"qty" 	=> $after_stock_qty
							);
						}
						// notification array generated
					} elseif($after_stock_qty < $before_stock_qty) {
						$stockfluctuation 	= -1;
					}
				}
			} else {
				if($before_manage_stock == "no" && $after_manage_stock == "yes"){
					$before_stock_status	= $product->get_stock_status();
					$after_stock_qty 		= $_POST['_stock'];
					
					if($before_stock_status!=="instock" && ($after_stock_qty > 0)){
						$stockfluctuation 	= 1;

						// always send notification product
						$request_qtyUp[] 	= array(
							"id" 	=> $product->get_id(),
							"qty" 	=> $after_stock_qty
						);
						// notification array generated
					} elseif($before_stock_status=="instock" && ($after_stock_qty = 0)) {
						$stockfluctuation 	= -1;
					}
				} elseif($before_manage_stock == "yes" && $after_manage_stock == "no") {
					$before_stock_qty	= $product->get_stock_quantity();
					$after_stock_status 	= $_POST['_stock_status'];

					if($after_stock_status == "instock" && ($before_stock_qty <= 0)){
						$stockfluctuation 	= 1;

						// before 0 then send notification product
						if($before_stock_qty == 0){
							$request_qtyUp[] 	= array(
								"id" 	=> $product->get_id(),
								"qty" 	=> $after_stock_status
							);
						}
						// notification array generated
					} elseif($after_stock_status !== "instock" && ($before_stock_qty > 0)) {
						$stockfluctuation 	= -1;
					}
				}
			}
		}
		if(is_array($request_qtyUp) && count($request_qtyUp)>0){
			$this->__sendStockEvent($request_qtyUp, "up");
		}
	}

	/**
	 * Check changes between variable product.  
	 *
	 * @author Manthan Kanani	
	 * @since 2.4.0
	 * @version 2.7.0
	**/
	public function __checkVariableStockDiff($post_id){
		if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
		if(get_post_type($post_id)!=='product_variation') return;

		$request_qtyUp 		= array();
		$keys 				= array_keys($_POST['variable_post_id']??array());

		foreach($keys as $key){
			$variation_id = $_POST['variable_post_id'][$key];

			$variation 				= new WC_Product_variation($variation_id);
			$before_managing_stock 	= $variation->managing_stock(); 		// true, false, parent
			$before_manage_stock 	= $before_managing_stock? "yes" : "no" ;  // (true || parent) then 1 else 0;
			$after_manage_stock 	= isset($_POST['variable_manage_stock'][$key]) && $_POST['variable_manage_stock'][$key]=="on"? "yes" : "no" ;


			if($before_manage_stock == $after_manage_stock){
				if($before_manage_stock == "no"){
					$before_stock_status	= $variation->get_stock_status();
					$after_stock_status 	= $_POST['variable_stock_status'][$key];

					if($before_stock_status !== "instock" && $after_stock_status == "instock"){
						$stockfluctuation 	= 1;

						// always send notification to variation
						$request_qtyUp[] 	= array(
							"id" 	=> $variation_id,
							"qty" 	=> $after_stock_status
						);
						// notification array generated
					} elseif($before_stock_status == "instock" && $after_stock_status !== "instock") {
						$stockfluctuation 	= -1;
					}
				} elseif($before_manage_stock == "yes") {
					if($before_managing_stock == "parent"){
						$product_id 			= $variation->get_parent_id();
						$product 				= wc_get_product($product_id);
						$before_stock_qty		= $product->get_stock_quantity();
						$after_stock_qty 		= $_POST['variable_stock'][$key];

						if($after_stock_qty > $before_stock_qty){
							$stockfluctuation 	= 1;

							// before 0 then send notification
							if($before_stock_qty == 0){
								$request_qtyUp[] 	= array(
									"id" 	=> $variation_id,
									"qty" 	=> $after_stock_qty
								);
							}
							// notification array generated
						} elseif($after_stock_qty < $before_stock_qty) {
							$stockfluctuation 	= -1;
						}
					} else {
						$before_stock_qty	= $variation->get_stock_quantity();
						$after_stock_qty 	= $_POST['variable_stock'][$key];

						if($after_stock_qty > $before_stock_qty){
							$stockfluctuation 	= 1;

							// before 0 then send notification
							if($before_stock_qty == 0){
								$request_qtyUp[] 	= array(
									"id" 	=> $variation_id,
									"qty" 	=> $after_stock_qty
								);
							}
							// notification array generated
						} elseif($after_stock_qty < $before_stock_qty) {
							$stockfluctuation 	= -1;
						}
					}
				}
			} else {
				if($before_manage_stock == "no" && $after_manage_stock == "yes"){
					$before_stock_status	= $variation->get_stock_status();
					$after_stock_qty 		= $_POST['variable_stock'][$key];
					
					if($before_stock_status!=="instock" && ($after_stock_qty > 0)){
						$stockfluctuation 	= 1;

						// always send notification to variation
						$request_qtyUp[] 	= array(
							"id" 	=> $variation_id,
							"qty" 	=> $after_stock_qty
						);
						// notification array generated
					} elseif($before_stock_status=="instock" && ($after_stock_qty = 0)) {
						$stockfluctuation 	= -1;
					}
				} elseif($before_manage_stock == "yes" && $after_manage_stock == "no") {
					if($before_managing_stock == "parent"){
						// This thing is not possible. because, Before managing stocks coming from parent
					} else {
						$before_stock_qty		= $variation->get_stock_quantity();
						$after_stock_status		= $_POST['variable_stock_status'][$key];

						if($after_stock_status == "instock" && ($before_stock_qty <= 0)){
							$stockfluctuation 	= 1;

							// before 0 then send notification product
							if($before_stock_qty == 0){
								$request_qtyUp[] 	= array(
									"id" 	=> $variation_id,
									"qty" 	=> $after_stock_status
								);
							}
							// notification array generated
						} elseif($after_stock_status !== "instock" && ($before_stock_qty > 0)) {
							$stockfluctuation 	= -1;
						}
					}
				}
			}
		}
		
		if(is_array($request_qtyUp) && count($request_qtyUp)>0){
			$this->__sendStockEvent($request_qtyUp, "up");
		}
	}

}
new SC_BackInStock();