<?php
namespace veryfi\split;

trait GetSplitDocument
{
    /**
     * Veryfi's Get a Documents from PDF endpoint allows you to retrieve a collection of previously processed documents. https://docs.veryfi.com/api/receipts-invoices/get-documents-from-pdf/
     *
     * @param string $document_id ID of the document you'd like to retrieve
     * @param array $kwargs Additional request parameters
     * @return string Data extracted from the Document
     */
    public function get_split_document(string $document_id, array $kwargs = []): string
    {
        $endpoint_name = "/documents-set/$document_id/";
        $request_arguments = ['id' => $document_id];
        $request_arguments = array_replace($request_arguments, $kwargs);
        return $this->request('GET', $endpoint_name, $request_arguments);
    }
}
