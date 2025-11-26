<?php

use PHPUnit\Framework\TestCase;
use veryfi\client;

class ClientTestCase extends TestCase
{
    protected string $client_id = 'your_client_id';
    protected string $client_secret = 'your_client_secret';
    protected string $username = 'your_username';
    protected string $api_key = 'your_api_key';
    protected string $receipt_path = __DIR__ . '/resources/receipt.jpg';
    protected string $w2_path = __DIR__ . '/resources/w2.png';
    protected string $any_doc_path = __DIR__ . '/resources/driver_license.png';
    protected string $bank_statement_path = __DIR__ . '/resources/bankstatement.pdf';
    protected bool $mock_responses = true;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client_id = getenv('VERYFI_CLIENT_ID');
        $this->client_secret = getenv('VERYFI_CLIENT_SECRET');
        $this->username = getenv('VERYFI_USERNAME');
        $this->api_key = getenv('VERYFI_API_KEY');
    }

    protected function generate_random_string(): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < 10; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
