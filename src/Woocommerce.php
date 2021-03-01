<?php

namespace Mindesia\Woocommerce;

class Woocommerce
{
    public function __construct()
    {
        if (class_exists('WooCommerce')) {
            add_filter('woocommerce_template_path', [$this, 'woocommerce_template']);
            add_action('after_setup_theme', [$this, 'add_woocommerce_support']);
            add_filter('woocommerce_sale_flash', '__return_false');

            // number of product
            remove_action('woocommerce_before_shop_loop', 'woocommerce_result_count', 20);
            // tag to order product
            remove_action('woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30);

            add_theme_support('wc-product-gallery-zoom');
            add_theme_support('wc-product-gallery-lightbox');
            add_theme_support('wc-product-gallery-slider');

            // Disable woocommerce styles
            add_filter('woocommerce_enqueue_styles', '__return_empty_array');

            remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);
            remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_price');

            remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20);

            add_filter('woocommerce_dropdown_variation_attribute_options_args', [$this, 'remove_first_option_dropdown_attributes'], 10);
            remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20);
        }
    }

    /**
     * Changes the Woocommerce template path in your theme
     * Replace 'components/woocommerce/' with your desired directory
     */
    public function woocommerce_template()
    {
        return 'Controller/woocommerce/';
    }

    public function add_woocommerce_support()
    {
        add_theme_support('woocommerce');
    }


    function remove_first_option_dropdown_attributes($args)
    {
        $args['show_option_none'] = '';
        return $args;
    }

    function translate_reply($translated)
    {
        $translated = str_ireplace('Quantity', 'New Label', $translated);
        return $translated;
    }
}
