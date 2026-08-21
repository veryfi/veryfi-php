<?php

declare(strict_types=1);

namespace veryfi\documents\lineitems;


use Exception;

/**
 * Model of Update Line Item.
 */
class LineItemUpdate extends SharedLineItem
{
    public ?int $order = null;
    /** @var string|array|null */
    public $description = null;
    /** @var float|array|null */
    public $total = null;

    /**
     * @param array $data json array to init the object.
     * @param bool $verify if true it throws bad argument exception if a bad argument is given.
     * @throws Exception throws 'Bad Argument' if a field is not in the model.
     */
    public function __construct(array $data,
                                bool $verify = true)
    {
        foreach ($data as $key => $val) {
            if (property_exists(__CLASS__, $key)) {
                $this->$key = $val;
            } else {
                if ($verify) {
                    throw new Exception('Bad Argument');
                }
            }
        }
    }
}
