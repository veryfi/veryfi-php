<?php
namespace veryfi\documents;
trait ProcessDocument
{
    /**
     * Process a document and extract all the fields from it. https://docs.veryfi.com/api/receipts-invoices/process-a-document/
     *
     * @param string $file_path Path on disk to a file to submit for data extraction.
     * @param array|string[] $categories Array of categories Veryfi can use to categorize the document.
     * @param bool $auto_delete Delete this document from Veryfi after data has been extracted.
     * @param array $additional_request_parameters Additional request parameters.
     * @return string Data extracted from the document.
     */
    public function process_document(string $file_path,
                                     array $categories = array(),
                                     bool $auto_delete = false,
                                     array $additional_request_parameters = array()): string
    {
        $endpoint_name = '/documents/';
        $file_name = pathinfo($file_path, PATHINFO_BASENAME);
        $file = fopen($file_path, 'rb');
        $file_data = base64_encode(fread($file, filesize($file_path)));
        $file_type = mime_content_type($file_path);
        $base64_with_hint = "data:$file_type;base64," . $file_data;
        $request_arguments = array(
            'file_name' => $file_name,
            'file_data' => $base64_with_hint,
            'categories' => $categories,
            'auto_delete' => $auto_delete
        );
        $request_arguments = array_replace($request_arguments, $additional_request_parameters);
        return $this->request('POST', $endpoint_name, $request_arguments);
    }
}
