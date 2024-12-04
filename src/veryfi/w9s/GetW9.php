<?php
namespace veryfi\w9s;
trait GetW9
{
    /**
     * Get a W9 document. https://docs.veryfi.com/api/w9s/get-a-w-9/
     *
     * @param string $document_id The ID of the document you'd like to retrieve.
     * @param array $kwargs Additional request parameters
     * @return string Data extracted from the document.
     */
    public function get_w9(string $document_id,  array $kwargs = []): string
    {
        $endpoint_name = "/w9s/{$document_id}/";
        $request_arguments = array_merge(['id' => $document_id], $kwargs);
        return $this->request('GET', $endpoint_name, $request_arguments);
    }
}
