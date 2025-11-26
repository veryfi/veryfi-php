<?php
namespace veryfi\documents\lineitems;
trait GetLineItems
{
    /**
     * Retrieve all line items for a document. https://docs.veryfi.com/api/receipts-invoices/get-document-line-items/
     *
     * @param int $document_id ID of the document you'd like to retrieve
     * @return string List of line items extracted from the document as string
     */
    public function get_line_items(int $document_id): string
    {
        $endpoint_name = "/documents/$document_id/line-items/";
        $request_arguments = array();
        return $this->request('GET', $endpoint_name, $request_arguments);
    }
}
