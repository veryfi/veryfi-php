<?php

use veryfi\client;

require_once __DIR__ . '/ClientTestCase.php';

class ClientW2sTest extends ClientTestCase
{
    public function test_process_w2_document_base64(): void
    {
        if ($this->mock_responses) {
            $veryfi_client = $this->getMockBuilder(Client::class)
                ->onlyMethods(['exec_curl'])
                ->setConstructorArgs([$this->client_id, $this->client_secret, $this->username, $this->api_key])
                ->getMock();

            $file_path = __DIR__ . '/resources/processW2Document.json';
            $file = fopen($file_path, 'r');
            $file_data = mb_convert_encoding(fread($file, filesize($file_path)), 'UTF-8');
            $veryfi_client->expects($this->once())
                ->method('exec_curl')
                ->willReturn($file_data);

        } else {
            $veryfi_client = new Client($this->client_id, $this->client_secret, $this->username, $this->api_key);
        }
        $file = $this->w2_path;
        $json_response = json_decode($veryfi_client->process_w2_base64($file, true), true);
        $this->assertNotEmpty($json_response['id']);
    }

    public function test_process_w2_document(): void
    {
        if ($this->mock_responses) {
            $veryfi_client = $this->getMockBuilder(Client::class)
                ->onlyMethods(['exec_curl'])
                ->setConstructorArgs([$this->client_id, $this->client_secret, $this->username, $this->api_key])
                ->getMock();

            $file_path = __DIR__ . '/resources/processW2Document.json';
            $file = fopen($file_path, 'r');
            $file_data = mb_convert_encoding(fread($file, filesize($file_path)), 'UTF-8');
            $veryfi_client->expects($this->once())
                ->method('exec_curl')
                ->willReturn($file_data);

        } else {
            $veryfi_client = new Client($this->client_id, $this->client_secret, $this->username, $this->api_key);
        }
        $file = $this->w2_path;
        $json_response = json_decode($veryfi_client->process_w2($file, true), true);
        $this->assertNotEmpty($json_response['id']);
    }

    public function test_process_w2_document_from_url(): void
    {
        if ($this->mock_responses) {
            $veryfi_client = $this->getMockBuilder(Client::class)
                ->onlyMethods(['exec_curl'])
                ->setConstructorArgs([$this->client_id, $this->client_secret, $this->username, $this->api_key])
                ->getMock();
            $file_path = __DIR__ . '/resources/processW2Document.json';
            $file = fopen($file_path, 'r');
            $file_data = mb_convert_encoding(fread($file, filesize($file_path)), 'UTF-8');
            $veryfi_client->expects($this->once())
                ->method('exec_curl')
                ->willReturn($file_data);

        } else {
            $veryfi_client = new Client($this->client_id, $this->client_secret, $this->username, $this->api_key);
        }

        $file_name = 'w2_form.pdf';
        $url = 'https://cdn.veryfi.com/wp-content/uploads/image.png';
        $json_response = json_decode($veryfi_client->process_w2_from_url($file_name, $url, null, true), true);
        $this->assertNotEmpty($json_response['id']);
    }

    /**
     * @throws Exception
     */
    public function test_get_w2_documents(): void
    {
        if ($this->mock_responses) {
            $veryfi_client = $this->getMockBuilder(Client::class)
                ->onlyMethods(['exec_curl'])
                ->setConstructorArgs([$this->client_id, $this->client_secret, $this->username, $this->api_key])
                ->getMock();
            $file_path = __DIR__ . '/resources/getW2Documents.json';
            $file = fopen($file_path, 'r');
            $file_data = mb_convert_encoding(fread($file, filesize($file_path)), 'UTF-8');
            $veryfi_client->expects($this->atLeastOnce())
                ->method('exec_curl')
                ->willReturn($file_data);

        } else {
            $veryfi_client = new Client($this->client_id, $this->client_secret, $this->username, $this->api_key);
        }
        $json_response = json_decode($veryfi_client->get_w2s(), true);
        $json_len = sizeof($json_response);
        $this->assertTrue($json_len > 1);
    }

    public function test_get_w2_document(): void
    {
        if ($this->mock_responses) {
            $veryfi_client = $this->getMockBuilder(Client::class)
                ->onlyMethods(['exec_curl'])
                ->setConstructorArgs([$this->client_id, $this->client_secret, $this->username, $this->api_key])
                ->getMock();
            $file_path = __DIR__ . '/resources/getW2Documents.json';
            $file = fopen($file_path, 'r');
            $file_data = mb_convert_encoding(fread($file, filesize($file_path)), 'UTF-8');
            $veryfi_client->expects($this->atLeastOnce())
                ->method('exec_curl')
                ->willReturn($file_data);

        } else {
            $veryfi_client = new Client($this->client_id, $this->client_secret, $this->username, $this->api_key);
        }


        if ($this->mock_responses) {
            $document_id = 12345;
        } else {
            $json_response = json_decode($veryfi_client->get_w2s(), true);
            $document_id = $json_response['results'][0]['id'];
        }

        $json_response = json_decode($veryfi_client->get_w2($document_id), true);
        $this->assertNotEmpty($json_response);
    }

    public function test_delete_w2_document(): void
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

            $file = $this->w2_path;
            $json_response = json_decode($veryfi_client->process_w2($file, true), true);
            $document_id = $json_response['id'];
        }

        $delete_json_response = json_decode($veryfi_client->delete_w2($document_id));
        $this->assertEquals(json_decode('{"status": "ok", "message": "Document has been deleted"}'), $delete_json_response);
    }
}
