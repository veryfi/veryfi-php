<?php
namespace veryfi\checks;
trait GetChecks
{
    /**
     * Get all checks. https://docs.veryfi.com/api/checks/get-checks/
     * @param array $kwargs Additional request parameters.
     * @return string A JSON with list of processes documents and metadata.
     */
    public function get_checks(array $kwargs = array()): string
    {
        $endpoint_name = '/checks/';
        return $this->request('GET', $endpoint_name, $kwargs);
    }
}
