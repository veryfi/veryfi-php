<?php
use PHPUnit\Framework\TestCase;
use veryfi\Client;


final class ClientTest extends TestCase
{
    private string $client_id = 'client_id';
    private string $client_secret = 'client_secret';
    private string $username = 'username';
    private string $api_key = 'api_key';
    private string $receipt_path = __DIR__ . '/resources/receipt.jpeg';
    private bool $mock_responses = true;

    public function test_get_documents(): void
    {
        if ($this->mock_responses) {
            $veryfi_client = $this->getMockBuilder(Client::class)
                ->onlyMethods(['exec_curl'])
                ->setConstructorArgs([$this->client_id, $this->client_secret, $this->username, $this->api_key])
                ->getMock();

            $file_path = __DIR__ . '/resources/getDocuments.json';
            $file = fopen($file_path, 'r');
            $file_data = utf8_encode(fread($file, filesize($file_path)));
            $veryfi_client->expects($this->once())
                ->method('exec_curl')
                ->willReturn($file_data);

        } else {
            $veryfi_client = new Client($this->client_id, $this->client_secret, $this->username, $this->api_key);
        }
        $json_response = json_decode($veryfi_client->get_documents(), true);
        $json_len = sizeof($json_response);
        $this->assertEquals(2, $json_len);
    }

    public function test_get_document(): void
    {
        if ($this->mock_responses) {
            $veryfi_client = $this->getMockBuilder(Client::class)
                ->onlyMethods(['exec_curl'])
                ->setConstructorArgs([$this->client_id, $this->client_secret, $this->username, $this->api_key])
                ->getMock();

            $file_path = __DIR__ . '/resources/getDocument.json';
            $file = fopen($file_path, 'r');
            $file_data = utf8_encode(fread($file, filesize($file_path)));
            $veryfi_client->expects($this->once())
                ->method('exec_curl')
                ->willReturn($file_data);
            $document_id = 31727276;

        } else {
            $veryfi_client = new Client($this->client_id, $this->client_secret, $this->username, $this->api_key);
            $documents = json_decode($veryfi_client->get_documents(), true);
            $document_id = $documents['documents'][0]['id'];
        }
        $json_response = json_decode($veryfi_client->get_document($document_id), true);
        $this->assertEquals($document_id, $json_response['id']);
    }

    public function test_process_document(): void
    {
        if ($this->mock_responses) {
            $veryfi_client = $this->getMockBuilder(Client::class)
                ->onlyMethods(['exec_curl'])
                ->setConstructorArgs([$this->client_id, $this->client_secret, $this->username, $this->api_key])
                ->getMock();

            $file_path = __DIR__ .'/resources/processDocument.json';
            $file = fopen($file_path, 'r');
            $file_data = utf8_encode(fread($file, filesize($file_path)));
            $veryfi_client->expects($this->once())
                ->method('exec_curl')
                ->willReturn($file_data);

        } else {
            $veryfi_client = new Client($this->client_id, $this->client_secret, $this->username, $this->api_key);
        }
        $categories = array('Advertising & Marketing', 'Automotive');
        $file = $this->receipt_path;
        $json_response = json_decode($veryfi_client->process_document($file, $categories, true), true);
        $this->assertEquals('In-n-out Burger', $json_response['vendor']['name']);
    }

    private function generate_random_string(): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < 10; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function test_update_document(): void
    {
        if ($this->mock_responses) {
            $veryfi_client = $this->getMockBuilder(Client::class)
                ->onlyMethods(['exec_curl'])
                ->setConstructorArgs([$this->client_id, $this->client_secret, $this->username, $this->api_key])
                ->getMock();

            $file_path = __DIR__ . '/resources/updateDocument.json';
            $file = fopen($file_path, 'r');
            $file_data = utf8_encode(fread($file, filesize($file_path)));
            $veryfi_client->expects($this->once())
                ->method('exec_curl')
                ->willReturn($file_data);
            $notes = 'Note updated';
            $parameters = array('notes' => $notes);
            $document_id = 31727276;

        } else {
            $veryfi_client = new Client($this->client_id, $this->client_secret, $this->username, $this->api_key);
            $documents = json_decode($veryfi_client->get_documents(), true);
            $document_id = $documents['documents'][0]['id'];
            $notes = $this->generate_random_string();
            $parameters = array('notes' => $notes);
        }
        $json_response = json_decode($veryfi_client->update_document($document_id, $parameters), true);
        $this->assertEquals($notes, $json_response['notes']);
    }

    public function test_delete_document(): void
    {
        if ($this->mock_responses) {
            $veryfi_client = $this->getMockBuilder(Client::class)
                ->onlyMethods(['exec_curl', 'process_document'])
                ->setConstructorArgs([$this->client_id, $this->client_secret, $this->username, $this->api_key])
                ->getMock();

            $file_path = __DIR__ . '/resources/deleteDocument.json';
            $file = fopen($file_path, 'r');
            $file_data = utf8_encode(fread($file, filesize($file_path)));
            $veryfi_client->expects($this->once())
                ->method('exec_curl')
                ->willReturn($file_data);

            $file_path = __DIR__ .'/resources/processDocument.json';
            $file = fopen($file_path, 'r');
            $file_data = utf8_encode(fread($file, filesize($file_path)));
            $veryfi_client->expects($this->once())
                ->method('process_document')
                ->willReturn($file_data);

        } else {
            $veryfi_client = new Client($this->client_id, $this->client_secret, $this->username, $this->api_key);
        }
        $categories = array('Advertising & Marketing', 'Automotive');
        $file = $this->receipt_path;
        $json_response = json_decode($veryfi_client->process_document($file, $categories, false), true);
        $id = $json_response['id'];
        $delete_json_response = json_decode($veryfi_client->delete_document($id));
        $this->assertEquals(json_decode('{"status": "ok", "message": "Document has been deleted"}'), $delete_json_response);
    }

    public function test_process_document_url(): void
    {
        if ($this->mock_responses) {
            $veryfi_client = $this->getMockBuilder(Client::class)
                ->onlyMethods(['exec_curl'])
                ->setConstructorArgs([$this->client_id, $this->client_secret, $this->username, $this->api_key])
                ->getMock();

            $file_path = __DIR__ . '/resources/processDocument.json';
            $file = fopen($file_path, 'r');
            $file_data = utf8_encode(fread($file, filesize($file_path)));
            $veryfi_client->expects($this->once())
                ->method('exec_curl')
                ->willReturn($file_data);

        } else {
            $veryfi_client = new Client($this->client_id, $this->client_secret, $this->username, $this->api_key);
        }
        $url = 'https://veryfi-testing-public.s3.us-west-2.amazonaws.com/receipt.jpg';
        $json_response = json_decode($veryfi_client->process_document_url($url, null, null, true, 1), true);
        $this->assertEquals('In-n-out Burger', $json_response['vendor']['name']);
    }

    public function test_bad_credentials(): void
    {
        $veryfi_client = new Client('', '', '', '');
        $json_response = json_decode($veryfi_client->get_documents(), true);
        $this->assertEquals('fail', $json_response['status']);
    }
}
