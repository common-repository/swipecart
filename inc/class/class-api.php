<?php

defined('ABSPATH') or die('No script kiddies please!');

/**
 * APIClass to Read Data for API
 * 
 * @package Swipecart
 * @author Manthan Kanani
 * @since 1.0.0
**/
class APIClass{

	/**
	 * Constructor for common items  
	 *
	 * @author Manthan Kanani	
	 * @since 2.1.0
	 * @version 2.6.4
	**/
	function __construct(){
		$this->nonceKey 		= 'swipecart_nonce';
	}

	/**
	 * AuthCombo retrive  
	 *
	 * @author Manthan Kanani	
	 * @since 2.6.4
	**/
	private function storeFrontToken(){
		$auth_generation 		= new SC_AuthGeneration();
		$authCombo 				= $auth_generation->getAuth();
		return $authCombo['StoreFrontToken'];
	}

	/**
	 * Currency Convertor  
	 *
	 * @author Manthan Kanani	
	 * @since 2.0.0
	 * @since 2.8.6
	**/
	static public function currency($price, $show_currency=true){
		if(!$show_currency){
			return floatval(number_format($price, 2, '.', ''));
		}
		$price_html		= wc_price($price);
		$price_entity 	= strip_tags($price_html);
		return html_entity_decode($price_entity);
	}

	/**
	 * Get Product by Categories  
	 *
	 * @author Manthan Kanani	
	 * @since 2.0.0
	 * @version 2.6.2
	**/
	static public function getCollectionByID($term_id, $is_really_product_category=true){
		/**===================Reserved for WPML or translation plugins===================**/
		$term_id 			= __SCML::translate_id('product_cat', $term_id);
		/**===================Reserved for WPML or translation plugins===================**/

		$term = get_term($term_id, 'product_cat');
		if(!$term) return false;
		
	
		$thumb_id   = get_term_meta($term_id, 'thumbnail_id', true);
		$img 		= ($thumb_id) ? wp_get_attachment_image_src($thumb_id,'full')[0] : wc_placeholder_img_src('full') ;

		return array(
			'value'        		=> (string) $term->term_id,
			'icon'     			=> $img,
			'label'      		=> wp_specialchars_decode($term->name),
			'product_page_id' 	=> 57,
			'count'     		=> $term->count,
			'parent'      		=> $term->parent,
		);
	}


	/**
	 * Get Product by Categories  
	 *
	 * @author Manthan Kanani	
	 * @since 2.2.1
	**/
	static public function getAllCollectionsCategories($order='desc', $orderBy='id', $page=1, $pp=10, $search=""){
		$result = array();

		$offset = ($pp * $page) - $pp;
		$args = array(
			'hide_empty'        => true,
			'number'        	=> $pp,
			'fields' 			=> 'ids',
			'offset'        	=> $offset,
		);

		if($orderBy) $args['orderby'] 	= $orderBy;
		if($order) $args['order'] 		= $order;
		if($search) $args['search'] 	= $search;

		$categories = get_terms("product_cat", $args);

		$result = array_map(function($term_id) {
			return APIClass::getCollectionByID($term_id);
		}, $categories);

		return $result;
	}




	/**
	 * Get Product by ID  
	 *
	 * @author Manthan Kanani	
	 * @since 2.0.4
	 * @version 2.8.0
	**/
	static public function getProductByID($product_id, $is_really_product=true){
		if(!$is_really_product){
			if(get_post_type($product_id)!=='product') return false;
		}

		/**===================Reserved for WPML or translation plugins===================**/
		$product_id 			= __SCML::translate_id('product', $product_id);
		/**===================Reserved for WPML or translation plugins===================**/

		$terms 					= array();
		$tags 					= array();
		$product_images 		= array();
		$product_attribute 		= array();
		$option_tags			= array();
		$variation 				= array();
		$product 				= wc_get_product( $product_id );
		$max_puchasable_qty 	= 99;

		$product_terms 	= get_the_terms($product_id,'product_cat');
		$product_tags 	= get_the_terms($product_id,'product_tag');

		if($product_terms){
			foreach(get_the_terms($product_id,'product_cat') as $term){
				$terms[] = array(
					'id'	=> $term->term_id,
					'name'	=> wp_specialchars_decode($term->name),
					'slug'	=> $term->slug,
				);
			}
		}

		if($product_tags){
			foreach(get_the_terms($product_id,'product_tag') as $tag){
				$tags[] = array(
					'id'	=> $tag->term_id,
					'name'	=> wp_specialchars_decode($tag->name),
					'slug'	=> $tag->slug,
				);
			}
		}

		$gallery_images 	= $product->get_gallery_image_ids() ?? array() ;
		$feature_image 		= ($product->get_image_id())? array($product->get_image_id()) : array() ;
		$images 			= array_unique(array_merge($feature_image, $gallery_images));
		if($images){
			foreach($images as $image_id){
				$img_src = wp_get_attachment_image_src( $image_id, 'full' )[0];
				$product_images[] = $img_src;
			}
		}


		if($product->is_type('variable')) {
			foreach($product->get_available_variations() as $i){
				$vars 				= array_values($i['attributes']);
				$vars 				= implode(' / ', $vars);

				$attributes = array_map(function($a, $b){
					return array(
						'name' => wc_attribute_label(str_replace('attribute_', '', $a)),
						'value' => $b
					);
				}, array_keys($i['attributes']), array_values($i['attributes']));

				$variation_image = $i['image']['url']??'';
				if($variation_image && !in_array($variation_image, $product_images)){
					$product_images[] = $variation_image;
				}

				$variation[] = array(
					'id' 				=> (string) $i['variation_id'],
					'price' 			=> (string) $i['display_price'],
					'title' 			=> wp_specialchars_decode($vars),
					'sku' 				=> $i['sku'],
					'qty' 				=> $i['is_in_stock'] ? ($i['max_qty']?:$max_puchasable_qty) : 0,
					'position' 			=> '',
					'inventoryPolicy' 	=> '',
					'compareAtPrice' 	=> $i['display_regular_price']==$i['display_price']?'':(string)$i['display_price'],
					'image' 			=> $variation_image,
					'availableForSale' 	=> $i['is_in_stock'] && $i['is_purchasable'],
					'selectedOptions' 	=> $attributes,
				);
			}
			foreach($product->get_variation_attributes() as $attribute_name=>$attribute) {
				$product_attribute[] = array(
					'name'   => wc_attribute_label(str_replace('attribute_', '', $attribute_name)),
					'values' => array_values($attribute),
				);
			}

			$variationPrice = [
				'min' => $product->get_variation_price(),
				'max' => $product->get_variation_price('max')
			];
		} else {
			/**
			 * ================================================
			 * Reserved for Simple Product as Custom Variation
			**/
			$variation[] = array(
				'id' 				=> (string) $product_id,
				'price' 			=> (string) $product->get_price(),
				'title' 			=> '',
				'sku' 				=> $product->get_sku(),
				'qty' 				=> $product->is_in_stock() ? ($product->get_max_purchase_quantity()>0?$product->get_max_purchase_quantity():$max_puchasable_qty) : 0,
				'position' 			=> '',
				'inventoryPolicy' 	=> '',
				'compareAtPrice' 	=> $product->get_regular_price()==$product->get_price()?'':(string)$product->get_regular_price(),
				'image' 			=> $product_images[0]??'',
				'availableForSale' 	=> $product->is_in_stock() && $product->is_purchasable(),
				'selectedOptions' 	=> array(),
			);
			/** 
			 * Reserved for Simple Product as Custom Variation
			 * ================================================
			**/

			if($product->get_attributes()){
				$product_attribute = array();
				foreach($product->get_attributes() as $attribute) {
					$options = array();
					if(isset($attribute['is_taxonomy']) && $attribute['is_taxonomy']){
						$options = wc_get_product_terms($product->get_id(), $attribute['name'], array('fields' => 'names'));
					} elseif (isset($attribute['value'])) {
						$options = array_map('trim', explode('|', $attribute['value']));
					}

					$product_attribute[] = array(
						'name'      => wc_attribute_label($attribute['name']),
						'slug'      => wc_attribute_taxonomy_slug($attribute['name']),
						'position'  => (int) $attribute['position'],
						'visible'   => (bool) $attribute['is_visible'],
						'variation' => (bool) $attribute['is_variation'],
						'values'   	=> $options,
					);
				}
			}

			$variationPrice = [
				'min' => $product->get_price(),
				'max' => $product->get_price()
			];
		}

		$optionTag = array();
		foreach($product_attribute as $option_tag){
			foreach($option_tag['values'] as $i){
				$optionTag[] = $i;
			}
		}

		$product_metas 		= $product->get_meta_data();
		$product_meta_loop 	= array();
		
		foreach($product_metas as $meta_key=>$meta_value) {
			$product_meta_loop[$meta_value->key] = $meta_value->value;
		}	

		return array(
			'id' 					=> (string) $product_id,
			'title'					=> wp_specialchars_decode($product->get_name()),
			'rating'				=> $product->get_average_rating(),
			'description'			=> $product->get_short_description(),
			'descriptionHtml'		=> $product->get_description(),
			'vendor'				=> 'admin',
			'productType'			=> $product->get_type(),
			'handle'				=> $product->get_slug(),
			'meta'					=> $product_meta_loop,
			'tags'					=> $optionTag,
			'options'				=> $product_attribute,
			'variants'				=> $variation,
			'images'				=> $product_images
		);
	}

