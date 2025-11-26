<?php

use veryfi\Client;

require_once __DIR__ . '/ClientTestCase.php';

class ClientChecksTest extends ClientTestCase
{
    public function test_process_check_base64(): void
    {
        if ($this->mock_responses) {
            $veryfi_client = $this->getMockBuilder(Client::class)
                ->onlyMethods(['exec_curl'])
                ->setConstructorArgs([$this->client_id, $this->client_secret, $this->username, $this->api_key])
                ->getMock();

            $file_path = __DIR__ . '/resources/processCheck.json';
            $file = fopen($file_path, 'r');
            $file_data = mb_convert_encoding(fread($file, filesize($file_path)), 'UTF-8');
            $veryfi_client->expects($this->atLeastOnce())
                ->method('exec_curl')
                ->willReturn($file_data);

        } else {
            $veryfi_client = new Client($this->client_id, $this->client_secret, $this->username, $this->api_key);
        }

        $file = $this->receipt_path;
        $json_response = json_decode($veryfi_client->process_check_base64($file), true);
        $this->assertNotEmpty($json_response['id']);
    }

    public function test_process_check(): void
    {
        if ($this->mock_responses) {
            $veryfi_client = $this->getMockBuilder(Client::class)
                ->onlyMethods(['exec_curl'])
                ->setConstructorArgs([$this->client_id, $this->client_secret, $this->username, $this->api_key])
                ->getMock();

            $file_path = __DIR__ . '/resources/processCheck.json';
            $file = fopen($file_path, 'r');
            $file_data = mb_convert_encoding(fread($file, filesize($file_path)), 'UTF-8');
            $veryfi_client->expects($this->atLeastOnce())
                ->method('exec_curl')
                ->willReturn($file_data);

        } else {
            $veryfi_client = new Client($this->client_id, $this->client_secret, $this->username, $this->api_key);
        }

        $file = $this->receipt_path;
        $json_response = json_decode($veryfi_client->process_check($file), true);
        $this->assertNotEmpty($json_response['id']);
    }

    public function test_process_check_url(): void
    {
        if ($this->mock_responses) {
            $veryfi_client = $this->getMockBuilder(Client::class)
                ->onlyMethods(['exec_curl'])
                ->setConstructorArgs([$this->client_id, $this->client_secret, $this->username, $this->api_key])
                ->getMock();

            $file_path = __DIR__ . '/resources/processCheck.json';
            $file = fopen($file_path, 'r');
            $file_data = mb_convert_encoding(fread($file, filesize($file_path)), 'UTF-8');
            $veryfi_client->expects($this->once())
                ->method('exec_curl')
                ->willReturn($file_data);

        } else {
            $veryfi_client = new Client($this->client_id, $this->client_secret, $this->username, $this->api_key);
        }

        $url = 'https://cdn-dev.veryfi.com/testing/veryfi-python/check.jpg';
        $json_response = json_decode($veryfi_client->process_check_from_url($url), true);
        $this->assertNotEmpty($json_response['id']);
    }

    public function test_get_checks(): void
    {
        if ($this->mock_responses) {
            $veryfi_client = $this->getMockBuilder(Client::class)
                ->onlyMethods(['exec_curl'])
                ->setConstructorArgs([$this->client_id, $this->client_secret, $this->username, $this->api_key])
                ->getMock();

            $file_path = __DIR__ . '/resources/getChecks.json';
            $file = fopen($file_path, 'r');
            $file_data = mb_convert_encoding(fread($file, filesize($file_path)), 'UTF-8');
            $veryfi_client->expects($this->atLeastOnce())
                ->method('exec_curl')
                ->willReturn($file_data);

        } else {
            $veryfi_client = new Client($this->client_id, $this->client_secret, $this->username, $this->api_key);
        }

        $json_response = json_decode($veryfi_client->get_checks(), true);
        $json_len = sizeof($json_response);
        $this->assertTrue($json_len > 0);


        if (isset($json_response['documents'][0]['id'])) {
            $document_id = $json_response['documents'][0]['id'];
        } elseif (isset($json_response['results'][0]['id'])) {
            $document_id = $json_response['results'][0]['id'];
        } else {
            $document_id = 987654321;
        }

        $json_response = json_decode($veryfi_client->get_check($document_id), true);
        $this->assertNotEmpty($json_response);
    }
    public function test_delete_check(): void
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
            $json_response = json_decode($veryfi_client->process_check_base64($file), true);
            $document_id = $json_response['id'];
        }

        $delete_json_response = json_decode($veryfi_client->delete_check($document_id));
        $this->assertEquals(json_decode('{"status": "ok", "message": "Document has been deleted"}'), $delete_json_response);
    }
}
