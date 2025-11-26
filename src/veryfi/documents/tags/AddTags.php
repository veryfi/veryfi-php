<?php
namespace veryfi\documents\tags;
trait AddTags
{
    /**
     * Add multiple tags on an existing document. https://docs.veryfi.com/api/receipts-invoices/add-tags-to-a-document/
     *
     * @param int $document_id ID of the document you'd like to add a Tag
     * @param array $tags array of strings
     * @return string Added tag data
     */
    public function add_tags(int $document_id,
                             array $tags): string
    {
        $endpoint_name = "/documents/$document_id/tags/";
        $request_arguments = array('tags' => $tags);
        return $this->request('POST', $endpoint_name, $request_arguments);
    }
}
