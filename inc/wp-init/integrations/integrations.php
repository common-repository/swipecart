<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/**
 * Swipecart Third-Party Integration
 *
 * @package Swipecart
 * @author Manthan Kanani
 * @since 1.0.0
**/
class Swipecart_Integrations {

	/**
	 * Constructor for Include files  
	 *
	 * @author Manthan Kanani	
	 * @since 2.5.0
	**/
	function __construct(){
		$this->__include_files();
	}

	/**
	 * Inclusion of Plugins this will includes only plugin who has init.php exists 
	 *
	 * @author Manthan Kanani	
	 * @since 1.0.0
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
new Swipecart_Integrations();