<?php
namespace veryfi\w8bene;
trait ProcessW8BENEUrl
{
    /**
     * Process a W-8BEN-E document. https://docs.veryfi.com/api/w-8ben-e/process-a-w-8-ben-e/
     * @param string $file_url Publicly accessible URL to a file
     * @param boolean $bounding_boxes Return bounding box and bounding region for extracted fields
     * @param boolean $confidence_details Return the score and OCR score fields in the document response
     * @param array $kwargs Additional request parameters
     * @return string Data extracted from the document
     */
    public function process_w8bene_from_url(string $file_url, bool $bounding_boxes = false, bool $confidence_details = false, array $kwargs = []): string
    {
        $endpoint_name = "/w-8ben-e/";
        $request_arguments = array_merge([
            'file_url' => $file_url,
            'bounding_boxes' => $bounding_boxes,
            'confidence_details' => $confidence_details
        ], $kwargs);

        return $this->request("POST", $endpoint_name, $request_arguments);
    }
}
