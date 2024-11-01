<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/**
 * Swipecart Initiate Notices
 *
 * @package Swipecart
 * @author Manthan Kanani
 * @since 2.4.2
**/
class Swipecart_Notices{

	/**
	 * constructor for the integration  
	 *
	 * @author Manthan Kanani	
	 * @since 2.4.2
	**/
	function __construct(){

	}

	/**
	 * Apply Webhook
	 *
	 * * notice-error – will display the message with a red left border.
	 * * notice-warning – will display the message with a yellow/orange left border.
	 * * notice-success – will display the message with a green left border.
	 * * notice-info – will display the message with a blue left border.
	 * 		optionally use is-dismissible to add a closing icon to your message via JavaScript. 
	 * 		 |-	Its behavior, however, applies only on the current screen. 
	 * 		 |-	It will not prevent a message from re-appearing once the page re-loads, or another page is loaded.
	 *
	 * @param $type (error|warning|success|info)
	 *
	 * @author Manthan Kanani	
	 * @since 2.4.2
	**/
	public static function __showNotice($type="warning", $message="", $is_dismissible=false){
		$type 	= strtolower($type);
		
		add_action( 'admin_notices', function() use($type, $message, $is_dismissible) {
			$class 			= 'notice notice-'.$type.' '.($is_dismissible?'is-dismissible':'');
			$message 		= __( $message, 'swipecart' );

			?>
				<div class="<?php echo $class;?>">
					<?php echo $message; ?>
				</div>
			<?php
		});
	}
}