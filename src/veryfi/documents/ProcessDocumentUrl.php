<?php
namespace veryfi\documents;
trait ProcessDocumentUrl
{
    /**
     * Process Document from url and extract all the fields from it. https://docs.veryfi.com/api/receipts-invoices/process-a-document/
     *
     * @param string|null $file_url Required if file_urls isn't specified. Publicly accessible URL to a file, e.g. "https://cdn.example.com/receipt.jpg".
     * @param array|null $file_urls Required if file_url isn't specifies. List of publicly accessible URLs to multiple files, e.g. ['https://cdn.example.com/receipt1.jpg', 'https://cdn.example.com/receipt2.jpg']
     * @param array|null $categories Array of categories to use when categorizing the document
     * @param bool $auto_delete Delete this/these document(s) from Veryfi after data has been extracted
     * @param int $boost_mode Flag that tells Veryfi whether boost mode should be enabled. When set to 1, Veryfi will skip data enrichment steps, but will process the document faster. Default value for this flag is 0.
     * @param string|null $external_id Optional custom document identifier. Use this if you would like to assign your own ID to documents.
     * @param int|null $max_pages_to_process When sending a long document to Veryfi for processing, this parameter controls how many pages of the document will be read and processed, starting from page 1.
     * @param array $kwargs Additional request parameters.
     * @return string Data extracted from the document.
     */
    public function process_document_url(string $file_url = null,
                                         array $file_urls = null,
                                         array $categories = null,
                                         bool $auto_delete = false,
                                         int $boost_mode = 0,
                                         string $external_id = null,
                                         int $max_pages_to_process = null,
                                         array $kwargs = array()): string
    {
        $endpoint_name = '/documents/';
        $request_arguments = array(
            'auto_delete' => $auto_delete,
            'boost_mode' => $boost_mode,
            'categories' => $categories,
            'external_id' => $external_id,
            'file_url' => $file_url,
            'file_urls' => $file_urls,
            'max_pages_to_process' => $max_pages_to_process
        );
        $request_arguments = array_replace($request_arguments, $kwargs);
        return $this->request('POST', $endpoint_name, $request_arguments);
    }
}
