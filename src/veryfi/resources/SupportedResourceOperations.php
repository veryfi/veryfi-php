<?php

declare(strict_types=1);

namespace veryfi\resources;

/**
 * Operations for resources already represented by the SDK.
 *
 * Request arrays accept the complete documented API body/query contract and
 * are forwarded unchanged to the shared request implementation.
 */
trait SupportedResourceOperations
{
    /** https://docs.veryfi.com/api/get-blueprints/ */
    public function get_blueprints(array $kwargs = []): string
    {
        return $this->request('GET', '/blueprints/', $kwargs);
    }

    /** https://docs.veryfi.com/api/anydocs/update-a-A-doc/ */
    public function update_any_document(int $document_id, array $fields_to_update): string
    {
        return $this->request('PUT', "/any-documents/$document_id/", $fields_to_update);
    }

    /** https://docs.veryfi.com/api/anydocs/process-a-A-doc-asynchronously/ */
    public function process_any_document_async(array $parameters): string
    {
        return $this->request('POST', '/any-documents/async/', $parameters);
    }

    /** https://docs.veryfi.com/api/anydocs/add-a-tag-to-a-A-doc/ */
    public function add_any_document_tag(int $document_id, string $tag): string
    {
        return $this->request('PUT', "/any-documents/$document_id/tags/", ['name' => $tag]);
    }

    /** https://docs.veryfi.com/api/anydocs/add-tags-to-a-A-doc/ */
    public function add_any_document_tags(int $document_id, array $tags): string
    {
        return $this->request('POST', "/any-documents/$document_id/tags/", ['tags' => $tags]);
    }

    /** https://docs.veryfi.com/api/anydocs/get-A-doc-tags/ */
    public function get_any_document_tags(int $document_id): string
    {
        return $this->request('GET', "/any-documents/$document_id/tags/");
    }

    /** https://docs.veryfi.com/api/anydocs/unlink-all-tags-from-a-A-doc/ */
    public function delete_any_document_tags(int $document_id): string
    {
        return $this->request('DELETE', "/any-documents/$document_id/tags/");
    }

    /** https://docs.veryfi.com/api/anydocs/unlink-a-tag-from-a-A-doc/ */
    public function delete_any_document_tag(int $document_id, int $tag_id): string
    {
        return $this->request('DELETE', "/any-documents/$document_id/tags/$tag_id/");
    }

    /** https://docs.veryfi.com/api/bank-statements/update-a-bank-statement/ */
    public function update_bank_statement(int $document_id, array $fields_to_update): string
    {
        return $this->request('PUT', "/bank-statements/$document_id/", $fields_to_update);
    }

    /** https://docs.veryfi.com/api/bank-statements/process-a-bank-statement-asynchronously/ */
    public function process_bank_statement_async(array $parameters): string
    {
        return $this->request('POST', '/bank-statements/async/', $parameters);
    }

    /** https://docs.veryfi.com/api/get-bank-statement-sets/ */
    public function get_bank_statement_sets(array $kwargs = []): string
    {
        return $this->request('GET', '/bank-statements-set/', $kwargs);
    }

    /** https://docs.veryfi.com/api/get-a-bank-statement-set/ */
    public function get_bank_statement_set(int $document_id): string
    {
        return $this->request('GET', "/bank-statements-set/$document_id/");
    }

    /** https://docs.veryfi.com/api/split-and-process-multiple-bank-statements/ */
    public function process_bank_statement_set(array $parameters): string
    {
        return $this->request('POST', '/bank-statements-set/', $parameters);
    }

    /** https://docs.veryfi.com/api/bank-statements/add-a-tag-to-a-bank-statement/ */
    public function add_bank_statement_tag(int $document_id, string $tag): string
    {
        return $this->request('PUT', "/bank-statements/$document_id/tags/", ['name' => $tag]);
    }

    /** https://docs.veryfi.com/api/bank-statements/add-tags-to-a-bank-statement/ */
    public function add_bank_statement_tags(int $document_id, array $tags): string
    {
        return $this->request('POST', "/bank-statements/$document_id/tags/", ['tags' => $tags]);
    }

    /** https://docs.veryfi.com/api/bank-statements/get-bank-statement-tags/ */
    public function get_bank_statement_tags(int $document_id): string
    {
        return $this->request('GET', "/bank-statements/$document_id/tags/");
    }

    /** https://docs.veryfi.com/api/bank-statements/unlink-all-tags-from-a-bank-statement/ */
    public function delete_bank_statement_tags(int $document_id): string
    {
        return $this->request('DELETE', "/bank-statements/$document_id/tags/");
    }

    /** https://docs.veryfi.com/api/bank-statements/unlink-a-tag-from-a-bank-statement/ */
    public function delete_bank_statement_tag(int $document_id, int $tag_id): string
    {
        return $this->request('DELETE', "/bank-statements/$document_id/tags/$tag_id/");
    }

