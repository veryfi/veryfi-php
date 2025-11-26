<?php

use veryfi\Client;

require_once __DIR__ . '/ClientTestCase.php';

class ClientSplitDocumentsTest extends ClientTestCase
{
    public function test_get_split_documents(): void
    {
        if ($this->mock_responses) {
            $veryfi_client = $this->getMockBuilder(Client::class)
                ->onlyMethods(['exec_curl'])
                ->setConstructorArgs([$this->client_id, $this->client_secret, $this->username, $this->api_key])
                ->getMock();

            $file_path = __DIR__ . '/resources/getDocuments.json';
            $file = fopen($file_path, 'r');
            $file_data = mb_convert_encoding(fread($file, filesize($file_path)), 'UTF-8');
            $veryfi_client->expects($this->once())
                ->method('exec_curl')
                ->willReturn($file_data);

        } else {
            $veryfi_client = new Client($this->client_id, $this->client_secret, $this->username, $this->api_key);
        }
        $json_response = json_decode($veryfi_client->get_split_documents(), true);
        $this->assertIsArray($json_response);
    }

    public function test_get_split_document(): void
    {
        if ($this->mock_responses) {
            $veryfi_client = $this->getMockBuilder(Client::class)
                ->onlyMethods(['exec_curl'])
                ->setConstructorArgs([$this->client_id, $this->client_secret, $this->username, $this->api_key])
                ->getMock();

            $file_path = __DIR__ . '/resources/getDocument.json';
            $file = fopen($file_path, 'r');
            $file_data = mb_convert_encoding(fread($file, filesize($file_path)), 'UTF-8');
            $veryfi_client->expects($this->once())
                ->method('exec_curl')
                ->willReturn($file_data);
            $document_id = '125661908';

        } else {
            $veryfi_client = new Client($this->client_id, $this->client_secret, $this->username, $this->api_key);
            $documents = json_decode($veryfi_client->get_split_documents(), true);
            if (isset($documents['documents'][0]['id'])) {
                $document_id = $documents['documents'][0]['id'];
            } else {
                $this->markTestSkipped('No documents found to test get_split_document');
            }
        }

        $json_response = json_decode($veryfi_client->get_split_document($document_id), true);
        $this->assertEquals($document_id, $json_response['id']);
    }

    public function test_split_document_from_base64(): void
    {
        if ($this->mock_responses) {
            $veryfi_client = $this->getMockBuilder(Client::class)
                ->onlyMethods(['exec_curl'])
                ->setConstructorArgs([$this->client_id, $this->client_secret, $this->username, $this->api_key])
                ->getMock();

            $file_path = __DIR__ . '/resources/processDocument.json';
            $file = fopen($file_path, 'r');
            $file_data = mb_convert_encoding(fread($file, filesize($file_path)), 'UTF-8');
            $veryfi_client->expects($this->once())
                ->method('exec_curl')
                ->willReturn($file_data);

        } else {
            $veryfi_client = new Client($this->client_id, $this->client_secret, $this->username, $this->api_key);
        }

        $file_path = $this->receipt_path;
        $file_name = 'receipt.jpg';
        $base64_encoded_string = base64_encode(file_get_contents($file_path));

        $json_response = json_decode($veryfi_client->split_document_from_base64($base64_encoded_string, $file_name), true);
        $this->assertIsArray($json_response);
    }

    public function test_split_document_from_url(): void
    {
        if ($this->mock_responses) {
            $veryfi_client = $this->getMockBuilder(Client::class)
                ->onlyMethods(['exec_curl'])
                ->setConstructorArgs([$this->client_id, $this->client_secret, $this->username, $this->api_key])
                ->getMock();

            $file_path = __DIR__ . '/resources/processDocument.json';
            $file = fopen($file_path, 'r');
            $file_data = mb_convert_encoding(fread($file, filesize($file_path)), 'UTF-8');
            $veryfi_client->expects($this->once())
                ->method('exec_curl')
                ->willReturn($file_data);

        } else {
            $veryfi_client = new Client($this->client_id, $this->client_secret, $this->username, $this->api_key);
        }

        $url = 'https://raw.githubusercontent.com/veryfi/veryfi-python/master/tests/assets/receipt_public.jpg';
        $json_response = json_decode($veryfi_client->split_document_from_url($url), true);
        $this->assertIsArray($json_response);
    }
}
