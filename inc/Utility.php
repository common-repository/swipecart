<?php

defined('ABSPATH') or die('No script kiddies please!');

/**
 * Swipecart Utility Functions
 *
 * @package Swipecart
 * @author Manthan Kanani
 * @since 1.0.0
**/
class Utility {

	/**
	 * Constructor for Encryption  
	 *
	 * @author Manthan Kanani	
	 * @since 2.2.1
	 * @version 2.4.1
	**/
	function __construct(){
		$this->hookURLs 	= array(); 
	}

	/**
	 * Encrypt Decrypt Function
	 *  
	 * @package Swipecart
	 * @author Manthan Kanani
	 * @since 1.0.0
	 * @version last updated in 2.3.0 
	**/
	public function encrypt_decrypt($string, $action='encrypt') {
		$auth_generation 	= new SC_AuthGeneration();
		$authCombo 			= $auth_generation->getAuth();
		$secret_key 		= $authCombo['StoreFrontToken'];
		$secret_iv			= $secret_key;

		$output = false;
		$encrypt_method = "AES-256-CBC";
		$key 	= hash('sha256', $secret_key);
		$iv 	= substr(hash('sha256', $secret_iv), 0, 16);
		if($action == 'encrypt') {
			$output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
			$output = base64_encode($output);
		} else {
			$output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
		}
		return $output;
	}


	/**
	 * URL Query String to Array
	 *  
	 * @package Swipecart
	 * @author Manthan Kanani
	 * @since 1.0.0
	**/
	public function url2Array($query_string){
		$state = urldecode($query_string);
		$states = explode('&', $state);
		foreach($states as $val){
			$get = explode('=', $val);
			$key = $get[0];
			$value = $get[1];
			if(!empty($key)){
				$param[$key] = $value;
			}
		}
		return $param;
	}

	/**
	 * Create `username` from 'email/name'
	 * 
	 * @package Swipecart
	 * @author Manthan Kanani
	 * @since 1.0.0
	**/
	public function create_username($from, $field) {
		$field = strtolower($field);
		if($from=='email'){
			$parts = explode('@', $field);
			$username = (count($parts) >= 1) ? $parts[0] : $parts ;
		} elseif($from=='name'){
			$parts = str_replace(' ', '_', $field);
			$username = preg_replace('/[^a-zA-Z0-9_.]/', '', $parts);	
		}

		if(username_exists($username)){
			$uname = $username;
			for($i=1;$i<99999;$i++){
				if(username_exists($uname)){
					$uname = $username.'_'.$i;
				} else {
					break;
				}
			}
		} else {
			$uname = $username;
		}
		if($uname){
			return $uname;
		}
		return false;
	}

	/**
	 * Create firstname and lastname from fullname 
	 * 
	 * @package Swipecart
	 * @author Manthan Kanani
	 * @since 1.0.0
	**/
	public function create_splitname($name) {
		$name 		= trim($name);
		$last_name 	= (strpos($name, ' ') === false) ? '' : preg_replace('#.*\s([\w-]*)$#', '$1', $name);
		$first_name = trim(preg_replace('#'.$last_name.'#', '', $name));
		return array($first_name, $last_name);
	}

	/**
	 * get hostname by Hostname
	 *
	 * @package Swipecart
	 * @author Manthan Kanani
	 * @since 2.1.0
	**/
	public function getHostnameByURL($hostname=""){
		$hostname 	= $hostname?:site_url();
		$hostURL	= parse_url($hostname);
		$hostname 	= gethostbyname($hostURL['host']);
		return $hostname;
	}

	/**
	 * Get Distance Between 2 LatLong
	 *
	 * @package Swipecart
	 * @author Manthan Kanani
	 * @since 1.0.0
	**/
	public function distanceBetweenLocation($lat1, $lon1, $lat2, $lon2, $unit) {
		$lat1 = (is_numeric($lat1)) ? $lat1 : 0.0 ;
		$lon1 = (is_numeric($lon1)) ? $lon1 : 0.0 ;
		$lat2 = (is_numeric($lat2)) ? $lat2 : 0.0 ;
		$lon2 = (is_numeric($lon2)) ? $lon2 : 0.0 ;
		if (($lat1 == $lat2) && ($lon1 == $lon2)) {
			return 0;
		} else {
			$theta = $lon1 - $lon2;
			$dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
			$dist = acos($dist);
			$dist = rad2deg($dist);
			$miles = $dist * 60 * 1.1515;
			$unit = strtoupper($unit);

			if ($unit == "K") {
				$distance = number_format(($miles * 1.609344), 2, '.', '');
			} elseif ($unit == "N") {
				$distance = number_format(($miles * 0.8684), 2, '.', '');
			} else {
				$distance = number_format($miles, 2, '.', '');
			}
			return $distance;
		}
	}

	/**
	 * Generate Random String
	 *
	 * @package Swipecart
	 * @author Manthan Kanani
	 * @since 1.0.0
	**/
	public function generateRandStr($length){
		$token = "";
		$codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
		$max = strlen($codeAlphabet);

		for($i=0; $i < $length; $i++) {
			$token .= $codeAlphabet[random_int(0, $max-1)];
		}
		return $token;
	}

	/**
	 * debug log
	 *
	 * @author Manthan Kanani	
	 * @since 2.0.1
	**/
	public function __logFile($text){
		global $swipecart;

		$logPath = $swipecart['dir_path'] . '/inc/debug.log';
		$fp = fopen($logPath, (file_exists($logPath)) ? 'a' : 'w');
		
		date_default_timezone_set("Asia/kolkata");    
		fwrite($fp, $text.' - '.date('Y-m-d H:i:s').PHP_EOL);
		fclose($fp);
	}

	/**
	 * Apply Webhook
	 *
	 * @author Manthan Kanani	
	 * @since 2.1.0
	**/
	public function __logWebHook($data){
		foreach($this->hookURLs as $url){
			wp_remote_post($url, $data);
		}
	}
}