    /** https://docs.veryfi.com/api/business-cards/update-a-business-card/ */
    public function update_business_card(int $document_id, array $fields_to_update): string
    {
        return $this->request('PUT', "/business-cards/$document_id/", $fields_to_update);
    }

    /** https://docs.veryfi.com/api/add-a-tag-to-a-business-card/ */
    public function add_business_card_tag(int $document_id, string $tag): string
    {
        return $this->request('PUT', "/business-cards/$document_id/tags/", ['name' => $tag]);
    }

    /** https://docs.veryfi.com/api/add-tags-to-a-business-card/ */
    public function add_business_card_tags(int $document_id, array $tags): string
    {
        return $this->request('POST', "/business-cards/$document_id/tags/", ['tags' => $tags]);
    }

    /** https://docs.veryfi.com/api/get-business-card-tags/ */
    public function get_business_card_tags(int $document_id): string
    {
        return $this->request('GET', "/business-cards/$document_id/tags/");
    }

    /** https://docs.veryfi.com/api/unlink-all-tags-from-a-business-card/ */
    public function delete_business_card_tags(int $document_id): string
    {
        return $this->request('DELETE', "/business-cards/$document_id/tags/");
    }

    /** https://docs.veryfi.com/api/unlink-a-tag-from-a-business-card/ */
    public function delete_business_card_tag(int $document_id, int $tag_id): string
    {
        return $this->request('DELETE', "/business-cards/$document_id/tags/$tag_id/");
    }

    /** https://docs.veryfi.com/api/checks/update-a-check/ */
    public function update_check(int $document_id, array $fields_to_update): string
    {
        return $this->request('PUT', "/checks/$document_id/", $fields_to_update);
    }

    /** https://docs.veryfi.com/api/checks/process-a-check-asynchronously/ */
    public function process_check_async(array $parameters): string
    {
        return $this->request('POST', '/checks/async/', $parameters);
    }

    /** https://docs.veryfi.com/api/checks/process-a-check-with-remittance/ */
    public function process_check_with_remittance(array $parameters): string
    {
        return $this->request('POST', '/check-with-document/', $parameters);
    }

    /** https://docs.veryfi.com/api/checks/add-a-tag-to-a-check/ */
    public function add_check_tag(int $document_id, string $tag): string
    {
        return $this->request('PUT', "/checks/$document_id/tags/", ['name' => $tag]);
    }

    /** https://docs.veryfi.com/api/checks/add-tags-to-a-check/ */
    public function add_check_tags(int $document_id, array $tags): string
    {
        return $this->request('POST', "/checks/$document_id/tags/", ['tags' => $tags]);
    }

    /** https://docs.veryfi.com/api/checks/get-check-tags/ */
    public function get_check_tags(int $document_id): string
    {
        return $this->request('GET', "/checks/$document_id/tags/");
    }

    /** https://docs.veryfi.com/api/checks/unlink-all-tags-from-a-check/ */
    public function delete_check_tags(int $document_id): string
    {
        return $this->request('DELETE', "/checks/$document_id/tags/");
    }

    /** https://docs.veryfi.com/api/checks/unlink-a-tag-from-a-check/ */
    public function delete_check_tag(int $document_id, int $tag_id): string
    {
        return $this->request('DELETE', "/checks/$document_id/tags/$tag_id/");
    }

    /** https://docs.veryfi.com/api/receipts-invoices/bulk-process-multiple-documents/ */
    public function process_documents_bulk(array $file_urls): string
    {
        return $this->request('POST', '/documents/bulk/', ['file_urls' => $file_urls]);
    }

    /** https://docs.veryfi.com/api/returns-a-list-of-document-tax-lines/ */
    public function get_tax_lines(int $document_id): string
    {
        return $this->request('GET', "/documents/$document_id/tax-lines/");
    }

    /** https://docs.veryfi.com/api/returns-document-tax-line/ */
    public function get_tax_line(int $document_id, int $tax_line_id): string
    {
        return $this->request('GET', "/documents/$document_id/tax-lines/$tax_line_id/");
    }

    /** https://docs.veryfi.com/api/create-a-tax-line/ */
    public function add_tax_line(int $document_id, array $tax_line): string
    {
        return $this->request('POST', "/documents/$document_id/tax-lines/", $tax_line);
    }

    /** https://docs.veryfi.com/api/update-a-tax-line/ */
    public function update_tax_line(int $document_id, int $tax_line_id, array $tax_line): string
    {
        return $this->request('PUT', "/documents/$document_id/tax-lines/$tax_line_id/", $tax_line);
    }

    /** https://docs.veryfi.com/api/delete-a-tax-line/ */
    public function delete_tax_line(int $document_id, int $tax_line_id): string
    {
        return $this->request('DELETE', "/documents/$document_id/tax-lines/$tax_line_id/");
    }

    /** https://docs.veryfi.com/api/w2s/update-a-w-2/ */
    public function update_w2(int $document_id, array $fields_to_update): string
    {
        return $this->request('PUT', "/w2s/$document_id/", $fields_to_update);
    }

