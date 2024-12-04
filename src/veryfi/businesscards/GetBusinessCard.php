<?php
namespace veryfi\businesscards;
trait GetBusinessCard
{
    /**
     * Get a specific Business card. https://docs.veryfi.com/api/business-cards/get-a-business-card/
     * @param int $document_id ID of the document you'd like to retrieve.
     * @param array $kwargs Additional request parameters.
     * @return string A Json of data extracted from the Document.
     */
    public function get_business_card(int $document_id, array $kwargs = array()): string
    {
        $endpoint_name = "/business-cards/$document_id/";
        $request_arguments = array('id' => $document_id);
        $request_arguments = array_replace($request_arguments, $kwargs);
        return $this->request('GET', $endpoint_name, $request_arguments);
    }
}
