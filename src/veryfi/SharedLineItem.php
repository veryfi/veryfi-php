<?php

declare(strict_types=1);

namespace veryfi;

/**
 * Model of Shared Line Item.
 */
class SharedLineItem
{
    public ?string $sku = null;
    public ?string $category = null;
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
}
