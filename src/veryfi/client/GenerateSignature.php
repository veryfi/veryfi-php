<?php
namespace veryfi\client;
trait GenerateSignature
{
    /**
     * Generate unique signature for payload params.
     * @param array $payload_params Associative array params to be sent to API request.
     * @param string $timestamp Unix string timestamp.
     * @return string Unique signature generated using the client_secret and the payload.
     */
    private function generate_signature(array $payload_params,
                                        string $timestamp): string
    {
        $payload = "timestamp:$timestamp";
        foreach ($payload_params as $key => $value) {
            if (gettype($value) == gettype(array())) {
                $value = json_encode($value);
            }
            $payload = "$payload,$key:$value";
        }
        $temporary_signature = hash_hmac('sha256', $payload, $this->client_secret, true);
        $base64_signature = base64_encode($temporary_signature);
        return trim(mb_convert_encoding($base64_signature, 'ISO-8859-1'));
    }
}