    /** https://docs.veryfi.com/api/get-w-2-sets/ */
    public function get_w2_sets(array $kwargs = []): string
    {
        return $this->request('GET', '/w2s-set/', $kwargs);
    }

    /** https://docs.veryfi.com/api/get-a-w-2-set/ */
    public function get_w2_set(int $document_id): string
    {
        return $this->request('GET', "/w2s-set/$document_id/");
    }

    /** https://docs.veryfi.com/api/split-and-process-a-pdf-with-multiple-w-2-s/ */
    public function process_w2_set(array $parameters): string
    {
        return $this->request('POST', '/w2s-set/', $parameters);
    }

    /** https://docs.veryfi.com/api/add-a-tag-to-a-w-2/ */
    public function add_w2_tag(int $document_id, string $tag): string
    {
        return $this->request('PUT', "/w2s/$document_id/tags/", ['name' => $tag]);
    }

    /** https://docs.veryfi.com/api/add-tags-to-a-w-2/ */
    public function add_w2_tags(int $document_id, array $tags): string
    {
        return $this->request('POST', "/w2s/$document_id/tags/", ['tags' => $tags]);
    }

    /** https://docs.veryfi.com/api/get-w-2-tags/ */
    public function get_w2_tags(int $document_id): string
    {
        return $this->request('GET', "/w2s/$document_id/tags/");
    }

    /** https://docs.veryfi.com/api/unlink-all-tags-from-a-w-2/ */
    public function delete_w2_tags(int $document_id): string
    {
        return $this->request('DELETE', "/w2s/$document_id/tags/");
    }

    /** https://docs.veryfi.com/api/unlink-a-tag-from-a-w-2/ */
    public function delete_w2_tag(int $document_id, int $tag_id): string
    {
        return $this->request('DELETE', "/w2s/$document_id/tags/$tag_id/");
    }

    /** https://docs.veryfi.com/api/w-8ben-e/update-a-w-8-ben-e/ */
    public function update_w8bene(int $document_id, array $fields_to_update): string
    {
        return $this->request('PUT', "/w-8ben-e/$document_id/", $fields_to_update);
    }

    /** https://docs.veryfi.com/api/add-a-tag-to-a-w-8-ben-e/ */
    public function add_w8bene_tag(int $document_id, string $tag): string
    {
        return $this->request('PUT', "/w-8ben-e/$document_id/tags/", ['name' => $tag]);
    }

    /** https://docs.veryfi.com/api/add-tags-to-a-w-8-ben-e/ */
    public function add_w8bene_tags(int $document_id, array $tags): string
    {
        return $this->request('POST', "/w-8ben-e/$document_id/tags/", ['tags' => $tags]);
    }

    /** https://docs.veryfi.com/api/get-w-8-ben-e-tags/ */
    public function get_w8bene_tags(int $document_id): string
    {
        return $this->request('GET', "/w-8ben-e/$document_id/tags/");
    }

    /** https://docs.veryfi.com/api/unlink-all-tags-from-a-w-8-ben-e/ */
    public function delete_w8bene_tags(int $document_id): string
    {
        return $this->request('DELETE', "/w-8ben-e/$document_id/tags/");
    }

    /** https://docs.veryfi.com/api/unlink-a-tag-from-a-w-8-ben-e/ */
    public function delete_w8bene_tag(int $document_id, int $tag_id): string
    {
        return $this->request('DELETE', "/w-8ben-e/$document_id/tags/$tag_id/");
    }

    /** https://docs.veryfi.com/api/w9s/update-a-w-9/ */
    public function update_w9(int $document_id, array $fields_to_update): string
    {
        return $this->request('PUT', "/w9s/$document_id/", $fields_to_update);
    }

    /** https://docs.veryfi.com/api/add-a-tag-to-a-w-9/ */
    public function add_w9_tag(int $document_id, string $tag): string
    {
        return $this->request('PUT', "/w9s/$document_id/tags/", ['name' => $tag]);
    }

    /** https://docs.veryfi.com/api/add-tags-to-a-w-9/ */
    public function add_w9_tags(int $document_id, array $tags): string
    {
        return $this->request('POST', "/w9s/$document_id/tags/", ['tags' => $tags]);
    }

    /** https://docs.veryfi.com/api/get-w-9-tags/ */
    public function get_w9_tags(int $document_id): string
    {
        return $this->request('GET', "/w9s/$document_id/tags/");
    }

    /** https://docs.veryfi.com/api/unlink-all-tags-from-a-w-9/ */
    public function delete_w9_tags(int $document_id): string
    {
        return $this->request('DELETE', "/w9s/$document_id/tags/");
    }

    /** https://docs.veryfi.com/api/unlink-a-tag-from-a-w-9/ */
    public function delete_w9_tag(int $document_id, int $tag_id): string
    {
        return $this->request('DELETE', "/w9s/$document_id/tags/$tag_id/");
    }
}
