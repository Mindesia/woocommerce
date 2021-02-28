<?php

namespace Mindesia\WP_Class;

use Timber\Timber;
use WC_Abstract_Legacy_Product;
use WC_Product;
use WC_Product_Factory;
use WC_Product_Variable;

class Product
{
    private $product;

    public function __construct($post = null)
    {
        if ($post == null) {
            $post = Timber::get_post();
        }
        $this->product = wc_get_product($post);
    }

    public function price_base()
    {
        return $this->product->get_regular_price();
    }

    public function price_promo()
    {
        return $this->product->get_sale_price();
    }

    public function type()
    {
        return $this->product->get_type();
    }

    public function link()
    {
        return $this->product->get_permalink();
    }

    public function thumbnail()
    {
        $image_id  = $this->product->get_image_id();
        return wp_get_attachment_image_url($image_id, 'full');
    }

    public function price_variation_range()
    {
        foreach ($this->product->get_available_variations() as $variation) {
            // Set for each variation ID the corresponding price in the data array (to be used in jQuery)
            $variations_data[$variation['variation_id']] = $variation['display_price'];
        }
    }

    public function price_variation_base_min()
    {
        if ($this->type() == "variable") {
            return $this->product->get_variation_regular_price('min');
        }
    }

    public function price_variation_base_max()
    {
        if ($this->type() == "variable") {
            return $this->product->get_variation_regular_price('max');
        }
    }

    public function price_variation_promo_min()
    {
        if ($this->type() == "variable") {
            return $this->product->get_variation_sale_price('min');
        }
    }

    public function price_variation_promo_max()
    {
        if ($this->type() == "variable") {
            return $this->product->get_variation_sale_price('max');
        }
    }

    public function title()
    {
        return $this->product->get_title();
    }

    public function display_price_in_variation_option_name($term)
    {
        global $wpdb, $product;

        if (empty($term)) return $term;
        if (empty($product->id)) return $term;

        $id = $product->get_id();

        $result = $wpdb->get_col("SELECT slug FROM {$wpdb->prefix}terms WHERE name = '$term'");

        $term_slug = (!empty($result)) ? $result[0] : $term;

        $query = "SELECT postmeta.post_id AS product_id
                        FROM {$wpdb->prefix}postmeta AS postmeta
                            LEFT JOIN {$wpdb->prefix}posts AS products ON ( products.ID = postmeta.post_id )
                        WHERE postmeta.meta_key LIKE 'attribute_%'
                            AND postmeta.meta_value = '$term_slug'
                            AND products.post_parent = $id";

        $variation_id = $wpdb->get_col($query);

        $parent = wp_get_post_parent_id($variation_id[0]);

        if ($parent > 0) {
            $_product = new WC_Product_Variation($variation_id[0]);
            return $term . ' (' . wp_kses(woocommerce_price($_product->get_price()), array()) . ')';
        }
        return $term;
    }
}
