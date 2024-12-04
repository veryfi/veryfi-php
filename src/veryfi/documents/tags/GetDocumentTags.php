<?php
namespace veryfi\documents\tags;
trait GetDocumentTags
{
    /**
     * Retrieve list of tags by document ID. https://docs.veryfi.com/api/receipts-invoices/get-document-tags/
     *
     * @param int $document_id ID of the document you'd like to retrieve tags.
     * @return string A JSON with list of tags from the Document.
     */
    public function get_document_tags(int $document_id): string
    {
        $endpoint_name = "/documents/$document_id/tags/";
        $request_arguments = array('id' => $document_id);
        return $this->request('GET', $endpoint_name, $request_arguments);
    }
}