	/**
	 * Get Order by ID  
	 *
	 * @author Manthan Kanani	
	 * @since 2.0.0
	 * @version 2.8.6
	**/
	public function getOrderByID($order_id, $user_id=null, $is_really_order=true){
		if(!$is_really_order){
			if(is_numeric($order_id) && get_post_type($order_id)!=='shop_order') return false;
		}

		if(is_numeric($order_id)){
			$order 				= wc_get_order($order_id);
		} else {
			$sc_checkout 		= new Swipecart_Checkout();
			$order 				= $sc_checkout->__getOrderByMeta($order_id);
			if($order){
				$order_id		= $order->get_id();
			} else {
				return false;
			}
		}

		if(!is_null($user_id) && $order->get_customer_id()!=$user_id) return false;

		$order_data 		= $order->get_data();
		$order_metas 		= $order->get_meta_data();
        $order_meta_loop 	= array();
		
		foreach($order_metas as $meta_key=>$meta_value) {
			$order_meta_loop[$meta_value->key] = $meta_value->value;
		}	
		
		if(!$is_really_order){
			$line_items 		= array();		
			foreach($order->get_items() as $item){
				$product			= $item->get_product();
				$product_img_id 	= $product->get_image_id();
				$img_src 			= wp_get_attachment_image_src( $product_img_id, 'full' )[0];

				$line_items[] = array(
					'price' 		=> $this->currency($product->get_price()),
					'title' 		=> $item->get_name(),
					'quantity' 		=> $item->get_quantity(),
					'image'			=> $img_src
				);
			}
		}

		/** Financial Status **/
		if(in_array($order_data['status'], ['completed'])){
			$financial_status = 'PAID';
		} elseif(in_array($order_data['status'], ['pending','processing','on-hold'])){
			$financial_status = 'UNPAID';
		} elseif(in_array($order_data['status'], ['refunded'])){
			$financial_status = 'REFUNDED';
		} elseif(in_array($order_data['status'], ['failed','cancelled'])){
			$financial_status = 'CANCELLED';
		} else {
			$financial_status = 'UNKNOWN';
		}

		/** Fulfilment Status **/
		$fulfillment_status = (in_array($order_data['status'], ['completed'])) ? 'FULFILLED' : 'UNFULFILLED' ;

		if(!$is_really_order){
			$order_billing 		= $order_data['billing'];
			$shippingAddress 	= array( $order_billing['address_1'], $order_billing['address_2'], $order_billing['city'], $order_billing['state'], $order_billing['postcode'], $order_billing['country'] );
			$shippingAddress 	= array_filter($shippingAddress);
			$shippingAddress 	= implode(", ", $shippingAddress);
			
			/** Pricing **/
			$pricing 			= array(
				array("name" => "subtotal", "value"=> $this->currency($order->get_subtotal())),
				array("name" => "shipping", "value"=> $this->currency($order_data['shipping_total'])),
				array("name" => "tax", "value"=> $this->currency($order_data['total_tax'] + $order_data['shipping_tax'] + $order_data['cart_tax'] + $order_data['discount_tax'] ) ),
				array("name" => "discount", "value"=> $this->currency($order_data['discount_total'])),
			);
		}

		$total 								= $this->currency($order_data['total']);

		$result['id'] 						= (string) $order_id;
		$result['name'] 					= "#".$order_id;
		$result['financialStatus'] 			= $financial_status;
		$result['fulfillmentStatus'] 		= $fulfillment_status;
		$result['processedAt'] 				= $order_data['date_modified']->date("Y-m-d\TH:i:s\Z");
		$result['createdAt'] 				= $order_data['date_created']->date("Y-m-d\TH:i:s\Z");
		$result['meta'] 					= ($order_meta_loop)?:array();

		if(!$is_really_order){
			$result['shippingAddress'] 		= $shippingAddress;
			$result['pricing'] 				= $pricing;
			$result['total'] 				= $total;
			$result['lineItems'] 			= $line_items;
		} else {
			$result['orderNumber'] 			= $order_id;
			$result['price'] 				= $total;
			$result['currentTotalPrice'] 	= $this->currency($order_data['total'], false);
			$result['lineItemCount']		= $order->get_item_count();
		}

		return $result;
	}


