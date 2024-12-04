<?php
namespace veryfi\client;
trait GetHeaders
{
    /**
     * Prepares the headers needed for a request.
     * @return array Associative array with headers.
     */
    private function get_headers(): array
    {
        return array(
            'User-Agent' => 'php veryfi-php/1.1.0',
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Client-ID' => $this->client_id,
            'Authorization' => "apikey $this->username:$this->api_key",
            'X-Veryfi-Request-Timestamp' => '',
            'X-Veryfi-Request-Signature' => ''
        );
    }
}