<?php

declare(strict_types=1);

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use veryfi\Client;

class ClientThinkingTest extends TestCase
{
    private string $receipt_path = __DIR__ . '/resources/receipt.jpg';

    /** Returns a client whose request() records its arguments instead of calling the API. */
    private function recordingClient(array &$captured): Client
    {
        $client = $this->getMockBuilder(Client::class)
            ->onlyMethods(['request'])
            ->setConstructorArgs(['client', 'secret', 'user', 'key'])
            ->getMock();

        $client->expects($this->once())
            ->method('request')
            ->willReturnCallback(function (string $verb, string $endpoint, array $arguments = []) use (&$captured): string {
                $captured = ['verb' => $verb, 'endpoint' => $endpoint, 'arguments' => $arguments];
                return '{}';
            });

        return $client;
    }

    #[DataProvider('modeProvider')]
    public function test_process_document_sends_thinking(?string $mode): void
    {
        $captured = [];
        $client = $this->recordingClient($captured);
        $mode === null
            ? $client->process_document($this->receipt_path)
            : $client->process_document($this->receipt_path, thinking: $mode);

        $this->assertSame('/documents/', $captured['endpoint']);
        $this->assertSame($mode !== null, array_key_exists('thinking', $captured['arguments']));
        $this->assertSame($mode, $captured['arguments']['thinking'] ?? null);
    }

    #[DataProvider('modeProvider')]
    public function test_process_document_base64_sends_thinking(?string $mode): void
    {
        $captured = [];
        $client = $this->recordingClient($captured);
        $mode === null
            ? $client->process_document_base64($this->receipt_path)
            : $client->process_document_base64($this->receipt_path, thinking: $mode);

        $this->assertSame('/documents/', $captured['endpoint']);
        $this->assertSame($mode !== null, array_key_exists('thinking', $captured['arguments']));
        $this->assertSame($mode, $captured['arguments']['thinking'] ?? null);
    }

    #[DataProvider('modeProvider')]
    public function test_process_document_url_sends_thinking(?string $mode): void
    {
        $captured = [];
        $client = $this->recordingClient($captured);
        $url = 'https://example.test/receipt.jpg';
        $mode === null
            ? $client->process_document_url($url)
            : $client->process_document_url($url, thinking: $mode);

        $this->assertSame('/documents/', $captured['endpoint']);
        $this->assertSame($mode !== null, array_key_exists('thinking', $captured['arguments']));
        $this->assertSame($mode, $captured['arguments']['thinking'] ?? null);
    }

    public function test_kwargs_still_work_and_can_override_thinking(): void
    {
        $captured = [];
        $client = $this->recordingClient($captured);
        $client->process_document_url('https://example.test/receipt.jpg', kwargs: ['thinking' => 'deep'], thinking: 'light');

        $this->assertSame('deep', $captured['arguments']['thinking']);
    }

    public static function modeProvider(): iterable
    {
        yield 'omitted' => [null];
        yield 'light' => ['light'];
        yield 'deep' => ['deep'];
    }
}
