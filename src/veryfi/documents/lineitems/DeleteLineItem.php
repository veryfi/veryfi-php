<?php
namespace veryfi\documents\lineitems;
trait DeleteLineItem
{
    /**
     * Delete an existing line item on an existing document. https://docs.veryfi.com/api/receipts-invoices/delete-a-line-item/
     *
     * @param int $document_id ID of the document you'd like to delete
     * @param int $line_item_id ID of the line item you'd like to delete
     * @return string A JSON response.
     */
    public function delete_line_item(int $document_id,
                                     int $line_item_id): string
    {
        $endpoint_name = "/documents/$document_id/line-items/$line_item_id";
        $request_arguments = array();
        return $this->request('DELETE', $endpoint_name, $request_arguments);
    }
}
