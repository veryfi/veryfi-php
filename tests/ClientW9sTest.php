<?php

use veryfi\client;

require_once __DIR__ . '/ClientTestCase.php';

class ClientW9sTest extends ClientTestCase
{
    public function test_process_w9(): void
    {
        if ($this->mock_responses) {
            $veryfi_client = $this->getMockBuilder(Client::class)
                ->onlyMethods(['exec_curl'])
                ->setConstructorArgs([$this->client_id, $this->client_secret, $this->username, $this->api_key])
                ->getMock();

            $file_path = __DIR__ . '/resources/processW9.json';
            $file = fopen($file_path, 'r');
            $file_data = mb_convert_encoding(fread($file, filesize($file_path)), 'UTF-8');
            $veryfi_client->expects($this->atLeastOnce())
                ->method('exec_curl')
                ->willReturn($file_data);

        } else {
            $veryfi_client = new Client($this->client_id, $this->client_secret, $this->username, $this->api_key);
        }

        $file = $this->receipt_path;
        $json_response = json_decode($veryfi_client->process_w9_base64($file), true);
        $this->assertNotEmpty($json_response['id']);
    }

    public function test_process_w9_url(): void
    {
        if ($this->mock_responses) {
            $veryfi_client = $this->getMockBuilder(Client::class)
                ->onlyMethods(['exec_curl'])
                ->setConstructorArgs([$this->client_id, $this->client_secret, $this->username, $this->api_key])
                ->getMock();

            $file_path = __DIR__ . '/resources/processW9.json';
            $file = fopen($file_path, 'r');
            $file_data = mb_convert_encoding(fread($file, filesize($file_path)), 'UTF-8');
            $veryfi_client->expects($this->once())
                ->method('exec_curl')
                ->willReturn($file_data);

        } else {
            $veryfi_client = new Client($this->client_id, $this->client_secret, $this->username, $this->api_key);
        }

        $url = 'https://cdn-dev.veryfi.com/testing/veryfi-python/w9.jpg';
        $file_name = 'w9.jpg';
        $json_response = json_decode($veryfi_client->process_w9_from_url($file_name, $url), true);
        $this->assertNotEmpty($json_response['id']);
    }

    public function test_get_w9s(): void
    {
        if ($this->mock_responses) {
            $veryfi_client = $this->getMockBuilder(Client::class)
                ->onlyMethods(['exec_curl'])
                ->setConstructorArgs([$this->client_id, $this->client_secret, $this->username, $this->api_key])
                ->getMock();

            $file_path = __DIR__ . '/resources/getW9s.json';
            $file = fopen($file_path, 'r');
            $file_data = mb_convert_encoding(fread($file, filesize($file_path)), 'UTF-8');
            $veryfi_client->expects($this->atLeastOnce())
                ->method('exec_curl')
                ->willReturn($file_data);

        } else {
            $veryfi_client = new Client($this->client_id, $this->client_secret, $this->username, $this->api_key);
        }

        $json_response = json_decode($veryfi_client->get_w9s(), true);
        $json_len = sizeof($json_response);
        $this->assertTrue($json_len > 0);


        if (isset($json_response['documents'][0]['id'])) {
            $document_id = $json_response['documents'][0]['id'];
        } elseif (isset($json_response['results'][0]['id'])) {
            $document_id = $json_response['results'][0]['id'];
        } else {
            $document_id = 5544332211;
        }

        $json_response = json_decode($veryfi_client->get_w9($document_id), true);
        $this->assertNotEmpty($json_response);
    }
    public function test_delete_w9(): void
    {
        if ($this->mock_responses) {
            $veryfi_client = $this->getMockBuilder(Client::class)
                ->onlyMethods(['exec_curl'])
                ->setConstructorArgs([$this->client_id, $this->client_secret, $this->username, $this->api_key])
                ->getMock();

            $file_path = __DIR__ . '/resources/deleteDocument.json';
            $file = fopen($file_path, 'r');
            $file_data = mb_convert_encoding(fread($file, filesize($file_path)), 'UTF-8');
            $veryfi_client->expects($this->once())
                ->method('exec_curl')
                ->willReturn($file_data);

            $document_id = 12345;

        } else {
            $veryfi_client = new Client($this->client_id, $this->client_secret, $this->username, $this->api_key);
            $file = $this->receipt_path;
            $json_response = json_decode($veryfi_client->process_w9_base64($file), true);
            $document_id = $json_response['id'];
        }

        $delete_json_response = json_decode($veryfi_client->delete_w9($document_id));
        $this->assertEquals(json_decode('{"status": "ok", "message": "Document has been deleted"}'), $delete_json_response);
    }
}
