<?php
namespace veryfi\documents\lineitems;

trait AddLineItem
{
    /**
     * Add a new line item on an existing document. https://docs.veryfi.com/api/receipts-invoices/create-a-line-item/
     *
     * @param int $document_id ID of the document you'd like to update
     * @param LineItem $payload line item object to add
     * @return string Added line item data
     */
    public function add_line_item(int $document_id,
                                  LineItem $payload): string
    {
        $endpoint_name = "/documents/$document_id/line-items/";
        $request_arguments = array_filter(get_object_vars($payload), static function($var){return $var !== null;});
        return $this->request('POST', $endpoint_name, $request_arguments);
    }
}
