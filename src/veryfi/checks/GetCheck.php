<?php
namespace veryfi\checks;
trait GetCheck
{
    /**
     * Get a specific check. https://docs.veryfi.com/api/checks/get-a-check/
     * @param int $document_id ID of the document you'd like to retrieve.
     * @param array $kwargs Additional request parameters.
     * @return string A Json of data extracted from the Document.
     */
    public function get_check(int $document_id, array $kwargs = array()): string
    {
        $endpoint_name = "/checks/$document_id/";
        $request_arguments = array('id' => $document_id);
        $request_arguments = array_replace($request_arguments, $kwargs);
        return $this->request('GET', $endpoint_name, $request_arguments);
    }
}
