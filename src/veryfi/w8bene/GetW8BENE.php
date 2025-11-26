<?php
namespace veryfi\w8bene;
trait GetW8BENE
{
    /**
     * Get a W-8BEN-E document. https://docs.veryfi.com/api/w-8ben-e/get-a-w-8-ben-e/
     *
     * @param string $document_id The ID of the document you'd like to retrieve.
     * @param array $kwargs Additional request parameters
     * @return string Data extracted from the document.
     */
    public function get_w8bene(string $document_id,  array $kwargs = []): string
    {
        $endpoint_name = "/w-8ben-e/{$document_id}/";
        $request_arguments = array_merge(['id' => $document_id], $kwargs);
        return $this->request('GET', $endpoint_name, $request_arguments);
    }
}
