<?php
namespace veryfi\bankstatements;
trait GetBankStatements
{
    /**
     * Get all bank statements. https://docs.veryfi.com/api/bank-statements/get-bank-statements/
     *
     * @param int $page The page number
     * @param int $page_size The number of documents per page
     * @param boolean $bounding_boxes Return bounding box and bounding region for extracted fields
     * @param boolean $confidence_details Return the score and OCR score fields in the document response
     * @param array $kwargs Additional request parameters
     * @return string Object of previously processed bank statements
     */
    public function get_bank_statements(int $page = 1, int $page_size = 50, bool $bounding_boxes = false, bool $confidence_details = false, array $kwargs = array()): string
    {
        $endpoint_name = "/bank-statements/";
        $request_arguments = array_merge([
            'page' => $page,
            'page_size' => $page_size,
            'bounding_boxes' => $bounding_boxes,
            'confidence_details' => $confidence_details
        ], $kwargs);
        return $this->request("GET", $endpoint_name, $request_arguments);
    }
}
