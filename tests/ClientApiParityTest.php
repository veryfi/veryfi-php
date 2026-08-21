<?php

declare(strict_types=1);

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use veryfi\Client;
use veryfi\documents\lineitems\LineItem;
use veryfi\documents\lineitems\LineItemUpdate;

class ClientApiParityTest extends TestCase
{
    #[DataProvider('newOperationProvider')]
    public function test_new_operation_contract(
        string $method,
        array $method_arguments,
        array $request_arguments
    ): void {
        $client = $this->getMockBuilder(Client::class)
            ->onlyMethods(['request'])
            ->setConstructorArgs(['client', 'secret', 'user', 'key'])
            ->getMock();

        $client->expects($this->once())
            ->method('request')
            ->with(...$request_arguments)
            ->willReturn('{"ok":true}');

        $this->assertSame('{"ok":true}', $client->$method(...$method_arguments));
    }

    public static function newOperationProvider(): iterable
    {
        yield 'get blueprints' => ['get_blueprints', [['page' => 2]], ['GET', '/blueprints/', ['page' => 2]]];
        yield 'update any document' => ['update_any_document', [1, ['external_id' => 'x']], ['PUT', '/any-documents/1/', ['external_id' => 'x']]];
        yield 'process any document async' => ['process_any_document_async', [['file_url' => 'https://example.test/a.pdf']], ['POST', '/any-documents/async/', ['file_url' => 'https://example.test/a.pdf']]];
        yield from self::tagCases('any document', 'any_document', '/any-documents');

        yield 'update bank statement' => ['update_bank_statement', [1, ['currency_code' => 'USD']], ['PUT', '/bank-statements/1/', ['currency_code' => 'USD']]];
        yield 'process bank statement async' => ['process_bank_statement_async', [['file_data' => 'abc']], ['POST', '/bank-statements/async/', ['file_data' => 'abc']]];
        yield 'get bank statement sets' => ['get_bank_statement_sets', [['page' => 2]], ['GET', '/bank-statements-set/', ['page' => 2]]];
        yield 'get bank statement set' => ['get_bank_statement_set', [1], ['GET', '/bank-statements-set/1/']];
        yield 'process bank statement set' => ['process_bank_statement_set', [['file_url' => 'https://example.test/a.pdf']], ['POST', '/bank-statements-set/', ['file_url' => 'https://example.test/a.pdf']]];
        yield from self::tagCases('bank statement', 'bank_statement', '/bank-statements');

        yield 'update business card' => ['update_business_card', [1, ['external_id' => 'x']], ['PUT', '/business-cards/1/', ['external_id' => 'x']]];
        yield from self::tagCases('business card', 'business_card', '/business-cards');

        yield 'update check' => ['update_check', [1, ['memo' => 'invoice']], ['PUT', '/checks/1/', ['memo' => 'invoice']]];
        yield 'process check async' => ['process_check_async', [['file_url' => 'https://example.test/check.jpg']], ['POST', '/checks/async/', ['file_url' => 'https://example.test/check.jpg']]];
        yield 'process check with remittance' => ['process_check_with_remittance', [['file_data' => 'abc']], ['POST', '/check-with-document/', ['file_data' => 'abc']]];
        yield from self::tagCases('check', 'check', '/checks');

        yield 'bulk process documents' => ['process_documents_bulk', [['https://example.test/1.pdf']], ['POST', '/documents/bulk/', ['file_urls' => ['https://example.test/1.pdf']]]];
        yield 'get tax lines' => ['get_tax_lines', [1], ['GET', '/documents/1/tax-lines/']];
        yield 'get tax line' => ['get_tax_line', [1, 2], ['GET', '/documents/1/tax-lines/2/']];
        yield 'add tax line' => ['add_tax_line', [1, ['order' => 1]], ['POST', '/documents/1/tax-lines/', ['order' => 1]]];
        yield 'update tax line' => ['update_tax_line', [1, 2, ['order' => 2]], ['PUT', '/documents/1/tax-lines/2/', ['order' => 2]]];
        yield 'delete tax line' => ['delete_tax_line', [1, 2], ['DELETE', '/documents/1/tax-lines/2/']];

        yield 'update w2' => ['update_w2', [1, ['external_id' => 'x']], ['PUT', '/w2s/1/', ['external_id' => 'x']]];
        yield 'get w2 sets' => ['get_w2_sets', [['page' => 2]], ['GET', '/w2s-set/', ['page' => 2]]];
        yield 'get w2 set' => ['get_w2_set', [1], ['GET', '/w2s-set/1/']];
        yield 'process w2 set' => ['process_w2_set', [['file_data' => 'abc']], ['POST', '/w2s-set/', ['file_data' => 'abc']]];
        yield from self::tagCases('w2', 'w2', '/w2s');

        yield 'update w8bene' => ['update_w8bene', [1, ['external_id' => 'x']], ['PUT', '/w-8ben-e/1/', ['external_id' => 'x']]];
        yield from self::tagCases('w8bene', 'w8bene', '/w-8ben-e');

        yield 'update w9' => ['update_w9', [1, ['name' => 'Acme']], ['PUT', '/w9s/1/', ['name' => 'Acme']]];
        yield from self::tagCases('w9', 'w9', '/w9s');

        yield 'process contract' => ['process_contract', [['file_url' => 'https://example.test/a.pdf']], ['POST', '/contracts/', ['file_url' => 'https://example.test/a.pdf']]];
        yield 'get contracts' => ['get_contracts', [['page' => 2]], ['GET', '/contracts/', ['page' => 2]]];
        yield 'get contract' => ['get_contract', [1, ['bounding_boxes' => true]], ['GET', '/contracts/1/', ['bounding_boxes' => true]]];
        yield 'update contract' => ['update_contract', [1, ['external_id' => 'x']], ['PUT', '/contracts/1/', ['external_id' => 'x']]];
        yield 'delete contract' => ['delete_contract', [1], ['DELETE', '/contracts/1/']];
        yield from self::tagCases('contract', 'contract', '/contracts');

        yield 'process markdown' => ['process_markdown_document', [['file_url' => 'https://example.test/a.pdf']], ['POST', '/parse/', ['file_url' => 'https://example.test/a.pdf']]];
        yield 'process markdown async' => ['process_markdown_document_async', [['file_data' => 'abc']], ['POST', '/parse/async/', ['file_data' => 'abc']]];
        yield 'get markdown documents' => ['get_markdown_documents', [['page' => 2]], ['GET', '/parse/', ['page' => 2]]];
        yield 'get markdown document' => ['get_markdown_document', [1, ['bounding_boxes' => true]], ['GET', '/parse/1/', ['bounding_boxes' => true]]];
        yield 'update markdown document' => ['update_markdown_document', [1, ['status' => 'processed']], ['PUT', '/parse/1/', ['status' => 'processed']]];
        yield 'delete markdown document' => ['delete_markdown_document', [1], ['DELETE', '/parse/1/']];
        yield 'process markdown set' => ['process_markdown_document_set', [[]], ['POST', '/parse-set/', []]];
        yield 'get markdown sets' => ['get_markdown_document_sets', [['page' => 2]], ['GET', '/parse-set/', ['page' => 2]]];
        yield 'get markdown set' => ['get_markdown_document_set', [1], ['GET', '/parse-set/1/']];

        yield 'extract document' => ['extract_document', [['document_types' => ['invoice'], 'file_data' => 'abc']], ['POST', '/extract/', ['document_types' => ['invoice'], 'file_data' => 'abc']]];
        yield 'get blocklist' => ['get_blocklisted_devices', [['page' => 2]], ['GET', '/fraud/blocklist/', ['page' => 2]]];
        yield 'add blocklist devices' => ['add_blocklisted_devices', [['device-1']], ['POST', '/fraud/blocklist/', ['device_ids' => ['device-1']]]];
        yield 'remove blocklist device' => ['remove_blocklisted_device', ['device-1'], ['DELETE', '/fraud/blocklist/device-1/']];

        yield 'get webhooks' => ['get_webhooks', [], ['GET', '/settings/webhooks/']];
        yield 'add webhook' => ['add_webhook', ['https://example.test/hook'], ['POST', '/settings/webhooks/', ['url' => 'https://example.test/hook']]];
        yield 'confirm webhook' => ['confirm_webhook', ['https://example.test/hook', 'secret'], ['POST', '/settings/webhooks/confirm/', ['url' => 'https://example.test/hook', 'secret' => 'secret']]];
        yield 'get client keys' => ['get_client_keys', [], ['GET', '/client-keys/']];
        yield 'create client keys' => ['create_client_keys', [], ['POST', '/client-keys/']];
        yield 'delete client key' => ['delete_client_key', [1], ['DELETE', '/client-keys/1/']];
        yield 'reset client keys' => ['reset_client_keys', [], ['POST', '/client-keys/reset/']];
        yield 'get api keys' => ['get_api_keys', [['include_archived' => true]], ['GET', '/settings/api-keys/', ['include_archived' => true], false, 'v1']];
        yield 'create api key' => ['create_api_key', ['key', ['full_access' => true]], ['POST', '/settings/api-keys/', ['name' => 'key', 'full_access' => true], false, 'v1']];
        yield 'get api key' => ['get_api_key', [1], ['GET', '/settings/api-keys/1/', [], false, 'v1']];
        yield 'update api key' => ['update_api_key', [1, ['name' => 'updated']], ['PUT', '/settings/api-keys/1/', ['name' => 'updated'], false, 'v1']];
        yield 'revoke api key' => ['revoke_api_key', [1], ['DELETE', '/settings/api-keys/1/', [], false, 'v1']];
        yield 'rotate api key' => ['rotate_api_key', [1], ['POST', '/settings/api-keys/1/rotate/', [], false, 'v1']];
        yield 'get api key permissions' => ['get_api_key_permissions', [], ['GET', '/settings/api-keys/available-permissions/', [], false, 'v1']];
        yield 'verify api key' => ['verify_api_key', [], ['GET', '/settings/api-keys/verify/', [], false, 'v1']];
        yield 'get tls certificates' => ['get_tls_certificates', [], ['GET', '/settings/tls-certificate/']];
        yield 'create tls certificate' => ['create_tls_certificate', [['certificate' => 'pem']], ['POST', '/settings/tls-certificate/', ['certificate' => 'pem']]];
        yield 'delete tls certificate' => ['delete_tls_certificate', [1], ['DELETE', '/settings/tls-certificate/1/']];
        yield 'get ocr counts' => ['get_ocr_counts', [['ocr_type' => 'pepsico_codes']], ['GET', '/ocr-counts/', ['ocr_type' => 'pepsico_codes']]];
        yield 'get openapi schema' => ['get_openapi_schema', [], ['GET', '/documents/schema/']];
        yield 'get release notifications' => ['get_release_notifications', [['product' => 'api']], ['GET', '/release-notifications/', ['product' => 'api'], false, 'v1', false]];
    }

