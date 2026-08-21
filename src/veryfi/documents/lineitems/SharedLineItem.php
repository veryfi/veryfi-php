<?php

declare(strict_types=1);

namespace veryfi\documents\lineitems;

/**
 * Model of Shared Line Item.
 */
class SharedLineItem
{
    public ?string $sku = null;
    /**
     * Category value accepted by the API (string or list depending on the model configuration).
     * @var string|array|null
     */
    public $category = null;
    public ?float $tax = null;
    public ?float $price = null;
    public ?string $unit_of_measure = null;
    public ?float $quantity = null;
    public ?string $upc = null;
    public ?float $tax_rate = null;
    public ?float $discount_rate = null;
    public ?string $start_date = null;
    public ?string $end_date = null;
    public ?string $hsn = null;
    public ?string $section = null;
    public ?string $weight = null;
    public ?string $expanded_description = null;
    public ?string $brand = null;
    /**
     * Tags associated with the line item.
     * @var string[]|null
     */
    public ?array $tags = null;
}
