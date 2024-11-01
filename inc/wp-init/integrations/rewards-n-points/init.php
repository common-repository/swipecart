<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/**
 * Swipecart Reward Initialization
 *
 * @package Swipecart
 * @author Manthan Kanani
 * @since 2.8.0
**/
class __SC_Rewards_Points_Init {

	/**
	 * constructor for the reward points
	 * 
	 * @since 2.8.0
	**/
	function __construct(){
		$this->__include_files();
		$this->__init();
	}


	/**
	 * inclusion fo the file
	 * 
	 * @since 2.8.0
	**/
	private function __include_files(){
		require_once(dirname(__FILE__) .'/rewardpoints.php');
	}


	/**
	 * Initialization fo the file
	 * 
	 * @since 2.8.0
	**/
	private function __init(){
		$this->validate_rewardplugin();
	}


	/**
	 * check reward plugin's availability  
	 *  
	 * @author Manthan Kanani	
	 * @since 2.8.0
	**/
	private function validate_rewardplugin() {
		$reward_points = new SC_RewardPoints();
		return $reward_points->check_reward_plugin();
	}
}
new __SC_Rewards_Points_Init();



