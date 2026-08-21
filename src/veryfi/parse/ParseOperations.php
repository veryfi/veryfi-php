<?php

declare(strict_types=1);

namespace veryfi\parse;

/**
 * Markdown document and markdown document-set operations.
 */
trait ParseOperations
{
    /** https://docs.veryfi.com/api/parse/convert-a-document-to-markdown/ */
    public function process_markdown_document(array $parameters): string
    {
        return $this->request('POST', '/parse/', $parameters);
    }

    /** https://docs.veryfi.com/api/parse/process-a-markdown-document-asynchronously/ */
    public function process_markdown_document_async(array $parameters): string
    {
        return $this->request('POST', '/parse/async/', $parameters);
    }

    /** https://docs.veryfi.com/api/parse/get-markdown-documents/ */
    public function get_markdown_documents(array $kwargs = []): string
    {
        return $this->request('GET', '/parse/', $kwargs);
    }

    /** https://docs.veryfi.com/api/parse/get-a-markdown-document/ */
    public function get_markdown_document(int $document_id, array $kwargs = []): string
    {
        return $this->request('GET', "/parse/$document_id/", $kwargs);
    }

    /** https://docs.veryfi.com/api/parse/update-a-markdown-document/ */
    public function update_markdown_document(int $document_id, array $fields_to_update): string
    {
        return $this->request('PUT', "/parse/$document_id/", $fields_to_update);
    }

    /** https://docs.veryfi.com/api/parse/delete-a-markdown-document/ */
    public function delete_markdown_document(int $document_id): string
    {
        return $this->request('DELETE', "/parse/$document_id/");
    }

    /** https://docs.veryfi.com/api/parse/process-a-markdown-document-set/ */
    public function process_markdown_document_set(array $parameters = []): string
    {
        return $this->request('POST', '/parse-set/', $parameters);
    }

    /** https://docs.veryfi.com/api/parse/get-markdown-document-sets/ */
    public function get_markdown_document_sets(array $kwargs = []): string
    {
        return $this->request('GET', '/parse-set/', $kwargs);
    }

    /** https://docs.veryfi.com/api/parse/get-a-markdown-document-set/ */
    public function get_markdown_document_set(int $document_id): string
    {
        return $this->request('GET', "/parse-set/$document_id/");
    }
}
