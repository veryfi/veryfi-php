<?php
namespace veryfi\documents\tags;
trait GetTags
{
    /**
     * Get list of tags.
     *
     * @return string A JSON with list of tags.
     */
    public function get_tags(): string
    {
        $endpoint_name = '/tags/';
        $request_arguments = array();
        return $this->request('GET', $endpoint_name, $request_arguments, true);
    }
}
