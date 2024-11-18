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
    public $name;
    public $slug;
    public $date_created;
    public $date_modified;
    public $status;
    public $featured;
    public $catalog_visibility;
    public $description;
    public $short_description;
    public $sku;
    public $price;
    public $regular_price;
    public $sale_price;
    public $date_on_sale_from;
    public $date_on_sale_to;
    public $total_sales;
    public $tax_status;
    public $tax_class;
    public $manage_stock;
    public $stock_quantity;
    public $stock_status;
    public $backorders;
    public $low_stock_amount;
    public $sold_individually;
    public $weight;
    public $length;
    public $width;
    public $height;
    public $upsell_ids;
    public $cross_sell_ids;
    public $parent_id;
    public $reviews_allowed;
    public $purchase_note;
    public $attributes;
    public $default_attributes;
    public $menu_order;
    public $post_password;
    public $virtual;
    public $downloadable;
    public $category_ids;
    public $tag_ids;
    public $shipping_class_id;
    public $downloads;
    public $image_id;
    public $gallery_image_ids;
    public $download_limit;
    public $download_expiry;
    public $rating_counts;
    public $average_rating;
    public $review_count;

    public function __construct($post = null)
    {
        if ($post == null) {
            $post = Timber::get_post();
        }
        $this->product = wc_get_product($post);

        foreach ($this->product->get_data() as $key => $value) {
            $this->$key = $value;
        }
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
