https://veryfi.github.io/veryfi-php/

![Veryfi Logo](https://cdn.veryfi.com/logos/veryfi-logo-wide-github.png)

[![php - version](https://img.shields.io/badge/php->=8-8892BF)](https://www.php.net/releases/8.0/en.php)
[![License: MIT](https://img.shields.io/badge/License-MIT-green.svg)](https://opensource.org/licenses/MIT)
[![code coverage](./metrics/code_coverage.svg)](./metrics/code_coverage.svg)
[![methods coverage](./metrics/methods_coverage.svg)](./metrics/methods_coverage.svg)



**Veryfi** is a php module for communicating with the [Veryfi OCR API](https://veryfi.com/api/)

## Installation
In your project root run
```bash
composer require veryfi/veryfi-php
```


## Getting Started

### Obtaining Client ID and user keys
If you don't have an account with Veryfi, please go ahead and register here: [https://hub.veryfi.com/signup/api/](https://hub.veryfi.com/signup/api/)

### php API Client Library
The **Veryfi** library can be used to communicate with Veryfi API. All available functionality is described here DOC

Below is the sample script using **Veryfi** to OCR and extract data from a document:

### Process a document
```php
use veryfi\Client;

$client_id = 'your_client_id';
$client_secret = 'your_client_secret';
$username = 'your_username';
$api_key = 'your_password';

$veryfi_client = new Client($client_id, $client_secret, $username, $api_key);
$categories = array('Advertising & Marketing', 'Automotive');
$file = 'path_to_your_image';
$return_associative = true;
$delete_after_processing = true;
$json_response = json_decode($veryfi_client->process_document($file, $categories, $delete_after_processing), $return_associative);
``` 

### Update a document
```php
use veryfi\Client;

$client_id = 'your_client_id';
$client_secret = 'your_client_secret';
$username = 'your_username';
$api_key = 'your_password';

$veryfi_client = new Client($client_id, $client_secret, $username, $api_key);
$document_id = 'your_document_id' //as int
$parameters = array('category' => 'Meals & Entertainment',
                    'total' => 11.23);
$return_associative = true;
$json_response = json_decode($veryfi_client->update_document($document_id, $parameters), $return_associative);
```


## Need help?
If you run into any issue or need help installing or using the library, please contact support@veryfi.com.

If you found a bug in this library or would like new features added, then open an issue or pull requests against this repo!

To learn more about Veryfi visit https://www.veryfi.com/

## Tutorial


Below is an introduction to the php SDK.


