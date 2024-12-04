<?php
namespace veryfi\documents\tags;
trait DeleteTags
{
    /**
     * Unlink all tags assigned to a specific document. https://docs.veryfi.com/api/receipts-invoices/unlink-all-tags-from-a-document/
     *
     * @param int $document_id ID of the document you'd like to delete their tags
     * @return string A JSON response.
     */
    public function delete_tags(int $document_id): string
    {
        $endpoint_name = "/documents/$document_id/tags/";
        $request_arguments = array();
        return $this->request('DELETE', $endpoint_name, $request_arguments);
    }
}
