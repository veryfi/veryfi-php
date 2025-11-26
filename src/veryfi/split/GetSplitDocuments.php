<?php
namespace veryfi\split;

trait GetSplitDocuments
{
    /**
     * Veryfi's Get a Submitted PDF endpoint allows you to retrieve a collection of previously processed documents. https://docs.veryfi.com/api/receipts-invoices/get-submitted-pdf/
     *
     * @param int $page The page number. The response is capped to maximum of 50 results per page.
     * @param int $page_size The number of Documents per page.
     * @param array $kwargs Additional request parameters
     * @return string JSON object of previously processed documents
     */
    public function get_split_documents(int $page = 1, int $page_size = 50, array $kwargs = []): string
    {
        $endpoint_name = '/documents-set/';
        $request_arguments = [
            'page' => $page,
            'page_size' => $page_size
        ];
        $request_arguments = array_replace($request_arguments, $kwargs);
        return $this->request('GET', $endpoint_name, $request_arguments);
    }
}
