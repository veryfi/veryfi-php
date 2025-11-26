<?php
namespace veryfi\documents\lineitems;
trait GetLineItem
{
    /**
     * Retrieve a line item for existing document by ID. https://docs.veryfi.com/api/receipts-invoices/get-a-line-item/
     *
     * @param int $document_id ID of the document you'd like to retrieve
     * @param int $line_item_id ID of the line item you'd like to retrieve
     * @return string Line item extracted from the document as string
     */
    public function get_line_item(int $document_id,
                                  int $line_item_id): string
    {
        $endpoint_name = "/documents/$document_id/line-items/$line_item_id";
        $request_arguments = array();
        return $this->request('GET', $endpoint_name, $request_arguments);
    }
}