	/**
	 * Get Products List by ID
	 *
	 * @author Manthan Kanani	
	 * @since 2.2.1
	 * @version 2.8.0
	**/
	static public function getProductListByID($product_id, $is_really_product=true){
		if(!$is_really_product){
			$post_type = get_post_type($product_id);
			if(!in_array($post_type, ['product','product_variation'])) return false;
		} else {
			$post_type = 'product';
		}

		$product_images 	= array();
		$product_attribute 	= array();
		$variation_image 	= array();
		$instockQty 		= 1;
		$total_inventory 	= 0;

		if($post_type=="product"){
			$product  			= wc_get_product($product_id);
		} else {
			$varient_id 		= $product_id;
			$variation 			= new WC_Product_Variation($varient_id);
			$product_id 		= $variation->get_parent_id();
			$product  			= wc_get_product($product_id);

			$variation_image 	= ($variation->get_image_id())? array($variation->get_image_id()) : array();
		}

		$feature_image 		= ($product->get_image_id())? array($product->get_image_id()) : array();
		$feature_image 		= ($variation_image) ? array_merge($variation_image, $feature_image) : $feature_image; 
		$gallery_images 	= $product->get_gallery_image_ids() ?? array();
		$images 			= array_unique(array_merge($feature_image, $gallery_images));
		$images 			= array_filter($images);
		if($images){
			foreach($images as $image_id){
				$img_src = wp_get_attachment_image_src( $image_id, 'full' )[0];

				$product_images[] = array(
					'src' => $img_src,
				);
				break;
			}
		}

		if($product->is_type('variable')) {
			$parent_qty 	= $product->get_stock_quantity();

			$variationPrice = [
				'min' => $product->get_variation_price(),
				'max' => $product->get_variation_price('max')
			];
			$variationRegularPrice = [
				'min' => $product->get_variation_regular_price()==$product->get_variation_price()?'':$product->get_variation_regular_price(),
				'max' => $product->get_variation_regular_price('max')==$product->get_variation_price('max')?'':$product->get_variation_regular_price('max')
			];

			foreach($product->get_visible_children() as $variationId) {
				$variation 			= wc_get_product($variationId);
				if($variation->managing_stock()){
					$total_inventory   += $variation->get_stock_quantity();
				} else {
					$stock_status 		= $variation->get_stock_status();
					if($stock_status=="instock"){
						$total_inventory   	   += $instockQty;
					} elseif($stock_status=="parent") {
						if($parent_qty > 0){
							$total_inventory   += $instockQty;
						} else {
							$total_inventory   += 0;
						}
					}
				}
			}
		} else {
			$variationPrice = [
				'min' => $product->get_price(),
				'max' => $product->get_price()
			];
			$variationRegularPrice = [
				'min' => $product->get_regular_price()==$product->get_price()?'':$product->get_regular_price(),
				'max' => $product->get_regular_price()==$product->get_price()?'':$product->get_regular_price()
			];
			if($product->managing_stock()){
				$total_inventory = $product->get_stock_quantity();
			} else {
				$total_inventory = ($product->get_stock_status()=="instock") ? $instockQty : 0 ;
			}
		}

		return array(
			'value' 				=> (string) $product_id,
			'icon'					=> $product_images[0]['src']??wc_placeholder_img_src('full'),
			'label'					=> wp_specialchars_decode($product->get_name()),
			'rating'				=> $product->get_average_rating(),
			'product_page_id' 		=> 57,
			'min_compare_price'		=> is_numeric($variationRegularPrice['min']) ? (string) $variationRegularPrice['min'] : "",
			'max_compare_price'		=> is_numeric($variationRegularPrice['max']) ? (string) $variationRegularPrice['max'] : "",
			'totalInventory'		=> $total_inventory,
			'min_variant_price'		=> is_numeric($variationPrice['min']) ? (string) $variationPrice['min'] : "",
			'max_variant_price'		=> is_numeric($variationPrice['max']) ? (string) $variationPrice['max'] : "",
			'currency_code'			=> get_woocommerce_currency(),
		);
	}

	/**
	 * Get Line Single Item by ID
	 *
	 * @author Manthan Kanani	
	 * @since 2.0.4
	 * @version 2.8.0
	**/
	static public function getCartLineByID($varient_id){
		$result 				= array();
		$max_puchasable_qty 	= 99;

		$post_type 				= get_post_type($varient_id);
		if(!in_array($post_type, ['product','product_variation'])) return false;

		/**===================Reserved for WPML or translation plugins===================**/
		$varient_id 			= __SCML::translate_id($post_type, $varient_id);
		/**===================Reserved for WPML or translation plugins===================**/
		
		if($post_type=="product"){
			$varientTitle 		= "";
			$product  			= wc_get_product($varient_id);
			$product_id 		= $varient_id;
			$title 				= $product->get_name();
			$price 				= $product->get_price();
			$img_id 			= $product->get_image_id();
			$qty 				= $product->is_in_stock() ? ($product->get_max_purchase_quantity()>0?$product->get_max_purchase_quantity():$max_puchasable_qty) : 0;
			$available_for_sale = $product->is_in_stock() && $product->is_purchasable();
		} else {
			$varientTitle 		= "";
			$variation 			= new WC_Product_Variation($varient_id);
			$product_id 		= $variation->get_parent_id();
			$product  			= wc_get_product($varient_id);
			$title 				= $product->get_name();
			$price 				= $variation->regular_price;
			$product_attribute 	= array();
			foreach($variation->get_attributes() as $value) {
				$product_attribute[] = $value;
			}
			$varientTitle 		= implode(' / ', $product_attribute);
			$img_id 			= $variation->get_image_id();
			$qty 				= $variation->is_in_stock() ? ($variation->get_max_purchase_quantity()>0?$variation->get_max_purchase_quantity():$max_puchasable_qty) : 0;
			$available_for_sale = $variation->is_in_stock() && $variation->is_purchasable();
		}
		$icon 					= wp_get_attachment_image_src($img_id, "full")[0]??"";

		return array(
			"value" 			=> (string) $product_id,
			"varientTitle" 		=> wp_specialchars_decode($varientTitle),
			"icon" 				=> $icon,
			"label" 			=> wp_specialchars_decode($title),
			"product_page_id" 	=> 57,
			"product_id" 		=> (string) $product_id,
			"varient_id" 		=> (string) $varient_id,
			"qty"				=> $qty,
			"availableForSale" 	=> $available_for_sale,
			"price" 			=> (string) $price,
		);
	}


	/**
	 * Get Attribute terms by attr  
	 *
	 * @author Manthan Kanani	
	 * @since 2.2.3
	**/
	static public function getAttributeTerms($attr){
		$terms = get_terms(array(
			'taxonomy' 		=> 'pa_'.$attr->attribute_name,
			'hide_empty' 	=> false,
		));
		$tags = array_map(function($term) {
			return array(
				"id" 	=> $term->term_id,
				"label" => $term->name,
				"value" => $term->slug
			);
		}, $terms);
		$term = array(
			"sectionName" => $attr->attribute_label,
			"sectionValue" => $attr->attribute_name,
			"tags" => $tags
		);
		return $term;
	}


	/**
	 * Get Customer Orders  
	 *
	 * @author Manthan Kanani	
	 * @since 2.0.0
	 * @version 2.7.1
	**/
	public function getOrders($order='desc', $orderBy='id', $page=1, $pp=10, $customer_id=0, $filters=array()){
		$result 		= array();

		$args = array(
			'orderby' 		=> $orderBy,
			'order' 		=> $order,
			'return' 		=> 'ids',
			'limit' 		=> $pp,
			'paged' 		=> $page
		);
		if($customer_id) $args['customer_id'] = $customer_id;
		if($filters){
			if(isset($filters["swipecart"]) && $filters["swipecart"] == true) {
				$args['meta_key'] = Swipecart_Checkout::ORDERMETA;
				$args['meta_compare'] = 'EXISTS';
			}
		}

		$orders 	= wc_get_orders($args);
		$result = array_map(function($id) {
			return APIClass::getOrderByID($id);
		}, $orders);
		$result = array_filter($result);
		$result = array_values($result);

		return $result;
	}


	/**
	 * Get All Products with search,category,tag filter  
	 *
	 * @author Manthan Kanani	
	 * @since 1.0.0
	 * @version 2.6.9
	**/
	static public function getProducts($order='desc', $orderBy='id', $page=1, $pp=10, $category_ids=array(), $search='', $filters=array(), $onlyID=false){
		$result 		= array();
		$order 			= $order ?: 'desc';
		$orderBy 		= $orderBy ?: 'id';
		$search 		= $search ?: '';

		$args = array(
			'limit'			=> $pp,
			'paged'			=> $page,
			'status'		=> 'publish',
			'return'		=> 'ids'
		);
		if($search) $args['s'] 			= $search;
		if($order) $args['order'] 		= $order;

		if($orderBy == "best_selling"){
			$args['orderby'] 	= 'meta_value_num';
			$args['meta_key'] 	= 'total_sales';
			$args['meta_type'] 	= 'NUMERIC';
		} elseif($orderBy == "price"){
			$args['orderby'] 	= 'meta_value_num';
			$args['meta_key'] 	= '_price';
		} elseif($orderBy == "recommended"){
			$args['orderby'] 	= 'menu_order';
		} else {
			$args['orderby'] 	= $orderBy;
		}

		if($category_ids){
			$args['tax_query'] 	= array(
				array(
					'taxonomy'	=> 'product_cat',
					'field' 	=> 'term_id',
					'terms' 	=> $category_ids, 
					'operator' 	=> 'IN',
				)
			);
		}
		if($filters){
			$args['tax_query']['relation'] = 'AND';
			foreach($filters as $filter){
				$args['tax_query'][] = array(
					'taxonomy' 	=> 'pa_'.$filter['attribute'],
					'field' 	=> 'slug',
					'terms' 	=> $filter['terms'],
					'operator' 	=> 'IN'
				);
			}
		}

		$products 	= wc_get_products($args);
		if($onlyID){
			return $products;
		}

		$result = array_map(function($product_id) {
			return APIClass::getProductListByID($product_id, false);
		}, $products);

		return $result;
	}