    private static function tagCases(string $label, string $method_resource, string $route): iterable
    {
        yield "add $label tag" => ["add_{$method_resource}_tag", [1, 'tag'], ['PUT', "$route/1/tags/", ['name' => 'tag']]];
        yield "add $label tags" => ["add_{$method_resource}_tags", [1, ['a', 'b']], ['POST', "$route/1/tags/", ['tags' => ['a', 'b']]]];
        yield "get $label tags" => ["get_{$method_resource}_tags", [1], ['GET', "$route/1/tags/"]];
        yield "delete all $label tags" => ["delete_{$method_resource}_tags", [1], ['DELETE', "$route/1/tags/"]];
        yield "delete $label tag" => ["delete_{$method_resource}_tag", [1, 2], ['DELETE', "$route/1/tags/2/"]];
    }

    public function test_get_parameters_are_encoded_in_the_query_string(): void
    {
        $client = $this->getMockBuilder(Client::class)
            ->onlyMethods(['exec_curl'])
            ->setConstructorArgs(['client', 'secret', 'user', 'key'])
            ->getMock();

        $client->expects($this->once())
            ->method('exec_curl')
            ->willReturnCallback(function ($curl): string {
                $url = curl_getinfo($curl, CURLINFO_EFFECTIVE_URL);
                $this->assertStringContainsString('page=2', $url);
                $this->assertStringContainsString('tag=needs%20review', $url);
                return '[]';
            });

        $this->assertSame('[]', $client->get_documents([
            'page' => 2,
            'tag' => 'needs review',
        ]));
    }

