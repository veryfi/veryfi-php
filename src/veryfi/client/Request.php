<?php
namespace veryfi\client;
trait Request
{
    /**
     * Submit the HTTP request.
     *
     * @param string $http_verb HTTP method.
     * @param string $endpoint_name Endpoint name such as 'documents', 'users', etc.
     * @param array $request_arguments Associative array payload to send to Veryfi.
     * @param bool $has_files if has files should do multipart request instead of json request.
     * @return string A JSON of the response data.
     */
    private function request(string $http_verb,
                             string $endpoint_name,
                             array  $request_arguments,
                             bool $has_files = false): string
    {
        $api_url = "$this->extend_url$endpoint_name";
        $time_stamp = (string) (time() * 1000);
        $signature = $this->generate_signature($request_arguments, $time_stamp);
        $this->headers['X-Veryfi-Request-Timestamp'] = $time_stamp;
        $this->headers['X-Veryfi-Request-Signature'] = $signature;
        $headers = array();
        foreach ($this->headers as $key => $value)
        {
            $headers[] = "$key:$value";
        }
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $api_url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $http_verb);
        curl_setopt($curl, CURLOPT_TIMEOUT, $this->api_timeout);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        if ($has_files) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, $request_arguments);
        } else {
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($request_arguments));
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        return $this->exec_curl($curl);
    }
}
