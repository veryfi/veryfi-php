<?php
namespace veryfi\documents\tags;
trait DeleteTag
{
    /**
     * Unlink tag assigned to a specific document. https://docs.veryfi.com/api/receipts-invoices/unlink-a-tag-from-a-document/
     *
     * @param int $document_id ID of the document you'd like to delete its tag
     * @param int $tag_id ID of the tag you'd like to delete
     * @return string A JSON response.
     */
    public function delete_tag(int $document_id,
                               int $tag_id): string
    {
        $endpoint_name = "/documents/$document_id/tags/$tag_id/";
        $request_arguments = array();
        return $this->request('DELETE', $endpoint_name, $request_arguments);
    }
}
