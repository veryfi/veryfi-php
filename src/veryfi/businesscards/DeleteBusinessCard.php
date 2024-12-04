<?php
namespace veryfi\businesscards;
trait DeleteBusinessCard
{
    /**
     * Delete a Business Card from Veryfi. https://docs.veryfi.com/api/business-cards/delete-a-business-card/
     * @param int $document_id ID of the document you'd like to delete.
     * @return string A JSON response.
     */
    public function delete_business_card(int $document_id): string
    {
        $endpoint_name = "/business-cards/$document_id/";
        $request_arguments = array('id' => $document_id);
        return $this->request('DELETE', $endpoint_name, $request_arguments);
    }
}
