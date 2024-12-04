<?php
namespace veryfi\documents;
trait GetDocument
{
    /**
     * Retrieve document by ID. https://docs.veryfi.com/api/receipts-invoices/get-a-document/
     * @param int $document_id ID of the document you'd like to retrieve.
     * @param array $kwargs Additional request parameters.
     * @return string A Json of data extracted from the Document.
     */
    public function get_document(int $document_id, array $kwargs = array()): string
    {
        $endpoint_name = "/documents/$document_id/";
        $request_arguments = array('id' => $document_id);
        $request_arguments = array_replace($request_arguments, $kwargs);
        return $this->request('GET', $endpoint_name, $request_arguments);
    }
}