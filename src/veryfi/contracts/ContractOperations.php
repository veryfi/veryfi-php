<?php

declare(strict_types=1);

namespace veryfi\contracts;

/**
 * Contract extraction and management operations.
 */
trait ContractOperations
{
    /** https://docs.veryfi.com/api/contracts/process-a-contract/ */
    public function process_contract(array $parameters): string
    {
        return $this->request('POST', '/contracts/', $parameters);
    }

    /** https://docs.veryfi.com/api/contracts/get-contracts/ */
    public function get_contracts(array $kwargs = []): string
    {
        return $this->request('GET', '/contracts/', $kwargs);
    }

    /** https://docs.veryfi.com/api/contracts/get-a-contract/ */
    public function get_contract(int $document_id, array $kwargs = []): string
    {
        return $this->request('GET', "/contracts/$document_id/", $kwargs);
    }

    /** https://docs.veryfi.com/api/contracts/update-a-contract/ */
    public function update_contract(int $document_id, array $fields_to_update): string
    {
        return $this->request('PUT', "/contracts/$document_id/", $fields_to_update);
    }

    /** https://docs.veryfi.com/api/contracts/delete-a-contract/ */
    public function delete_contract(int $document_id): string
    {
        return $this->request('DELETE', "/contracts/$document_id/");
    }

    /** https://docs.veryfi.com/api/add-a-tag-to-a-contract/ */
    public function add_contract_tag(int $document_id, string $tag): string
    {
        return $this->request('PUT', "/contracts/$document_id/tags/", ['name' => $tag]);
    }

    /** https://docs.veryfi.com/api/add-tags-to-a-contract/ */
    public function add_contract_tags(int $document_id, array $tags): string
    {
        return $this->request('POST', "/contracts/$document_id/tags/", ['tags' => $tags]);
    }

    /** https://docs.veryfi.com/api/get-contract-tags/ */
    public function get_contract_tags(int $document_id): string
    {
        return $this->request('GET', "/contracts/$document_id/tags/");
    }

    /** https://docs.veryfi.com/api/unlink-all-tags-from-a-contract/ */
    public function delete_contract_tags(int $document_id): string
    {
        return $this->request('DELETE', "/contracts/$document_id/tags/");
    }

    /** https://docs.veryfi.com/api/unlink-a-tag-from-a-contract/ */
    public function delete_contract_tag(int $document_id, int $tag_id): string
    {
        return $this->request('DELETE', "/contracts/$document_id/tags/$tag_id/");
    }
}
