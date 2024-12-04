<?php
namespace veryfi\w9s;
trait GetW9s
{
    /**
     * Get all W9 documents. https://docs.veryfi.com/api/w9s/get-w-9-s/
     *
     * @param array $kwargs Additional request parameters
     * @return string An array of JSON with all W2 documents.
     */
    public function get_w9s(array $kwargs = []): string
    {
        $endpoint_name = '/w9s/';
        return $this->request('GET', $endpoint_name, $kwargs);
    }
}
