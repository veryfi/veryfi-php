<?php
namespace veryfi\anydocs;
trait GetAnyDocuments
{
    /**
     * Get all any documents. https://docs.veryfi.com/api/anydocs/get-%E2%88%80-docs/
     *
     * @param int $page The page number
     * @param int $page_size The number of documents per page
     * @param array $additional_request_parameters Additional request parameters
     * @return string Object of previously processed any documents
     */
    public function get_any_documents(int $page = 1, int $page_size = 50, array $additional_request_parameters = []): string
    {
        $endpoint_name = "/any-documents/";
        $request_arguments = array_merge(['page' => $page, 'page_size' => $page_size], $additional_request_parameters);
        return $this->request("GET", $endpoint_name, $request_arguments);
    }
}
