<?php
namespace veryfi\w8bene;
trait DeleteW8BENE
{
    /**
     * Delete a W-8BEN-E from Veryfi. https://docs.veryfi.com/api/w-8ben-e/delete-a-w-8-ben-e/
     * @param string $document_id The ID of the document you'd like to retrieve.
     * @param array $kwargs Additional request parameters
     * @return string Data extracted from the document.
     */
    public function delete_w8bene(string $document_id,  array $kwargs = []): string
    {
        $endpoint_name = "/w-8ben-e/{$document_id}/";
        $request_arguments = array_merge(['id' => $document_id], $kwargs);
        return $this->request('DELETE', $endpoint_name, $request_arguments);
    }
}
