<?php
namespace veryfi\w2s;
use CURLFile;

trait ProcessW2
{
    /**
     * Process a W2 document from a file path and extract all fields from it. https://docs.veryfi.com/api/w2s/process-a-w-2/
     *
     * @param string $file_path Path on disk to a file to submit for data extraction.
     * @param bool $auto_delete Delete this document from Veryfi after data has been extracted.
     * @param int $max_pages_to_process The number of pages to process for the document.
     * @param array $kwargs Additional request parameters.
     * @return string Data extracted from the document.
     */
    public function process_w2_document(string $file_path,
                                        bool $auto_delete = false,
                                        int $max_pages_to_process = 1,
                                        array $kwargs = array()): string
    {
        $endpoint_name = '/w2s/';
        $file_name = pathinfo($file_path, PATHINFO_BASENAME);
        $file_type = mime_content_type($file_path);
        $request_arguments = array(
            'file_name' => $file_name,
            'file' => new CURLFile($file_path, $file_type, $file_name),
            'auto_delete' => $auto_delete,
            'max_pages_to_process' => $max_pages_to_process
        );
        $request_arguments = array_replace($request_arguments, $kwargs);
        return $this->request('POST', $endpoint_name, $request_arguments);
    }
}
