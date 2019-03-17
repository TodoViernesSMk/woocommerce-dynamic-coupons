<?php
/*
Plugin Name: WooCommerce Dynamic Coupons
Plugin URI: https://www.webtronic.ie
Description: Allows coupons discounts to be dynamic based on order values
Version: 1.0
Author: Webtronic
Author URI: http://www.webtronic.ie
License: A "Slug" license name e.g. GPL2
Text Domain: dynamic-coupons
WC requires at least: 3.0
WC tested up to: 3.0
*/
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define("WDC_ASSETS_PATH", plugin_dir_url(__FILE__) . "assets/");
define('WDC_VIEWS_DIR', plugin_dir_path(__FILE__) . "views/");
define('WDC_BASE_DIR', plugin_dir_path(__FILE__));

/**
 * Check if WooCommerce is active
 **/
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {


    include("includes/DynamicCoupons.php");


}