    public function test_anydocs_uses_documented_blueprint_name(): void
    {
        $client = $this->getMockBuilder(Client::class)
            ->onlyMethods(['request'])
            ->setConstructorArgs(['client', 'secret', 'user', 'key'])
            ->getMock();

        $client->expects($this->once())
            ->method('request')
            ->with(
                'POST',
                '/any-documents/',
                $this->callback(function (array $body): bool {
                    return $body['blueprint_name'] === 'driver-license'
                        && !array_key_exists('template_name', $body);
                }),
                true
            )
            ->willReturn('{}');

        $client->process_any_document(__DIR__ . '/resources/driver_license.png', 'driver-license');
    }

    #[DataProvider('multipartRegressionProvider')]
    public function test_local_file_helpers_use_multipart(string $method, array $arguments, string $route): void
    {
        $client = $this->getMockBuilder(Client::class)
            ->onlyMethods(['request'])
            ->setConstructorArgs(['client', 'secret', 'user', 'key'])
            ->getMock();

        $client->expects($this->once())
            ->method('request')
            ->with(
                'POST',
                $route,
                $this->callback(fn(array $body): bool => $body['file'] instanceof CURLFile),
                true
            )
            ->willReturn('{}');

        $client->$method(...$arguments);
    }

    public static function multipartRegressionProvider(): iterable
    {
        $receipt = __DIR__ . '/resources/receipt.jpg';
        $w2 = __DIR__ . '/resources/w2.png';
        yield 'W-2' => ['process_w2', [$w2], '/w2s/'];
        yield 'W-9' => ['process_w9', [$receipt], '/w9s/'];
        yield 'classify' => ['classify_document', [$receipt], '/classify/'];
        yield 'split' => ['split_document', [__DIR__ . '/resources/bankstatement.pdf'], '/documents-set/'];
    }

