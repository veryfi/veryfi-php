<?php
namespace veryfi\bankstatements;
use CURLFile;

trait ProcessBankStatement
{
    /**
     * Process a bank statement from a file path and extract all fields from it. https://docs.veryfi.com/api/bank-statements/process-a-bank-statement/
     *
     * @param string $file_path Path on disk to a file to submit for data extraction.
     * @param bool $bounding_boxes Return bounding box and bounding region for extracted fields.
     * @param bool $confidence_details Return the score and OCR score fields in the document response.
     * @param array $kwargs Additional request parameters.
     * @return string Data extracted from the document.
     */
    public function process_bank_statement(string $file_path,
                                           bool $bounding_boxes = false,
                                           bool $confidence_details = false,
                                           array $kwargs = array()): string
    {
        $endpoint_name = '/bank-statements/';
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