	/** 
	 * Get Recommended Products
	 *
	 * @author Manthan Kanani
	 * @since 2.6.0
	**/
	static public function getRecommendedProducts($product_id='', $pp=10){
		$result 				= array();
		$products_ids 			= array(); 

		if($product_id && $parent_id = APIClass::getParentProductIdByVarientId($product_id)){
			$product 			= wc_get_product($parent_id);
			$category 			= wp_get_post_terms($parent_id, 'product_cat', array('fields' => 'ids'));

			$upsell_ids 		= $product->get_upsell_ids();
			$cosssell_ids 		= $product->get_cross_sell_ids();

			$products_ids 		= array_merge($upsell_ids, $cosssell_ids);
			$product_ids 		= array_unique($products_ids);

			if(count($products_ids) < $pp){
				$catrecom_ids		= APIClass::getProducts('desc', 'recommended', 1, $pp*10, array($category), '', array(), true);
				$products_ids 		= array_merge($products_ids, $catrecom_ids);
				$product_ids 		= array_unique($products_ids);
			}
		}
		if(count($products_ids) < $pp){
			$recommended_ids		= APIClass::getProducts('desc', 'recommended', 1, $pp*10, array(), '', array(), true);
			$products_ids 			= array_merge($products_ids, $recommended_ids);
			$product_ids 			= array_unique($products_ids);
		}

		foreach(array_keys($product_ids, $product_id) as $key) {
			unset($product_ids[$key]);
		}

		$product_ids 	= array_slice($product_ids, 0, $pp);
		$result = array_map(function($product_id) {
			return APIClass::getProductListByID($product_id);
		}, $product_ids);

		return $result;
	}


	/**
	 * get parent product ID by varient or product ID 
	 * 
	 * @author Manthan Kanani
	 * @since 2.6.0
	**/
	static public function getParentProductIdByVarientId($varient_id=''){
		$post_type 		= get_post_type($varient_id);

		if($post_type=="product"){
			return $varient_id;
		} elseif($post_type=="product_variation") {
			$variation 			= new WC_Product_Variation($varient_id);
			return $variation->get_parent_id();
		}
		return false;
	}


	/** 
	 * Get Line Items for Cart
	 *
	 * @author Manthan Kanani
	 * @since 2.2.1
	**/
	static public function GetProductsByVarientId($line_ids=array()){
		$result 		= array();
		$result = array_map(function($line) {
			return APIClass::getCartLineByID($line["varient_id"]);
		}, $line_ids);
		$result = array_filter($result);
		$result = array_values($result);

		return $result;
	}


	/** 
	 * Get Product Filters
	 *
	 * @author Manthan Kanani
	 * @since 2.2.1
	**/
	static public function getProductFilters(){
		$result 		= array();

		$attributes 	= wc_get_attribute_taxonomies();
		$attributes 	= array_values($attributes);
		$result = array_map(function($attr) {
			return APIClass::getAttributeTerms($attr);
		}, $attributes);
		$result = array_filter($result);
		$result = array_values($result);

		return $result;
	}


	/**
	 * Get All Products by IDs  
	 *
	 * @author Manthan Kanani	
	 * @since 2.2.1
	**/
	static public function GetProductsByIds($product_ids=array()){
		$result 		= array();

		$result = array_map(function($product_id) {
			return APIClass::getProductListByID($product_id, false);
		}, $product_ids);
		$result = array_filter($result);
		$result = array_values($result);

		return $result;
	}


	/**
	 * Get All Collections by IDs  
	 *
	 * @author Manthan Kanani	
	 * @since 2.0.0
	 * @version 2.6.2
	**/
	static public function GetCollectionsByIds($collection_ids=array()){
		$result 		= array();

		$result = array_map(function($category_id) {
			return APIClass::getCollectionByID($category_id);
		}, $collection_ids);
		$result = array_filter($result);
		$result = array_values($result);

		return $result;
	}


	/**
	 * Get Reviews by Product IDs  
	 *
	 * @author Manthan Kanani	
	 * @since 2.7.0
	 * @version 2.7.1
	**/
	static public function getReviewsByProductId($product_id, $user_id=null, $page=1, $pp=10){
		$result 				= array();
		$reviewed 				= false;
		$my_review_id 			= 0;

		$post_type 				= get_post_type($product_id);
		if(!in_array($post_type, ['product','product_variation'])) return false;

		/**===================Reserved for WPML or translation plugins===================**/
		$product_id 			= __SCML::translate_id($post_type, $product_id);
		/**===================Reserved for WPML or translation plugins===================**/
		
		if($post_type=="product_variation"){
			$variation 			= new WC_Product_Variation($product_id);
			$product_id 		= $variation->get_parent_id();
		}

		// get product review count and average ratings
		$product 		= wc_get_product($product_id);
		$rating  		= $product->get_average_rating();
		$count   		= $product->get_rating_count();
		
		//check if i already commented
		if(!is_null($user_id)){
			$my_review_count = get_comments(array(
				'post_id' => $product_id,
				'user_id' => $user_id,
				'count'   => true,
			));
			if($my_review_count){
				if($my_review_count==1){
					$my_comment = get_comments(array(
						'post_id' => $product_id,
						'user_id' => $user_id
					));
					$my_review_id = $my_comment[0]->comment_ID;
				}
				$reviewed = true;
			}
		}

		$comments = get_comments(array(
			'post_id' 	=> $product_id,
			'status' 	=> 'approve',
			'number' 	=> $pp,
			'offset' 	=> ($pp * $page) - $pp
		));
		foreach ($comments as $comment) {
			$author = get_user_by('email', $comment->comment_author_email);

			$review = array(
				'id' 			=> $comment->comment_ID,
				'user_name' 	=> $comment->comment_author,
				'user_img' 		=> get_avatar_url($comment->comment_author_email),
				'rating' 		=> get_comment_meta($comment->comment_ID, 'rating', true),
				'description' 	=> $comment->comment_content,
				'created_at' 	=> $comment->comment_date,
			);
			if(!is_null($user_id) && $author->ID==$user_id){
				$review["is_mine"] = true;
			}
			$reviews[] = $review;
		}

		if($page==1){
			$result["count"] 		= $product->get_rating_count();
			$result["rating"] 		= $product->get_average_rating();
			if($reviewed){
				$result["reviewed"] 		= $reviewed;
				if($my_review_id){
					$result["my_review_id"] 	= $my_review_id;
				}
			}
		}
		$result["reviews"] 	= $reviews;

		return $result;
	}


	/**
	 * Get Reviews by Product IDs  
	 *
	 * @author Manthan Kanani	
	 * @since 2.7.1
	**/
	static public function getReviewById($review_id, $author_id=null){
		$result 				= array();

		$comment 	= get_comment($review_id);
		if(!$comment) return false;
		if($comment->user_id!=$author_id && $comment->comment_approved !="1") return false;

		$review = array(
			'id' 			=> $comment->comment_ID,
			'user_name' 	=> $comment->comment_author,
			'user_img' 		=> get_avatar_url($comment->comment_author_email),
			'rating' 		=> get_comment_meta($comment->comment_ID, 'rating', true),
			'description' 	=> $comment->comment_content,
			'created_at' 	=> $comment->comment_date
		);
		return $review;
	}



