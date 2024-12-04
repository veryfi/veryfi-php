<?php
namespace veryfi\w2s;
trait ProcessW2
{
    /**
     * Process a W2 document from a file path and extract all fields from it. https://docs.veryfi.com/api/w2s/process-a-w-2/
     *
     * @param string $file_path Path on disk to a file to submit for data extraction.
     * @param bool $auto_delete Delete this document from Veryfi after data has been extracted.
     * @param int $max_pages_to_process The number of pages to process for the document.
     * @param array $additional_request_parameters Additional request parameters.
     * @return string Data extracted from the document.
     */
    public function process_w2_document(string $file_path,
                                        bool $auto_delete = false,
                                        int $max_pages_to_process = 1,
                                        array $additional_request_parameters = array()): string
    {
        $endpoint_name = '/w2s/';
        $file_name = pathinfo($file_path, PATHINFO_BASENAME);
        $file = fopen($file_path, 'rb');
        $file_data = base64_encode(fread($file, filesize($file_path)));
        $file_type = mime_content_type($file_path);
        $base64_with_hint = "data:$file_type;base64," . $file_data;
        $request_arguments = array(
            'file_name' => $file_name,
            'file_data' => $base64_with_hint,
            'auto_delete' => $auto_delete,
            'max_pages_to_process' => $max_pages_to_process
        );
        $request_arguments = array_replace($request_arguments, $additional_request_parameters);
        return $this->request('POST', $endpoint_name, $request_arguments);
    }
}
