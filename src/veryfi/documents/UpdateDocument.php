<?php
namespace veryfi\documents;
trait UpdateDocument
{
    /**
     * Update data for a previously processed document, including almost any field like `vendor`, `date`, `notes` and etc.
     * https://docs.veryfi.com/api/receipts-invoices/update-a-document/
     * <code>
     * $parameters = array('notes' => 'see me');
     * veryfi_client->update_document(id, $parameters);
     * </code>
     *
     * @param int $document_id  ID of the document you'd like to update.
     * @param array $fields_to_update Fields to update.
     * @return string A document json with updated fields, if fields are writable. Otherwise, a document with unchanged fields.
     */
    public function update_document(int $document_id,
                                    array $fields_to_update): string
    {
        $endpoint_name = "/documents/$document_id/";
        return $this->request('PUT', $endpoint_name, $fields_to_update);
    }
}
