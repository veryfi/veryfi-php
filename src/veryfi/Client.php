<?php

declare(strict_types=1);
namespace veryfi;

use Exception;

/**
 * Veryfi-sdk for php
 *
 * @author Sebastian Carmona Tobon
 * @author Alejandro Uribe Sánchez
 * @license MIT
 */
class Client
{

    /**
     * Default categories to process document.
     *
     * @var array static.
     */
    const  CATEGORIES = [
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
     * Api version to use Veryfi by default 'v8'
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
     * @param string $api_version Api version to use Veryfi, currently v8
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
            'User-Agent' => 'php veryfi-php/1.0.4',
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
                             array  $request_arguments,
                             bool $force_v7 = false): string
    {
        $api_url = "$this->extend_url$endpoint_name";
        if ($force_v7) {
            $api_url = str_replace("v8","v7", $api_url);
        }
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
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($request_arguments));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        return $this->exec_curl($curl);
    }

    /**\internal
     * Exec the curl, needed for mock it.
     *
     * @param $curl Curl handle of request.
     * @return string A JSON response.
     */
    protected function exec_curl($curl): string
    {
        return curl_exec($curl);
    }

    /**
     * Get list of documents. https://docs.veryfi.com/api/receipts-invoices/search-documents/
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
     * Retrieve document by ID. https://docs.veryfi.com/api/receipts-invoices/get-a-document/
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
     * Process a document and extract all the fields from it. https://docs.veryfi.com/api/receipts-invoices/process-a-document/
     *
     * @param string $file_path Path on disk to a file to submit for data extraction.
     * @param array|string[] $categories Array of categories Veryfi can use to categorize the document.
     * @param bool $auto_delete Delete this document from Veryfi after data has been extracted.
     * @param array $additional_request_parameters Additional request parameters.
     * @return string Data extracted from the document.
     */
    public function process_document(string $file_path,
                                     array $categories = self::CATEGORIES,
                                     bool $auto_delete = false,
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
            'auto_delete' => $auto_delete
        );
        $request_arguments = array_replace($request_arguments, $additional_request_parameters);
        return $this->request('POST', $endpoint_name, $request_arguments);
    }

    /**
     * Process Document from url and extract all the fields from it. https://docs.veryfi.com/api/receipts-invoices/process-a-document/
     *
     * @param string|null $file_url Required if file_urls isn't specified. Publicly accessible URL to a file, e.g. "https://cdn.example.com/receipt.jpg".
     * @param array|null $file_urls Required if file_url isn't specifies. List of publicly accessible URLs to multiple files, e.g. ['https://cdn.example.com/receipt1.jpg', 'https://cdn.example.com/receipt2.jpg']
     * @param array|null $categories Array of categories to use when categorizing the document
     * @param bool $auto_delete Delete this/these document(s) from Veryfi after data has been extracted
     * @param int $boost_mode Flag that tells Veryfi whether boost mode should be enabled. When set to 1, Veryfi will skip data enrichment steps, but will process the document faster. Default value for this flag is 0.
     * @param string|null $external_id Optional custom document identifier. Use this if you would like to assign your own ID to documents.
     * @param int|null $max_pages_to_process When sending a long document to Veryfi for processing, this parameter controls how many pages of the document will be read and processed, starting from page 1.
     * @param array $additional_request_parameters Additional request parameters.
     * @return string Data extracted from the document.
     */
    public function process_document_url(string $file_url = null,
                                         array $file_urls = null,
                                         array $categories = null,
                                         bool $auto_delete = false,
                                         int $boost_mode = 0,
                                         string $external_id = null,
                                         int $max_pages_to_process = null,
                                         array $additional_request_parameters = array()): string
    {
        $endpoint_name = '/documents/';
        $request_arguments = array(
            'auto_delete' => $auto_delete,
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
     * Delete Document from Veryfi. https://docs.veryfi.com/api/receipts-invoices/delete-a-document/
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
     * https://docs.veryfi.com/api/receipts-invoices/update-a-document/
     * <code>
     * $parameters = array('notes' => 'see me');
     * veryfi_client->update_document(id, $parameters);
     * </code>
     *
     * @param int $document_id  ID of the document you'd like to update.
     * @param array $fields_to_update Fields to update.
     * @return string A document json with updated fields, if fields are writable. Otherwise, a document with unchanged fields.
     */
    public function update_document(int $document_id,
                                    array $fields_to_update): string
    {
        $endpoint_name = "/documents/$document_id/";
        return $this->request('PUT', $endpoint_name, $fields_to_update);
    }

    /**
     * Retrieve all line items for a document. https://docs.veryfi.com/api/receipts-invoices/get-document-line-items/
     *
     * @param int $document_id ID of the document you'd like to retrieve
     * @return string List of line items extracted from the document as string
     */
    public function get_line_items(int $document_id): string
    {
        $endpoint_name = "/documents/$document_id/line-items/";
        $request_arguments = array();
        return $this->request('GET', $endpoint_name, $request_arguments);
    }

    /**
     * Retrieve a line item for existing document by ID. https://docs.veryfi.com/api/receipts-invoices/get-a-line-item/
     *
     * @param int $document_id ID of the document you'd like to retrieve
     * @param int $line_item_id ID of the line item you'd like to retrieve
     * @return string Line item extracted from the document as string
     */
    public function get_line_item(int $document_id,
                                  int $line_item_id): string
    {
        $endpoint_name = "/documents/$document_id/line-items/$line_item_id";
        $request_arguments = array();
        return $this->request('GET', $endpoint_name, $request_arguments);
    }

    /**
     * Add a new line item on an existing document. https://docs.veryfi.com/api/receipts-invoices/create-a-line-item/
     *
     * @param int $document_id ID of the document you'd like to update
     * @param AddLineItem $payload line item object to add
     * @return string Added line item data
     */
    public function add_line_item(int $document_id,
                                  AddLineItem $payload): string
    {
        $endpoint_name = "/documents/$document_id/line-items/";
        $request_arguments = array_filter(get_object_vars($payload), static function($var){return $var !== null;});
        return $this->request('POST', $endpoint_name, $request_arguments);
    }

    /**
     * Update an existing line item on an existing document. https://docs.veryfi.com/api/receipts-invoices/update-a-line-item/
     *
     * @param int $document_id ID of the document you'd like to update
     * @param int $line_item_id ID of the line item you'd like to update
     * @param UpdateLineItem $payload line item object to update
     * @return string Line item data with updated fields, if fields are writable. Otherwise, line item data with unchanged fields.
     */
    public function update_line_item(int $document_id,
                                     int $line_item_id,
                                     UpdateLineItem $payload): string
    {
        $endpoint_name = "/documents/$document_id/line-items/$line_item_id";
        $request_arguments = array_filter(get_object_vars($payload), static function($var){return $var !== null;});
        return $this->request('PUT', $endpoint_name, $request_arguments);
    }

    /**
     * Delete all line items on an existing document. https://docs.veryfi.com/api/receipts-invoices/delete-all-document-line-items/
     *
     * @param int $document_id  ID of the document you'd like to delete
     * @return string A JSON response.
     */
    public function delete_line_items(int $document_id): string
    {
        $endpoint_name = "/documents/$document_id/line-items/";
        $request_arguments = array();
        return $this->request('DELETE', $endpoint_name, $request_arguments);
    }

    /**
     * Delete an existing line item on an existing document. https://docs.veryfi.com/api/receipts-invoices/delete-a-line-item/
     *
     * @param int $document_id ID of the document you'd like to delete
     * @param int $line_item_id ID of the line item you'd like to delete
     * @return string A JSON response.
     */
    public function delete_line_item(int $document_id,
                                     int $line_item_id): string
    {
        $endpoint_name = "/documents/$document_id/line-items/$line_item_id";
        $request_arguments = array();
        return $this->request('DELETE', $endpoint_name, $request_arguments);
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

    /**
     * Add a new tag on an existing document. https://docs.veryfi.com/api/receipts-invoices/add-a-tag-to-a-document/
     *
     * @param int $document_id ID of the document you'd like to add a Tag
     * @param string $tag line item object to add
     * @return string Added tag data
     */
    public function add_tag(int $document_id,
                            string $tag): string
    {
        $endpoint_name = "/documents/$document_id/tags/";
        $request_arguments = array('name' => $tag);
        return $this->request('PUT', $endpoint_name, $request_arguments);
    }

    /**
     * Unlink all tags assigned to a specific document. https://docs.veryfi.com/api/receipts-invoices/unlink-all-tags-from-a-document/
     *
     * @param int $document_id ID of the document you'd like to delete their tags
     * @return string A JSON response.
     */
    public function delete_tags(int $document_id): string
    {
        $endpoint_name = "/documents/$document_id/tags/";
        $request_arguments = array();
        return $this->request('DELETE', $endpoint_name, $request_arguments);
    }

    /**
     * Get list of tags.
     *
     * @return string A JSON with list of tags.
     */
    public function get_tags(): string
    {
        $endpoint_name = '/tags/';
        $request_arguments = array();
        return $this->request('GET', $endpoint_name, $request_arguments, true);
    }

    /**
     * Retrieve list of tags by document ID. https://docs.veryfi.com/api/receipts-invoices/get-document-tags/
     *
     * @param int $document_id ID of the document you'd like to retrieve tags.
     * @return string A JSON with list of tags from the Document.
     */
    public function get_document_tags(int $document_id): string
    {
        $endpoint_name = "/documents/$document_id/tags/";
        $request_arguments = array('id' => $document_id);
        return $this->request('GET', $endpoint_name, $request_arguments);
    }

    /**
     * Unlink tag assigned to a specific document. https://docs.veryfi.com/api/receipts-invoices/unlink-a-tag-from-a-document/
     *
     * @param int $document_id ID of the document you'd like to delete its tag
     * @param int $tag_id ID of the tag you'd like to delete
     * @return string A JSON response.
     */
    public function delete_tag(int $document_id,
                               int $tag_id): string
    {
        $endpoint_name = "/documents/$document_id/tags/$tag_id/";
        $request_arguments = array();
        return $this->request('DELETE', $endpoint_name, $request_arguments);
    }

    /**
     * Add multiple tags on an existing document. https://docs.veryfi.com/api/receipts-invoices/add-tags-to-a-document/
     *
     * @param int $document_id ID of the document you'd like to add a Tag
     * @param array $tags array of strings
     * @return string Added tag data
     */
    public function add_tags(int $document_id,
                            array $tags): string
    {
        $endpoint_name = "/documents/$document_id/tags/";
        $request_arguments = array('tags' => $tags);
        return $this->request('POST', $endpoint_name, $request_arguments);
    }

    /**
     * Replace multiple tags on an existing document
     *
     * @param int $document_id ID of the document you'd like to add a Tag
     * @param array $tags array of strings
     * @return string Added tag data
     */
    public function replace_tags(int $document_id,
                             array $tags): string
    {
        $endpoint_name = "/documents/$document_id/";
        $request_arguments = array('tags' => $tags);
        return $this->request('PUT', $endpoint_name, $request_arguments);
    }

    /**
     * Get all W2 documents. https://docs.veryfi.com/api/w2s/get-w-2-s/
     *
     * @param int|null $page The page number, response is capped to a maximum of 50 results per page.
     * @param array $additional_request_parameters Additional request parameters
     * @return string An array of JSON with all W2 documents.
     * @throws Exception when API version is not supported for W2 documents.
     */
    public function get_w2_documents(int $page = null,  array $additional_request_parameters = []): string
    {
        $endpoint_name = '/w2s/';
        $request_arguments = array_merge([
            'page' => $page,
        ], $additional_request_parameters);
        return $this->request('GET', $endpoint_name, $request_arguments);
    }

    /**
     * Get a W2 document. https://docs.veryfi.com/api/w2s/get-a-w-2/
     *
     * @param string $document_id The ID of the document you'd like to retrieve.
     * @param array $additional_request_parameters Additional request parameters
     * @return string Data extracted from the document.
     */
    public function get_w2_document(string $document_id,  array $additional_request_parameters = []): string
    {
        $endpoint_name = "/w2s/{$document_id}/";
        $request_arguments = array_merge(['id' => $document_id], $additional_request_parameters);
        return $this->request('GET', $endpoint_name, $request_arguments);
    }

    /**
     * DELETE a W2 document. https://docs.veryfi.com/api/w2s/delete-a-w-2/
     *
     * @param string $document_id The ID of the document you'd like to retrieve.
     * @param array $additional_request_parameters Additional request parameters
     * @return string Data extracted from the document.
     */
    public function delete_w2_document(string $document_id,  array $additional_request_parameters = []): string
    {
        $endpoint_name = "/w2s/{$document_id}/";
        $request_arguments = array_merge(['id' => $document_id], $additional_request_parameters);
        return $this->request('DELETE', $endpoint_name, $request_arguments);
    }

    /**
     * Process a W2 document from a file path and extract all fields from it. https://docs.veryfi.com/api/w2s/process-a-w-2/
     *
     * @param string $file_path Path on disk to a file to submit for data extraction.
     * @param bool $auto_delete Delete this document from Veryfi after data has been extracted.
     * @param int $max_pages_to_process The number of pages to process for the document.
     * @param array $additional_request_parameters Additional request parameters.
     * @return string Data extracted from the document.
     */
    public function process_w2_document(string $file_path,
                                        bool $auto_delete = false,
                                        int $max_pages_to_process = 1,
                                        array $additional_request_parameters = array()): string
    {
        $endpoint_name = '/w2s/';
        $file_name = pathinfo($file_path, PATHINFO_BASENAME);
        $file = fopen($file_path, 'rb');
        $file_data = base64_encode(fread($file, filesize($file_path)));
        $request_arguments = array(
            'file_name' => $file_name,
            'file_data' => $file_data,
            'auto_delete' => $auto_delete,
            'max_pages_to_process' => $max_pages_to_process
        );
        $request_arguments = array_replace($request_arguments, $additional_request_parameters);
        return $this->request('POST', $endpoint_name, $request_arguments);
    }

    /**
     * Process a W2 document from a URL and extract all fields from it. https://docs.veryfi.com/api/w2s/process-a-w-2/
     *
     * @param string $file_name The file name including the extension
     * @param string $file_url Publicly accessible URL to a file
     * @param array|null $file_urls List of publicly accessible URLs to multiple files
     * @param boolean $auto_delete Delete this document from Veryfi after data has been extracted
     * @param int $max_pages_to_process The number of pages to process for the document
     * @param array $additional_request_parameters Additional request parameters
     * @return string Data extracted from the document
     */
    public function process_w2_document_from_url(string $file_name, string $file_url, array $file_urls = null, bool $auto_delete = false, int $max_pages_to_process = 1, array $additional_request_parameters = []): string
    {
        $endpoint_name = "/w2s/";
        $request_arguments = array_merge([
            'file_name' => $file_name,
            'auto_delete' => $auto_delete,
            'file_url' => $file_url,
            'file_urls' => $file_urls,
            'max_pages_to_process' => $max_pages_to_process
        ], $additional_request_parameters);

        return $this->request("POST", $endpoint_name, $request_arguments);
    }

    /**
     * Process any document and extract all the fields from it. https://docs.veryfi.com/api/anydocs/process-%E2%88%80-doc/
     *
     * @param string $file_path Path on disk to a file to submit for data extraction
     * @param string $template_name The name of the extraction template
     * @param int $max_pages_to_process The number of pages to process for the document
     * @param array $additional_request_parameters Additional request parameters
     * @return string Data extracted from the document
     */
    public function process_any_document_from_file(string $file_path, string $template_name, int $max_pages_to_process = 20, array $additional_request_parameters = []): string
    {
        $endpoint_name = "/any-documents/";
        $file_name = basename($file_path);
        $base64_encoded_string = base64_encode(file_get_contents($file_path));
        $request_arguments = array_merge([
            'file_name' => $file_name,
            'file_data' => $base64_encoded_string,
            'template_name' => $template_name,
            'max_pages_to_process' => $max_pages_to_process
        ], $additional_request_parameters);

        return $this->request("POST", $endpoint_name, $request_arguments);
    }

    /**
     * Process any document from a file path and extract all fields from it. https://docs.veryfi.com/api/anydocs/process-%E2%88%80-doc/
     *
     * @param string $file_path Path on disk to a file to submit for data extraction.
     * @param string $template_name The name of the extraction template.
     * @param int $max_pages_to_process The number of pages to process for the document.
     * @param array $additional_request_parameters Additional request parameters.
     * @return string Data extracted from the document.
     */
    public function process_any_document(string $file_path,
                                         string $template_name,
                                         int $max_pages_to_process = 20,
                                         array $additional_request_parameters = array()): string
    {
        $endpoint_name = '/any-documents/';
        $file_name = pathinfo($file_path, PATHINFO_BASENAME);
        $file = fopen($file_path, 'rb');
        $file_data = base64_encode(fread($file, filesize($file_path)));
        $request_arguments = array(
            'file_name' => $file_name,
            'file_data' => $file_data,
            'template_name' => $template_name,
            'max_pages_to_process' => $max_pages_to_process
        );
        $request_arguments = array_replace($request_arguments, $additional_request_parameters);
        return $this->request('POST', $endpoint_name, $request_arguments);
    }

    /**
     * Process any document from a URL and extract all fields from it. https://docs.veryfi.com/api/anydocs/process-%E2%88%80-doc/
     *
     * @param string $file_url Publicly accessible URL to a file
     * @param string $template_name The name of the extraction template
     * @param int $max_pages_to_process The number of pages to process for the document
     * @param array $additional_request_parameters Additional request parameters
     * @return string Data extracted from the document
     */
    public function process_any_document_url(string $file_url, string $template_name, int $max_pages_to_process = 20, array $additional_request_parameters = []): string
    {
        $endpoint_name = "/any-documents/";
        $request_arguments = array_merge([
            'file_url' => $file_url,
            'template_name' => $template_name,
            'max_pages_to_process' => $max_pages_to_process
        ], $additional_request_parameters);

        return $this->request("POST", $endpoint_name, $request_arguments);
    }

    /**
     * Get all any documents. https://docs.veryfi.com/api/anydocs/get-%E2%88%80-docs/
     *
     * @param int $page The page number
     * @param int $page_size The number of documents per page
     * @param array $additional_request_parameters Additional request parameters
     * @return string Object of previously processed any documents
     */
    public function get_any_documents(int $page = 1, int $page_size = 50, array $additional_request_parameters = []): string
    {
        $endpoint_name = "/any-documents/";
        $request_arguments = array_merge(['page' => $page, 'page_size' => $page_size], $additional_request_parameters);
        return $this->request("GET", $endpoint_name, $request_arguments);
    }

    /**
     * Get a specific any document. https://docs.veryfi.com/api/anydocs/get-a-%E2%88%80-doc/
     *
     * @param int $document_id The unique identifier of the document
     * @param array $additional_request_parameters Additional request parameters
     * @return string Object of a previously processed document
     */
    public function get_any_document(int $document_id, array $additional_request_parameters = array()): string
    {
        $endpoint_name = "/any-documents/$document_id/";
        return $this->request("GET", $endpoint_name, $additional_request_parameters);
    }

    /**
     * Get a specific bank statement. https://docs.veryfi.com/api/bank-statements/get-a-bank-statement/
     *
     * @param int $document_id The unique identifier of the document
     * @param boolean $bounding_boxes Return bounding box and bounding region for extracted fields
     * @param boolean $confidence_details Return the score and OCR score fields in the document response
     * @param array $additional_request_parameters Additional request parameters
     * @return string Object of a previously processed bank statement
     */
    public function get_bank_statement(int $document_id, bool $bounding_boxes = false, bool $confidence_details = false, array $additional_request_parameters = array()): string
    {
        $endpoint_name = "/bank-statements/$document_id/";
        $request_arguments = array_merge([
            'bounding_boxes' => $bounding_boxes,
            'confidence_details' => $confidence_details
        ], $additional_request_parameters);
        return $this->request("GET", $endpoint_name, $request_arguments);
    }

    /**
     * Get all bank statements. https://docs.veryfi.com/api/bank-statements/get-bank-statements/
     *
     * @param int $page The page number
     * @param int $page_size The number of documents per page
     * @param boolean $bounding_boxes Return bounding box and bounding region for extracted fields
     * @param boolean $confidence_details Return the score and OCR score fields in the document response
     * @param array $additional_request_parameters Additional request parameters
     * @return string Object of previously processed bank statements
     */
    public function get_bank_statements(int $page = 1, int $page_size = 50, bool $bounding_boxes = false, bool $confidence_details = false, array $additional_request_parameters = array()): string
    {
        $endpoint_name = "/bank-statements/";
        $request_arguments = array_merge([
            'page' => $page,
            'page_size' => $page_size,
            'bounding_boxes' => $bounding_boxes,
            'confidence_details' => $confidence_details
        ], $additional_request_parameters);
        return $this->request("GET", $endpoint_name, $request_arguments);
    }

    /**
     * Process a bank statement from a file path and extract all fields from it. https://docs.veryfi.com/api/bank-statements/process-a-bank-statement/
     *
     * @param string $file_path Path on disk to a file to submit for data extraction.
     * @param bool $bounding_boxes Return bounding box and bounding region for extracted fields.
     * @param bool $confidence_details Return the score and OCR score fields in the document response.
     * @param array $additional_request_parameters Additional request parameters.
     * @return string Data extracted from the document.
     */
    public function process_bank_statement(string $file_path,
                                           bool $bounding_boxes = false,
                                           bool $confidence_details = false,
                                           array $additional_request_parameters = array()): string
    {
        $endpoint_name = '/bank-statements/';
        $file_name = pathinfo($file_path, PATHINFO_BASENAME);
        $file = fopen($file_path, 'rb');
        $file_data = base64_encode(fread($file, filesize($file_path)));
        $request_arguments = array(
            'file_name' => $file_name,
            'file_data' => $file_data,
            'bounding_boxes' => $bounding_boxes,
            'confidence_details' => $confidence_details
        );
        $request_arguments = array_replace($request_arguments, $additional_request_parameters);
        return $this->request('POST', $endpoint_name, $request_arguments);
    }

    /**
     * Process a bank statement and extract all fields from it. https://docs.veryfi.com/api/bank-statements/process-a-bank-statement/
     *
     * @param string $file_path Path on disk to a file to submit for data extraction
     * @param boolean $bounding_boxes Return bounding box and bounding region for extracted fields
     * @param boolean $confidence_details Return the score and OCR score fields in the document response
     * @param array $additional_request_parameters Additional request parameters
     * @return string Data extracted from the document
     */
    public function process_bank_statement_from_file(string $file_path, bool $bounding_boxes = false, bool $confidence_details = false, array $additional_request_parameters = []): string
    {
        $endpoint_name = "/bank-statements/";
        $file_name = basename($file_path);
        $base64_encoded_string = base64_encode(file_get_contents($file_path));
        $request_arguments = array_merge([
            'file_name' => $file_name,
            'file_data' => $base64_encoded_string,
            'bounding_boxes' => $bounding_boxes,
            'confidence_details' => $confidence_details
        ], $additional_request_parameters);

        return $this->request("POST", $endpoint_name, $request_arguments);
    }

    /**
     * Process a bank statement from a URL and extract all fields from it. https://docs.veryfi.com/api/bank-statements/process-a-bank-statement/
     *
     * @param string $file_url Publicly accessible URL to a file
     * @param boolean $bounding_boxes Return bounding box and bounding region for extracted fields
     * @param boolean $confidence_details Return the score and OCR score fields in the document response
     * @param array $additional_request_parameters Additional request parameters
     * @return string Data extracted from the document
     */
    public function process_bank_statement_url(string $file_url, bool $bounding_boxes = false, bool $confidence_details = false, array $additional_request_parameters = []): string
    {
        $endpoint_name = "/bank-statements/";
        $request_arguments = array_merge([
            'file_url' => $file_url,
            'bounding_boxes' => $bounding_boxes,
            'confidence_details' => $confidence_details
        ], $additional_request_parameters);

        return $this->request("POST", $endpoint_name, $request_arguments);
    }
}
