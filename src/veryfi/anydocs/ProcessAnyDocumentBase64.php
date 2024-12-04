<?php
namespace veryfi\anydocs;
trait ProcessAnyDocumentBase64
{
    /**
     * Process any document from a file path and extract all fields from it. https://docs.veryfi.com/api/anydocs/process-%E2%88%80-doc/
     *
     * @param string $file_path Path on disk to a file to submit for data extraction.
     * @param string $template_name The name of the extraction template.
     * @param int $max_pages_to_process The number of pages to process for the document.
     * @param array $kwargs Additional request parameters.
     * @return string Data extracted from the document.
     */
    public function process_any_document_base64(string $file_path,
                                         string $template_name,
                                         int $max_pages_to_process = 20,
                                         array $kwargs = array()): string
    {
        $endpoint_name = '/any-documents/';
        $file_name = pathinfo($file_path, PATHINFO_BASENAME);
        $file = fopen($file_path, 'rb');
        $file_data = base64_encode(fread($file, filesize($file_path)));
        $file_type = mime_content_type($file_path);
        $base64_with_hint = "data:$file_type;base64," . $file_data;
        $request_arguments = array(
            'file_name' => $file_name,
            'file_data' => $base64_with_hint,
            'template_name' => $template_name,
            'max_pages_to_process' => $max_pages_to_process
        );
        $request_arguments = array_replace($request_arguments, $kwargs);
        return $this->request('POST', $endpoint_name, $request_arguments);
    }
}
