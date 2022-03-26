<?php

namespace veryfi;


/**
 * Veryfi-sdk for php
 *
 * @author Sebastian Carmona Tobon
 * @license MIT
 */
class Client
{

    /**
     * Default categories to process document.
     *
     * @var array static.
     */
    const CATEGORIES = [
        'Advertising & Marketing',
        'Automotive',
        'Bank Charges & Fees',
        'Legal & Professional Services',
        'Insurance',
        'Meals & Entertainment',
        'Office Supplies & Software',
        'Taxes & Licenses',
        'Travel',
        'Rent & Lease',
        'Repairs & Maintenance',
        'Payroll',
        'Utilities',
        'Job Supplies',
        'Grocery'
    ];

    /**
     * Base url of Veryfi by default 'https://api.veryfi.com/api/'.
     *
     * @var string
     */
    public string $base_url;
    /**
     * Api version to use Veryfi by default 'v7'
     *
     * @var string
     */
    public string $api_version;
    /**
     * Api timeout to call Veryfi API by default 120.
     *
     * @var int
     */
    public int $api_timeout;
    /**
     * Client id provided by Veryfi.
     *
     * @var string
     */
    public string $client_id;
    /**
     * Client secret provided by Veryfi.
     *
     * @var string
     */
    public string $client_secret;
    /**
     * Username provided by Veryfi.
     *
     * @var string
     */
    public string $username;
    /**
     * Api key provided by Veryfi.
     *
     * @var string
     */
    public string $api_key;
    /**
     * Associative array of headers.
     *
     * @var array
     */
    private array $headers;
    /**
     * Base URL to Veryfi API.
     *
     * @var string
     */
    private string $extend_url;

    /**
     * Constructor of Veryfi client
     *
     * @param string $client_id Client id provided by Veryfi.
     * @param string $client_secret Client secret provided by Veryfi.
     * @param string $username Username provided by Veryfi.
     * @param string $api_key Api key provided by Veryfi.
     * @param string $base_url Base url of Veryfi by default 'https://api.veryfi.com/api/',
     * @param string $api_version Api version to use Veryfi, currently 'v8
     * @param int $api_timeout Api timeout for call Veryfi api, by default 120
     */
    public function __construct(string $client_id,
                                string $client_secret,
                                string $username,
                                string $api_key,
                                string $base_url = 'https://api.veryfi.com/api/',
                                string $api_version = 'v8',
                                int    $api_timeout = 120)
    {
        $this->client_id = $client_id;
        $this->client_secret = $client_secret;
        $this->username = $username;
        $this->api_key = $api_key;
        $this->base_url = $base_url;
        $this->api_version = $api_version;
        $this->api_timeout = $api_timeout;
        $this->headers = $this->get_headers();
        $this->extend_url = $this->get_url();
    }

    /**
     * Prepares the headers needed for a request.
     *
     * @return array Associative array with headers.
     */
    private function get_headers(): array
    {
        return array(
            'User-Agent' => 'php veryfi-php/1.0.0',
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Client-ID' => $this->client_id,
            'Authorization' => "apikey $this->username:$this->api_key",
            'X-Veryfi-Request-Timestamp' => '',
            'X-Veryfi-Request-Signature' => ''
        );
    }

    /**
     * Get API Base URL with API Version.
     *
     * @return string Base URL to Veryfi API.
     */
    private function get_url(): string
    {
        return "$this->base_url$this->api_version/partner";
    }

    /**
     * Generate unique signature for payload params.
     *
     * @param array $payload_params Associative array params to be sent to API request.
     * @param string $timestamp Unix string timestamp.
     * @return string Unique signature generated using the client_secret and the payload.
     */
    private function generate_signature(array $payload_params,
                                        string $timestamp): string
    {
        $payload = "timestamp:$timestamp";
        return Client::generate_custom_signature($payload_params, $payload, $this->client_secret);
    }

    /**
     * Generate unique signature for payload params with init payload.
     *
     * @param array $payload_params Associative array params to be sent to API request.
     * @param string $payload init payload
     * @param string $client_secret your client secret
     * @return string Unique signature generated using the client_secret and the payload.
     */
    private static function generate_custom_signature(array $payload_params,
                                                      string $payload,
                                                      string $client_secret): string
    {
        foreach ($payload_params as $key => $value) {
            if (gettype($value) == gettype(array())) {
                $value = json_encode($value);
            }
            $payload = strlen($payload) > 0 ? "$payload,$key:$value" : "$key:$value";
        }
        $temporary_signature = hash_hmac('sha256', $payload, $client_secret, true);
        return trim(utf8_decode(base64_encode($temporary_signature)));
    }

