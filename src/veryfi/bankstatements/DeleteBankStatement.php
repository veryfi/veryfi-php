<?php
namespace veryfi\bankstatements;
trait DeleteBankStatement
{
    /**
     * Delete a Bank Statement from Veryfi. https://docs.veryfi.com/api/bank-statements/delete-a-bank-statement/
     * @param int $document_id ID of the document you'd like to delete.
     * @return string A JSON response.
     */
    public function delete_bank_statement(int $document_id): string
    {
        $endpoint_name = "/bank-statements/$document_id/";
        $request_arguments = array('id' => $document_id);
        return $this->request('DELETE', $endpoint_name, $request_arguments);
    }
}
