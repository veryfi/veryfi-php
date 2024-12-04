<?php
namespace veryfi\businesscards;
trait ProcessBusinessCardUrl
{
    /**
     * Process Business card and extract all the fields from it. https://docs.veryfi.com/api/business-cards/process-a-business-card/
     *
     * @param string $file_url Publicly accessible URL to a file
     * @param boolean $bounding_boxes Return bounding box and bounding region for extracted fields
     * @param boolean $confidence_details Return the score and OCR score fields in the document response
     * @param array $kwargs Additional request parameters
     * @return string Data extracted from the document
     */
    public function process_business_card_from_url(string $file_url, bool $bounding_boxes = false, bool $confidence_details = false, array $kwargs = []): string
    {
        $endpoint_name = "/business-cards/";
        $request_arguments = array_merge([
            'file_url' => $file_url,
            'bounding_boxes' => $bounding_boxes,
            'confidence_details' => $confidence_details
        ], $kwargs);

        return $this->request("POST", $endpoint_name, $request_arguments);
    }
}
