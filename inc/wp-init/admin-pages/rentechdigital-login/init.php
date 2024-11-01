<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/**
 * Swipecart Admin Pages
 *
 * @package Swipecart
 * @author Manthan Kanani
 * @since 2.1.0
**/
class Swipecart_RentechDigital_Login {

	/**
	 * Constructor for Include files  
	 *
	 * @author Manthan Kanani	
	 * @since 2.1.0
	 * @version 2.5.0
	**/
	function __construct(){
		$this->pageSlug 		= "swipecart";

		$this->__include_files();
		$this->__WC_init();
	}


	/** 
	 * Inclusion of files
	 *
	 * @author Manthan Kanani
	 * @since 2.5.0
	**/
	private function __include_files(){
		require_once('class/class-authorization.php');
	}

	/**
	 * Call Initialization and
	 *
	 * @author Manthan Kanani   
	 * @since 2.2.6
	**/
	private function __WC_init(){
		$generalUtility 	= new GeneralUtility();
		$WCPath 			= "woocommerce/woocommerce.php";

		if($generalUtility->isPluginActivated($WCPath) && $generalUtility->swipecartCan()){
			add_action('admin_menu', [ &$this, 'register_main_menu' ]);
			add_action('activated_plugin', [ $this, '__activatePlugin']);
			add_action('admin_enqueue_scripts', [ $this, 'submenu_page_styling']);
		}
	}


	/**
	 * Register Hooks for upgradation
	 *
	 * @author Manthan Kanani   
	 * @since 2.1.0
	 * @version 2.8.1
	**/
	public function __activatePlugin($plugin) {
		global $swipecart;

		if($plugin == $swipecart['basename']){
			Swipecart_User::__register();
			Swipecart_User::__auto_update();

			exit(wp_safe_redirect(admin_url('admin.php?page='.$this->pageSlug)));
		}
	}

	/**
	 * Register Submenu on wordpress dashboard
	 *
	 * @author Manthan Kanani	
	 * @since 2.1.2
	**/
	public function register_main_menu() {
		global $swipecart;
		
		$SVG 		= new SC_SVGIcons();
		$icon 		= $SVG->getEncodedSVGIcon('swipecart-o');
		$icon_url 	= $swipecart['dir_url'].'/assets/icons/rentech-o.svg';

		add_menu_page('Swipecart', 'Swipecart', 'manage_woocommerce', $this->pageSlug, [&$this, 'submenu_page_callback'], $icon, 56);
	}

	/**
	 * Submenu Callback
	 *
	 * @author Manthan Kanani	
	 * @since 2.1.0
	 * @version 2.5.1
	**/
	public function submenu_page_callback() {
		$api 		= new APIClass();
		$nonce 		= $api->nonceValidation();

		$siteURL 	= get_site_url();
		$siteURL 	= str_replace(["https://","http://"], "", $siteURL);
		$url 		= GeneralUtility::__getServerURL()."/ecommerce-app/?session=".$nonce."&shop=".$siteURL;

		?>
			<div class="wrap">
				<div class="swipecart">
					<h1 class="wp-heading-inline">Swipecart</h1>
					<div class="loader-circle">
						<div class="loader"></div>
						<div>Redirecting you to <a href="<?=$url?>" target="_blank">rentechdigital.com</a></div>
					</div>
					<script type="text/javascript">setTimeout(()=>{window.open('<?=$url?>','_self')},0)</script>
				</div>
			</div>
		<?php
	}

    /**
	 * Submenu Styling
	 *
	 * @author Manthan Kanani	
	 * @since 2.2.5
	**/
	public function submenu_page_styling($hook) {
		wp_register_style('sc-rentechdigital', plugin_dir_url( __FILE__ ).'assets/style.css', false, GeneralUtility::__styleVersion());
		
		if($hook=="toplevel_page_swipecart"){
			wp_enqueue_style('sc-rentechdigital');
		}
	}


}
new Swipecart_RentechDigital_Login();