<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/**
 * Swipecart Multilingual for WPML
 *
 * @package Swipecart
 * @author Manthan Kanani
 * @since 2.6.1
**/
class SC_Translator{

	/**
	 * constructor for the WPML Integrations  
	 * 
	 * @param $language should ulanguage_code like 'de','en','es'
	 * 
	 * @author Manthan Kanani	
	 * @since 2.6.1
	**/
	function __construct($language=null){
		$this->language = $language;
	}


	/**
	 * check available languages for WPML
	 *
	 * @author Manthan Kanani	
	 * @since 2.6.1
	**/
	public function get_available_lang() {
		$active_mapped_language 	= array();

		$wpml_active_languages 		= apply_filters('wpml_active_languages', NULL, 'orderby=id&order=desc');
		$wpml_all_languages 		= SitePress_EditLanguages::get_active_languages();

		$mapped_filters = array_filter($wpml_all_languages, function ($a) {
			$either_is_inactive 	= $a["active"] ?? false;
			$if_isnot_mapped 		= $a["can_be_translated_automatically"] ?? false;
			return $either_is_inactive && $if_isnot_mapped;
		});

		foreach($mapped_filters as $code => $meta){
			$active_mapped_language[] = array(
				"id"				=> $meta["id"],
				"code" 				=> $meta["code"],
				"default_locale" 	=> $meta["default_locale"],
				"english_name" 		=> $meta["english_name"],
				"native_name" 		=> $meta["native_name"],
				"display_name" 		=> $meta["display_name"],
				"mapping"			=> array(
					"sourceCode" 	=> $meta["mapping"]->sourceCode,
					"sourceName" 	=> $meta["mapping"]->sourceName,
					"targetId" 		=> $meta["mapping"]->targetId,
					"targetCode" 	=> $meta["mapping"]->targetCode,
				),
				"built_in" 			=> $meta["built_in"],
				"url"				=> $wpml_active_languages[$code]["url"],
				"flag_url"			=> $meta["flag_url"],
			);
		}

		return $active_mapped_language;
	}

	/**
	 * get default WPML Language
	 *
	 * @author Manthan Kanani	
	 * @since 2.6.2
	**/
	public function get_default_lang() {
		return apply_filters('wpml_default_language', NULL);
	}

	/**
	 * check available languages for WPML
	 *
	 * @author Manthan Kanani	
	 * @since 2.6.1
	**/
	public function get_unmapped_lang() {
		$unmapped_languages 	= array();

		$wpml_active_languages 		= apply_filters('wpml_active_languages', NULL, 'orderby=id&order=desc');
		$active_languages 			= SitePress_EditLanguages::get_active_languages();
		
		$unmapped_filters = array_filter($active_languages, function ($value) {
			$if_isnot_mapped 		= $value["can_be_translated_automatically"] ?? false;
			return !$if_isnot_mapped;
		});

		foreach($unmapped_filters as $code => $meta){
			$unmapped_languages[] = array(
				"id"				=> $meta["id"],
				"code" 				=> $meta["code"],
				"default_locale" 	=> $meta["default_locale"],
				"english_name" 		=> $meta["english_name"],
				"native_name" 		=> $meta["native_name"],
				"display_name" 		=> $meta["display_name"],
				"mapping"			=> $meta["mapping"],
				"built_in" 			=> $meta["built_in"],
				"url"				=> $wpml_active_languages[$code]["url"],
				"flag_url"			=> $meta["flag_url"],
			);
		}
		return $unmapped_languages;
	}


	/**
	 * check if the params is valid
	 * 
	 * @param $request contains global $request variable
	 *
	 * @author Manthan Kanani
	 * @since 2.6.2
	**/
	public function is_valid_params($request="") {
		return $this->get_requested_lang() ? true : false;
	}


