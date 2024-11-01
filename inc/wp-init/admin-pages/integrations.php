<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/**
 * Swipecart Admin Pages
 *
 * @package Swipecart
 * @author Manthan Kanani
 * @since 2.1.0
**/
class Swipecart_AdminPages {

	/**
	 * Constructor for Include files  
	 *
	 * @author Manthan Kanani	
	 * @since 2.1.0
	**/
	function __construct(){
		$this->__include_files();
	}

	/**
	 * Inclusion of files
	 *
	 * @author Manthan Kanani	
	 * @since 2.1.0
	 * @version 2.4.0
	**/
	private function __include_files(){
		$dirs = @scandir( dirname(__FILE__) );
		foreach($dirs as $dir){
			if(!is_dir(dirname(__FILE__).'/'.$dir) || '.' === $dir[0] || 'CVS' === $dir)
				continue;
			if(file_exists( dirname(__FILE__) .'/' . $dir . '/init.php'))
				require_once(dirname(__FILE__) .'/' . $dir . '/init.php');
		}
	}
}
new Swipecart_AdminPages();