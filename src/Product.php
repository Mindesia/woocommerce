<?php

namespace Mindesia\Woocommerce;

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

    public function id()
    {
        return absint($this->product->get_id());
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

    /**
     * Find related product
     *
     * @param integer $limit
     * @return array
     */
    public function find_related_products(int $limit = 5): array
    {
        $related_ids = wc_get_related_products($this->id(), $limit);
        $related_posts =  Timber::get_posts($related_ids);

        $related_products =  [];

        foreach ($related_posts as $related_post) {
            array_push($related_products, new Product($related_post));
        }

        return $related_products;
    }
}
