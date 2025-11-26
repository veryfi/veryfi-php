<?php
namespace veryfi\documents\tags;
trait ReplaceTags
{
    /**
     * Replace multiple tags on an existing document
     *
     * @param int $document_id ID of the document you'd like to add a Tag
     * @param array $tags array of strings
     * @return string Added tag data
     */
    public function replace_tags(int $document_id,
                                 array $tags): string
    {
        $endpoint_name = "/documents/$document_id/";
        $request_arguments = array('tags' => $tags);
        return $this->request('PUT', $endpoint_name, $request_arguments);
    }
}
