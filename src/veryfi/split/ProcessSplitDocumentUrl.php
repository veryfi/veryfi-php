<?php
namespace veryfi\split;

trait ProcessSplitDocumentUrl
{
    /**
     * Split document PDF from url and extract all the fields from it. https://docs.veryfi.com/api/receipts-invoices/split-and-process-a-pdf/
     *
     * @param string|null $file_url Required if file_urls isn't specified. Publicly accessible URL to a file.
     * @param array|null $file_urls Required if file_url isn't specified. List of publicly accessible URLs to multiple files.
     * @param array $kwargs Additional request parameters
     * @return string Data extracted from the document.
     */
    public function split_document_from_url(?string $file_url = null, ?array $file_urls = null, array $kwargs = []): string
    {
        $endpoint_name = '/documents-set/';
        $request_arguments = [
            'file_url' => $file_url,
            'file_urls' => $file_urls
        ];
        $request_arguments = array_replace($request_arguments, $kwargs);
        return $this->request('POST', $endpoint_name, $request_arguments);
    }
}
