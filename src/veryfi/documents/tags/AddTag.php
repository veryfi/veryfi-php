<?php
namespace veryfi\documents\tags;
trait AddTag
{
    /**
     * Add a new tag on an existing document. https://docs.veryfi.com/api/receipts-invoices/add-a-tag-to-a-document/
     *
     * @param int $document_id ID of the document you'd like to add a Tag
     * @param string $tag line item object to add
     * @return string Added tag data
     */
    public function add_tag(int $document_id,
                            string $tag): string
    {
        $endpoint_name = "/documents/$document_id/tags/";
        $request_arguments = array('name' => $tag);
        return $this->request('PUT', $endpoint_name, $request_arguments);
    }
}
