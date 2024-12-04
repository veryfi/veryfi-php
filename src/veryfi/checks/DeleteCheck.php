<?php
namespace veryfi\checks;
trait DeleteCheck
{
    /**
     * Delete a Check from Veryfi. https://docs.veryfi.com/api/checks/delete-a-check/
     * @param int $document_id ID of the document you'd like to delete.
     * @return string A JSON response.
     */
    public function delete_check(int $document_id): string
    {
        $endpoint_name = "/checks/$document_id/";
        $request_arguments = array('id' => $document_id);
        return $this->request('DELETE', $endpoint_name, $request_arguments);
    }
}