	/**
	 * create Review 
	 *
	 * @author Manthan Kanani	
	 * @since 2.7.0
	**/
	static public function addProductReview($product_id, $author_id, $rating, $description, $data=array()){
		$result 				= array();

		$post_type 				= get_post_type($product_id);
		if(!in_array($post_type, ['product','product_variation'])) return false;
		
		if($post_type=="product_variation"){
			$variation 			= new WC_Product_Variation($product_id);
			$product_id 		= $variation->get_parent_id();
		}

		$author = get_user_by('id', $author_id);
		if(!$author) return false;
		
		$description 	= strip_tags($description,"<b><strong><i><em><u><a><ul><ol><li><blockquote><img><code><style><ins><del><br>");
		$description 	= trim($description);

		$comment_author_IP 	= (!empty($_SERVER['REMOTE_ADDR']) && rest_is_ip_address(wp_unslash($_SERVER['REMOTE_ADDR']))) ? wc_clean(wp_unslash($_SERVER['REMOTE_ADDR'])) : '127.0.0.1';
		$comment_agent 		= $data['headers']['user_agent'][0] ?? '';

		$commentdata = array(
			'user_id' 				=> $author_id,
			'comment_post_ID' 		=> $product_id,
			'comment_content' 		=> $description,
			'comment_author'		=> $author->display_name,
			'comment_author_email' 	=> $author->user_email,
			'comment_author_IP'		=> $comment_author_IP,
			'comment_agent'			=> $comment_agent,
			'comment_type' 			=> 'review',
			'comment_approved' 		=> 0,
			'comment_meta'  		=> array(
				'rating' 	=> $rating
			)
		);

		$review_id = wp_insert_comment($commentdata);
		if(!is_wp_error($review_id)){
			return array(
				"review_id" 	=> $review_id,
				"description" 	=> $description
			);
		}

		return false;
	}



	/**
	 * Update Product Review by ID 
	 *
	 * @author Manthan Kanani	
	 * @since 2.7.0
	**/
	static public function updateProductReview($review_id, $author_id, $rating, $description){
		$result 				= array();

		$comment 	= get_comment($review_id);
		if(!$comment) return false;
		if($comment->user_id!=$author_id) return false;
		
		$description 	= strip_tags($description,"<b><strong><i><em><u><a><ul><ol><li><blockquote><img><code><style><ins><del><br>");
		$description 	= trim($description);

		$commentdata = array(
			'comment_ID' 			=> $review_id,
			'comment_content' 		=> $description,
			'comment_approved' 		=> 0,
			'comment_meta'  		=> array(
				'rating' 	=> $rating
			)
		);

		wp_update_comment($commentdata);
		return array(
			"review_id" 		=> $review_id,
			"description" 		=> $description
		);
	}


	/**
	 * Update Product Review by ID 
	 *
	 * @author Manthan Kanani	
	 * @since 2.7.0
	**/
	static public function deleteProductReview($review_id, $author_id){
		$result 				= array();

		$review 	= get_comment($review_id);
		if(!$review) return false;
		if($review->user_id!=$author_id) return false;
		
		return wp_delete_comment($review_id, true);
	}



	/**
	 * Generate Checkout  
	 *
	 * @author Manthan Kanani	
	 * @since 2.0.5
	**/
	static public function generateCheckout($line_items, $authToken=null) {
		$utility  		= new Utility();
		$sc_checkout 	= new Swipecart_Checkout();

		$result = array_map(function($v) {
			$variation_id 		= (int) $v['variation_id'];
			$product_attribute 	= array();

			$value = array(
				'id' 			=> $variation_id,
				'quantity' 		=> $v['quantity'],
			);

			if(get_post_type($variation_id)!=="product"){
				$variation 		= new WC_Product_Variation($variation_id);
				$product_id 	= $variation->get_parent_id();
				foreach($variation->get_attributes() as $attribute_name=>$attr_value) {
					$product_attribute[wc_attribute_label($attribute_name)] = $attr_value;
				}
				$value["variation_id"] 	= $variation_id;
				$value["variation"] 	= $product_attribute;
			}
			return $value; 
		}, $line_items);

		$encrypted['items'] 	= json_encode($result);
		$encrypted['ordermeta']	= $utility->generateRandStr(10);
		if($authToken){
			$encrypted['auth']		= $authToken;
		}
		$encrypted["meta"] = array();

		$checkout = $sc_checkout->__checkout_query_arg($encrypted, 'encrypt');
		return $checkout;
	}


	/**
	 * Generate cart by line_items  
	 *
	 * @author Manthan Kanani	
	 * @since 2.8.0
	**/
	static public function generateCart($line_items) {
		$generated_cart = array();
		$subtotal 		= 0;

		foreach($line_items as $v){
			$variation_id 		= (int) $v['varient_id'];
			$post_type 			= get_post_type($variation_id);
			if(!in_array($post_type, ['product','product_variation'])) continue;

			$value = array(
				'id' 			=> $variation_id,
				'quantity' 		=> $v['quantity'],
				'post_type'		=> $post_type
			);

			if($post_type !== "product") {
				$variation 		= new WC_Product_Variation($variation_id);
				foreach($variation->get_attributes() as $attribute_name=>$attr_value) {
					$product_attribute[wc_attribute_label($attribute_name)] = $attr_value;
				}
				$value["variation_id"] 	= $variation_id;
				$value["variation"] 	= $product_attribute;
				$value["price"]			= $variation->get_price();
			} else {
				$product  				= wc_get_product($variation_id);
				$value["price"]			= $product->get_price();
			}

			$subtotal += $value["price"] * $value["quantity"];
			$generated_cart[] = $value;
		}

		return array(
			"cart"  		=> $generated_cart,
			"pricing"		=> array(
				"subtotal"	=> $subtotal,
				"shipping"	=> 0,
				"tax"		=> 0,
				"discount"	=> 0,
			) 
		);
	}


	/**
	 * Get User Email Exstance  
	 *
	 * @author Manthan Kanani	
	 * @since 1.0.0
	**/
	static public function checkUserEmailExist($email) {
		$login = get_user_by('login', $email);
		$email = get_user_by('email', $email);
		return ($login || $email) ? $email : false ;
	}


	/**
	 * Token string must be in this way :- email,password,StoreFrontToken,timestring,random  
	 *
	 * @author Manthan Kanani	
	 * @since 2.0.0
	 * @version 2.6.4
	**/
	public function tokenValidation($value){
		$utility  		= new Utility();
		$separator 		= '~';
		$string_count 	= 5;
		$expiry_day		= 30;
		$random_length 	= 10;

		if(gettype($value)=='array'){
			// Process of Encryption
			$random 		= $utility->generateRandStr($random_length);
			$main_string 	= $value;

			$main_string[] 		= $this->storeFrontToken();
			$main_string[] 		= date('Y-m-d H:i:s');
			$main_string[] 		= $random;

			if(count($main_string)!==$string_count) return false;

			$encode = array_map("urlencode", $main_string);
			$string = implode($separator, $encode);

			$key = $utility->encrypt_decrypt($string, 'encrypt');

			return $key;

		} elseif(gettype($value)=='string') {
			// Process of Decryption
			$string = $utility->encrypt_decrypt($value, 'decrypt');

			$decode = explode($separator, $string);
			$main_string = array_map("urldecode", $decode);

			if(count($main_string)!==$string_count) return false;

			$email 				= $main_string[0];
			$password 			= $main_string[1];
			$StoreFrontToken 	= $main_string[2];
			$timestring 		= $main_string[3];
			$random 			= $main_string[4];

			if($StoreFrontToken 	!= $this->storeFrontToken()) return false;
			if(strtotime($timestring) < strtotime('-'.$expiry_day.' days')) return false;

			$user = wp_authenticate($email, $password);
			if(!is_wp_error($user)) return $user;
			return false;
		}
	}


