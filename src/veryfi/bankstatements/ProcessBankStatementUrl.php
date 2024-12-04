<?php
namespace veryfi\bankstatements;
trait ProcessBankStatementUrl
{
    /**
     * Process a bank statement from a URL and extract all fields from it. https://docs.veryfi.com/api/bank-statements/process-a-bank-statement/
     *
     * @param string $file_url Publicly accessible URL to a file
     * @param boolean $bounding_boxes Return bounding box and bounding region for extracted fields
     * @param boolean $confidence_details Return the score and OCR score fields in the document response
     * @param array $additional_request_parameters Additional request parameters
     * @return string Data extracted from the document
     */
    public function process_bank_statement_url(string $file_url, bool $bounding_boxes = false, bool $confidence_details = false, array $additional_request_parameters = []): string
    {
        $endpoint_name = "/bank-statements/";
        $request_arguments = array_merge([
            'file_url' => $file_url,
            'bounding_boxes' => $bounding_boxes,
            'confidence_details' => $confidence_details
        ], $additional_request_parameters);

        return $this->request("POST", $endpoint_name, $request_arguments);
    }
}
