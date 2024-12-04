<?php
namespace veryfi\w2s;
trait ProcessW2Url
{
    /**
     * Process a W2 document from a URL and extract all fields from it. https://docs.veryfi.com/api/w2s/process-a-w-2/
     *
     * @param string $file_name The file name including the extension
     * @param string $file_url Publicly accessible URL to a file
     * @param array|null $file_urls List of publicly accessible URLs to multiple files
     * @param boolean $auto_delete Delete this document from Veryfi after data has been extracted
     * @param int $max_pages_to_process The number of pages to process for the document
     * @param array $kwargs Additional request parameters
     * @return string Data extracted from the document
     */
    public function process_w2_document_from_url(string $file_name, string $file_url, array $file_urls = null, bool $auto_delete = false, int $max_pages_to_process = 1, array $kwargs = []): string
    {
        $endpoint_name = "/w2s/";
        $request_arguments = array_merge([
            'file_name' => $file_name,
            'auto_delete' => $auto_delete,
            'file_url' => $file_url,
            'file_urls' => $file_urls,
            'max_pages_to_process' => $max_pages_to_process
        ], $kwargs);

        return $this->request("POST", $endpoint_name, $request_arguments);
    }
}
