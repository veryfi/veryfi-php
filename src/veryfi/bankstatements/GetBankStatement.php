<?php
namespace veryfi\bankstatements;
trait GetBankStatement
{
    /**
     * Get a specific bank statement. https://docs.veryfi.com/api/bank-statements/get-a-bank-statement/
     *
     * @param int $document_id The unique identifier of the document
     * @param boolean $bounding_boxes Return bounding box and bounding region for extracted fields
     * @param boolean $confidence_details Return the score and OCR score fields in the document response
     * @param array $additional_request_parameters Additional request parameters
     * @return string Object of a previously processed bank statement
     */
    public function get_bank_statement(int $document_id, bool $bounding_boxes = false, bool $confidence_details = false, array $additional_request_parameters = array()): string
    {
        $endpoint_name = "/bank-statements/$document_id/";
        $request_arguments = array_merge([
            'bounding_boxes' => $bounding_boxes,
            'confidence_details' => $confidence_details
        ], $additional_request_parameters);
        return $this->request("GET", $endpoint_name, $request_arguments);
    }
}
