<?php
/*
Plugin Name: WooCommerce Dynamic Coupons
Plugin URI: https://www.webtronic.ie
Description: Allows coupons discounts to be dynamic based on order values
Version: 1.0
Author: Webtronic
Author URI: http://www.webtronic.ie
License: A "Slug" license name e.g. GPL2
*/
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define("DC_ASSETS_PATH", plugin_dir_url(__FILE__) . "assets/");
define('DC_VIEWS_DIR', plugin_dir_path(__FILE__) . "views/");
define('DC_BASE_DIR', plugin_dir_path(__FILE__));


include("includes/DynamicCoupons.php");