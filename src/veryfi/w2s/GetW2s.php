<?php
namespace veryfi\w2s;
trait GetW2s
{
    /**
     * Get all W2 documents. https://docs.veryfi.com/api/w2s/get-w-2-s/
     *
     * @param int|null $page The page number, response is capped to a maximum of 50 results per page.
     * @param array $kwargs Additional request parameters
     * @return string An array of JSON with all W2 documents.
     */
    public function get_w2s(int $page = null,  array $kwargs = []): string
    {
        $endpoint_name = '/w2s/';
        $request_arguments = array_merge([
            'page' => $page,
        ], $kwargs);
        return $this->request('GET', $endpoint_name, $request_arguments);
    }
}
