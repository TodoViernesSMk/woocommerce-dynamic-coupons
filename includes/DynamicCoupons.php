<?php
/**
 * Project: woocommerce-dynamic-coupons
 * User: Brendan Doyle
 * Date: 14/03/2019
 * Time: 12:38
 */
if (!defined('ABSPATH')) {
    exit;
}

class DynamicCoupons
{


    public function __construct()
    {
        add_action('init', array($this, 'discount_init'), 0);
        add_filter('woocommerce_coupon_discount_types', array($this, 'custom_discount_type'), 10, 1);
        add_filter('woocommerce_coupon_is_valid_for_product', array($this, 'woocommerce_coupon_is_valid_for_product'), 10, 4);
        add_filter('woocommerce_coupon_get_discount_amount', array($this, 'woocommerce_coupon_get_discount_amount'), 20, 5);
        add_action("admin_enqueue_scripts", array($this, 'register_admin_assets'));
        add_action("add_meta_boxes", array($this, "discount_meta_box_add"));
        add_action("save_post", array($this, "save_discount_meta_box"), 10, 3);
        add_action('woocommerce_coupon_options', array($this, 'add_coupon_discount_dropdown'), 10, 0);
        add_action('woocommerce_coupon_options_save', array($this, 'save_coupon_discount_dropdown'));
    }


    function add_coupon_discount_dropdown()
    {


        $discounts = $this->get_discounts();

        woocommerce_wp_select(array('id' => 'dynamic_discount', 'label' => __('Select Discount', 'woocommerce'), 'desc_tip' => 'Chose which discount bands you want to apply to this coupon', 'options' => $discounts));
    }


    function save_coupon_discount_dropdown($post_id)
    {
        $dynamic_discount = isset($_POST['dynamic_discount']) ? $_POST['dynamic_discount'] : '';
        update_post_meta($post_id, 'dynamic_discount', $dynamic_discount);
    }


    public function register_admin_assets()
    {
        wp_enqueue_script('dc-admin', DC_ASSETS_PATH . 'js/admin.js', array('jquery'), '1.0.0', true);
        wp_enqueue_style('dc-admin', DC_ASSETS_PATH . 'css/admin.css', array(), '1.0.0');

    }

    public function get_discounts()
    {


        $args = array(

            'post_type' => 'dynamic_discounts',
            'numberposts' => -1,
            'post_status' => 'publish'

        );

        $posts = get_posts($args);


        $discounts = array();


        $discounts[] = "";

        foreach ($posts as $post) {


            $discounts[$post->ID] = $post->post_title;


        }


        return $discounts;


    }

    public function custom_discount_type($discount_types)
    {


        $discount_types["value_based_discount"] = __("Value Based Discount", 'dynamic-coupons');

        return $discount_types;
    }


    public function discount_init()
    {

        $labels = array(
            'name' => _x('Discounts', 'Post Type General Name', 'dynamic-coupons'),
            'singular_name' => _x('Discount', 'Post Type Singular Name', 'dynamic-coupons'),
            'menu_name' => __('Discounts', 'dynamic-coupons'),
            'name_admin_bar' => __('Discount', 'dynamic-coupons'),
            'archives' => __('Item Archives', 'dynamic-coupons'),
            'attributes' => __('Item Attributes', 'dynamic-coupons'),
            'parent_item_colon' => __('Parent Item:', 'dynamic-coupons'),
            'all_items' => __('Discounts', 'dynamic-coupons'),
            'add_new_item' => __('Add New Item', 'dynamic-coupons'),
            'add_new' => __('Add New', 'dynamic-coupons'),
            'new_item' => __('New Item', 'dynamic-coupons'),
            'edit_item' => __('Edit Item', 'dynamic-coupons'),
            'update_item' => __('Update Item', 'dynamic-coupons'),
            'view_item' => __('View Item', 'dynamic-coupons'),
            'view_items' => __('View Items', 'dynamic-coupons'),
            'search_items' => __('Search Item', 'dynamic-coupons'),
            'not_found' => __('Not found', 'dynamic-coupons'),
            'not_found_in_trash' => __('Not found in Trash', 'dynamic-coupons'),
            'featured_image' => __('Featured Image', 'dynamic-coupons'),
            'set_featured_image' => __('Set featured image', 'dynamic-coupons'),
            'remove_featured_image' => __('Remove featured image', 'dynamic-coupons'),
            'use_featured_image' => __('Use as featured image', 'dynamic-coupons'),
            'insert_into_item' => __('Insert into item', 'dynamic-coupons'),
            'uploaded_to_this_item' => __('Uploaded to this item', 'dynamic-coupons'),
            'items_list' => __('Items list', 'dynamic-coupons'),
            'items_list_navigation' => __('Items list navigation', 'dynamic-coupons'),
            'filter_items_list' => __('Filter items list', 'dynamic-coupons'),
        );
        $args = array(
            'label' => __('Discount', 'dynamic-coupons'),
            'description' => __('Manage dynamic discounts', 'dynamic-coupons'),
            'labels' => $labels,
            'supports' => array('title'),
            'taxonomies' => array(),
            'hierarchical' => false,
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => 'edit.php?post_type=product',
            'show_in_admin_bar' => true,
            'show_in_nav_menus' => false,
            'can_export' => true,
            'has_archive' => false,
            'exclude_from_search' => true,
            'publicly_queryable' => false,
            'capability_type' => 'post',
        );
        register_post_type('dynamic_discounts', $args);

    }


