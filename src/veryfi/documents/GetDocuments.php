<?php
namespace veryfi\documents;
trait GetDocuments
{
    /**
     * Get list of documents. https://docs.veryfi.com/api/receipts-invoices/search-documents/
     * @param array $kwargs Additional request parameters.
     * @return string A JSON with list of processes documents and metadata.
     */
    public function get_documents(array $kwargs = array()): string
    {
        $endpoint_name = '/documents/';
        return $this->request('GET', $endpoint_name, $kwargs);
    }
}