	/**
	 * generate nonce and update nonce
	 *
	 * @author Manthan Kanani	
	 * @since 2.1.0
	 * @version 2.4.2
	**/
	public function nonceValidation($value=null){
		$utility  		= new Utility();
		$expiry_day		= 1;
		$random_length 	= 32;

		if(!empty($value)){
			// Process of Decryption
			if(get_option($this->nonceKey)==$value){
				update_option($this->nonceKey,"");
				return true;
			}
			return false;
		} else {
			// Process of Encryption
			$random 			= $utility->generateRandStr($random_length);

			update_option($this->nonceKey,$random);
			return $random;
		}
	}


	/**
	 * generate Access Token and login the user  
	 *
	 * @author Manthan Kanani	
	 * @since 2.0.0
	 * @version 2.6.0
	**/
	public function SignIn($email, $password){
		$result 		= array();
		$utility  		= new Utility();

		if($this->checkUserEmailExist($email)){

			$user = wp_authenticate($email, $password);
			if(!is_wp_error($user)){
				$customer 	= new WC_Customer($user->ID);

				$username  			= $user->user_login;
				$user_email  		= $user->user_email;
				$user_nicename  	= $user->user_nicename;
				$display_name  		= $user->display_name;
				$password   		= $password;

				$token = $this->tokenValidation(array($user_email, $password));

				$result = array(
					'firstName'			=> $customer->get_first_name(),
					'lastName'			=> $customer->get_last_name(),
					'email'				=> $customer->get_email(),
					'phone'				=> $customer->get_billing_phone(),
					'wishlist'			=> array(),
					'token'				=> $token,
					'profile_pic'		=> $customer->get_avatar_url(),
					'tags'				=> [],
				);
			}
		} 
		return $result;
	}


	/**
	 * Get Customer by ID 
	 *
	 * @author Manthan Kanani	
	 * @since 2.0.3
	**/
	public function getCustomerByID($user_id, $full_data=false){
		$user 		= get_userdata($user_id);
		if(!$user) return false;

		$customer 	= new WC_Customer($user_id);
		$billing = array(
			"first_name" 	=> $customer->get_billing_first_name(),
			"last_name" 	=> $customer->get_billing_last_name(),
			"company" 		=> $customer->get_billing_company(),
			"address_1" 	=> $customer->get_billing_address_1(),
			"address_2" 	=> $customer->get_billing_address_2(),
			"city" 			=> $customer->get_billing_city(),
			"state" 		=> $customer->get_billing_state(),
			"postcode" 		=> $customer->get_billing_postcode(),
			"country" 		=> $customer->get_billing_country(),
			"email" 		=> $customer->get_billing_email(),
			"phone" 		=> $customer->get_billing_phone()
		);
		$shipping = array(
			"first_name" 	=> $customer->get_shipping_first_name(),
			"last_name" 	=> $customer->get_shipping_last_name(),
			"company" 		=> $customer->get_shipping_company(),
			"address_1" 	=> $customer->get_shipping_address_1(),
			"address_2" 	=> $customer->get_shipping_address_2(),
			"city" 			=> $customer->get_shipping_city(),
			"state" 		=> $customer->get_shipping_state(),
			"postcode" 		=> $customer->get_shipping_postcode(),
			"country" 		=> $customer->get_shipping_country(),
			"phone" 		=> $customer->get_billing_phone()
		);

		$date_created 	= $customer->get_date_created()->date('Y-m-d H:i:s');
		$date_modified 	= ($customer->get_date_modified()) ? $customer->get_date_modified()->date('Y-m-d H:i:s') : $date_created ;

		$userdata = array(
			"displayName"			=> $customer->get_display_name(),
			"email"					=> $customer->get_email(),
			"firstName"				=> $customer->get_first_name(),
			"lastName"				=> $customer->get_last_name(),
			"phone"					=> $customer->get_billing_phone(),
			"tags" 					=> [],
		);

		if($full_data){
			$userdata["date_created"] 		= $date_created;
			$userdata["date_modified"] 		= $date_modified;
			$userdata["role"] 				= $customer->get_role();
			$userdata["username"] 			= $customer->get_username();
			$userdata["billing"] 			= $billing;
			$userdata["shipping"] 			= $shipping;
			$userdata["is_paying_customer"] = $customer->get_is_paying_customer();
		}

		return $userdata;
	}	


	/**
	 * register User  
	 *
	 * @author Manthan Kanani	
	 * @since 2.0.0
	**/
	public function customerSignUp($email, $fname, $lname, $phone, $password){
		$result 		= array();
		$displayname 	= implode(' ', array($fname, $lname));
		
		$utility 		= new Utility();
		$username 		= $utility->create_username('name', $displayname);

		$userdata 	= array(
			'user_email'    => $email,
			'user_pass'     => $password,
			'role'          => 'customer',
			'user_login'    => $username,
			'user_nicename' => $displayname,
			'display_name'	=> $displayname,
			'first_name'    => $fname,
			'last_name'     => $lname,
		);

		$user_id = wp_insert_user($userdata);
		if(!is_wp_error($user_id)){
			if($phone){
				update_user_meta( $user_id, "billing_phone", $phone );
			}
			$result = $this->getCustomerByID($user_id);
		} else {
			return false;
		}
		return $result;
	}


	/**
	 * Update User 
	 *
	 * @author Manthan Kanani	
	 * @since 2.2.4
	 * @version 2.6.8
	**/
	public function ChangeProfile($userdata){
		$result 		= array();

		$user_email = $userdata['user_email'] ?? "";
		unset($userdata['user_email']);
		
		if(!isset($userdata['first_name']))
			$userdata['first_name'] = get_user_meta($userdata['ID'], 'first_name',true);
		if(!isset($userdata['last_name']))
			$userdata['last_name'] 	= get_user_meta($userdata['ID'], 'last_name',true);

		$displayname = implode(' ', array($userdata['first_name'], $userdata['last_name']));

		$userdata['user_nicename'] 	= $displayname; 
		$userdata['display_name'] 	= $displayname;

		$user_id 		= wp_update_user($userdata);

		if(!is_wp_error($user_id)){
			if(isset($userdata['billing_phone'])){
				update_user_meta( $user_id, "billing_phone", $userdata['billing_phone'] );
			}
			$result = $this->getCustomerByID($user_id);

			if($result && $user_email && isset($userdata['user_pass'])){
				$result["token"] = $this->tokenValidation(array($user_email, $userdata['user_pass']));
			}
		} 
		return $result;
	}

	/**
	 * Get Adresses  
	 *
	 * @author Manthan Kanani	
	 * @since 2.0.3
	**/
	public function getAddresses($user_id){
		$user 		= get_userdata($user_id);
		if(!$user) return false;

		$customer 	= new WC_Customer($user_id);
		$address[] = array(
			"id" 			=> "billing",
			"address1" 		=> $customer->get_billing_address_1(),
			"address2" 		=> $customer->get_billing_address_2(),
			"city" 			=> $customer->get_billing_city(),
			"company" 		=> $customer->get_billing_company(),
			"country" 		=> $customer->get_billing_country(),
			"firstName" 	=> $customer->get_billing_first_name(),
			"lastName" 		=> $customer->get_billing_last_name(),
			"phone" 		=> $customer->get_billing_phone(),
			"province" 		=> $customer->get_billing_state(),
			"zip" 			=> $customer->get_billing_postcode(),
			"email" 		=> $customer->get_billing_email(),
		);
		$address[] = array(
			"id" 			=> "shipping",
			"address1" 		=> $customer->get_shipping_address_1(),
			"address2" 		=> $customer->get_shipping_address_2(),
			"city" 			=> $customer->get_shipping_city(),
			"company" 		=> $customer->get_shipping_company(),
			"country" 		=> $customer->get_shipping_country(),
			"firstName" 	=> $customer->get_shipping_first_name(),
			"lastName" 		=> $customer->get_shipping_last_name(),
			"phone" 		=> $customer->get_billing_phone(),
			"province" 		=> $customer->get_shipping_state(),
			"zip" 			=> $customer->get_shipping_postcode(),
		);

		return $address;
	}


