<?php
namespace veryfi\documents\lineitems;

trait UpdateLineItem
{
    /**
     * Update an existing line item on an existing document. https://docs.veryfi.com/api/receipts-invoices/update-a-line-item/
     *
     * @param int $document_id ID of the document you'd like to update
     * @param int $line_item_id ID of the line item you'd like to update
     * @param LineItemUpdate $payload line item object to update
     * @return string Line item data with updated fields, if fields are writable. Otherwise, line item data with unchanged fields.
     */
    public function update_line_item(int $document_id,
                                     int $line_item_id,
                                     LineItemUpdate $payload): string
    {
        $endpoint_name = "/documents/$document_id/line-items/$line_item_id";
        $request_arguments = array_filter(get_object_vars($payload), static function($var){return $var !== null;});
        return $this->request('PUT', $endpoint_name, $request_arguments);
    }
}
