<?php
namespace veryfi\w2s;
trait GetW2
{
    /**
     * Get a W2 document. https://docs.veryfi.com/api/w2s/get-a-w-2/
     *
     * @param string $document_id The ID of the document you'd like to retrieve.
     * @param array $kwargs Additional request parameters
     * @return string Data extracted from the document.
     */
    public function get_w2(string $document_id,  array $kwargs = []): string
    {
        $endpoint_name = "/w2s/{$document_id}/";
        $request_arguments = array_merge(['id' => $document_id], $kwargs);
        return $this->request('GET', $endpoint_name, $request_arguments);
    }
}