	/**
	 * Update Adresses  
	 *
	 * @author Manthan Kanani	
	 * @since 2.0.3
	**/
	public function updateAddress($user_id, $address){
		$user 		= get_userdata($user_id);
		if(!$user) return false;
		
		update_user_meta( $user_id, $address['id']."_address_1", $address['address_1'] );
		update_user_meta( $user_id, $address['id']."_address_2", $address['address_2'] );
		update_user_meta( $user_id, $address['id']."_city", $address['city'] );
		update_user_meta( $user_id, $address['id']."_company", $address['company'] );
		update_user_meta( $user_id, $address['id']."_country", $address['country'] );
		update_user_meta( $user_id, $address['id']."_first_name", $address['first_name'] );
		update_user_meta( $user_id, $address['id']."_last_name", $address['last_name'] );
		update_user_meta( $user_id, $address['billing']."_phone", $address['phone'] );
		update_user_meta( $user_id, $address['id']."_state", $address['state'] );
		update_user_meta( $user_id, $address['id']."_postcode", $address['postcode'] );

		return array(
			"address1" 		=> get_user_meta( $user_id, $address['id']."_address_1", true),
			"address2" 		=> get_user_meta( $user_id, $address['id']."_address_2", true),
			"city" 			=> get_user_meta( $user_id, $address['id']."_city", true),
			"company" 		=> get_user_meta( $user_id, $address['id']."_company", true),
			"country" 		=> get_user_meta( $user_id, $address['id']."_country", true),
			"firstName" 	=> get_user_meta( $user_id, $address['id']."_first_name", true),
			"lastName" 		=> get_user_meta( $user_id, $address['id']."_last_name", true),
			"phone" 		=> get_user_meta( $user_id, $address['billing']."_phone", true),
			"province" 		=> get_user_meta( $user_id, $address['id']."_state", true),
			"zip" 			=> get_user_meta( $user_id, $address['id']."_postcode", true),
		);
	}


	/**
	 * Delete user if user is given type  
	 *
	 * @author Manthan Kanani	
	 * @since 2.3.0
	**/
	public function deleteUser($user_id, $role='all'){
		require_once(ABSPATH.'wp-admin/includes/user.php');
		
		$user 		= get_userdata($user_id);
		if(!$user) return false;
		if($role!='all'){
			if(in_array($role, $user->roles)){
				wp_delete_user($user_id);
				return true;
			}
		} else{
			wp_delete_user($user_id);
			return true;
		}
		return false;		
	}




	/**
	 * Forgot User password  
	 *
	 * @author Manthan Kanani	
	 * @since 2.0.0
	**/
	public function forgotPassword($email){
		$result 		= array();
		$user_id 		= false;
		
		$EmailUserID 	= email_exists($email);
		$UNUserID 		= username_exists($email);

		$user_id = $EmailUserID ?? $UNUserID ;

		if($user_id){
			$user 		= new WP_User(intval($user_id));
			$reset_key 	= get_password_reset_key( $user );
			$wc_emails 	= WC()->mailer()->get_emails();
			$wc_emails['WC_Email_Customer_Reset_Password']->trigger( $user->user_login, $reset_key );
			$result = array( 'customerRecover' => array() );
		}

		return $result;
	}

	/**
	 * Webhook read,update,view,remove event  
	 *
	 * @author Manthan Kanani	
	 * @since 2.4.0
	**/
	public function webhookCRUD($action, $topic, $callbackURL=null){
		$result 			= array();
		$webhookString		= 'swipecart_webhooks';

		$webhooks 			= get_option($webhookString);

		if(in_array($action, ['c','u'])){
			if(!is_array($webhooks)) $webhooks = array();

			$webhooks[$topic] = array(
				"url" 		=> $callbackURL
			);

			update_option($webhookString, $webhooks, false);

			return true;
		} elseif(in_array($action, ['r','d'])){
			if($action == "d"){
				if(is_array($webhooks) && count($webhooks)>0){
					unset($webhooks[$topic]);
					update_option($webhookString, $webhooks, false);
				} else {
					delete_option($webhookString);
				}
				return true;
			} else {
				if(isset($webhooks[$topic]) && isset($webhooks[$topic]["url"])){
					return array(
						"topic" 	=> $topic,
						"url" 		=> $webhooks[$topic]["url"]
					);
				} else {
					return false; 
				}
			}
		}
		return false;
	}

	/**
	 * get post count  
	 *
	 * @author Manthan Kanani	
	 * @since 2.6.4
	**/
	public function getPostCount($post_type='post', $status='publish'){
		$post 			= (array) wp_count_posts($post_type);
		$post_count 	= ($status) ? $post[$status] : array_sum($post);

		return $post_count;
	}

	/**
	 * get term count  
	 *
	 * @author Manthan Kanani	
	 * @since 2.6.4
	**/
	public function getTermCount($tax_type='category'){
		$term_count 	= wp_count_terms($tax_type);
		return $term_count;
	}

	/**
	 * get available languages  
	 *
	 * @author Manthan Kanani	
	 * @since 2.6.1
	 * @version 2.6.2
	**/
	public function getAvailableLanguages(){
		$result 				= array();
		$available_languages	= array();
		$unmapped_languages		= array();
		$required_rest_params	= array();
		$active_ml_plugins		= array();

		$multilingual 			= new SC_Multilingual();
		$active_ml_plugins 		= $multilingual->installed_ml_plugins();
		
		if(!$active_ml_plugins) return false;

		$available_languages 	= __SCML::get_available_lang();
		$unmapped_languages 	= __SCML::get_unavailable_lang();
		$required_rest_params 	= __SCML::required_rest_params();

		return array(
			"available_languages" 	=> $available_languages,
			"unmapped_languages" 	=> $unmapped_languages,
			"active_plugins" 		=> $active_ml_plugins,
			"rest_params" 			=> $required_rest_params,
		);
	}


	/**
	 * get available multilingual plugins
	 *
	 * @author Manthan Kanani	
	 * @since 2.8.0
	**/
	public function getMultilingualPlugins(){
		$multilingual 			= new SC_Multilingual();
		$active_ml_plugins 		= $multilingual->installed_ml_plugins();
		if(!$active_ml_plugins) return array();
		return $active_ml_plugins;
	}

	/**
	 * get available points and reward plugins
	 *
	 * @author Manthan Kanani	
	 * @since 2.8.0
	**/
	public function getPointsAndRewardsPlugins(){
		$pointsnrewards 		= new SC_RewardPoints();
		$active_plugins 		= $pointsnrewards->installed_reward_plugins();
		if(!$active_plugins) return array();
		return $active_plugins;
	}


