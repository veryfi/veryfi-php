<?php
namespace veryfi\w8bene;
trait ProcessW8BENE
{
    /**
     * Process a W-8BEN-E document. https://docs.veryfi.com/api/w-8ben-e/process-a-w-8-ben-e/
     * @param string $file_path Path on disk to a file to submit for data extraction.
     * @param bool $bounding_boxes Return bounding box and bounding region for extracted fields.
     * @param bool $confidence_details Return the score and OCR score fields in the document response.
     * @param array $kwargs Additional request parameters.
     * @return string Data extracted from the document.
     */
    public function process_w8bene(string $file_path,
                                  bool $bounding_boxes = false,
                                  bool $confidence_details = false,
                                  array $kwargs = array()): string
    {
        $endpoint_name = '/w-8ben-e/';
        $file_name = pathinfo($file_path, PATHINFO_BASENAME);
        $file_type = mime_content_type($file_path);
        $request_arguments = array(
            'file_name' => $file_name,
            'file' => new CURLFile($file_path, $file_type, $file_name),
            'bounding_boxes' => $bounding_boxes,
            'confidence_details' => $confidence_details
        );
        $request_arguments = array_replace($request_arguments, $kwargs);
        return $this->request('POST', $endpoint_name, $request_arguments, true);
    }
}