    public function test_line_item_models_accept_current_documented_fields(): void
    {
        $line_item = new LineItem([
            'order' => 1,
            'description' => ['value' => 'Item'],
            'total' => ['value' => 10.0],
            'expanded_description' => 'Expanded item',
            'brand' => 'Veryfi',
            'category' => ['Supplies'],
            'tags' => ['reviewed'],
            'country_of_origin' => ['value' => 'US'],
            'discount_price' => ['value' => 8.0],
            'lot' => ['value' => 'LOT-1'],
            'product_info' => ['brand' => 'Veryfi'],
            'tax_code' => ['value' => 'VAT'],
            'manufacturer' => ['value' => 'Veryfi'],
            'subtotal' => ['value' => 8.0],
            'type' => ['value' => 'product'],
        ]);
        $update = new LineItemUpdate([
            'brand' => 'Updated',
            'tags' => ['approved'],
        ]);

        $this->assertSame(['Supplies'], $line_item->category);
        $this->assertSame(['reviewed'], $line_item->tags);
        $this->assertSame(['value' => 'Item'], $line_item->description);
        $this->assertSame(['value' => 'VAT'], $line_item->tax_code);
        $this->assertSame('Updated', $update->brand);
        $this->assertSame(['approved'], $update->tags);
    }
}
