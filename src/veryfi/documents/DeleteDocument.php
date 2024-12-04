<?php
namespace veryfi\documents;
trait DeleteDocument
{
    /**
     * Delete Document from Veryfi. https://docs.veryfi.com/api/receipts-invoices/delete-a-document/
     *
     * @param int $document_id ID of the document you'd like to delete.
     * @return string A JSON response.
     */
    public function delete_document(int $document_id): string
    {
        $endpoint_name = "/documents/$document_id/";
        $request_arguments = array('id' => $document_id);
        return $this->request('DELETE', $endpoint_name, $request_arguments);
    }
}
