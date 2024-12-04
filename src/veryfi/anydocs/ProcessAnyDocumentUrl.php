<?php
namespace veryfi\anydocs;
trait ProcessAnyDocumentUrl
{
    /**
     * Process any document from a URL and extract all fields from it. https://docs.veryfi.com/api/anydocs/process-%E2%88%80-doc/
     *
     * @param string $file_url Publicly accessible URL to a file
     * @param string $template_name The name of the extraction template
     * @param int $max_pages_to_process The number of pages to process for the document
     * @param array $additional_request_parameters Additional request parameters
     * @return string Data extracted from the document
     */
    public function process_any_document_url(string $file_url, string $template_name, int $max_pages_to_process = 20, array $additional_request_parameters = []): string
    {
        $endpoint_name = "/any-documents/";
        $request_arguments = array_merge([
            'file_url' => $file_url,
            'template_name' => $template_name,
            'max_pages_to_process' => $max_pages_to_process
        ], $additional_request_parameters);

        return $this->request("POST", $endpoint_name, $request_arguments);
    }
}
