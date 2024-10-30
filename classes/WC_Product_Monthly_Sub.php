<?php


class WC_Product_Monthly_Sub extends \WC_Product {
    /**
     * __construct function.
     *
     * @access public
     * @param mixed $product
     */
    public function __construct( $product ) {
        $this->product_type = 'monthly_sub';
        $this->supports[]   = 'ajax_add_to_cart';
        parent::__construct( $product );
    }

    /**
     * Get internal type.
     * Needed for WooCommerce 3.0 Compatibility
     * @return string
     */
    public function get_type() {
        return $this->product_type;
    }

    /**
     * Get the add to cart button url.
     *
     * @return string
     */
    public function add_to_cart_url() {
        $url = $this->is_purchasable() && $this->is_in_stock() ? remove_query_arg(
            'added-to-cart',
            add_query_arg(
                array(
                    'add-to-cart' => $this->get_id(),
                ),
                ( function_exists( 'is_feed' ) && is_feed() ) || ( function_exists( 'is_404' ) && is_404() ) ? $this->get_permalink() : ''
            )
        ) : $this->get_permalink();
        return apply_filters( 'woocommerce_product_add_to_cart_url', $url, $this );
    }

    /**
     * Get the add to cart button text.
     *
     * @return string
     */
    public function add_to_cart_text() {
        $text = $this->is_purchasable() && $this->is_in_stock() ? __( 'Add to cart', 'woocommerce' ) : __( 'Read more', 'woocommerce' );

        return apply_filters( 'woocommerce_product_add_to_cart_text', $text, $this );
    }

    public function add_to_cart_description() {
        /* translators: %s: Product title */
        $text = $this->is_purchasable() && $this->is_in_stock() ? __( 'Add &ldquo;%s&rdquo; to your cart', 'woocommerce' ) : __( 'Read more about &ldquo;%s&rdquo;', 'woocommerce' );

        return apply_filters( 'woocommerce_product_add_to_cart_description', sprintf( $text, $this->get_name() ), $this );
    }
}