	/**
	 * get rest language
	 * 
	 * @param $request contains global $request variable
	 *
	 * @author Manthan Kanani
	 * @since 2.6.2
	**/
	public function get_rest_lang($request="") {
		$available_params 	= $this->required_rest_params();
		
		foreach($available_params as $key=>$value){
			if($key == "get"){
				if(array_key_exists($value["key"], $_GET) && in_array($_GET[$value["key"]], $value["enum"])){
					return $_GET[$value["key"]];
				}
			} else if($key == "json") {
				$reqjson 	= file_get_contents("php://input");
				$data 		= json_decode($reqjson, true);
				if(json_last_error() === 0 && array_key_exists($value["key"], $data) && in_array($data[$value["key"]], $value["enum"])) {
					return $data[$value["key"]];
				}
			}
		}
		return false;
	}


	/**
	 * get params language value
	 * 
	 * @param $request contains global $request variable
	 *
	 * @author Manthan Kanani
	 * @since 2.6.2
	**/
	public function get_requested_lang($request="") {
		return self::get_rest_lang()?: self::get_default_lang();
	}


	/**
	 * check available languages for WPML
	 *
	 * @author Manthan Kanani	
	 * @since 2.6.1
	 * @version 2.6.2
	**/
	public function required_rest_params() {
		$active_mapped_language 		= $this->get_available_lang();

		$active_mapped_language_code 	= array_map(function($val){
			return $val["code"];
		}, $active_mapped_language);

		$rest_params  = array(
			"get" => array(
				"key" => "lang",
				"value" => "code",
				"enum" => $active_mapped_language_code
			),
			"json" => array(
				"key" => WPML_REST_Extend_Args::REST_LANGUAGE_ARGUMENT,
				"value" => "code",
				"enum" => $active_mapped_language_code
			),
		);
		
		return $rest_params;
	}


	/**
	 * get translated id automatically, language params will be taken from request_params
	 * 
	 * @param $element = post, page, {custom post type}, nav_menu, nav_menu_item, category, post_tag, {custom taxonomy}
	 * @param $id = id
	 * 
	 * @author Manthan Kanani
	 * @since 2.6.2
	**/
	public function __tr_id($element, $id){
		if($this->language = $this->get_requested_lang()){
			return $this->__object_id($element, $id);
		}
		return $id;
	}


	/**
	 * get translated data and or key based on its id and elements
	 * 
	 * @param $element = post, page, {custom post type}, nav_menu, nav_menu_item, category, post_tag, {custom taxonomy}
	 * @param $post = post or taxonomy
	 * @param $id = taxonomy_id or post_id
	 * @param $key = whether to return specific key or meta like "meta:key"
	 * 
	 * @author Manthan Kanani
	 * @since 2.6.1
	**/
	public function __tr($element, $post, $id, $key=null){
		if($key)
			return $this->__tr_key($element, $post, $id, $key);
		return $this->__object_id($element, $id);
	}


	/**
	 * get dedicated Key from ID 
	 * 
	 * @param $element = post, page, {custom post type}, nav_menu, nav_menu_item, category, post_tag, {custom taxonomy}
	 * @param $post = post or taxonomy
	 * @param $id = taxonomy_id or post_id
	 * @param $key = whether to return specific key or meta like "meta:key"
	 *
	 * @author Manthan Kanani
	 * @since 2.6.1
	**/
	private function __tr_key($element, $tax_post, $id, $key){
		$exploder 	= ":";
		$key_parts 	= explode($exploder, $key);

		if($key_parts[0]=="meta"){
			$translate_id 	= $this->__object_id($element, $id);
			$metakey 		= str_replace($key_parts[0].$exploder, "", $key);
			if($tax_post=="post"){
				return get_post_meta($translate_id, $metakey, true);
			} elseif($tax_post=="taxonomy") {
				return get_term_meta($translate_id, $metakey, true);
			}
		} else {
			$translate_id 	= $this->__object_id($element, $id);
			if($tax_post=="post"){
				$post 		= get_post($translate_id, ARRAY_A);
				return $post[$key] ?? false;
			} elseif($tax_post=="taxonomy") {
				$term 		= get_term($translate_id, $element, ARRAY_A);
				return $post[$key] ?? false;
			}
		}
	}


	/**
	 * get translated id by element
	 * 
	 * @author Manthan Kanani
	 * @since 2.6.1
	**/
	private function __object_id($element, $id){
		$return_original_if_missing = true;
		return apply_filters('wpml_object_id', $id, $element, $return_original_if_missing, $this->language);
	}
}