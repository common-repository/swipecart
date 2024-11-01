<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );


require_once(dirname(__FILE__) .'/multilingual.php');


/**
 * Swipecart Multilingual Initialization
 *
 * @package Swipecart
 * @author Manthan Kanani
 * @since 2.6.1
**/
class __SCML {

	/**
	 * translate Product Data  
	 *
	 * @param $element = post, page, {custom post type}, nav_menu, nav_menu_item, category, post_tag, {custom taxonomy}
	 * @param $id = id of specific element
	 * 
	 * @return translated_id or same_id in case of failure
	 * 
	 * @author Manthan Kanani	
	 * @since 2.6.2
	**/
	static public function translate_id($element, $id) {
		if(!self::validate_multilingual()) return $id;

		$wpml = new SC_Translator();
		return $wpml->__tr_id($element, $id);
	}


	/**
	 * translate Product Data  
	 *
	 * @param $id product_id
	 * @param $key whether to return specific key for the given post
	 * @param $language ulanguage_code like 'de','en','es' 
	 * 
	 * @author Manthan Kanani	
	 * @since 2.6.1
	**/
	static public function translate_product($id, $key, $language=null) {
		return self::translate_cpt('product', $id, $key, $language);
	}

	/**
	 * translate Data  
	 * 
	 * @param $id product_id
	 * @param $key whether to return specific key for the given post
	 * @param $language ulanguage_code like 'de','en','es' 
	 *
	 * @author Manthan Kanani	
	 * @since 2.6.1
	**/
	static public function translate_product_cat($id, $key, $language=null) {
		return self::translate_ctax('product_cat', $id, $key, $language);
	}

	/**
	 * translate CPT Data  
	 *
	 * @param $cpt from post, page, {custom post type}
	 * @param $id product_id
	 * @param $key whether to return specific key for the given post
	 * @param $language ulanguage_code like 'de','en','es' 
	 * 
	 * @author Manthan Kanani	
	 * @since 2.6.1
	**/
	static public function translate_cpt($cpt, $id, $key, $language=null) {
		if(!$language && !self::validate_multilingual()) return false;

		$wpml = new SC_Translator($language);
		return $wpml->__tr('post', $id, $key);
	}

	/**
	 * translate custom Taxonomy
	 * 
	 * @param $cpt from category, post_tag, {custom taxonomy}
	 * @param $id product_id
	 * @param $key whether to return specific key for the given post
	 * @param $language ulanguage_code like 'de','en','es' 
	 * 
	 * @author Manthan Kanani	
	 * @since 2.6.1
	**/
	static public function translate_ctax($ctax, $id, $key, $language=null) {
		if(!$language && !self::validate_multilingual()) return false;

		$wpml = new SC_Translator($language);
		return $wpml->__tr('taxonomy', $id, $key);
	}


	/**
	 * get available languages  
	 *  
	 * @author Manthan Kanani	
	 * @since 2.6.1
	**/
	static public function get_available_lang() {
		if(!self::validate_multilingual()) return false;

		$wpml = new SC_Translator();
		return $wpml->get_available_lang();
	}


	/**
	 * get unavailable languages  
	 *  
	 * @author Manthan Kanani	
	 * @since 2.6.1
	**/
	static public function get_unavailable_lang() {
		if(!self::validate_multilingual()) return false;

		$wpml = new SC_Translator();
		return $wpml->get_unmapped_lang();
	}


	/**
	 * get required rest params  
	 *  
	 * @author Manthan Kanani	
	 * @since 2.6.1
	**/
	static public function required_rest_params() {
		if(!self::validate_multilingual()) return false;

		$wpml = new SC_Translator();
		return $wpml->required_rest_params();
	}

	/**
	 * is valid rest params  
	 * 
	 * @param $request params contains $_REQUEST global variable 
	 * 
	 * @author Manthan Kanani	
	 * @since 2.6.2
	**/
	static public function is_valid_rest_params($request="") {
		if(!self::validate_multilingual()) return false;
		
		$wpml = new SC_Translator();
		return $wpml->is_valid_params($request)?: $wpml->get_default_lang();
	}


	/**
	 * check multilingual ability  
	 *  
	 * @author Manthan Kanani	
	 * @since 2.6.1
	**/
	static public function validate_multilingual() {
		$multilingual = new SC_Multilingual();
		return $multilingual->check_multilingual();
	}
}