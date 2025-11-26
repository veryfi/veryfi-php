<?php

use veryfi\Client;
use veryfi\documents\lineitems\LineItem;
use veryfi\documents\lineitems\LineItemUpdate;

require_once __DIR__ . '/ClientTestCase.php';

class ClientLineItemsTest extends ClientTestCase
{
    public function test_get_line_items(): void
    {
        $document_id = 44691518;
        if ($this->mock_responses) {
            $veryfi_client = $this->getMockBuilder(Client::class)
                ->onlyMethods(['exec_curl'])
                ->setConstructorArgs([$this->client_id, $this->client_secret, $this->username, $this->api_key])
                ->getMock();

            $file_path = __DIR__ . '/resources/getLineItems.json';
            $file = fopen($file_path, 'r');
            $file_data = mb_convert_encoding(fread($file, filesize($file_path)), 'UTF-8');
            $veryfi_client->expects($this->once())
                ->method('exec_curl')
                ->willReturn($file_data);
        } else {
            $veryfi_client = new Client($this->client_id, $this->client_secret, $this->username, $this->api_key);
        }
        $json_response = json_decode($veryfi_client->get_line_items($document_id), true);
        $this->assertNotEmpty($json_response['line_items']);
    }

    public function test_get_line_item(): void
    {
        $document_id = 44691518;
        $line_item_id = 101170751;
        if ($this->mock_responses) {
            $veryfi_client = $this->getMockBuilder(Client::class)
                ->onlyMethods(['exec_curl'])
                ->setConstructorArgs([$this->client_id, $this->client_secret, $this->username, $this->api_key])
                ->getMock();

            $file_path = __DIR__ . '/resources/getLineItem.json';
            $file = fopen($file_path, 'r');
            $file_data = mb_convert_encoding(fread($file, filesize($file_path)), 'UTF-8');
            $veryfi_client->expects($this->once())
                ->method('exec_curl')
                ->willReturn($file_data);
        } else {
            $veryfi_client = new Client($this->client_id, $this->client_secret, $this->username, $this->api_key);
        }
        $json_response = json_decode($veryfi_client->get_line_item($document_id, $line_item_id), true);
        $this->assertNotEmpty($json_response);
        $this->assertEquals($line_item_id, $json_response['id']);
    }

    /**
     * @throws Exception
     */
    public function test_update_line_item(): void
    {
        $document_id = 44691518;
        $line_item_id = 101170751;
        if ($this->mock_responses) {
            $veryfi_client = $this->getMockBuilder(Client::class)
                ->onlyMethods(['exec_curl'])
                ->setConstructorArgs([$this->client_id, $this->client_secret, $this->username, $this->api_key])
                ->getMock();

            $file_path = __DIR__ . '/resources/updateLineItem.json';
            $file = fopen($file_path, 'r');
            $file_data = mb_convert_encoding(fread($file, filesize($file_path)), 'UTF-8');
            $veryfi_client->expects($this->once())
                ->method('exec_curl')
                ->willReturn($file_data);
        } else {
            $veryfi_client = new Client($this->client_id, $this->client_secret, $this->username, $this->api_key);
        }
        $fields_to_update = array('description' => 'TEST');
        $fields_to_update = new LineItemUpdate($fields_to_update);
        $json_response = json_decode($veryfi_client->update_line_item($document_id, $line_item_id, $fields_to_update), true);
        $this->assertEquals('TEST', $json_response['description']);
    }

    /**
     * @throws Exception
     */
    public function test_add_line_item(): void
    {
        $document_id = 44691518;
        if ($this->mock_responses) {
            $veryfi_client = $this->getMockBuilder(Client::class)
                ->onlyMethods(['exec_curl'])
                ->setConstructorArgs([$this->client_id, $this->client_secret, $this->username, $this->api_key])
                ->getMock();

            $file_path = __DIR__ . '/resources/addLineItem.json';
            $file = fopen($file_path, 'r');
            $file_data = mb_convert_encoding(fread($file, filesize($file_path)), 'UTF-8');
            $veryfi_client->expects($this->once())
                ->method('exec_curl')
                ->willReturn($file_data);
        } else {
            $veryfi_client = new Client($this->client_id, $this->client_secret, $this->username, $this->api_key);
        }
        $line_item = array('order' => 20, 'description' => 'TEST', 'total' => 20.1, 'sku' => 'aqw');
        $line_item = new LineItem($line_item);
        $json_response = json_decode($veryfi_client->add_line_item($document_id, $line_item), true);
        $this->assertEquals($line_item->order, $json_response['order']);
        $this->assertEquals($line_item->description, $json_response['description']);
        $this->assertEquals($line_item->total, $json_response['total']);
        $this->assertEquals($line_item->sku, $json_response['sku']);
        if (!$this->mock_responses) {
            $id = $json_response['id'];
            $json_response = json_decode($veryfi_client->delete_line_item($document_id, $id), true);
            $this->assertEquals('ok', $json_response['status']);
        }
    }

    public function test_delete_line_item(): void
    {
        $document_id = 44691518;
        $line_item_id = 189951682;
        if ($this->mock_responses) {
            $veryfi_client = $this->getMockBuilder(Client::class)
                ->onlyMethods(['exec_curl'])
                ->setConstructorArgs([$this->client_id, $this->client_secret, $this->username, $this->api_key])
                ->getMock();

            $file_path = __DIR__ . '/resources/deleteLineItem.json';
            $file = fopen($file_path, 'r');
            $file_data = mb_convert_encoding(fread($file, filesize($file_path)), 'UTF-8');
            $veryfi_client->expects($this->once())
                ->method('exec_curl')
                ->willReturn($file_data);
            $json_response = json_decode($veryfi_client->delete_line_item($document_id, $line_item_id), true);
            $this->assertEquals('ok', $json_response['status']);
        } else {
            $this->assertTrue(true); // Tested before
        }
    }

    public function test_delete_line_items(): void
    {
        $document_id = 51208553;
        if ($this->mock_responses) {
            $veryfi_client = $this->getMockBuilder(Client::class)
                ->onlyMethods(['exec_curl'])
                ->setConstructorArgs([$this->client_id, $this->client_secret, $this->username, $this->api_key])
                ->getMock();

            $file_path = __DIR__ . '/resources/deleteLineItems.json';
            $file = fopen($file_path, 'r');
            $file_data = mb_convert_encoding(fread($file, filesize($file_path)), 'UTF-8');
            $veryfi_client->expects($this->once())
                ->method('exec_curl')
                ->willReturn($file_data);
        } else {
            $veryfi_client = new Client($this->client_id, $this->client_secret, $this->username, $this->api_key);
        }
        $json_response = json_decode($veryfi_client->delete_line_items($document_id), true);
        $this->assertEquals('ok', $json_response['status']);
    }
}
