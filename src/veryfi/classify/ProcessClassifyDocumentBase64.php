<?php
namespace veryfi\classify;

trait ProcessClassifyDocumentBase64
{
    /**
     * Classify a document and extract all the fields from it. https://docs.veryfi.com/api/classify/classify-a-document/
     *
     * @param string $base64_encoded_string Buffer string of a file to submit for classify extraction
     * @param string $file_name The file name including the extension
     * @param array $kwargs Additional request parameters
     * @return string Data extracted from the document
     */
    public function classify_document_from_base64(string $base64_encoded_string, string $file_name, array $kwargs = []): string
    {
        $endpoint_name = '/classify/';
        $request_arguments = [
            'file_name' => $file_name,
            'file_data' => $base64_encoded_string,
        ];
        $request_arguments = array_replace($request_arguments, $kwargs);
        return $this->request('POST', $endpoint_name, $request_arguments);
    }
}
