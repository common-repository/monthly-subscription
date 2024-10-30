<?php
/**
 * Plugin Name: Monthly Subscription
 * Plugin URI: https://cookers.at/development/monthly-subscription/
 * Description: Subscription plugin for a custom WooCommerce product with verification-shortcode for protected content.
 * Version: 0.9.3
 * Author: Gerhard Kocher
 * Author URI: https://cookers.at
 **/

namespace at\cookers\wp;


if (version_compare(PHP_VERSION, "7.0", '<')) {
    throw new Exception("Minimum PHP version 7.0 required! Found: " . PHP_VERSION);
}


class Monthly_Subscription {
    function __construct() {
        add_action( 'init', array($this, 'init_product_class') );

        add_filter( 'product_type_selector', array($this, 'add_product_type_selector') );

        add_filter( 'woocommerce_product_data_tabs', array($this, 'edit_product_data_tabs') );
        add_action( 'woocommerce_product_options_general_product_data', array($this, 'add_general_options') );
        add_action( 'admin_footer', array($this, 'display_pricing_options') );

        add_action( 'woocommerce_process_product_meta_monthly_sub', array($this, 'save_option_field') );

        add_action( "woocommerce_monthly_sub_add_to_cart", function() {
            do_action( 'woocommerce_simple_add_to_cart' );
        });


        add_action( 'woocommerce_order_status_changed', array($this, 'sub_payment_complete') , 10, 3);


        require_once __DIR__ . "/classes/Subscription_Checker_Shortcode.php";
        new Subscription_Checker_Shortcode();
    }


    /**
     * Initialize the custom product type class.
     */
    function init_product_class() {
        require_once __DIR__ . "/classes/WC_Product_Monthly_Sub.php";
    }


    /**
     * Add the product type class to the selector dropdown.
     *
     * @param $types
     * @return mixed: the new types array.
     */
    function add_product_type_selector($types) {
        $types[ 'monthly_sub' ] = __( 'Monthly Subscription' );
        return $types;
    }


    /**
     * Edit the visibility of product data tabs.
     *
     * @param $tabs
     * @return mixed: the new tabs array.
     */
    function edit_product_data_tabs( $tabs ){
//        $tabs['attribute']['class'][] = 'hide_if_monthly_sub';
        $tabs['shipping']['class'][] = 'hide_if_monthly_sub';
        $tabs['inventory']['class'][] = 'hide_if_monthly_sub';
        return $tabs;
    }


    /**
     * Add the custom options to the general tab.
     */
    function add_general_options() {
        global $post;

        echo '<div class="option_group show_if_monthly_sub">';

        woocommerce_wp_text_input( array(
                                       'id' => '_subscription_duration',
                                       'class'    => array('show_if_monthly_sub'),
                                       'label' => __( 'Subscription duration:', 'woocommerce' ),
                                       'type' => 'number',
                                       'value' => get_post_meta( get_the_ID(), '_subscription_duration', true ),
                                       'wrapper_class' => 'form-field-wide',
                                       'desc_tip' => true,
                                       'description' => __( 'Duration of the subscription (in months) calculated from today, or added to active subscription if applicable.', 'woocommerce' ),
                                   ) );

        woocommerce_wp_select( array(
                                   'id'      => '_subscription_unit',
                                   'class'    => array('show_if_monthly_sub'),
                                   'label'   => __( 'Subscription duration unit:', 'woocommerce' ),
                                   'options' => [
                                           'months' => 'Months',
                                           'weeks' => 'Weeks',
                                           'days' => 'Days',
                                   ], //this is where I am having trouble
                                   'value'   => get_post_meta( get_the_ID(), '_subscription_unit', true ),
                               ) );

        echo '</div>';
    }


    /**
     * Display the pricing using jQuery.
     */
    function display_pricing_options() {
        if ( 'product' != get_post_type() ) :
            return;
        endif;
        ?><script type='text/javascript'>
            jQuery( '.options_group.pricing' ).addClass( 'show_if_monthly_sub' );
        </script><?php
    }


    /**
     * Save the custom options to the post meta.
     *
     * @param $post_id: the product.
     */
    function save_option_field( $post_id ) {
        if ( isset( $_POST['_subscription_duration'] ) ) :
            update_post_meta( $post_id, '_subscription_duration', sanitize_text_field( $_POST['_subscription_duration'] ) );
        endif;
    }


    /**
     * Payment complete handler: add the subscription to usermeta.
     * @param $order_id
     * @throws Exception
     */
    function sub_payment_complete($order_id, $old_status = "", $new_status = "", $order_object = "" ){
        if( $new_status == "completed" ) {
            $order_object = wc_get_order($order_id);

            // calculate the subscription duration to add
            $subscription_duration = 0;
            foreach ($order_object->get_items() as $item) {
                $product_id = $item->get_product_id();
                $product_quantity = $item->get_quantity();
                $add_duration = get_post_meta($product_id, '_subscription_duration', true);
                if ($add_duration && $product_quantity > 0) {
                    $subscription_duration += $add_duration * $product_quantity;
                }
            }

            // if no subscription: return;
            if ($subscription_duration === 0 || !is_numeric($subscription_duration)) {
                return;
            }

            // get the user and add the subscription
            file_put_contents(__DIR__ . "/log.txt", "$order_object" . "\n\n", FILE_APPEND);
            $user = $order_object->get_customer_id();
            file_put_contents(__DIR__ . "/log.txt", "user $user" . "\n\n", FILE_APPEND);
            if ($user) {
                $subscription_end_date = get_user_meta($user, "subscription_end_date", true);

                $date = \DateTime::createFromFormat("d.m.Y", $subscription_end_date);
                if ($date === false || array_sum($date::getLastErrors())) {
                    $date = new \DateTime('today');
                }
                $date->modify("+" . $subscription_duration . " months");

                update_user_meta($user, "subscription_end_date", $date->format("d.m.Y"));
            }
        }
    }
}



$foo = new Monthly_Subscription();
