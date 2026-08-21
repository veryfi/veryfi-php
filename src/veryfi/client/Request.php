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
     * @param bool $has_files If true, send a multipart request.
     * @param string|null $api_version API version override for endpoints outside the configured version.
     * @param bool $partner Whether the route is under the partner namespace.
     * @return string A JSON of the response data.
     */
    protected function request(string $http_verb,
                               string $endpoint_name,
                               array $request_arguments = array(),
                               bool $has_files = false,
                               ?string $api_version = null,
                               bool $partner = true): string
    {
        $http_verb = strtoupper($http_verb);
        foreach ($request_arguments as $argument) {
            if ($argument instanceof \CURLFile) {
                $has_files = true;
                break;
            }
        }

        if ($api_version === null && $partner) {
            $api_url = "$this->extend_url$endpoint_name";
        } else {
            $version = $api_version ?? $this->api_version;
            $namespace = $partner ? '/partner' : '';
            $api_url = "$this->base_url$version$namespace$endpoint_name";
        }

        if (in_array($http_verb, array('GET', 'HEAD'), true) && $request_arguments) {
            $query = http_build_query($request_arguments, '', '&', PHP_QUERY_RFC3986);
            $api_url .= (strpos($api_url, '?') === false ? '?' : '&') . $query;
        }

        $time_stamp = (string) (time() * 1000);
        $signature = $this->generate_signature($request_arguments, $time_stamp);
        $this->headers['X-Veryfi-Request-Timestamp'] = $time_stamp;
        $this->headers['X-Veryfi-Request-Signature'] = $signature;
        $request_headers = $this->headers;
        if ($has_files) {
            unset($request_headers['Content-Type']);
        }
        $headers = array();
        foreach ($request_headers as $key => $value)
        {
            $headers[] = "$key:$value";
        }
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $api_url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $http_verb);
        curl_setopt($curl, CURLOPT_TIMEOUT, $this->api_timeout);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        if (!in_array($http_verb, array('GET', 'HEAD'), true) && $request_arguments) {
            if ($has_files) {
                curl_setopt($curl, CURLOPT_POSTFIELDS, $request_arguments);
            } else {
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($request_arguments));
            }
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        return $this->exec_curl($curl);
    }
}
