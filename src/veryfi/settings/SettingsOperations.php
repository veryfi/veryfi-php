<?php

declare(strict_types=1);

namespace veryfi\settings;

/**
 * API settings, credentials, webhooks, TLS, schema, and usage operations.
 */
trait SettingsOperations
{
    /** https://docs.veryfi.com/api/settings/get-webhooks/ */
    public function get_webhooks(): string
    {
        return $this->request('GET', '/settings/webhooks/');
    }

    /** https://docs.veryfi.com/api/settings/add-a-webhook/ */
    public function add_webhook(string $url): string
    {
        return $this->request('POST', '/settings/webhooks/', ['url' => $url]);
    }

    /** https://docs.veryfi.com/api/settings/confirm-a-webhook/ */
    public function confirm_webhook(string $url, string $secret): string
    {
        return $this->request('POST', '/settings/webhooks/confirm/', [
            'url' => $url,
            'secret' => $secret,
        ]);
    }

    /** https://docs.veryfi.com/api/settings/retrieve-client-keys-list/ */
    public function get_client_keys(): string
    {
        return $this->request('GET', '/client-keys/');
    }

    /** https://docs.veryfi.com/api/settings/create-client-keys/ */
    public function create_client_keys(): string
    {
        return $this->request('POST', '/client-keys/');
    }

    /** https://docs.veryfi.com/api/settings/remove-a-client-key/ */
    public function delete_client_key(int $id): string
    {
        return $this->request('DELETE', "/client-keys/$id/");
    }

    /** https://docs.veryfi.com/api/settings/reset-client-keys/ */
    public function reset_client_keys(): string
    {
        return $this->request('POST', '/client-keys/reset/');
    }

    /** https://docs.veryfi.com/api/settings/retrieve-api-keys-list/ */
    public function get_api_keys(array $kwargs = []): string
    {
        return $this->request('GET', '/settings/api-keys/', $kwargs, false, 'v1');
    }

    /** https://docs.veryfi.com/api/settings/create-api-key/ */
    public function create_api_key(string $name, array $parameters = []): string
    {
        return $this->request(
            'POST',
            '/settings/api-keys/',
            array_replace(['name' => $name], $parameters),
            false,
            'v1'
        );
    }

    /** https://docs.veryfi.com/api/settings/retrieve-api-key/ */
    public function get_api_key(int $id): string
    {
        return $this->request('GET', "/settings/api-keys/$id/", [], false, 'v1');
    }

    /** https://docs.veryfi.com/api/settings/update-api-key/ */
    public function update_api_key(int $id, array $fields_to_update): string
    {
        return $this->request('PUT', "/settings/api-keys/$id/", $fields_to_update, false, 'v1');
    }

    /** https://docs.veryfi.com/api/settings/revoke-api-key/ */
    public function revoke_api_key(int $id): string
    {
        return $this->request('DELETE', "/settings/api-keys/$id/", [], false, 'v1');
    }

    /** https://docs.veryfi.com/api/settings/rotate-api-key/ */
    public function rotate_api_key(int $id): string
    {
        return $this->request('POST', "/settings/api-keys/$id/rotate/", [], false, 'v1');
    }

    /** https://docs.veryfi.com/api/settings/available-permissions/ */
    public function get_api_key_permissions(): string
    {
        return $this->request('GET', '/settings/api-keys/available-permissions/', [], false, 'v1');
    }

    /** https://docs.veryfi.com/api/settings/verify-the-calling-key/ */
    public function verify_api_key(): string
    {
        return $this->request('GET', '/settings/api-keys/verify/', [], false, 'v1');
    }

    /** https://docs.veryfi.com/api/get-tls-certificates/ */
    public function get_tls_certificates(): string
    {
        return $this->request('GET', '/settings/tls-certificate/');
    }

    /** https://docs.veryfi.com/api/process-a-tls-certificate/ */
    public function create_tls_certificate(array $parameters = []): string
    {
        return $this->request('POST', '/settings/tls-certificate/', $parameters);
    }

    /** https://docs.veryfi.com/api/delete-a-tls-certificate/ */
    public function delete_tls_certificate(int $certificate_id): string
    {
        return $this->request('DELETE', "/settings/tls-certificate/$certificate_id/");
    }

    /** https://docs.veryfi.com/api/get-ocr-counts/ */
    public function get_ocr_counts(array $kwargs = []): string
    {
        return $this->request('GET', '/ocr-counts/', $kwargs);
    }

    /** https://docs.veryfi.com/api/get-open-api-schema/ */
    public function get_openapi_schema(): string
    {
        return $this->request('GET', '/documents/schema/');
    }

    /** https://docs.veryfi.com/api/get-release-notifications/ */
    public function get_release_notifications(array $kwargs = []): string
    {
        return $this->request('GET', '/release-notifications/', $kwargs, false, 'v1', false);
    }
}
