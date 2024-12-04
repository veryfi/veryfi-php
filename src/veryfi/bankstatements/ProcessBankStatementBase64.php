<?php
namespace veryfi\bankstatements;
trait ProcessBankStatementBase64
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
    public function process_bank_statement_base64(string $file_path,
                                           bool $bounding_boxes = false,
                                           bool $confidence_details = false,
                                           array $kwargs = array()): string
    {
        $endpoint_name = '/bank-statements/';
        $file_name = pathinfo($file_path, PATHINFO_BASENAME);
        $file = fopen($file_path, 'rb');
        $file_data = base64_encode(fread($file, filesize($file_path)));
        $file_type = mime_content_type($file_path);
        $base64_with_hint = "data:$file_type;base64," . $file_data;
        $request_arguments = array(
            'file_name' => $file_name,
            'file_data' => $base64_with_hint,
            'bounding_boxes' => $bounding_boxes,
            'confidence_details' => $confidence_details
        );
        $request_arguments = array_replace($request_arguments, $kwargs);
        return $this->request('POST', $endpoint_name, $request_arguments);
    }
}