	/**
	 * get list of all plugins  
	 *
	 * @author Manthan Kanani	
	 * @since 2.8.0
	**/
	public function getPlugins(){
		$result				= array();
		if(!function_exists('get_plugins')){
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		foreach(get_plugins() as $v=>$u){
			$result[] = array(
				"name" => $u["Name"],
				"version" => $u["Version"],
				"author" => $u["Author"],
				"pluginpath" => $v
			);
		}
		return $result;
	}


	/**
	 * Site public Sitedata  
	 *
	 * @author Manthan Kanani	
	 * @since 2.4.0
	 * @version 2.8.0
	**/
	public function getPublicSiteData(){
		$result 				= array();
		$fullSiteData 			= $this->getSiteData();

		return array(
			"config" => array(
				"version" => $fullSiteData['config']['version']
			),
			"site"	=> array(
				"url"  					=> $fullSiteData['site']['url'],
				"name"  				=> $fullSiteData['site']['name'],
				"tagline"  				=> $fullSiteData['site']['tagline'],
				"timezone"				=> $fullSiteData['site']['timezone'],
				"language"				=> $fullSiteData['site']['language'],
				"appearance"  			=> array(
					"logo" 				=> $fullSiteData['site']['appearance']['logo'],
					"favicon" 			=> $fullSiteData['site']['appearance']['favicon']
				),
				"count" 	=> array(
					"post"				=> $fullSiteData['site']['count']["post"],
					"category"			=> $fullSiteData['site']['count']["category"],
					"page"				=> $fullSiteData['site']['count']["page"],
				),
				"plugins" 	=> array(
					"multilingual" 		=> $this->getMultilingualPlugins(),
					"points-n-rewards" 	=> $this->getPointsAndRewardsPlugins(),
				)
			),
			"page"	=> array(
				"privacy_policy" 		=> $fullSiteData['page']['privacy_policy'],
				"terms_n_conditions" 	=> $fullSiteData['page']['terms_n_conditions'],
				"checkoutURL"			=> $fullSiteData['page']['checkoutURL']
			),
			"store" => array(
				"placeholderUrl" 		=> $fullSiteData['store']['placeholderUrl'],
				"currency"	=> array(
					"code"				=> $fullSiteData['store']['currency']['code'],
					"symbol"			=> $fullSiteData['store']['currency']['symbol'],
					"decimal"			=> $fullSiteData['store']['currency']['decimal'],
				),
				"address"	=> array(
					"address" 			=> $fullSiteData['store']['address']['address'],
					"address2" 			=> $fullSiteData['store']['address']['address2'],
					"city" 				=> $fullSiteData['store']['address']['city'],
					"state" 			=> $fullSiteData['store']['address']['state'],
					"country" 			=> $fullSiteData['store']['address']['country'],
					"postalCode" 		=> $fullSiteData['store']['address']['postalCode']
				),
				"count" 	=> array(
					"product"			=> $fullSiteData['store']['count']['product'],
					"product_cat" 		=> $fullSiteData['store']['count']['product_cat']
				)
			)
		);
	}

	/**
	 * Site Parameter Callback  
	 *
	 * @author Manthan Kanani	
	 * @since 2.2.2
	 * @version 2.6.10
	**/
	public function getSiteData($partial=false){
		$result 						= array();
		$pagePrivacyPolicy  			= "";
		$pageTnCPolicy  				= "";
		$pageCheckout  					= "";
		$addressAddress 				= "";
		$addressAddress2 				= "";
		$addressCity 					= "";
		$addressState 					= "";
		$addressCountry 				= "";
		$addressPostalCode 				= "";
		$countryCurrencyCode  			= "";
		$countryCurrencySymbol  		= "";
		$countryCurrencyDecimal 		= "";
		$placeholderImageURL    		= "";
		$countProduct 					= 0;
		$countCategory 					= 0;
		$stockKeeper 					= "";
		$admins 						= array();

		$version  						= GeneralUtility::__pluginVersion();

		$siteUrl 						= get_site_url();
		$siteName 						= get_bloginfo('name');
		$siteTagline 					= get_bloginfo('description');

		$siteLogo						= wp_get_attachment_url(get_theme_mod('custom_logo')) ?: "" ;
		$siteFavicon					= get_site_icon_url() ?: "" ;
			
		$siteAdminEmail 				= get_bloginfo('admin_email');
		$siteLanguage					= get_bloginfo('language');

		$timezoneObject					= wp_timezone();
		$dateTime 						= new DateTime();
		$timezoneOffset 				= $timezoneObject->getOffset($dateTime);

		$timezoneArray					= (array) $timezoneObject;
		$timezoneArray['offset']		= $timezoneOffset;
		unset($timezoneArray['timezone_type']);

		if(!$partial){
			$pagePrivacyPolicy  		= get_privacy_policy_url() ?? "" ;
			$pageTnCPolicy  			= get_permalink(wc_terms_and_conditions_page_id()) ?: "" ;
			$pageCheckout  				= wc_get_checkout_url() ?: "" ;

			$addressAddress 			= WC()->countries->get_base_address();
			$addressAddress2 			= WC()->countries->get_base_address_2();
			$addressCity 				= WC()->countries->get_base_city();
			$addressState 				= WC()->countries->get_base_state();
			$addressCountry 			= WC()->countries->get_base_country();
			$addressPostalCode 			= WC()->countries->get_base_postcode();

			$countryCurrencyCode  		= get_woocommerce_currency();
			$countryCurrencySymbol  	= get_woocommerce_currency_symbol($countryCurrencyCode);
			$countryCurrencyDecimal 	= wc_get_price_decimals();

			$placeholderImageURL    	= wc_placeholder_img_src('full');

			$stockKeeper 				= get_option('woocommerce_stock_email_recipient');
		}

		$admins_ids = get_users(array(
			'role__in' 	=> 'administrator',
			'fields'    => 'ID',
		));
		foreach($admins_ids as $admin_id){
			$admin   	= get_userdata($admin_id);
			$admins[] 	= array(
				"first_name"	=> $admin->first_name,
				"last_name"		=> $admin->last_name,
				"display_name"	=> $admin->display_name,
				"email"			=> $admin->user_email,
			);
		}

		return array(
			"config" => array(
				"version" => $version,
			),
			"site"	=> array(
				"url"  			=> $siteUrl,
				"name"  		=> $siteName,
				"tagline"  		=> $siteTagline,
				"admin_email"  	=> $siteAdminEmail,
				"timezone"		=> $timezoneArray,
				"language"		=> $siteLanguage,
				"appearance"  	=> array(
					"logo" 		=> $siteLogo,
					"favicon" 	=> $siteFavicon
				),
				"count" => array(
					"post"			=> $this->getPostCount() ?? 0,
					"category"		=> $this->getTermCount() ?? 0,
					"page"			=> $this->getPostCount("page") ?? 0,
				),
				"plugins" => $this->getPlugins()
			),
			"page"	=> array(
				"privacy_policy" 		=> $pagePrivacyPolicy,
				"terms_n_conditions" 	=> $pageTnCPolicy,
				"checkoutURL"			=> $pageCheckout
			),
			"store" => array(
				"placeholderUrl" => $placeholderImageURL,
				"currency"	=> array(
					"code"			=> $countryCurrencyCode,
					"symbol"		=> $countryCurrencySymbol,
					"decimal"		=> $countryCurrencyDecimal,
				),
				"address"	=> array(
					"address" 		=> $addressAddress,
					"address2" 		=> $addressAddress2,
					"city" 			=> $addressCity,
					"state" 		=> $addressState,
					"country" 		=> $addressCountry,
					"postalCode" 	=> $addressPostalCode,
				),
				"stockKeeperEmail"  => $stockKeeper,
				"storeFrontToken"	=> $this->storeFrontToken(),
				"count" 			=> array(
					"product"		=> $this->getPostCount("product") ?? 0,
					"product_cat" 	=> $this->getTermCount("product_cat") ?? 0
				)
			),
			"admin" 		=> $admins
		);
	}
}
