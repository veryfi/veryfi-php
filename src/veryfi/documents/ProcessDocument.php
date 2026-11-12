<?php
namespace veryfi\documents;
use CURLFile;

trait ProcessDocument
{
    /**
     * Process a document and extract all the fields from it. https://docs.veryfi.com/api/receipts-invoices/process-a-document/
     *
     * @param string $file_path Path on disk to a file to submit for data extraction.
     * @param array|string[] $categories Array of categories Veryfi can use to categorize the document.
     * @param bool $auto_delete Delete this document from Veryfi after data has been extracted.
     * @param array $kwargs Additional request parameters.
     * @param string|null $thinking Extra LLM pass on the line items: "light" or "deep" (slower). Omit to turn it off. Needs thinking mode enabled for your account.
     * @return string Data extracted from the document.
     */
    public function process_document(string $file_path,
                                     array $categories = array(),
                                     bool $auto_delete = false,
                                     array $kwargs = array(),
                                     ?string $thinking = null): string
    {
        $endpoint_name = '/documents/';
        $file_name = pathinfo($file_path, PATHINFO_BASENAME);
        $file_type = mime_content_type($file_path);
        $request_arguments = array(
            'file_name' => $file_name,
            'file' => new CURLFile($file_path, $file_type, $file_name),
            'categories' => $categories,
            'auto_delete' => $auto_delete
        );
        if ($thinking !== null) {
            $request_arguments['thinking'] = $thinking;
        }
        $request_arguments = array_replace($request_arguments, $kwargs);
        return $this->request('POST', $endpoint_name, $request_arguments, true);
    }
}