    function woocommerce_coupon_is_valid_for_product($valid, $product, $coupon, $values)
    {


        if (!$coupon->is_type('value_based_discount')) {
            return $valid;
        }

        $product_cats = wp_get_post_terms($product->get_id(), 'product_cat', array("fields" => "ids"));


        //var_dump( $coupon->product_ids );

        // Specific products get the discount
        if (sizeof($coupon->get_product_ids()) > 0) {
            if (in_array($product->get_id(), $coupon->get_product_ids())) {
                $valid = true;
            }
        }

        // Category discounts
        if (sizeof($coupon->get_product_categories()) > 0) {
            if (sizeof(array_intersect($product_cats, $coupon->get_product_categories())) > 0) {
                $valid = true;
            }
        }

        if (!sizeof($coupon->get_product_ids(0)) && !sizeof($coupon->get_product_categories())) {
            // No product ids - all items discounted
            $valid = true;
        }

        // Specific product ID's excluded from the discount
        if (sizeof($coupon->get_excluded_product_ids()) > 0) {
            if (in_array($product->get_id(), $coupon->get_excluded_product_ids())) {
                $valid = false;
            }
        }

        // Specific categories excluded from the discount
        if (sizeof($coupon->get_excluded_product_categories()) > 0) {
            if (sizeof(array_intersect($product_cats, $coupon->get_excluded_product_categories())) > 0) {
                $valid = false;
            }
        }

        // Sale Items excluded from discount
        if ($coupon->get_exclude_sale_items() == 'yes') {
            $product_ids_on_sale = wc_get_product_ids_on_sale();

            if (isset($product->variation_id)) {
                if (in_array($product->variation_id, $product_ids_on_sale, true)) {
                    $valid = false;
                }
            } elseif (in_array($product->get_id(), $product_ids_on_sale, true)) {
                $valid = false;
            }
        }

        return $valid;
    }

//function to get coupon amount for "custom_discount"
    function woocommerce_coupon_get_discount_amount($discount, $discounting_amount, $cart_item, $single, $coupon)
    {


        if ($coupon->is_type('value_based_discount')) {
            return $this->get_coupon_discount($cart_item, $coupon);
        } else {
            return $discount;
        }
    }


    function discount_meta_box_html()
    {

        global $post;

        wp_nonce_field(basename(__FILE__), "discount-rules-nonce");

        $discounts = get_post_meta($post->ID, 'dynamic_discounts', true);


        include(DC_VIEWS_DIR . "metabox.php");
    }

    function discount_meta_box_add()
    {
        add_meta_box("discount-rules", "Discount Rules", array($this, "discount_meta_box_html"), "dynamic_discounts", "advanced", "high", null);
    }


    function save_discount_meta_box($post_id, $post, $update)
    {
        if (!isset($_POST["discount-rules-nonce"]) || !wp_verify_nonce($_POST["discount-rules-nonce"], basename(__FILE__)))
            return $post_id;

        if (!current_user_can("edit_post", $post_id))
            return $post_id;

        if (defined("DOING_AUTOSAVE") && DOING_AUTOSAVE)
            return $post_id;


        $discounts = array();

        $count = count($_POST['discount_min']);

        for ($i = 0; $i < $count; $i++) {


            $discounts[] = array('min' => $_POST['discount_min'][$i], 'max' => $_POST['discount_max'][$i], 'amount' => $_POST['discount_amount'][$i]);

        }


        update_post_meta($post_id, 'dynamic_discounts', $discounts);


    }


    function get_coupon_discount($cart_item, $coupon)
    {


        $coupon_id = $coupon->get_id();
        $discount_id = get_post_meta($coupon_id, 'dynamic_discount', true);
        $discounts = get_post_meta($discount_id, 'dynamic_discounts', true);


        $line_total = $cart_item['line_total'];


        if (empty($discount_id)) {
            return 0;
        }

        if (empty($discounts)) {
            return 0;
        }


        foreach ($discounts as $discount) {


            if ($discount['min'] <= $line_total && $discount['max'] >= $line_total) {

                return $discount['amount'];
                break;

            }


        }


    }


}

$dynamic_coupons = new DynamicCoupons();