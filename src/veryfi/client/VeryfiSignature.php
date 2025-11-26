<?php
namespace veryfi\client;
trait VeryfiSignature
{
    /**
     * Verify the signature from a webhook.
     *
     * @param array $payload_params the payload params returned by the webhook.
     * @param string $client_secret your client secret.
     * @param string $client_signature x-veryfi-signature header.
     * @return bool returns true if the signature is valid else false.
     */
    public static function verify_signature(array $payload_params,
                                            string $client_secret,
                                            string $client_signature): bool
    {
        $payload = "";
        foreach ($payload_params as $key => $value) {
            if (gettype($value) == gettype("")) {
                $value = "'$value'";
            }
            $payload = strlen($payload) > 0 ? "$payload, '$key': $value" : "'$key': $value";
        }
        $payload = "{{$payload}}";
        $temporary_signature = hash_hmac('SHA256', $payload, $client_secret, true);
        $signature = trim(mb_convert_encoding(base64_encode($temporary_signature), 'ISO-8859-1'));
        return $signature == $client_signature;
    }
}
