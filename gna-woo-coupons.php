<?php
/*
Plugin Name: GNA WooCommerce Coupons
Version: 0.9.2
Plugin URI: http://wordpress.org/plugins/gna-woo-coupons/
Author: Chris Dev
Author URI: http://webgna.com/
Description: Additional functionality for WooCommerce Coupons: Allow discounts with specific day, weekdays only, weekends only or day of week, etc.
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Text Domain: gna-woo-coupons
*/

if(!defined('ABSPATH'))exit; //Exit if accessed directly

include_once('gna-woo-coupons-core.php');

register_activation_hook(__FILE__, array('GNA_WooCoupons', 'activate_handler'));		//activation hook
register_deactivation_hook(__FILE__, array('GNA_WooCoupons', 'deactivate_handler'));	//deactivation hook
