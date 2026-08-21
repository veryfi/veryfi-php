<?php

declare(strict_types=1);

namespace veryfi\documents\lineitems;

/**
 * Model of Shared Line Item.
 */
class SharedLineItem
{
    /** @var string|array|null */
    public $sku = null;
    /**
     * Category value accepted by the API (string or list depending on the model configuration).
     * @var string|array|null
     */
    public $category = null;
    /** @var float|array|null */
    public $tax = null;
    /** @var float|array|null */
    public $price = null;
    /** @var string|array|null */
    public $unit_of_measure = null;
    /** @var float|array|null */
    public $quantity = null;
    /** @var string|array|null */
    public $upc = null;
    /** @var float|array|null */
    public $tax_rate = null;
    /** @var float|array|null */
    public $discount_rate = null;
    /** @var string|array|null */
    public $start_date = null;
    /** @var string|array|null */
    public $end_date = null;
    /** @var string|array|null */
    public $hsn = null;
    /** @var string|array|null */
    public $section = null;
    /** @var string|array|null */
    public $weight = null;
    public ?string $expanded_description = null;
    public ?string $brand = null;
    /**
     * Tags associated with the line item.
     * @var string[]|null
     */
    public ?array $tags = null;
    /** @var string|array|null */
    public $country_of_origin = null;
    /** @var string|array|null */
    public $date = null;
    /** @var float|array|null */
    public $discount_price = null;
    /** @var float|array|null */
    public $discount = null;
    /** @var string|array|null */
    public $lot = null;
    /** @var array|null */
    public $product_info = null;
    /** @var string|array|null */
    public $reference = null;
    /** @var string|array|null */
    public $tax_code = null;
    /** @var string|array|null */
    public $manufacturer = null;
    /** @var float|array|null */
    public $subtotal = null;
    /** @var string|array|null */
    public $type = null;
}
