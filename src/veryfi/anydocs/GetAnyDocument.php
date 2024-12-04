<?php
namespace veryfi\anydocs;
trait GetAnyDocument
{
    /**
     * Get a specific any document. https://docs.veryfi.com/api/anydocs/get-a-%E2%88%80-doc/
     *
     * @param int $document_id The unique identifier of the document
     * @param array $additional_request_parameters Additional request parameters
     * @return string Object of a previously processed document
     */
    public function get_any_document(int $document_id, array $additional_request_parameters = array()): string
    {
        $endpoint_name = "/any-documents/$document_id/";
        return $this->request("GET", $endpoint_name, $additional_request_parameters);
    }
}
