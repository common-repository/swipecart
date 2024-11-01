<?php

defined('ABSPATH') or die('No script kiddies please!');

/**
 * List URLs that will be used in whole app.
 *
 * @package Swipecart
 * @author Manthan Kanani
 * @since 2.4.2
**/
class SwipecartEvents {
	/** 
	 * Construct Development
	 *
	 * @author Manthan Kanani
	 * @since 2.4.2 
	**/
	function __construct(){
		$this->__initialization();
	}


	/** 
	 * Intialization of events
	 *
	 * @author Manthan Kanani
	 * @since 2.4.2 
	**/
	private function __initialization(){
		$this->__showSSLNotice();
	}


	/**
	 * show SSL notice to admin header   
	 *
	 * @author Manthan Kanani   
	 * @since 2.4.2
	**/
	private function __showSSLNotice(){
		$generalUtility 	= new GeneralUtility();
		if(!is_ssl()){
			Swipecart_Notices::__showNotice("error", __("<p><strong>Swipecart:</strong> We don't provide support for non-SSL(insecure) Websites.</p>", "swipecart"));
		}
	}
}