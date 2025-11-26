<?php

use PHPUnit\Framework\TestCase;
use veryfi\Client;
use veryfi\documents\lineitems\LineItem;
use veryfi\documents\lineitems\LineItemUpdate;

class ClientCoverageTest extends TestCase
{
    public function test_line_item_valid_init(): void
    {
        $data = ['order' => 1, 'description' => 'Test Item', 'total' => 10.0];
        $line_item = new LineItem($data);
        $this->assertEquals(1, $line_item->order);
        $this->assertEquals('Test Item', $line_item->description);
        $this->assertEquals(10.0, $line_item->total);
    }

    public function test_line_item_invalid_init(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Bad Argument');
        $data = ['invalid_field' => 'value'];
        new LineItem($data);
    }

    public function test_line_item_update_valid_init(): void
    {
        $data = ['order' => 1, 'description' => 'Updated Item'];
        $line_item_update = new LineItemUpdate($data);
        $this->assertEquals(1, $line_item_update->order);
        $this->assertEquals('Updated Item', $line_item_update->description);
        $this->assertNull($line_item_update->total);
    }

    public function test_line_item_update_invalid_init(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Bad Argument');
        $data = ['invalid_field' => 'value'];
        new LineItemUpdate($data);
    }

    public function test_generate_signature(): void
    {
        $client_id = 'test_id';
        $client_secret = 'test_secret';
        $username = 'test_user';
        $api_key = 'test_key';

        $client = new Client($client_id, $client_secret, $username, $api_key);

        $reflection = new ReflectionClass(Client::class);
        $method = $reflection->getMethod('generate_signature');
        $method->setAccessible(true);

        $timestamp = '1234567890';


        $payload_params = ['key' => 'value'];
        $signature = $method->invoke($client, $payload_params, $timestamp);
        $this->assertNotEmpty($signature);


        $payload_params = ['key' => ['nested' => 'value']];
        $signature = $method->invoke($client, $payload_params, $timestamp);
        $this->assertNotEmpty($signature);


        $obj = new stdClass();
        $obj->prop = 'value';
        $payload_params = ['key' => $obj];
        $signature = $method->invoke($client, $payload_params, $timestamp);
        $this->assertNotEmpty($signature);


        $payload_params = ['key' => 123];
        $signature = $method->invoke($client, $payload_params, $timestamp);
        $this->assertNotEmpty($signature);
    }
}
