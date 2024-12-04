<?php
namespace veryfi\w2s;
trait GetW2s
{
    /**
     * Get all W2 documents. https://docs.veryfi.com/api/w2s/get-w-2-s/
     *
     * @param int|null $page The page number, response is capped to a maximum of 50 results per page.
     * @param array $additional_request_parameters Additional request parameters
     * @return string An array of JSON with all W2 documents.
     * @throws Exception when API version is not supported for W2 documents.
     */
    public function get_w2_documents(int $page = null,  array $additional_request_parameters = []): string
    {
        $endpoint_name = '/w2s/';
        $request_arguments = array_merge([
            'page' => $page,
        ], $additional_request_parameters);
        return $this->request('GET', $endpoint_name, $request_arguments);
    }
}
