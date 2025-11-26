<?php
namespace veryfi\w8bene;
trait GetW8BENEs
{
    /**
     * Get all W-8BEN-E documents.https://docs.veryfi.com/api/w-8ben-e/get-w-8-ben-es/
     *
     * @param array $kwargs Additional request parameters
     * @return string An array of JSON with all W2 documents.
     */
    public function get_w8benes(array $kwargs = []): string
    {
        $endpoint_name = '/w-8ben-e/';
        return $this->request('GET', $endpoint_name, $kwargs);
    }
}
