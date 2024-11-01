<?php

defined('ABSPATH') or die('No script kiddies please!');

/**
 * REST API Route List with functions...
 *
 * @package Swipecart
 * @author Manthan Kanani
 * @since 1.0.0
**/
class REST_API_v1_Controller {

	/**
	 * defined const to use forever
	**/
	const NAMESPACE = 'sc/v1';

	/**
	 * public to use in constructor
	**/
	public $namespace = self::NAMESPACE;

	/**
	 * Constructor for common items
	 *
	 * @author Manthan Kanani	
	 * @since 2.0.0
	 * @version 2.8.0
	**/
	public function __construct() {
		
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
	 * Get Product by Categories  
	 *
	 * @author Manthan Kanani	
	 * @since 2.2.1
	**/
	public function getAllCollectionsCategories($request){
		$api 		= new APIClass();
		$order 		= $request->get_param('order');
		$orderBy 	= $request->get_param('orderBy');
		$page 		= $request->get_param('page');
		$pp 		= $request->get_param('pp');
		$search 	= $request->get_param('search');

		$result = $api->getAllCollectionsCategories($order, $orderBy, $page, $pp, $search);
		if($result) {
			$arr = array(
				"success" 	=> true, 
				"message" 	=> "success", 
				"data" 		=> array("collections" => $result)
			);
		} else {
			$arr = array("success" => false, "message" => __("No collections found", "swipecart"));
			return new WP_REST_Response($arr, 400);
		}
		return $arr;
	}

	/**
	 * Get Product by Categories  
	 *
	 * @author Manthan Kanani	
	 * @since 2.0.0
	**/
	public function GetCollectionsByIds($request){
		$api 		= new APIClass();
		$id 		= $request->get_param('ids');

		$result = $api->GetCollectionsByIds($id, false);
		if($result) {
			$arr = array(
				"success" 	=> true, 
				"message" 	=> "success", 
				"data" 		=> array("collections" => $result)
			);
		} else {
			$arr = array("success" => false, "message" => __("No collections found", "swipecart"));
			return new WP_REST_Response($arr, 400);
		}
		return $arr;
	}


	/**
	 * Get All Products with search,category,tag filter  
	 *
	 * @author Manthan Kanani	
	 * @since 1.0.0
	**/
	public function getProducts($request){
		$api 			= new APIClass();
		$category 		= $request->get_param('category');
		$category 		= ($category)? $category : array() ;
		$order 			= $request->get_param('order');
		$orderBy 		= $request->get_param('orderBy');
		$page 			= $request->get_param('page');
		$pp 			= $request->get_param('pp');
		$search 		= $request->get_param('search');
		$filter 		= $request->get_param('filter');
		
		$result = $api->getProducts($order, $orderBy, $page, $pp, $category, $search, $filter);
		if($result) {
			$arr = array(
				"success" 	=> true, 
				"message" 	=> "success", 
				"data" 		=> array("products" => $result)
			);
		} else {
			$arr = array("success" => false, "message" => __("No products found.", "swipecart"));
			return new WP_REST_Response($arr, 400);
		}
		return $arr;
	}


	/**
	 * Search Product By ID  
	 *
	 * @author Manthan Kanani	
	 * @since 2.0.0
	**/
	public function SearchProducts($request){
		$api 			= new APIClass();
		$page 			= $request->get_param('page');
		$pp 			= $request->get_param('pp');
		$search 		= $request->get_param('search');

		$result = $api->getProducts('', '', $page, $pp, array(), $search);
		if($result) {
			$arr = array(
				"success" 	=> true, 
				"message" 	=> "success", 
				"data" 		=> array("products" => $result)
			);
		} else {
			$arr = array("success" => false, "message" => __("No products found.", "swipecart"));
			return new WP_REST_Response($arr, 400);
		}
		return $arr;
	}


	/**
	 * Get Products by IDs  
	 *
	 * @author Manthan Kanani	
	 * @since 2.0.0
	**/
	public function GetProductsByIds($request){
		$api 		= new APIClass();
		$id 		= $request->get_param('ids');

		$result = $api->GetProductsByIds($id, false);
		if($result) {
			$arr = array(
				"success" 	=> true, 
				"message" 	=> "success", 
				"data" 		=> array("products" => $result)
			);
		} else {
			$arr = array("success" => false, "message" => __("No products found", "swipecart"));
			return new WP_REST_Response($arr, 400);
		}
		return $arr;
	}

	/**
	 * Get Single Product  
	 *
	 * @author Manthan Kanani	
	 * @since 2.0.0
	**/
	public function GetProductById($request){
		$api 		= new APIClass();
		$id 		= $request->get_param('id');

		$result = $api->getProductByID($id, false);
		if($result) {
			$arr = array(
				"success" 	=> true, 
				"message" 	=> "success", 
				"data" 		=> array("product" => $result)
			);
		} else {
			$arr = array("success" => false, "message" => __("The requested product does not exist.", "swipecart"));
			return new WP_REST_Response($arr, 400);
		}
		return $arr;
	}

	/**
	 * Get Products by Collections IDs  
	 *
	 * @author Manthan Kanani	
	 * @since 2.2.1
	**/
	public function getProductsByCollectionId($request){
		$api 			= new APIClass();
		$category 		= $request->get_param('id');
		$category 		= ($category)? $category : array() ;
		$order 			= $request->get_param('order');
		$orderBy 		= $request->get_param('orderBy');
		$page 			= $request->get_param('page');
		$pp 			= $request->get_param('pp');
		$filter 		= $request->get_param('filter');
		
		$result = $api->getProducts($order, $orderBy, $page, $pp, array($category), '', $filter);
		if($result) {
			$arr = array(
				"success" 	=> true, 
				"message" 	=> "success", 
				"data" 		=> array("products" => $result)
			);
		} else {
			$arr = array("success" => false, "message" => __("No products found", "swipecart"));
			return new WP_REST_Response($arr, 400);
		}
		return $arr;
	}


	/**
	 * Get Recommended Products
	 *
	 * @author Manthan Kanani	
	 * @since 2.6.0
	**/
	public function getRecommendedProducts($request){
		$api 			= new APIClass();
		$product_id 	= $request->get_param('product_id');
		$pp 			= $request->get_param('pp');

		$result 	= $api->getRecommendedProducts($product_id, $pp);
		if($result){
			$arr = array(
				"success" 	=> true, 
				"message" 	=> "success", 
				"data" 		=> array("products" => $result)
			);
		} else {
			$arr = array("success" => false, "message" => __("No products found", "swipecart"));
			return new WP_REST_Response($arr, 400);
		}
		return $arr;
	}


	/**
	 * Get Products by Collections IDs  
	 *
	 * @author Manthan Kanani	
	 * @since 2.2.1
	**/
	public function getProductsByVarientId($request){
		$api 		= new APIClass();
		$ids 		= $request->get_param('ids');
		
		if($result = $api->GetProductsByVarientId($ids)) {
			$arr = array(
				"success" 	=> true, 
				"message" 	=> "success", 
				"data" 		=> array("products" => $result)
			);
		} else {
			$arr = array("success" => false, "message" => __("No products found", "swipecart"));
			return new WP_REST_Response($arr, 400);
		}
		return $arr;
	}


	/**
	 * Get Product Filters  
	 *
	 * @author Manthan Kanani	
	 * @since 2.2.1
	**/
	public function getProductFilters($request){
		$api 		= new APIClass();
		
		if($result = $api->getProductFilters()) {
			$arr = array(
				"success" 	=> true, 
				"message" 	=> "success", 
				"data" 		=> array("filter" => array( "sections" => $result ))
			);
		} else {
			$arr = array("success" => false, "message" => __("Not any product filters found", "swipecart"));
			return new WP_REST_Response($arr, 400);
		}
		return $arr;
	}


	/**
	 * Get Product reviews by ID  
	 *
	 * @author Manthan Kanani	
	 * @since 2.7.0
	 * @version 2.7.1
	**/
	public function getReviewsByProductId($request){
		$api 				= new APIClass();
		$product_id 		= $request->get_param('product_id');
		$page 				= $request->get_param('page');
		$pp 				= $request->get_param('pp');
		$user 				= $request->get_param('loggedin_data');

		$viewer_id			= $user ? $user->ID : null;

		if($result = $api->getReviewsByProductId($product_id, $viewer_id, $page, $pp)) {
			$arr = array(
				"success" 	=> true, 
				"message" 	=> "success", 
				"data" 		=> $result
			);
		} else {
			$arr = array("success" => false, "message" => __("No Product Reviews", "swipecart"));
			return new WP_REST_Response($arr, 400);
		}
		return $arr;
	}


	/**
	 * Get Product reviews by ID  
	 *
	 * @author Manthan Kanani	
	 * @since 2.7.0
	 * @version 2.7.1
	**/
	public function getReviewById($request){
		$api 				= new APIClass();
		$review_id 			= $request->get_param('review_id');
		$user 				= $request->get_param('loggedin_data');

		$viewer_id			= $user ? $user->ID : null;

		if($result = $api->getReviewById($review_id, $viewer_id)) {
			$arr = array(
				"success" 	=> true, 
				"message" 	=> "success", 
				"data" 		=> $result
			);
		} else {
			$arr = array("success" => false, "message" => __("No Product Review", "swipecart"));
			return new WP_REST_Response($arr, 400);
		}
		return $arr;
	}


	/**
	 * Get Product reviews by ID  
	 *
	 * @author Manthan Kanani	
	 * @since 2.7.0
	 * @version 2.7.1
	**/
	public function addProductReview($request){
		$api 				= new APIClass();
		$product_id 		= $request->get_param('product_id');
		$rating 			= $request->get_param('rating');
		$description 		= $request->get_param('description');
		$user 				= $request->get_param('loggedin_data');

		$data['headers'] 	= $request->get_headers();

		if($result = $api->addProductReview($product_id, $user->ID, $rating, $description, $data)){
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


	/**
	 * Update Product review by ID  
	 *
	 * @author Manthan Kanani	
	 * @since 2.7.0
	 * @version 2.7.1
	**/
	public function updateProductReview($request){
		$api 				= new APIClass();
		$review_id 			= $request->get_param('review_id');
		$rating 			= $request->get_param('rating');
		$description 		= $request->get_param('description');
		$user 				= $request->get_param('loggedin_data');

		if($result = $api->updateProductReview($review_id, $user->ID, $rating, $description)){
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
	

	/**
	 * delete Product review by ID  
	 *
	 * @author Manthan Kanani	
	 * @since 2.7.0
	 * @version 2.7.1
	**/
	public function deleteProductReview($request){
		$api 				= new APIClass();
		$review_id 			= $request->get_param('review_id');
		$user 				= $request->get_param('loggedin_data');

		if($result = $api->deleteProductReview($review_id, $user->ID)){
			$arr = array(
				"success" 	=> true, 
				"message" 	=> "success"
			);
		} else {
			$arr = array("success" => false, "message" => __("Something went wrong.", "swipecart"));
			return new WP_REST_Response($arr, 400);
		}
		return $arr;
	}


	/**
	 * generate Access Token by email and password  
	 *
	 * @author Manthan Kanani	
	 * @since 2.0.0
	**/
	public function SignIn($request){
		$api 		= new APIClass();
		$email 		= $request->get_param('email');
		$password 	= $request->get_param('password');

		$result = $api->SignIn($email, $password);
		if($result) {
			$arr = array(
				"success" 	=> true, 
				"message" 	=> __("Welcome", "swipecart"), 
				"data" 		=> $result
			);
		} else {
			$arr = array("success" => false, "message" => __("Could not log-in", "swipecart"));
			return new WP_REST_Response($arr, 400);
		}
		return $arr;
	}

	/**
	 * register User  
	 *
	 * @author Manthan Kanani	
	 * @since 2.0.0
	**/
	public function customerSignUp($request){
		$api 		= new APIClass();
		$email 		= $request->get_param('email');
		$first_name = $request->get_param('firstName');
		$last_name 	= $request->get_param('lastName');
		$phone 		= $request->get_param('phone');
		$password 	= $request->get_param('password');
		
		$result = $api->customerSignUp($email, $first_name, $last_name, $phone, $password);

		if($result) {
			$arr = array(
				"success" 	=> true, 
				"message" 	=> __("Signed up", "swipecart"), 
				"data" 		=> array(
					"customerCreate" => array(
						"customer" => $result
					)
				)
			);
		} else {
			$arr = array("success" => false, "message" => __("Could not finish sign-up", "swipecart"));
			return new WP_REST_Response($arr, 400);
		}
		return $arr;
	}

	/**
	 * Forgot password of registered User  
	 *
	 * @author Manthan Kanani	
	 * @since 2.0.0
	**/
	public function forgotPassword($request){
		$api 		= new APIClass();
		$email 		= $request->get_param('email');

		$result = $api->forgotPassword($email);
		if($result) {
			$arr = array(
				"success" 	=> true, 
				"message" 	=> __("Reset password link sent.", "swipecart"), 
				"data" 		=> $result
			);
		} else {
			$arr = array("success" => false, "message" => __("Could not send email link", "swipecart"));
			return new WP_REST_Response($arr, 400);
		}
		return $arr;
	}


	/**
	 * Update User Profile  
	 *
	 * @author Manthan Kanani	
	 * @since 2.0.4
	 * @version 2.7.1
	**/
	public function ChangeProfile($request){
		$api 				= new APIClass();
		$first_name 		= $request->get_param('firstName');
		$last_name 			= $request->get_param('lastName');
		$phone 				= $request->get_param('phone');
		$user 				= $request->get_param('loggedin_data');

		$userdata['ID'] 		= $user->ID;
		$userdata['user_email'] = $user->user_email;

		if($first_name) $userdata['first_name'] = $first_name;
		if($last_name) $userdata['last_name'] 	= $last_name;

		$userdata['billing_phone'] 	= $phone ?? null;
		
		if($result = $api->ChangeProfile($userdata)){
			$arr = array(
				"success" 	=> true, 
				"message" 	=> __("Profile updated successfully", "swipecart"), 
				"data" 		=> $result
			);
		} else {
			$arr = array("success" => false, "message" => __("User not updated", "swipecart"));
			return new WP_REST_Response($arr, 400);
		}
		
		return $arr;
	}


	/**
	 * Update User Profile  
	 *
	 * @author Manthan Kanani	
	 * @since 2.0.4
	 * @version 2.7.1
	**/
	public function ChangePassword($request){
		$api 				= new APIClass();
		$user_pass 			= $request->get_param('password');
		$user 				= $request->get_param('loggedin_data');

		$userdata['ID'] 		= $user->ID;
		$userdata['user_email'] = $user->user_email;

		if($user_pass) $userdata['user_pass'] 	= $user_pass;
		
		if($result = $api->ChangeProfile($userdata)){
			$arr = array(
				"success" 	=> true, 
				"message" 	=> __("Password updated successfully", "swipecart"), 
				"data" 		=> $result
			);
		} else {
			$arr = array("success" => false, "message" => __("Password not updated", "swipecart"));
			return new WP_REST_Response($arr, 400);
		}
		
		return $arr;
	}


	/**
	 * Get Addresses  
	 *
	 * @author Manthan Kanani	
	 * @since 2.0.0
	**/
	public function getAddresses($request){
		$api 				= new APIClass();
		$user 				= $request->get_param('loggedin_data');

		if($result = $api->getAddresses($user->ID)){
			$arr = array(
				"success" 	=> true, 
				"message" 	=> "Address retrived Successfully", 
				"data" 		=> array("address" => $result)
			);
		} else {
			$arr = array("success" => false, "message" => __("Unable to retrive User", "swipecart"));
			return new WP_REST_Response($arr, 400);
		}
		return $arr;
	}


	/**
	 * Get Addresses  
	 *
	 * @author Manthan Kanani	
	 * @since 2.0.0
	 * @version 2.7.1
	**/
	public function updateAddress($request){
		$api 							= new APIClass();
		$user 							= $request->get_param('loggedin_data');

		$addressdata['id'] 				=  $request->get_param('id');
		$addressdata['address_1'] 		=  $request->get_param('address1');
		$addressdata['address_2'] 		=  $request->get_param('address2');
		$addressdata['city'] 			=  $request->get_param('city');
		$addressdata['company'] 		=  $request->get_param('company');
		$addressdata['country'] 		=  $request->get_param('country');
		$addressdata['first_name'] 		=  $request->get_param('firstName');
		$addressdata['last_name'] 		=  $request->get_param('lastName');
		$addressdata['phone'] 			=  $request->get_param('phone');
		$addressdata['state'] 			=  $request->get_param('province');
		$addressdata['postcode'] 		=  $request->get_param('zip');

		if($result = $api->updateAddress($user->ID, $addressdata)){
			$arr = array(
				"success" 	=> true, 
				"message" 	=> __("Address Updated Successfully", "swipecart"), 
				"data" 		=> $result
			);
		} else {
			$arr = array("success" => false, "error" => __("Address does not exist", "swipecart"));
			return new WP_REST_Response($arr, 400);
		}
		
		return $arr;
	}


	/**
	 * Delete user and wipe out his data  
	 *
	 * @author Manthan Kanani	
	 * @since 2.3.0
	 * @version 2.7.1
	**/
	public function deleteCustomer($request){
		$api 				= new APIClass();
		$user 				= $request->get_param('loggedin_data');

		if($result = $api->deleteUser($user->ID, 'customer')){
			$arr = array(
				"success" 	=> true, 
				"message" 	=> __("Customer Removed Successfully.", "swipecart"), 
				"data" 		=> array( "role" => "customer", "removed" => $result )
			);
		} else {
			$arr = array("success" => false, "error" => __("You're not a customer. Please Contact Site Administrator", "swipecart"));
			return new WP_REST_Response($arr, 400);
		}
		return $arr;
	}


	/**
	 * Generate Checkout
	 *
	 * @author Manthan Kanani	
	 * @since 2.0.0
	 * @version 2.7.1
	**/
	public function generateCheckout($request){
		$api 					= new APIClass();
		$line_items 			= $request->get_param('lineItems');
		$authorization 			= $request->get_header('Authorization');
		$StoreFrontToken		= str_replace('Bearer ','',$authorization);

		$checkOutToken = $api->tokenValidation($StoreFrontToken) ? $StoreFrontToken : null ;
		if(!$checkOutToken && $StoreFrontToken !== $this->storeFrontToken()){
			$arr = array("success" => false, "error" => __("Invalid User !!", "swipecart"));
			return new WP_REST_Response($arr, 400);
		}

		if($result = $api->generateCheckout($line_items, $checkOutToken)){
			$arr = array(
				"success" 	=> true, 
				"message" 	=> __("Checkout generated", "swipecart"), 
				"data" 		=> $result
			);
		} else {
			$arr = array("success" => false, "error" => __("Invalid User !!", "swipecart"));
			return new WP_REST_Response($arr, 400);
		}
		return $arr;		
	}


	/**
	 * Get Customer Orders  
	 *
	 * @author Manthan Kanani	
	 * @since 2.0.0
	 * @version 2.7.1
	**/
	public function getOrders($request){
		$api 				= new APIClass();
		$order 				= $request->get_param('order');
		$orderBy 			= $request->get_param('orderBy');
		$page 				= $request->get_param('page');
		$pp 				= $request->get_param('pp');
		$user 				= $request->get_param('loggedin_data');

        $result = $api->getOrders($order, $orderBy, $page, $pp, $user->ID);
        if($result) {
            $arr = array(
                "success"   => true, 
                "message"   => "success", 
                "data"      => array("order" => $result)
            );
        } else {
            $arr = array("status" => false, "error" => __("No orders found", "swipecart"));
        }

		return $arr;
	}


	/**
	 * Get Order By ID  
	 *
	 * @author Manthan Kanani	
	 * @since 2.0.0
	 * @version 2.7.1
	**/
	public function getOrderById($request){
		$api 				= new APIClass();
		$id 				= $request->get_param('id');
		$user 				= $request->get_param('loggedin_data');

		$result = $api->getOrderByID($id, $user->ID, false);
		if($result) {
			$arr = array(
				"success" 	=> true, 
				"message" 	=> "success", 
				"data" 		=> array("order" => $result)
			);
		} else {
			$arr = array("status" => false, "error" => __("The requested order does not exist.", "swipecart"));
		}
		return $arr;
	}


	/**
	 * webhook topic
	 *
	 * @author Manthan Kanani	
	 * @since 2.4.0
	**/
	public function webhookTopic($request){
		$api 				= new APIClass();
		$action 			= $request->get_param('action');
		$topic 				= $request->get_param('topic');
		$callbackURL 		= $request->get_param('callbackURL');
		$callbackURL 		= ($callbackURL)? $callbackURL : null ;
		
		if($result = $api->webhookCRUD($action, $topic, $callbackURL)){
			if(in_array($action, ['c','u'])){
				$arr = array(
					"success" 	=> true, 
					"message" 	=> __("Webhook saved gracefully", "swipecart")
				);
			} else {
				$arr = array(
					"success" 	=> true, 
					"message" 	=> __("Webhook actioned gracefully", "swipecart"),
					"data" 		=> $result
				);
			}
		} else {
			$arr = array("success" => false, "error" => __("Invalid webhook !!", "swipecart"));
			return new WP_REST_Response($arr, 400);
		}
		return $arr;
	}



	/**
	 * Get Analytics
	 *
	 * @author Manthan Kanani	
	 * @since 2.7.1
	**/
	public function getAnalytics($request){
		$api 				= new APIClass();
		$type 				= $request->get_param('type');
		$count 				= $request->get_param('count');
		$order 				= $request->get_param('order');
		$orderBy 			= $request->get_param('orderBy');
		$page 				= $request->get_param('page');
		$pp 				= $request->get_param('pp');
		$filters 			= $request->get_param('filter');

		if($type=="order"){
			if(!$count){
				$result = $api->getOrders($order, $orderBy, $page, $pp, null, $filters);
			}

			if($result) {
				$arr = array(
					"success"   => true, 
					"message"   => "success", 
					"data"      => array("order" => $result)
				);
			} else {
				$arr = array("status" => false, "error" => __("No orders found", "swipecart"));
			}
		} else {
			$arr = array("status" => false, "error" => __("Invalid request Type", "swipecart"));
		}

		return $arr;
	}



	/**
	 * get public sitedata
	 *
	 * @author Manthan Kanani	
	 * @since 2.4.0
	**/
	public function getPublicSiteData($request){
		$api 			= new APIClass();

		$siteData = $api->getPublicSiteData();
		$arr = array(
			"success" 	=> true, 
			"message" 	=> __("Site Data retrived Successfully", "swipecart"), 
			"data" 		=> $siteData
		);
		return $arr;
	}



	/**
	 * get list of available languages
	 * 
	 * @author Manthan Kanani
	 * @since 2.6.1
	**/
	public function getAvailableLanguages($request){
		$api 			= new APIClass();

		if($languages = $api->getAvailableLanguages()){
			$arr = array(
				"success" 	=> true, 
				"message" 	=> __("Language data retrived Successfully", "swipecart"), 
				"data" 		=> $languages
			);
		} else {
			$arr = array("success" => false, "error" => __("No Languages Found", "swipecart"));
			return new WP_REST_Response($arr, 401);
		}
		return $arr;
	}

	

	/**
	 * Verify Authorization and return sideData
	 *
	 * @author Manthan Kanani	
	 * @since 2.1.0
	 * @version 2.4.0
	**/
	public function verifyAuthorization($request){
		$api 			= new APIClass();

		$siteData = $api->getSiteData();
		$arr = array(
			"success" 	=> true, 
			"message" 	=> __("Authorized Successfully", "swipecart"), 
			"data" 		=> $siteData
		);
		return $arr;
	}



	/**
	 * Verify Nonce
	 *
	 * @author Manthan Kanani	
	 * @since 2.1.1
	 * @version 2.4.0
	**/
	public function verifyNonce($request){
		$api 			= new APIClass();
		$nonce 			= $request->get_param('nonce');

		if($result = $api->nonceValidation($nonce)){
			$arr = array(
				"success" 	=> true, 
				"message" 	=> __("Nonce Validated", "swipecart")
			);
		} else {
			$arr = array("success" => false, "error" => __("Invalid Nonce !!", "swipecart"));
			return new WP_REST_Response($arr, 401);
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
		 * sc/v1/getAllCollections
		 *
		 * @param (str) {order}		=> ('asc'||'desc')
		 * @param (str) {orderBy}	=> ('id'||'name'||'popularity'||'date')
		 * @param (int) {page}		=> ({}>0)
		 * @param (int) {pp}		=> ({}>=0)
		 * @param (str) {search}	=> (!='')
		 *
		 * @return json
		 */
		register_rest_route($this->namespace, 'getAllCollections', [
			'methods' 				=> 'POST',
			'callback' 				=> array( $this, 'getAllCollectionsCategories'),
			'permission_callback' 	=> array( $this, 'verifyHeaderPermissionCallback' ),
			'args' => array(
				'order' => array(
					'required' 			=> false,
					'sanitize_callback'	=> array($this, 'orderSanitizeCallback'),
					'validate_callback'	=> array($this, 'orderValidateCallback'),
				),
				'orderBy' => array(
					'required' 			=> false,
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
				),
				'search' => array(
					'required' 			=> false,
					'sanitize_callback'	=> 'sanitize_text_field',
					'validate_callback'	=> array($this, 'textRequiredValidateCallback'),
				)
			)
		]);


		/**
		 * Get Products by IDs
		 * sc/v1/GetCollectionsById
		 *
		 * @param (int) {{ids}}	=> (array({})>0)
		 *
		 * @return json
		 */
		register_rest_route($this->namespace, 'GetCollectionsById', [
			'methods' 				=> 'POST',
			'callback' 				=> array( $this, 'GetCollectionsByIds'),
			'permission_callback' 	=> array( $this, 'verifyHeaderPermissionCallback' ),
			'args' => array(
				'ids' => array(
					'required' 			=> true,
					'sanitize_callback'	=> array($this, 'numericPositiveArraySanitizeCallback'),
					'validate_callback'	=> array($this, 'numericPositiveArrayValidateCallback'),
				)
			)
		]);


		/**
		 * Get Product by ID
		 * sc/v1/GetProductById
		 *
		 * @param (int) {{id}}	=> ({}>0)
		 *
		 * @return json
		 */
		register_rest_route($this->namespace, 'GetProductById', [
			'methods' 				=> 'POST',
			'callback' 				=> array( $this, 'GetProductById'),
			'permission_callback' 	=> array( $this, 'verifyHeaderPermissionCallback' ),
			'args' => array(
				'id' => array(
					'required' 			=> true,
					'sanitize_callback'	=> 'absint',
					'validate_callback'	=> array($this, 'numericPositiveValidateCallback'),
				)
			)
		]);


		/**
		 * Get Products by IDs
		 * sc/v1/GetProductsById
		 *
		 * @param (int) {{ids}}	=> (array({})>0)
		 *
		 * @return json
		 */
		register_rest_route($this->namespace, 'GetProductsById', [
			'methods' 				=> 'POST',
			'callback' 				=> array( $this, 'GetProductsByIds'),
			'permission_callback' 	=> array( $this, 'verifyHeaderPermissionCallback' ),
			'args' => array(
				'ids' => array(
					'required' 			=> true,
					'sanitize_callback'	=> array($this, 'numericPositiveArraySanitizeCallback'),
					'validate_callback'	=> array($this, 'numericPositiveArrayValidateCallback'),
				)
			)
		]);


		/**
		 * Search Products by Name
		 * sc/v1/SearchProducts
		 *
		 * @param (int) {{search}}	=> (!='')
		 * @param (int) {page}		=> ({}>0)
		 * @param (int) {pp}		=> ({}>=0)
		 *
		 * @return json
		 */
		register_rest_route($this->namespace, 'SearchProducts', [
			'methods' 				=> 'POST',
			'callback' 				=> array( $this, 'SearchProducts'),
			'permission_callback' 	=> array( $this, 'verifyHeaderPermissionCallback' ),
			'args' => array(
				'search' => array(
					'required' 			=> true,
					'sanitize_callback'	=> 'sanitize_text_field',
					'validate_callback'	=> array($this, 'textRequiredValidateCallback'),
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
				),
			)
		]);


		/**
		 * get filtered Products 
		 * sc/v1/GetProducts
		 *
		 * @param (str) {order}		=> ('asc'||'desc')
		 * @param (str) {orderBy}	=> ('id'||'best_selling'||'price'||'recommended'||'date')
		 * @param (int) {page}		=> ({}>0)
		 * @param (int) {pp}		=> ({}>=0)
		 * @param (int) {category}	=> (!='')
		 * @param (obj) {filter}	=> ([{[sectionValue],[tags][{value}]}])
		 * @param (str) {search}	=> (!='')
		 *
		 * @return json
		 */
		register_rest_route($this->namespace, 'GetProducts', [
			'methods' 				=> 'POST',
			'callback' 				=> array( $this, 'getProducts'),
			'permission_callback' 	=> array( $this, 'verifyHeaderPermissionCallback' ),
			'args' => array(
				'order' => array(
					'default' 			=> 'desc',
					'sanitize_callback'	=> array($this, 'orderSanitizeCallback'),
					'validate_callback'	=> array($this, 'orderValidateCallback'),
				),
				'orderBy' => array(
					'default' 			=> 'id',
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
				),
				'category' => array(
					'required' 			=> false,
					'sanitize_callback'	=> array($this, 'numericPositiveArraySanitizeCallback'),
					'validate_callback'	=> array($this, 'numericPositiveArrayValidateCallback')
				),
				'filter' => array(
					'required' 			=> false,
					'sanitize_callback'	=> array($this, 'filterArraySanitizeCallback'),
					'validate_callback'	=> array($this, 'filterArrayValidateCallback'),
				),
				'search' => array('required' => false )
			)
		]);


		/**
		 * Search Products by Name
		 * sc/v1/GetProductsByCollectionId
		 *
		 * @param (int) {{id}}		=> (!='')
		 * @param (str) {order}		=> ('asc'||'desc')
		 * @param (str) {orderBy}	=> ('id'||'best_selling'||'price'||'recommended'||'date')
		 * @param (int) {page}		=> ({}>0)
		 * @param (int) {pp}		=> ({}>=0)
		 * @param (obj) {filter}	=> ([{[sectionValue],[tags][{value}]}])
		 * @param (str) {search}	=> (!='')
		 *
		 * @return json
		 */
		register_rest_route($this->namespace, 'GetProductsByCollectionId', [
			'methods' 				=> 'POST',
			'callback' 				=> array( $this, 'getProductsByCollectionId'),
			'permission_callback' 	=> array( $this, 'verifyHeaderPermissionCallback' ),
			'args' => array(
				'id' => array(
					'required' 			=> true,
					'sanitize_callback'	=> 'absint',
					'validate_callback'	=> array($this, 'numericPositiveValidateCallback'),
				),
				'order' => array(
					'default' 			=> 'desc',
					'sanitize_callback'	=> array($this, 'orderSanitizeCallback'),
					'validate_callback'	=> array($this, 'orderValidateCallback'),
				),
				'orderBy' => array(
					'default' 			=> 'id',
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
				),
				'filter' => array(
					'required' 			=> false,
					'sanitize_callback'	=> array($this, 'filterArraySanitizeCallback'),
					'validate_callback'	=> array($this, 'filterArrayValidateCallback'),
				),
				'search' => array('required' => false)
			)
		]);


		/**
		 * Get recommended Products
		 * sc/v1/GetRecommandedProducts
		 *
		 * @param (int) {product_id}	=> (!='')
		 * @param (int) {pp}			=> ({}>=0)
		 *
		 * @return json
		 */
		register_rest_route($this->namespace, 'GetRecommandedProducts', [
			'methods' 				=> 'POST',
			'callback' 				=> array( $this, 'getRecommendedProducts'),
			'permission_callback' 	=> array( $this, 'verifyHeaderPermissionCallback' ),
			'args' => array(
				'product_id' => array(
					'required' 			=> false,
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
		 * Search Products by Name
		 * sc/v1/GetProductsByVarientId
		 *
		 * @param (int) {{ids}}		=> (!=formatted_array)
		 *
		 * @return json
		 */
		register_rest_route($this->namespace, 'GetProductsByVarientId', [
			'methods' 				=> 'POST',
			'callback' 				=> array( $this, 'getProductsByVarientId'),
			'permission_callback' 	=> array( $this, 'verifyHeaderPermissionCallback' ),
			'args' => array(
				'ids' => array(
					'required' 			=> true,
					'sanitize_callback'	=> array($this, 'cartVariationSanitizeCallback'),
					'validate_callback'	=> array($this, 'cartVariationValidateCallback'),
				)
			)
		]);


		/**
		 * Search Product Filters
		 * sc/v1/GetProductFilters
		 *
		 * @return json
		 */
		register_rest_route($this->namespace, 'GetProductFilters', [
			'methods' 				=> 'POST',
			'callback' 				=> array( $this, 'getProductFilters'),
			'permission_callback' 	=> array( $this, 'verifyHeaderPermissionCallback' )
		]);


		/**
		 * Get Product Review
		 * sc/v1/GetReviewsByProductId
		 *
		 * @param (int) {{product_id}}	=> (!='')
		 * @param (int) {page}			=> ({}>0)
		 * @param (int) {pp}			=> ({}>=0)
		 *
		 * @return json
		 */
		register_rest_route($this->namespace, 'GetReviewsByProductId', [
			'methods' 				=> 'POST',
			'callback' 				=> array( $this, 'getReviewsByProductId'),
			'permission_callback' 	=> array( $this, 'verifyHeaderPermissionCallback' ),
			'args' => array(
				'product_id' => array(
					'required' 			=> true,
					'sanitize_callback'	=> 'absint',
					'validate_callback'	=> array($this, 'numericPositiveValidateCallback'),
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
		 * Get Product Review By ID
		 * sc/v1/GetReviewsById
		 *
		 * @param (int) {{review_id}}	=> (!='')
		 *
		 * @return json
		 */
		register_rest_route($this->namespace, 'GetReviewById', [
			'methods' 				=> 'POST',
			'callback' 				=> array( $this, 'getReviewById'),
			'permission_callback' 	=> array( $this, 'verifyHeaderPermissionCallback' ),
			'args' => array(
				'review_id' => array(
					'required' 			=> true,
					'sanitize_callback'	=> 'absint',
					'validate_callback'	=> array($this, 'numericPositiveValidateCallback'),
				)
			)
		]);


		/**
		 * Add New Product Review
		 * sc/v1/AddProductReview
		 *
		 * @param (int) {{review_id}}		=> (!='')
		 * @param (int) {{description}}		=> (!='')
		 * @param (int) {{rating}}			=> (0<{}<5)
		 *
		 * @return json
		 */
		register_rest_route($this->namespace, 'AddProductReview', [
			'methods' 				=> 'POST',
			'callback' 				=> array( $this, 'addProductReview'),
			'permission_callback' 	=> array( $this, 'verifyAccessTokenPermissionCallback' ),
			'args' => array(
				'product_id' => array(
					'required' 			=> true,
					'sanitize_callback'	=> 'absint',
					'validate_callback'	=> array($this, 'numericPositiveValidateCallback'),
				),
				'rating' => array(
					'required' 			=> true,
					'sanitize_callback'	=> 'absint',
					'validate_callback'	=> array($this, 'ratingValidateCallback'),
				),
				'description' => array(
					'required' 			=> true,
					'sanitize_callback'	=> array($this, 'trimSanitizeCallback'),
					'validate_callback'	=> array($this, 'textRequiredValidateCallback'),
				)
			)
		]);


		/**
		 * Update Product Review
		 * sc/v1/UpdateProductReview
		 *
		 * @param (int) {{review_id}}		=> (!='')
		 * @param (int) {{description}}		=> (!='')
		 * @param (int) {{rating}}			=> (0<{}<5)
		 *
		 * @return json
		 */
		register_rest_route($this->namespace, 'UpdateProductReview', [
			'methods' 				=> 'POST',
			'callback' 				=> array( $this, 'updateProductReview'),
			'permission_callback' 	=> array( $this, 'verifyAccessTokenPermissionCallback' ),
			'args' => array(
				'review_id' => array(
					'required' 			=> true,
					'sanitize_callback'	=> 'absint',
					'validate_callback'	=> array($this, 'numericPositiveValidateCallback'),
				),
				'rating' => array(
					'required' 			=> true,
					'sanitize_callback'	=> 'absint',
					'validate_callback'	=> array($this, 'ratingValidateCallback'),
				),
				'description' => array(
					'required' 			=> true,
					'sanitize_callback'	=> array($this, 'trimSanitizeCallback'),
					'validate_callback'	=> array($this, 'textRequiredValidateCallback'),
				)
			)
		]);



		/**
		 * Update Product Review
		 * sc/v1/DeleteProductReview
		 *
		 * @param (int) {{review_id}}		=> (!='')
		 *
		 * @return json
		 */
		register_rest_route($this->namespace, 'DeleteProductReview', [
			'methods' 				=> 'POST',
			'callback' 				=> array( $this, 'deleteProductReview'),
			'permission_callback' 	=> array( $this, 'verifyAccessTokenPermissionCallback' ),
			'args' => array(
				'review_id' => array(
					'required' 			=> true,
					'sanitize_callback'	=> 'absint',
					'validate_callback'	=> array($this, 'numericPositiveValidateCallback'),
				)
			)
		]);



		/**
		 * Get Product by ID
		 * sc/v1/GetOrderById
		 *
		 * @param (int) {{id}}	=> ({}>0)
		 *
		 * @return json
		 */
		register_rest_route($this->namespace, 'GetOrderById', [
			'methods' 				=> 'POST',
			'callback' 				=> array( $this, 'getOrderById'),
			'permission_callback' 	=> array( $this, 'verifyAccessTokenPermissionCallback' ),
			'args' => array(
				'id' => array(
					'required' 			=> true,
					'sanitize_callback'	=> 'sanitize_text_field',
					'validate_callback'	=> array($this, 'textRequiredValidateCallback'),
				)
			)
		]);


		/**
		 * Get Woocommerce Customer Orders
		 * sc/v1/GetOrders
		 *
		 * @param (int) {customer}	=> ({}>0)
		 * @param (str) {order}		=> ('asc'||'desc')
		 * @param (str) {orderBy}	=> ('id'||'name'||'popularity'||'date')
		 * @param (int) {page}		=> ({}>0)
		 * @param (int) {pp}		=> ({}>=0)
		 *
		 * @return json
		 */
		register_rest_route($this->namespace, 'GetOrders', [
			'methods' 				=> 'POST',
			'callback' 				=> array( $this, 'getOrders'),
			'permission_callback' 	=> array( $this, 'verifyAccessTokenPermissionCallback' ),
			'args' => array(
				'order' => array(
					'required' 			=> false,
					'sanitize_callback'	=> array($this, 'orderSanitizeCallback'),
					'validate_callback'	=> array($this, 'orderValidateCallback'),
				),
				'orderBy' => array(
					'required' 			=> false,
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
		 * Create Customer for Woocommerce
		 * sc/v1/SignUp
		 *
		 * @param (str) {{email}}		=> (=@)
		 * @param (str) {{firstName}}	=> (!='')
		 * @param (str) {{lastName}}	=> (!='')
		 * @param (str) {{password}}	=> (!='')
		 * @param (str) {phone}			=> (=#)
		 *
		 * @return json
		 */
		register_rest_route($this->namespace, 'SignUp', [
			'methods' 				=> 'POST',
			'callback' 				=> array( $this, 'customerSignUp'),
			'permission_callback' 	=> array( $this, 'verifyHeaderPermissionCallback' ),
			'args' => array(
				'email' => array(
					'required' 			=> true,
					'sanitize_callback'	=> 'sanitize_email',
					'validate_callback'	=> 'is_email',
				),
				'firstName' => array(
					'required' 			=> true,
					'sanitize_callback'	=> 'sanitize_text_field',
					'validate_callback'	=> array($this, 'textRequiredValidateCallback'),
				),
				'lastName' => array(
					'required' 			=> true,
					'sanitize_callback'	=> 'sanitize_text_field',
					'validate_callback'	=> array($this, 'textRequiredValidateCallback'),
				),
				'phone' => array(
					'required' 			=> false,
					'sanitize_callback'	=> array($this, 'telSanitizeCallback'),
					'validate_callback'	=> array($this, 'telValidateCallback'),
				),
				'password' => array(
					'required' 			=> true,
					'validate_callback'	=> array($this, 'textRequiredValidateCallback'),
				)
			)
		]);


		/**
		 * Get User token after Successfully loggedin
		 * sc/v1/SignIn
		 *
		 * @param (str) {{email}}		=> (=@)
		 * @param (str) {{password}}	=> (!='')
		 *
		 * @return json
		 */
		register_rest_route($this->namespace, 'SignIn', [
			'methods' 				=> 'POST',
			'callback' 				=> array( $this, 'SignIn'),
			'permission_callback' 	=> array( $this, 'verifyHeaderPermissionCallback' ),
			'args' => array(
				'email' => array(
					'required' 			=> true,
					'sanitize_callback'	=> 'sanitize_email',
					'validate_callback'	=> 'is_email',
				),
				'password' => array(
					'required' 			=> true,
					'validate_callback'	=> array($this, 'textRequiredValidateCallback'),
				)
			)
		]);


		/**
		 * Delete user as per UK norms
		 * sc/v1/DeleteUser
		 *
		 * @return json
		 */
		register_rest_route($this->namespace, 'DeleteUser', [
			'methods' 				=> 'POST',
			'callback' 				=> array( $this, 'deleteCustomer'),
			'permission_callback' 	=> array( $this, 'verifyAccessTokenPermissionCallback' ),
		]);


		/**
		 * Forgot User Password
		 * sc/v1/ForgotPassword
		 *
		 * @param (str) {{email}}		=> (=@)
		 *
		 * @return json
		 */
		register_rest_route($this->namespace, 'ForgotPassword', [
			'methods' 				=> 'POST',
			'callback' 				=> array( $this, 'forgotPassword'),
			'permission_callback' 	=> array( $this, 'verifyHeaderPermissionCallback' ),
			'args' => array(
				'email' => array(
					'required' 			=> true,
					'sanitize_callback'	=> 'sanitize_email',
					'validate_callback'	=> 'is_email',
				)
			)
		]);


		/**
		 * Create Customer for Woocommerce
		 * sc/v1/ChangeProfile
		 *
		 * @param (str) {{firstName}}	=> (!='')
		 * @param (str) {{lastName}}	=> (!='')
		 * @param (str) {phone}			=> (=#)
		 *
		 * @return json
		 */
		register_rest_route($this->namespace, 'ChangeProfile', [
			'methods' 				=> 'POST',
			'callback' 				=> array( $this, 'ChangeProfile'),
			'permission_callback' 	=> array( $this, 'verifyAccessTokenPermissionCallback' ),
			'args' => array(
				'firstName' => array(
					'required' 			=> true,
					'sanitize_callback'	=> 'sanitize_text_field',
					'validate_callback'	=> array($this, 'textRequiredValidateCallback'),
				),
				'lastName' => array(
					'required' 			=> true,
					'sanitize_callback'	=> 'sanitize_text_field',
					'validate_callback'	=> array($this, 'textRequiredValidateCallback'),
				),
				'phone' => array(
					'required' 			=> false,
					'sanitize_callback'	=> array($this, 'telSanitizeCallback'),
					'validate_callback'	=> array($this, 'telValidateCallback'),
				),
			)
		]);


		/**
		 * Change Password of User
		 * sc/v1/ChangePassword
		 *
		 * @param (str) {{password}}	=> (!='')
		 *
		 * @return json
		 */
		register_rest_route($this->namespace, 'ChangePassword', [
			'methods' 				=> 'POST',
			'callback' 				=> array( $this, 'ChangePassword'),
			'permission_callback' 	=> array( $this, 'verifyAccessTokenPermissionCallback' ),
			'args' => array(
				'password' => array(
					'required' 			=> true,
					'validate_callback'	=> array($this, 'textRequiredValidateCallback'),
				),
			)
		]);


		/**
		 * get Addresses
		 * sc/v1/GetAddresses
		 *
		 * @return json
		 */
		register_rest_route($this->namespace, 'GetAddresses', [
			'methods' 				=> 'POST',
			'callback' 				=> array( $this, 'getAddresses'),
			'permission_callback' 	=> array( $this, 'verifyAccessTokenPermissionCallback' ),
		]);


		/**
		 * update Address
		 * sc/v1/UpdateAddress
		 *
		 * @param (str) {{id}}			=> (!='')
		 * @param (str) {address1}		=> (!='')
		 * @param (str) {address2}		=> (!='')
		 * @param (str) {city}			=> (!='')
		 * @param (str) {company}		=> (!='')
		 * @param (str) {country}		=> (!='')
		 * @param (str) {firstName}		=> (!='')
		 * @param (str) {lastName}		=> (!='')
		 * @param (str) {phone}			=> (=#)
		 * @param (str) {province}		=> (!='')
		 * @param (str) {zip}			=> (!='')
		 *
		 * @return json
		 */
		register_rest_route($this->namespace, 'UpdateAddress', [
			'methods' 				=> 'POST',
			'callback' 				=> array( $this, 'updateAddress'),
			'permission_callback' 	=> array( $this, 'verifyAccessTokenPermissionCallback' ),
			'args' => array(
				'id' => array(
					'required' 			=> true,
					'sanitize_callback'	=> 'sanitize_text_field',
					'validate_callback'	=> array($this, 'addressIDValidateCallback'),
				),
				'address1' => array(
					'required' 			=> false,
					'sanitize_callback'	=> 'sanitize_text_field',
				),
				'address2' => array(
					'required' 			=> false,
					'sanitize_callback'	=> 'sanitize_text_field',
				),
				'city' => array(
					'required' 			=> false,
					'sanitize_callback'	=> 'sanitize_text_field',
				),
				'company' => array(
					'required' 			=> false,
					'sanitize_callback'	=> 'sanitize_text_field',
				),
				'country' => array(
					'required' 			=> false,
					'sanitize_callback'	=> 'sanitize_text_field',
				),
				'firstName' => array(
					'required' 			=> false,
					'sanitize_callback'	=> 'sanitize_text_field',
				),
				'lastName' => array(
					'required' 			=> false,
					'sanitize_callback'	=> 'sanitize_text_field',
				),
				'phone' => array(
					'required' 			=> false,
					'sanitize_callback'	=> array($this, 'telSanitizeCallback'),
					'validate_callback'	=> array($this, 'telValidateCallback'),
				),
				'province' => array(
					'required' 			=> false,
					'sanitize_callback'	=> 'sanitize_text_field',
					'validate_callback'	=> array($this, 'textRequiredValidateCallback'),
				),
				'zip' => array(
					'required' 			=> false,
					'sanitize_callback'	=> 'sanitize_text_field',
					'validate_callback'	=> array($this, 'textRequiredValidateCallback'),
				)
			)
		]);


		/**
		 * Generate Checkout URL
		 * sc/v1/GenerateCheckout
		 *
		 * @param (obj) {{lineItems}}		=> (array())
		 *
		 * @return json
		 */
		register_rest_route($this->namespace, 'GenerateCheckout', [
			'methods' 				=> 'POST',
			'callback' 				=> array( $this, 'generateCheckout'),
			'permission_callback' 	=> '__return_true',
			'args'					=> array(
				'lineItems' => array(
					'required' 			=> true,
					'sanitize_callback'	=> array($this, 'lineItemsArraySanitizeCallback'),
					'validate_callback'	=> array($this, 'lineItemsArrayValidateCallback'),
				)
			)
		]);


		/**
		 * Test Authorization
		 * sc/v1/webhooks/woocommerce
		 *
		 * @param (str) {{action}}		=> ('c'||'r'||'u'||'d')
		 * @param (str) {{topic}}		=> (!='')
		 * @param (str) {callbackURL}	=> (=http://)
		 *
		 * @return json
		 */
		register_rest_route($this->namespace, 'webhooks/woocommerce', [
			'methods' 				=> 'POST',
			'callback' 				=> array( $this, 'webhookTopic'),
			'permission_callback' 	=> array( $this, 'verifyHeaderSecretPermissionCallback' ),
			'args'					=> array(
				'action' => array(
					'required' 			=> true,
					'sanitize_callback'	=> array($this, 'actionSanitizeCallback'),
					'validate_callback'	=> array($this, 'actionValidateCallback'),
				),
				'topic' => array(
					'required' 			=> true,
					'sanitize_callback'	=> 'sanitize_text_field',
					'validate_callback'	=> array($this, 'textRequiredValidateCallback'),
				),
				'callbackURL' => array(
					'required' 			=> false,
					'sanitize_callback'	=> 'sanitize_url',
					'validate_callback'	=> array($this, 'urlValidateCallback'),
				),
			)
		]);


		/**
		 * get Analytics Data
		 * sc/v1/GetAnalytics
		 *
		 * @return json
		 */
		register_rest_route($this->namespace, 'GetAnalytics', [
			'methods' 				=> 'POST',
			'callback' 				=> array( $this, 'getAnalytics'),
			'permission_callback' 	=> array( $this, 'verifyHeaderSecretPermissionCallback' ),
			'args'					=> array(
				'type' => array(
					'required' 			=> true,
					'enum'				=> ['order'],
					'type'				=> 'string',
					'sanitize_callback' => 'sanitize_key',
					'validate_callback' => 'rest_validate_request_arg',
				),
				'count' => array(
					'default' 			=> false,
					'type'				=> 'boolean',
					'sanitize_callback'	=> 'rest_sanitize_boolean',
				),
				'order' => array(
					'default' 			=> 'desc',
					'enum'				=> ['asc','desc'],
					'type'				=> 'string',
					'sanitize_callback' => 'sanitize_key',
					'validate_callback' => 'rest_validate_request_arg',
				),
				'orderBy' => array(
					'default' 			=> 'id',
					'enum'				=> ['id','name','date','modified'],
					'type'				=> 'string',
					'sanitize_callback' => 'sanitize_key',
					'validate_callback' => 'rest_validate_request_arg',
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
				),
				'filter' => array('required' => false),
			)
		]);


		/**
		 * get Private Data
		 * sc/v1/GetSiteData
		 *
		 * @return json
		 */
		register_rest_route($this->namespace, 'GetSiteData', [
			'methods' 				=> 'POST',
			'callback' 				=> array( $this, 'getPublicSiteData'),
			'permission_callback' 	=> '__return_true'
		]);



		/**
		 * get Avaiable Language
		 * sc/v1/GetAvailableLanguage
		 *
		 * @return json
		 */
		register_rest_route($this->namespace, 'GetAvailableLanguage', [
			'methods' 				=> 'POST',
			'callback' 				=> array( $this, 'getAvailableLanguages'),
			'permission_callback' 	=> array( $this, 'verifyHeaderPermissionCallback' ),
		]);


		/**
		 * Test Authorization by secret
		 * sc/v1/verify/authorization
		 *
		 * @return json
		 */
		register_rest_route($this->namespace, 'verify/authorization', [
			'methods' 				=> 'POST',
			'callback' 				=> array( $this, 'verifyAuthorization'),
			'permission_callback' 	=> array( $this, 'verifyHeaderSecretPermissionCallback' ),
		]);


		/**
		 * Verify Nonce
		 * sc/v1/verify/nonce
		 *
		 * @param (str) {{nonce}}		=> (!="")
		 *
		 * @return json
		 */
		register_rest_route($this->namespace, 'verify/nonce', [
			'methods' 				=> 'POST',
			'callback' 				=> array( $this, 'verifyNonce'),
			'permission_callback' 	=> array( $this, 'verifyHeaderPermissionCallback' ),
			'args'					=> array(
				'nonce' => array(
					'required' 			=> true,
					'sanitize_callback'	=> 'sanitize_text_field',
					'validate_callback'	=> array($this, 'textRequiredValidateCallback'),
				)
			)
		]);
	}








	/**
	 * Verify request Header Callbacks
	 *
	 * @return bool
	**/
	public function verifyHeaderPermissionCallback(WP_REST_Request $request){
		$api 						= new APIClass();
		$authorizationBearer  		= $request->get_header('Authorization');
		$BearerToken				= str_replace('Bearer ','',$authorizationBearer);

		if($BearerToken == $this->storeFrontToken()) return true;
		if($user = $api->tokenValidation($BearerToken)){
			$request->set_param('loggedin_data', $user);
			return true;
		}
		return false;
	}
	public function verifyAccessTokenPermissionCallback(WP_REST_Request $request){
		$api 					= new APIClass();
		$authorization 			= $request->get_header('Authorization');
		$BearerToken			= str_replace('Bearer ','',$authorization);

		if($user = $api->tokenValidation($BearerToken)) {
			$request->set_param('loggedin_data', $user);
			return true;
		}
		return false;
	}
	public function verifyHeaderSecretPermissionCallback(WP_REST_Request $request){
		if(!class_exists('WC_SC_Webhook')) return false;
		$webhook 				= new WC_SC_Webhook();

		$authorization 			= $request->get_header('Authorization');
		$wooToken				= str_replace('Bearer ','',$authorization);

		if($webhook->isValidSecret($wooToken)) return true;
		return false;
	}

	/**
	 * Sanitize Callbacks
	 *
	 * @return filtered Value
	**/
	public function returnCSVArraySanitizeCallback($param){
		$param = explode(',', $param);
		$param = array_map('intval', $param);
		return $param;
	}
	public function numericPositiveArraySanitizeCallback($param){
		$result = array_map(function($v) {
			return $this->numericPositiveValidateCallback($v)? $v : '' ;
		}, $param);
		$result = array_filter($result);
		$result = array_unique($result);
		return array_values($result);
	}
	public function lineItemsArraySanitizeCallback($param){
		$result = array_map(function($v) {
			return array(
				'variation_id' 	=> (int) $v['varient_id'],
				'quantity' 		=> (int) $v['quantity']
			);
		}, $param);
		return $result;
	}
	public function filterArraySanitizeCallback($param){
		$result = array_map(function($v){
			$tags = array();
			if(!is_array($v['tags']) || !count($v['tags'])) return;
			foreach($v['tags'] as $tag){
				$tags[] = $tag['value'];
			}
			return array(
				'attribute' => $v['sectionValue'],
				'terms' 	=> $tags
			);
		}, $param);
		$result = array_filter($result);
		return array_values($result);
	}
	public function orderSanitizeCallback($param){
		$param = strtolower($param);
		return (in_array($param, ['0','asc'])) ? 'asc' : 'desc';
	}
	public function orderBySanitizeCallback($param){
		$param = strtolower($param);

		if(in_array($param,['name','title'])){
			$param = 'title';
		} elseif(in_array($param,['date','created'])){
			$param = 'date';
		} elseif(in_array($param,['popularity'])){
			$param = 'best_selling';
		}

		$orders = array('id','best_selling','price','recommended','relevance','title','date','modified');
		return in_array($param, $orders) ? $param : 'id';
	}
	public function telSanitizeCallback($param){
		$param = str_replace(' ', '', $param);
		$param = str_replace('-', '', $param);
		return $param;
	}
	public function trimSanitizeCallback($param){
		return trim($param);
	}
	public function actionSanitizeCallback($param){
		return (in_array($param, array('c','r','u','d')))? $param : 'r' ;
	}
	public function cartVariationSanitizeCallback($params){
		$result = array_map(function($v) {
			return array(
				'product_id' 	=> (int) $v['product_id'],
				'varient_id' 	=> (int) $v['varient_id'],
			);
		}, $params);
		return $result;
	}

	/**
	 * Validate Callbacks
	 *
	 * @return bool
	**/
	public function numericPositiveValidateCallback($param){
		return (is_numeric($param) && ($param>0));
	}
	public function numericPositiveArrayValidateCallback($param){
		return (is_array($param));
	}
	public function numericCSVValidateCallback($params){
		$param = explode(',', $params);
		foreach($param as $a) {
			if (!is_numeric($a)) return false;
		}
		return true;
	}
	public function ratingValidateCallback($param){
		return (is_numeric($param) && ($param>0) && ($param<=5));
	}
	public function cartVariationValidateCallback($params){
		foreach($params as $param){
			if(!is_numeric($param['product_id']) || !is_numeric($param['varient_id'])) return false;
		}
		return true;
	}
	public function lineItemsArrayValidateCallback($param){
		return (is_array($param));
	}
	public function filterArrayValidateCallback($param){
		return (is_array($param));
	}
	public function textRequiredValidateCallback($param){
		return (isset($param) && !empty($param));
	}
	public function telValidateCallback($param){
		$valid = true;
		if(empty($param)) return $valid;
		$param = $this->telSanitizeCallback($param);

		$mobileDigitsLength = strlen($param);
		if ($mobileDigitsLength < 10 || $mobileDigitsLength > 15) {
			$valid = false;
		} else {
			if (!preg_match("/^[+]?[1-9][0-9]{9,14}$/", $param)) {
				$valid = false;
			}
		}
		return $valid;
	}
	public function addressIDValidateCallback($param){
		$address = array('billing','shipping');
		$param = strtolower($param);
		return in_array($param, $address);
	}
	public function ratingMaxFiveValidateCallback($param){
		return (is_numeric($param) && ($param>=0 && $param<=5));
	}
	public function orderValidateCallback($param){
		$param = strtolower($param);
		return in_array($param, ['0','1','asc','desc']);
	}
	public function actionValidateCallback($param){
		return in_array($param, ['c','r','u','d']);
	}
	public function urlValidateCallback($param){
		return sanitize_url($param) ? true : false;
	}
	public function orderByValidateCallback($param){
		$orders = array('id','rand','comment_count','best_selling','price','recommended','relevance','name','title','popularity','date','created','modified');
		$param = strtolower($param);
		return in_array($param, $orders);
	}
}

/**
 * Initialize Rest API Route  
 *
 * @author Manthan Kanani	
 * @since 1.0.0
**/
add_action('rest_api_init', function(){
	$controller = new REST_API_v1_Controller();
	$controller->register_routes();
});