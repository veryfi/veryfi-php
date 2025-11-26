<?php
namespace veryfi\documents\lineitems;
trait DeleteLineItems
{
    /**
     * Delete all line items on an existing document. https://docs.veryfi.com/api/receipts-invoices/delete-all-document-line-items/
     *
     * @param int $document_id  ID of the document you'd like to delete
     * @return string A JSON response.
     */
    public function delete_line_items(int $document_id): string
    {
        $endpoint_name = "/documents/$document_id/line-items/";
        $request_arguments = array();
        return $this->request('DELETE', $endpoint_name, $request_arguments);
    }
}