    /**
     * Submit the HTTP request.
     *
     * @param string $http_verb HTTP method.
     * @param string $endpoint_name Endpoint name such as 'documents', 'users', etc.
     * @param array $request_arguments Associative array payload to send to Veryfi.
     * @return string A JSON of the response data.
     */
    private function request(string $http_verb,
                             string $endpoint_name,
                             array  $request_arguments): string
    {
        $api_url = "$this->extend_url$endpoint_name";
        $time_stamp = (string) (time() * 1000);
        $signature = $this->generate_signature($request_arguments, $time_stamp);
        $this->headers['X-Veryfi-Request-Timestamp'] = $time_stamp;
        $this->headers['X-Veryfi-Request-Signature'] = $signature;
        $headers = array();
        foreach ($this->headers as $key => $value)
        {
            array_push($headers, "$key:$value");
        }
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $api_url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $http_verb);
        curl_setopt($curl, CURLOPT_TIMEOUT, $this->api_timeout);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($request_arguments));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        return $this->exec_curl($curl);
    }

    /**\internal
     * Exec the curl, needed for mock it.
     *
     * @param CurlHandle $curl Curl handle of request.
     * @return string A JSON response.
     */
    protected function exec_curl($curl): string
    {
        return curl_exec($curl);
    }

    /**
     * Get list of documents.
     *
     * @return string A JSON with list of processes documents and metadata.
     */
    public function get_documents(): string
    {
        $endpoint_name = '/documents/';
        $request_arguments = array();
        return $this->request('GET', $endpoint_name, $request_arguments);
    }

    /**
     * Retrieve document by ID.
     *
     * @param int $document_id ID of the document you'd like to retrieve.
     * @return string A Json of data extracted from the Document.
     */
    public function get_document(int $document_id): string
    {
        $endpoint_name = "/documents/$document_id/";
        $request_arguments = array('id' => $document_id);
        return $this->request('GET', $endpoint_name, $request_arguments);
    }

    /**
     * Process a document and extract all the fields from it.
     *
     * @param string $file_path Path on disk to a file to submit for data extraction.
     * @param array|string[] $categories Array of categories Veryfi can use to categorize the document.
     * @param bool $delete_after_processing Delete this document from Veryfi after data has been extracted.
     * @param array $additional_request_parameters Additional request parameters.
     * @return string Data extracted from the document.
     */
    public function process_document(string $file_path,
                                     array $categories = self::CATEGORIES,
                                     bool $delete_after_processing = false,
                                     array $additional_request_parameters = array()): string
    {
        $endpoint_name = '/documents/';
        $file_name = pathinfo($file_path, PATHINFO_BASENAME);
        $file = fopen($file_path, 'rb');
        $file_data = base64_encode(fread($file, filesize($file_path)));
        $request_arguments = array(
            'file_name' => $file_name,
            'file_data' => $file_data,
            'categories' => $categories,
            'auto_delete' => $delete_after_processing
        );
        $request_arguments = array_replace($request_arguments, $additional_request_parameters);
        return $this->request('POST', $endpoint_name, $request_arguments);
    }

    /**
     * Process Document from url and extract all the fields from it.
     *
     * @param string|null $file_url Required if file_urls isn't specified. Publicly accessible URL to a file, e.g. "https://cdn.example.com/receipt.jpg".
     * @param array|null $file_urls Required if file_url isn't specifies. List of publicly accessible URLs to multiple files, e.g. ['https://cdn.example.com/receipt1.jpg', 'https://cdn.example.com/receipt2.jpg']
     * @param array|null $categories Array of categories to use when categorizing the document
     * @param bool $delete_after_processing Delete this/these document(s) from Veryfi after data has been extracted
     * @param int $boost_mode Flag that tells Veryfi whether boost mode should be enabled. When set to 1, Veryfi will skip data enrichment steps, but will process the document faster. Default value for this flag is 0.
     * @param string|null $external_id Optional custom document identifier. Use this if you would like to assign your own ID to documents.
     * @param int|null $max_pages_to_process When sending a long document to Veryfi for processing, this parameter controls how many pages of the document will be read and processed, starting from page 1.
     * @param array $additional_request_parameters Additional request parameters.
     * @return string Data extracted from the document.
     */
    public function process_document_url(string $file_url = null,
                                         array $file_urls = null,
                                         array $categories = null,
                                         bool $delete_after_processing = false,
                                         int $boost_mode = 0,
                                         string $external_id = null,
                                         int $max_pages_to_process = null,
                                         array $additional_request_parameters = array()): string
    {
        $endpoint_name = '/documents/';
        $request_arguments = array(
            'auto_delete' => $delete_after_processing,
            'boost_mode' => $boost_mode,
            'categories' => $categories,
            'external_id' => $external_id,
            'file_url' => $file_url,
            'file_urls' => $file_urls,
            'max_pages_to_process' => $max_pages_to_process
        );
        $request_arguments = array_replace($request_arguments, $additional_request_parameters);
        return $this->request('POST', $endpoint_name, $request_arguments);
    }

    /**
     * Delete Document from Veryfi.
     *
     * @param int $document_id ID of the document you'd like to delete.
     * @return string A JSON response.
     */
    public function delete_document(int $document_id): string
    {
        $endpoint_name = "/documents/$document_id/";
        $request_arguments = array('id' => $document_id);
        return $this->request('DELETE', $endpoint_name, $request_arguments);
    }

    /**
     * Update data for a previously processed document, including almost any field like `vendor`, `date`, `notes` and etc.
     *
     * <code>
     * $parameters = array('notes' => 'see me');
     * veryfi_client->update_document(id, $parameters);
     * </code>
     *
     * @param int $document_id  ID of the document you'd like to update.
     * @param array $fields_to_update Fields to update.
     * @return string A document json with updated fields, if fields are writable. Otherwise a document with unchanged fields.
     */
    public function update_document(int $document_id,
                                    array $fields_to_update): string
    {
        $endpoint_name = "/documents/$document_id/";
        return $this->request('PUT', $endpoint_name, $fields_to_update);
    }

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
        $signature = Client::generate_custom_signature($payload_params, "", $client_secret);
        return $client_signature == $signature;
    }
}
