<?php
namespace veryfi\checks;
use CURLFile;

trait ProcessCheck
{
    /**
     * Process Check and extract all the fields from it.https://docs.veryfi.com/api/checks/process-a-check/
     * @param string $file_path Path on disk to a file to submit for data extraction.
     * @param bool $bounding_boxes Return bounding box and bounding region for extracted fields.
     * @param bool $confidence_details Return the score and OCR score fields in the document response.
     * @param array $kwargs Additional request parameters.
     * @return string Data extracted from the document.
     */
    public function process_check(string $file_path,
                                          bool $bounding_boxes = false,
                                          bool $confidence_details = false,
                                          array $kwargs = array()): string
    {
        $endpoint_name = '/checks/';
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
