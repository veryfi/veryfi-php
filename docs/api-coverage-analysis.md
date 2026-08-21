# Veryfi PHP SDK — Official API Coverage Analysis

**SDK:** `veryfi/veryfi-php` (PHP >= 7.4)

**Source of truth:** [https://docs.veryfi.com/](https://docs.veryfi.com/) (sitemap retrieved 2026-08-21)

**Scope:** Public HTTP API operations documented under `/api/` excluding Getting Started guides, Lens, Fraud conceptual pages, and the v7 overview. Interactive API and authentication conceptual pages are not counted as operations.

This report does **not** change SDK source. It compares each documented operation to the public `veryfi\Client` methods.

## SDK architecture (this repository)

- Language: **PHP** (`composer.json`, namespace `veryfi\`).
- Public surface: a single `Client` class composed from resource traits under `src/veryfi/`.
- HTTP: custom cURL client in `src/veryfi/client/Request.php` (no extra HTTP library).
- Base URL: `https://api.veryfi.com/api/` + `v8` + `/partner` (`GetUrl.php`).
- Auth: `Client-ID` header, `Authorization: apikey {username}:{api_key}`, plus HMAC `X-Veryfi-Request-Timestamp` / `X-Veryfi-Request-Signature` (`GetHeaders.php`, `GenerateSignature.php`). Matches [standard key authentication](https://docs.veryfi.com/api/getting-started/authentication/standard-keys/).
- Responses: raw JSON **strings** (`string` return types). The only public request models are `LineItem`, `LineItemUpdate`, and `SharedLineItem`.
- Extra parameters: most methods accept `$kwargs` and merge them into the payload.
- Upload modes: many resources expose three helpers for one POST route — filesystem (`file` / `CURLFile`), base64 (`file_data`), and URL (`file_url` / `file_urls`).

### Cross-cutting HTTP client behavior (affects coverage)

1. `Request::request()` always sets `CURLOPT_POSTFIELDS` (JSON-encoded unless `$has_files` is true). **GET/DELETE query parameters documented on list/get endpoints are not placed on the query string.**
2. Headers always include `Content-Type: application/json`, including when `$has_files` is true (multipart `CURLFile`). That can prevent a correct multipart boundary content type.
3. `process_w2()` and `process_w9()` attach `CURLFile` but call `request()` **without** `$has_files = true`, so the file is JSON-encoded instead of uploaded as multipart.

## Discovery notes

- **151** operation pages were taken from `https://docs.veryfi.com/sitemap.xml` (paths under `/api/` minus getting-started / v7 / overview).
- Each page was opened and parsed for HTTP method, route, content types, path/query/body parameter names.
- Matching is **HTTP method + normalized route** (`:document_id` ≡ `{document_id}` ≡ `$document_id`; trailing slashes ignored; `/api/v8/partner` prefix stripped).
- Upload-mode helpers that share one method+route count as **one** API operation.
- Dedicated `POST .../async` routes are **separate** operations from the sync process route.
- Settings API keys live under **`/api/v1/partner/settings/api-keys`**, not v8. The SDK hard-defaults to v8.

## Coverage classification rules used

| Class | Meaning in this audit |
|---|---|
| IMPLEMENTED | Public method(s), correct method+route, documented parameters can be sent in the documented location/encoding (including `$kwargs` for optional POST/PUT JSON body fields). |
| PARTIAL | Method exists and route/verb match, but parameters, encoding, query-string handling, upload mode, or request models are incomplete or incorrect. |
| MISSING | No public method uses that method+route. |
| UNCERTAIN | Not used: every sitemap operation was mapped or explicitly marked missing. |

`$kwargs` is treated as supporting optional **JSON body** fields. It is **not** treated as correct support for **query** parameters, because the client does not serialize them onto the URL.

## Primary coverage table

| Product | API Operation | SDK Method | HTTP Method | Route | Docs URL | Endpoint Coverage | Parameter Coverage | Tests | Recommended Action |
|---|---|---|---|---|---|---|---|---|---|
| AnyDocs | Get ∀Docs | `get_any_documents()` | GET | `/api/v8/partner/any-documents` | [Get ∀Docs](https://docs.veryfi.com/api/anydocs/get-A-docs/) | PARTIAL | PARTIAL | Yes | Send documented query parameters as a query string, not a JSON body. |
| AnyDocs | Process a ∀Doc | `process_any_document()`, `process_any_document_base64()`, `process_any_document_url()` | POST | `/api/v8/partner/any-documents` | [Process a ∀Doc](https://docs.veryfi.com/api/anydocs/process-a-A-doc/) | PARTIAL | PARTIAL | Yes | Align AnyDocs process parameter with documented `blueprint_name` (keep `template_name` as alias if needed). Add `auto_delete`. Expose remaining kwargs in PHPDoc. |
| AnyDocs | Delete a ∀Doc | `delete_any_document()` | DELETE | `/api/v8/partner/any-documents/:document_id` | [Delete a ∀Doc](https://docs.veryfi.com/api/anydocs/delete-a-A-doc/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| AnyDocs | Get a ∀Doc | `get_any_document()` | GET | `/api/v8/partner/any-documents/:document_id` | [Get a ∀Doc](https://docs.veryfi.com/api/anydocs/get-a-A-doc/) | PARTIAL | PARTIAL | Yes | Send documented query parameters as a query string, not a JSON body. |
| AnyDocs | Update a ∀Doc | — | PUT | `/api/v8/partner/any-documents/:document_id` | [Update a ∀Doc](https://docs.veryfi.com/api/anydocs/update-a-A-doc/) | MISSING | MISSING | No | Add an update method for this resource. |
| AnyDocs | Unlink all tags from a ∀Doc | — | DELETE | `/api/v8/partner/any-documents/:document_id/tags` | [Unlink all tags from a ∀Doc](https://docs.veryfi.com/api/anydocs/unlink-all-tags-from-a-A-doc/) | MISSING | MISSING | No | Add tag methods for this resource, following the receipts-invoices tag pattern. |
| AnyDocs | Get ∀Doc tags | — | GET | `/api/v8/partner/any-documents/:document_id/tags` | [Get ∀Doc tags](https://docs.veryfi.com/api/anydocs/get-A-doc-tags/) | MISSING | MISSING | No | Add a public Client method for this operation. |
| AnyDocs | Add tags to a ∀Doc | — | POST | `/api/v8/partner/any-documents/:document_id/tags` | [Add tags to a ∀Doc](https://docs.veryfi.com/api/anydocs/add-tags-to-a-A-doc/) | MISSING | MISSING | No | Add tag methods for this resource, following the receipts-invoices tag pattern. |
| AnyDocs | Add a tag to a ∀Doc | — | PUT | `/api/v8/partner/any-documents/:document_id/tags` | [Add a tag to a ∀Doc](https://docs.veryfi.com/api/anydocs/add-a-tag-to-a-A-doc/) | MISSING | MISSING | No | Add tag methods for this resource, following the receipts-invoices tag pattern. |
| AnyDocs | Unlink a tag from a ∀Doc | — | DELETE | `/api/v8/partner/any-documents/:document_id/tags/:tag_id` | [Unlink a tag from a ∀Doc](https://docs.veryfi.com/api/anydocs/unlink-a-tag-from-a-A-doc/) | MISSING | MISSING | No | Add tag methods for this resource, following the receipts-invoices tag pattern. |
| AnyDocs | Process a ∀Doc asynchronously | — | POST | `/api/v8/partner/any-documents/async` | [Process a ∀Doc asynchronously](https://docs.veryfi.com/api/anydocs/process-a-A-doc-asynchronously/) | MISSING | MISSING | No | Add a dedicated async process method (or route `.../async`) for this resource. |
| AnyDocs | Get Blueprints | — | GET | `/api/v8/partner/blueprints` | [Get Blueprints](https://docs.veryfi.com/api/get-blueprints/) | MISSING | MISSING | No | Add a public Client method for this operation. |
| Bank Statements | Get Bank Statements | `get_bank_statements()` | GET | `/api/v8/partner/bank-statements` | [Get Bank Statements](https://docs.veryfi.com/api/bank-statements/get-bank-statements/) | PARTIAL | PARTIAL | Yes | Send documented query parameters as a query string, not a JSON body. |
| Bank Statements | Process a Bank Statement | `process_bank_statement()`, `process_bank_statement_base64()`, `process_bank_statement_url()` | POST | `/api/v8/partner/bank-statements` | [Process a Bank Statement](https://docs.veryfi.com/api/bank-statements/process-a-bank-statement/) | IMPLEMENTED | IMPLEMENTED | Yes | Optional extras are already forwarded via `$kwargs`. Document remaining process parameters in PHPDoc/README. |
| Bank Statements | Get Bank Statement sets | — | GET | `/api/v8/partner/bank-statements-set` | [Get Bank Statement sets](https://docs.veryfi.com/api/get-bank-statement-sets/) | MISSING | MISSING | No | Add a public Client method for this operation. |
| Bank Statements | Split and process multiple Bank Statements | — | POST | `/api/v8/partner/bank-statements-set` | [Split and process multiple Bank Statements](https://docs.veryfi.com/api/split-and-process-multiple-bank-statements/) | MISSING | MISSING | No | Add a public Client method for this operation. |
| Bank Statements | Get a Bank Statement set | — | GET | `/api/v8/partner/bank-statements-set/:document_id` | [Get a Bank Statement set](https://docs.veryfi.com/api/get-a-bank-statement-set/) | MISSING | MISSING | No | Add a public Client method for this operation. |
| Bank Statements | Delete a Bank Statement | `delete_bank_statement()` | DELETE | `/api/v8/partner/bank-statements/:document_id` | [Delete a Bank Statement](https://docs.veryfi.com/api/bank-statements/delete-a-bank-statement/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Bank Statements | Get a Bank Statement | `get_bank_statement()` | GET | `/api/v8/partner/bank-statements/:document_id` | [Get a Bank Statement](https://docs.veryfi.com/api/bank-statements/get-a-bank-statement/) | PARTIAL | PARTIAL | Yes | Send documented query parameters as a query string, not a JSON body. |
| Bank Statements | Update a Bank Statement | — | PUT | `/api/v8/partner/bank-statements/:document_id` | [Update a Bank Statement](https://docs.veryfi.com/api/bank-statements/update-a-bank-statement/) | MISSING | MISSING | No | Add an update method for this resource. |
| Bank Statements | Unlink all tags from a Bank Statement | — | DELETE | `/api/v8/partner/bank-statements/:document_id/tags` | [Unlink all tags from a Bank Statement](https://docs.veryfi.com/api/bank-statements/unlink-all-tags-from-a-bank-statement/) | MISSING | MISSING | No | Add tag methods for this resource, following the receipts-invoices tag pattern. |
| Bank Statements | Get Bank Statement tags | — | GET | `/api/v8/partner/bank-statements/:document_id/tags` | [Get Bank Statement tags](https://docs.veryfi.com/api/bank-statements/get-bank-statement-tags/) | MISSING | MISSING | No | Add a public Client method for this operation. |
| Bank Statements | Add tags to a Bank Statement | — | POST | `/api/v8/partner/bank-statements/:document_id/tags` | [Add tags to a Bank Statement](https://docs.veryfi.com/api/bank-statements/add-tags-to-a-bank-statement/) | MISSING | MISSING | No | Add tag methods for this resource, following the receipts-invoices tag pattern. |
| Bank Statements | Add a tag to a Bank Statement | — | PUT | `/api/v8/partner/bank-statements/:document_id/tags` | [Add a tag to a Bank Statement](https://docs.veryfi.com/api/bank-statements/add-a-tag-to-a-bank-statement/) | MISSING | MISSING | No | Add tag methods for this resource, following the receipts-invoices tag pattern. |
| Bank Statements | Unlink a tag from a Bank Statement | — | DELETE | `/api/v8/partner/bank-statements/:document_id/tags/:tag_id` | [Unlink a tag from a Bank Statement](https://docs.veryfi.com/api/bank-statements/unlink-a-tag-from-a-bank-statement/) | MISSING | MISSING | No | Add tag methods for this resource, following the receipts-invoices tag pattern. |
| Bank Statements | Process a Bank Statement asynchronously | — | POST | `/api/v8/partner/bank-statements/async` | [Process a Bank Statement asynchronously](https://docs.veryfi.com/api/bank-statements/process-a-bank-statement-asynchronously/) | MISSING | MISSING | No | Add a dedicated async process method (or route `.../async`) for this resource. |
| Business Cards | Get Business Cards | `get_business_cards()` | GET | `/api/v8/partner/business-cards` | [Get Business Cards](https://docs.veryfi.com/api/business-cards/get-business-cards/) | PARTIAL | PARTIAL | Yes | Send documented query parameters as a query string, not a JSON body. |
| Business Cards | Process a Business Card | `process_business_card()`, `process_business_card_base64()`, `process_business_card_from_url()` | POST | `/api/v8/partner/business-cards` | [Process a Business Card](https://docs.veryfi.com/api/business-cards/process-a-business-card/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Business Cards | Delete a Business Card | `delete_business_card()` | DELETE | `/api/v8/partner/business-cards/:document_id` | [Delete a Business Card](https://docs.veryfi.com/api/business-cards/delete-a-business-card/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Business Cards | Get a Business Card | `get_business_card()` | GET | `/api/v8/partner/business-cards/:document_id` | [Get a Business Card](https://docs.veryfi.com/api/business-cards/get-a-business-card/) | PARTIAL | PARTIAL | No | Send documented query parameters as a query string, not a JSON body. Add tests. |
| Business Cards | Update a Business Card | — | PUT | `/api/v8/partner/business-cards/:document_id` | [Update a Business Card](https://docs.veryfi.com/api/business-cards/update-a-business-card/) | MISSING | MISSING | No | Add an update method for this resource. |
| Business Cards | Unlink all tags from a Business Card | — | DELETE | `/api/v8/partner/business-cards/:document_id/tags` | [Unlink all tags from a Business Card](https://docs.veryfi.com/api/unlink-all-tags-from-a-business-card/) | MISSING | MISSING | No | Add tag methods for this resource, following the receipts-invoices tag pattern. |
| Business Cards | Get Business Card tags | — | GET | `/api/v8/partner/business-cards/:document_id/tags` | [Get Business Card tags](https://docs.veryfi.com/api/get-business-card-tags/) | MISSING | MISSING | No | Add a public Client method for this operation. |
| Business Cards | Add tags to a Business Card | — | POST | `/api/v8/partner/business-cards/:document_id/tags` | [Add tags to a Business Card](https://docs.veryfi.com/api/add-tags-to-a-business-card/) | MISSING | MISSING | No | Add tag methods for this resource, following the receipts-invoices tag pattern. |
| Business Cards | Add a tag to a Business Card | — | PUT | `/api/v8/partner/business-cards/:document_id/tags` | [Add a tag to a Business Card](https://docs.veryfi.com/api/add-a-tag-to-a-business-card/) | MISSING | MISSING | No | Add tag methods for this resource, following the receipts-invoices tag pattern. |
| Business Cards | Unlink a tag from a Business Card | — | DELETE | `/api/v8/partner/business-cards/:document_id/tags/:tag_id` | [Unlink a tag from a Business Card](https://docs.veryfi.com/api/unlink-a-tag-from-a-business-card/) | MISSING | MISSING | No | Add tag methods for this resource, following the receipts-invoices tag pattern. |
| Checks | Process a Check With Remittance | — | POST | `/api/v8/partner/check-with-document` | [Process a Check With Remittance](https://docs.veryfi.com/api/checks/process-a-check-with-remittance/) | MISSING | MISSING | No | Add process-check-with-remittance method posting to `/check-with-document`. |
| Checks | Get Checks | `get_checks()` | GET | `/api/v8/partner/checks` | [Get Checks](https://docs.veryfi.com/api/checks/get-checks/) | PARTIAL | PARTIAL | Yes | Send documented query parameters as a query string, not a JSON body. |
| Checks | Process a Check | `process_check()`, `process_check_base64()`, `process_check_from_url()` | POST | `/api/v8/partner/checks` | [Process a Check](https://docs.veryfi.com/api/checks/process-a-check/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Checks | Delete a Check | `delete_check()` | DELETE | `/api/v8/partner/checks/:document_id` | [Delete a Check](https://docs.veryfi.com/api/checks/delete-a-check/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Checks | Get a Check | `get_check()` | GET | `/api/v8/partner/checks/:document_id` | [Get a Check](https://docs.veryfi.com/api/checks/get-a-check/) | PARTIAL | PARTIAL | No | Send documented query parameters as a query string, not a JSON body. Add tests. |
| Checks | Update a Check | — | PUT | `/api/v8/partner/checks/:document_id` | [Update a Check](https://docs.veryfi.com/api/checks/update-a-check/) | MISSING | MISSING | No | Add an update method for this resource. |
| Checks | Unlink all tags from a Check | — | DELETE | `/api/v8/partner/checks/:document_id/tags` | [Unlink all tags from a Check](https://docs.veryfi.com/api/checks/unlink-all-tags-from-a-check/) | MISSING | MISSING | No | Add tag methods for this resource, following the receipts-invoices tag pattern. |
| Checks | Get Check tags | — | GET | `/api/v8/partner/checks/:document_id/tags` | [Get Check tags](https://docs.veryfi.com/api/checks/get-check-tags/) | MISSING | MISSING | No | Add a public Client method for this operation. |
| Checks | Add tags to a Check | — | POST | `/api/v8/partner/checks/:document_id/tags` | [Add tags to a Check](https://docs.veryfi.com/api/checks/add-tags-to-a-check/) | MISSING | MISSING | No | Add tag methods for this resource, following the receipts-invoices tag pattern. |
| Checks | Add a tag to a Check | — | PUT | `/api/v8/partner/checks/:document_id/tags` | [Add a tag to a Check](https://docs.veryfi.com/api/checks/add-a-tag-to-a-check/) | MISSING | MISSING | No | Add tag methods for this resource, following the receipts-invoices tag pattern. |
| Checks | Unlink a tag from a Check | — | DELETE | `/api/v8/partner/checks/:document_id/tags/:tag_id` | [Unlink a tag from a Check](https://docs.veryfi.com/api/checks/unlink-a-tag-from-a-check/) | MISSING | MISSING | No | Add tag methods for this resource, following the receipts-invoices tag pattern. |
| Checks | Process a Check asynchronously | — | POST | `/api/v8/partner/checks/async` | [Process a Check asynchronously](https://docs.veryfi.com/api/checks/process-a-check-asynchronously/) | MISSING | MISSING | No | Add a dedicated async process method (or route `.../async`) for this resource. |
| Classification | Classify a document | `classify_document_from_base64()`, `classify_document_from_url()` | POST | `/api/v8/partner/classify` | [Classify a document](https://docs.veryfi.com/api/classify/classify-a-document/) | PARTIAL | PARTIAL | Yes | Add multipart `file` upload helper; currently only URL and base64. |
| Classification | Classify and possibly extract data from a document | — | POST | `/api/v8/partner/extract` | [Classify and possibly extract data from a document](https://docs.veryfi.com/api/classify-and-possibly-extract-data-from-a-document/) | MISSING | MISSING | No | Add `extract`/`classify-and-extract` public method posting to `/extract`. |
| Contracts | Get Contracts | — | GET | `/api/v8/partner/contracts` | [Get Contracts](https://docs.veryfi.com/api/contracts/get-contracts/) | MISSING | MISSING | No | Add a public Client method for this operation. |
| Contracts | Process a Contract | — | POST | `/api/v8/partner/contracts` | [Process a Contract](https://docs.veryfi.com/api/contracts/process-a-contract/) | MISSING | MISSING | No | Add a public Client method for this operation. |
| Contracts | Delete a Contract | — | DELETE | `/api/v8/partner/contracts/:document_id` | [Delete a Contract](https://docs.veryfi.com/api/contracts/delete-a-contract/) | MISSING | MISSING | No | Add a public Client method for this operation. |
| Contracts | Get a Contract | — | GET | `/api/v8/partner/contracts/:document_id` | [Get a Contract](https://docs.veryfi.com/api/contracts/get-a-contract/) | MISSING | MISSING | No | Add a public Client method for this operation. |
| Contracts | Update a Contract | — | PUT | `/api/v8/partner/contracts/:document_id` | [Update a Contract](https://docs.veryfi.com/api/contracts/update-a-contract/) | MISSING | MISSING | No | Add an update method for this resource. |
| Contracts | Unlink all tags from a Contract | — | DELETE | `/api/v8/partner/contracts/:document_id/tags` | [Unlink all tags from a Contract](https://docs.veryfi.com/api/unlink-all-tags-from-a-contract/) | MISSING | MISSING | No | Add tag methods for this resource, following the receipts-invoices tag pattern. |
| Contracts | Get Contract tags | — | GET | `/api/v8/partner/contracts/:document_id/tags` | [Get Contract tags](https://docs.veryfi.com/api/get-contract-tags/) | MISSING | MISSING | No | Add a public Client method for this operation. |
| Contracts | Add tags to a Contract | — | POST | `/api/v8/partner/contracts/:document_id/tags` | [Add tags to a Contract](https://docs.veryfi.com/api/add-tags-to-a-contract/) | MISSING | MISSING | No | Add tag methods for this resource, following the receipts-invoices tag pattern. |
| Contracts | Add a tag to a Contract | — | PUT | `/api/v8/partner/contracts/:document_id/tags` | [Add a tag to a Contract](https://docs.veryfi.com/api/add-a-tag-to-a-contract/) | MISSING | MISSING | No | Add tag methods for this resource, following the receipts-invoices tag pattern. |
| Contracts | Unlink a tag from a Contract | — | DELETE | `/api/v8/partner/contracts/:document_id/tags/:tag_id` | [Unlink a tag from a Contract](https://docs.veryfi.com/api/unlink-a-tag-from-a-contract/) | MISSING | MISSING | No | Add tag methods for this resource, following the receipts-invoices tag pattern. |
| Fraud Detection | Get devices from blocklist | — | GET | `/api/v8/partner/fraud/blocklist` | [Get devices from blocklist](https://docs.veryfi.com/api/get-devices-from-blocklist/) | MISSING | MISSING | No | Add a public Client method for this operation. |
| Fraud Detection | Add devices to blocklist | — | POST | `/api/v8/partner/fraud/blocklist` | [Add devices to blocklist](https://docs.veryfi.com/api/add-devices-to-blocklist/) | MISSING | MISSING | No | Add a public Client method for this operation. |
| Fraud Detection | Remove a device from blocklist | — | DELETE | `/api/v8/partner/fraud/blocklist/:device_id` | [Remove a device from blocklist](https://docs.veryfi.com/api/remove-a-device-from-blocklist/) | MISSING | MISSING | No | Add a public Client method for this operation. |
| Parse Documents | Get Markdown Documents | — | GET | `/api/v8/partner/parse` | [Get Markdown Documents](https://docs.veryfi.com/api/parse/get-markdown-documents/) | MISSING | MISSING | No | Add a public Client method for this operation. |
| Parse Documents | Convert a Document to Markdown | — | POST | `/api/v8/partner/parse` | [Convert a Document to Markdown](https://docs.veryfi.com/api/parse/convert-a-document-to-markdown/) | MISSING | MISSING | No | Add a public Client method for this operation. |
| Parse Documents | Get Markdown Document Sets | — | GET | `/api/v8/partner/parse-set` | [Get Markdown Document Sets](https://docs.veryfi.com/api/parse/get-markdown-document-sets/) | MISSING | MISSING | No | Add a public Client method for this operation. |
| Parse Documents | Process a Markdown Document Set | — | POST | `/api/v8/partner/parse-set` | [Process a Markdown Document Set](https://docs.veryfi.com/api/parse/process-a-markdown-document-set/) | MISSING | MISSING | No | Add a public Client method for this operation. |
| Parse Documents | Get a Markdown Document Set | — | GET | `/api/v8/partner/parse-set/:document_id` | [Get a Markdown Document Set](https://docs.veryfi.com/api/parse/get-a-markdown-document-set/) | MISSING | MISSING | No | Add a public Client method for this operation. |
| Parse Documents | Delete a Markdown Document | — | DELETE | `/api/v8/partner/parse/:document_id` | [Delete a Markdown Document](https://docs.veryfi.com/api/parse/delete-a-markdown-document/) | MISSING | MISSING | No | Add a public Client method for this operation. |
| Parse Documents | Get a Markdown Document | — | GET | `/api/v8/partner/parse/:document_id` | [Get a Markdown Document](https://docs.veryfi.com/api/parse/get-a-markdown-document/) | MISSING | MISSING | No | Add a public Client method for this operation. |
| Parse Documents | Update a Markdown Document | — | PUT | `/api/v8/partner/parse/:document_id` | [Update a Markdown Document](https://docs.veryfi.com/api/parse/update-a-markdown-document/) | MISSING | MISSING | No | Add an update method for this resource. |
| Parse Documents | Process a Markdown Document asynchronously | — | POST | `/api/v8/partner/parse/async` | [Process a Markdown Document asynchronously](https://docs.veryfi.com/api/parse/process-a-markdown-document-asynchronously/) | MISSING | MISSING | No | Add a dedicated async process method (or route `.../async`) for this resource. |
| Receipts & Invoices | Search Documents | `get_documents()` | GET | `/api/v8/partner/documents` | [Search Documents](https://docs.veryfi.com/api/receipts-invoices/search-documents/) | PARTIAL | PARTIAL | Yes | Send documented query parameters as a query string, not a JSON body. |
| Receipts & Invoices | Process a Document | `process_document()`, `process_document_base64()`, `process_document_url()` | POST | `/api/v8/partner/documents` | [Process a Document](https://docs.veryfi.com/api/receipts-invoices/process-a-document/) | IMPLEMENTED | IMPLEMENTED | Yes | Optional extras are already forwarded via `$kwargs`. Document remaining process parameters in PHPDoc/README. |
| Receipts & Invoices | Get Submitted PDF | `get_split_documents()` | GET | `/api/v8/partner/documents-set` | [Get Submitted PDF](https://docs.veryfi.com/api/receipts-invoices/get-submitted-pdf/) | PARTIAL | PARTIAL | Yes | Send documented query parameters as a query string, not a JSON body. |
| Receipts & Invoices | Split and process a PDF | `split_document_from_base64()`, `split_document_from_url()` | POST | `/api/v8/partner/documents-set` | [Split and process a PDF](https://docs.veryfi.com/api/receipts-invoices/split-and-process-a-pdf/) | PARTIAL | PARTIAL | Yes | Add multipart `file` upload helper; currently only URL and base64. |
| Receipts & Invoices | Get Documents from PDF | `get_split_document()` | GET | `/api/v8/partner/documents-set/:document_id` | [Get Documents from PDF](https://docs.veryfi.com/api/receipts-invoices/get-documents-from-pdf/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Receipts & Invoices | Delete a Document | `delete_document()` | DELETE | `/api/v8/partner/documents/:document_id` | [Delete a Document](https://docs.veryfi.com/api/receipts-invoices/delete-a-document/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Receipts & Invoices | Get a Document | `get_document()` | GET | `/api/v8/partner/documents/:document_id` | [Get a Document](https://docs.veryfi.com/api/receipts-invoices/get-a-document/) | PARTIAL | PARTIAL | Yes | Send documented query parameters as a query string, not a JSON body. |
| Receipts & Invoices | Update a Document | `update_document()` | PUT | `/api/v8/partner/documents/:document_id` | [Update a Document](https://docs.veryfi.com/api/receipts-invoices/update-a-document/) | IMPLEMENTED | IMPLEMENTED | Yes | None. Optional: add typed update model matching documented writable fields. |
| Receipts & Invoices | Delete all document Line Items | `delete_line_items()` | DELETE | `/api/v8/partner/documents/:document_id/line-items` | [Delete all document Line Items](https://docs.veryfi.com/api/receipts-invoices/delete-all-document-line-items/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Receipts & Invoices | Get document Line Items | `get_line_items()` | GET | `/api/v8/partner/documents/:document_id/line-items` | [Get document Line Items](https://docs.veryfi.com/api/receipts-invoices/get-document-line-items/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Receipts & Invoices | Create a Line Item | `add_line_item()` | POST | `/api/v8/partner/documents/:document_id/line-items` | [Create a Line Item](https://docs.veryfi.com/api/receipts-invoices/create-a-line-item/) | PARTIAL | PARTIAL | Yes | Expand LineItem / LineItemUpdate models to all documented body fields (sku, quantity, total, tax, price, etc.). |
| Receipts & Invoices | Delete a Line Item | `delete_line_item()` | DELETE | `/api/v8/partner/documents/:document_id/line-items/:line_item_id` | [Delete a Line Item](https://docs.veryfi.com/api/receipts-invoices/delete-a-line-item/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Receipts & Invoices | Get a Line Item | `get_line_item()` | GET | `/api/v8/partner/documents/:document_id/line-items/:line_item_id` | [Get a Line Item](https://docs.veryfi.com/api/receipts-invoices/get-a-line-item/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Receipts & Invoices | Update a Line Item | `update_line_item()` | PUT | `/api/v8/partner/documents/:document_id/line-items/:line_item_id` | [Update a Line Item](https://docs.veryfi.com/api/receipts-invoices/update-a-line-item/) | PARTIAL | PARTIAL | Yes | Expand LineItem / LineItemUpdate models to all documented body fields (sku, quantity, total, tax, price, etc.). |
| Receipts & Invoices | Unlink all Tags from a Document | `delete_tags()` | DELETE | `/api/v8/partner/documents/:document_id/tags` | [Unlink all Tags from a Document](https://docs.veryfi.com/api/receipts-invoices/unlink-all-tags-from-a-document/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Receipts & Invoices | Get Document Tags | `get_document_tags()` | GET | `/api/v8/partner/documents/:document_id/tags` | [Get Document Tags](https://docs.veryfi.com/api/receipts-invoices/get-document-tags/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Receipts & Invoices | Add Tags to a Document | `add_tags()` | POST | `/api/v8/partner/documents/:document_id/tags` | [Add Tags to a Document](https://docs.veryfi.com/api/receipts-invoices/add-tags-to-a-document/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Receipts & Invoices | Add a Tag to a Document | `add_tag()` | PUT | `/api/v8/partner/documents/:document_id/tags` | [Add a Tag to a Document](https://docs.veryfi.com/api/receipts-invoices/add-a-tag-to-a-document/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Receipts & Invoices | Unlink a Tag from a Document | `delete_tag()` | DELETE | `/api/v8/partner/documents/:document_id/tags/:tag_id` | [Unlink a Tag from a Document](https://docs.veryfi.com/api/receipts-invoices/unlink-a-tag-from-a-document/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Receipts & Invoices | Returns a list of document Tax Lines | — | GET | `/api/v8/partner/documents/:document_id/tax-lines` | [Returns a list of document Tax Lines](https://docs.veryfi.com/api/returns-a-list-of-document-tax-lines/) | MISSING | MISSING | No | Add tax-line CRUD methods under documents. |
| Receipts & Invoices | Create a Tax Line | — | POST | `/api/v8/partner/documents/:document_id/tax-lines` | [Create a Tax Line](https://docs.veryfi.com/api/create-a-tax-line/) | MISSING | MISSING | No | Add tax-line CRUD methods under documents. |
| Receipts & Invoices | Delete a Tax Line | — | DELETE | `/api/v8/partner/documents/:document_id/tax-lines/:tax_line_id` | [Delete a Tax Line](https://docs.veryfi.com/api/delete-a-tax-line/) | MISSING | MISSING | No | Add tax-line CRUD methods under documents. |
| Receipts & Invoices | Returns document Tax Line | — | GET | `/api/v8/partner/documents/:document_id/tax-lines/:tax_line_id` | [Returns document Tax Line](https://docs.veryfi.com/api/returns-document-tax-line/) | MISSING | MISSING | No | Add tax-line CRUD methods under documents. |
| Receipts & Invoices | Update a Tax Line | — | PUT | `/api/v8/partner/documents/:document_id/tax-lines/:tax_line_id` | [Update a Tax Line](https://docs.veryfi.com/api/update-a-tax-line/) | MISSING | MISSING | No | Add an update method for this resource. |
| Receipts & Invoices | Bulk Process Multiple Documents | — | POST | `/api/v8/partner/documents/bulk` | [Bulk Process Multiple Documents](https://docs.veryfi.com/api/receipts-invoices/bulk-process-multiple-documents/) | MISSING | MISSING | No | Add bulk document processing method posting to `/documents/bulk`. |
| Settings | Retrieve api-keys list | — | GET | `/api/v1/partner/settings/api-keys` | [Retrieve api-keys list](https://docs.veryfi.com/api/settings/retrieve-api-keys-list/) | MISSING | MISSING | No | Add a public Client method for this operation. |
| Settings | Create api-key | — | POST | `/api/v1/partner/settings/api-keys` | [Create api-key](https://docs.veryfi.com/api/settings/create-api-key/) | MISSING | MISSING | No | Add a public Client method for this operation. |
| Settings | Revoke api-key | — | DELETE | `/api/v1/partner/settings/api-keys/:id` | [Revoke api-key](https://docs.veryfi.com/api/settings/revoke-api-key/) | MISSING | MISSING | No | Add a public Client method for this operation. |
| Settings | Retrieve api-key | — | GET | `/api/v1/partner/settings/api-keys/:id` | [Retrieve api-key](https://docs.veryfi.com/api/settings/retrieve-api-key/) | MISSING | MISSING | No | Add a public Client method for this operation. |
| Settings | Update api-key | — | PUT | `/api/v1/partner/settings/api-keys/:id` | [Update api-key](https://docs.veryfi.com/api/settings/update-api-key/) | MISSING | MISSING | No | Add an update method for this resource. |
| Settings | Rotate api-key | — | POST | `/api/v1/partner/settings/api-keys/:id/rotate` | [Rotate api-key](https://docs.veryfi.com/api/settings/rotate-api-key/) | MISSING | MISSING | No | Add a public Client method for this operation. |
| Settings | Available permissions | — | GET | `/api/v1/partner/settings/api-keys/available-permissions` | [Available permissions](https://docs.veryfi.com/api/settings/available-permissions/) | MISSING | MISSING | No | Add a public Client method for this operation. |
| Settings | Verify the calling key | — | GET | `/api/v1/partner/settings/api-keys/verify` | [Verify the calling key](https://docs.veryfi.com/api/settings/verify-the-calling-key/) | MISSING | MISSING | No | Add a public Client method for this operation. |
| Settings | Get release notifications | — | GET | `/api/v1/release-notifications` | [Get release notifications](https://docs.veryfi.com/api/get-release-notifications/) | MISSING | MISSING | No | Add a public Client method for this operation. |
| Settings | Retrieve client-keys list | — | GET | `/api/v8/partner/client-keys` | [Retrieve client-keys list](https://docs.veryfi.com/api/settings/retrieve-client-keys-list/) | MISSING | MISSING | No | Add a public Client method for this operation. |
| Settings | Create client-keys | — | POST | `/api/v8/partner/client-keys` | [Create client-keys](https://docs.veryfi.com/api/settings/create-client-keys/) | MISSING | MISSING | No | Add a public Client method for this operation. |
| Settings | Remove a client-key | — | DELETE | `/api/v8/partner/client-keys/:id` | [Remove a client-key](https://docs.veryfi.com/api/settings/remove-a-client-key/) | MISSING | MISSING | No | Add a public Client method for this operation. |
| Settings | Reset client-keys | — | POST | `/api/v8/partner/client-keys/reset` | [Reset client-keys](https://docs.veryfi.com/api/settings/reset-client-keys/) | MISSING | MISSING | No | Add a public Client method for this operation. |
| Settings | Get OpenAPI schema | — | GET | `/api/v8/partner/documents/schema` | [Get OpenAPI schema](https://docs.veryfi.com/api/get-open-api-schema/) | MISSING | MISSING | No | Add a public Client method for this operation. |
| Settings | Get ocr-counts | — | GET | `/api/v8/partner/ocr-counts` | [Get ocr-counts](https://docs.veryfi.com/api/get-ocr-counts/) | MISSING | MISSING | No | Add a public Client method for this operation. |
| Settings | Get Tls Certificates | — | GET | `/api/v8/partner/settings/tls-certificate` | [Get Tls Certificates](https://docs.veryfi.com/api/get-tls-certificates/) | MISSING | MISSING | No | Add a public Client method for this operation. |
| Settings | Process a Tls Certificate | — | POST | `/api/v8/partner/settings/tls-certificate` | [Process a Tls Certificate](https://docs.veryfi.com/api/process-a-tls-certificate/) | MISSING | MISSING | No | Add a public Client method for this operation. |
| Settings | Delete a Tls Certificate | — | DELETE | `/api/v8/partner/settings/tls-certificate/:certificate_id` | [Delete a Tls Certificate](https://docs.veryfi.com/api/delete-a-tls-certificate/) | MISSING | MISSING | No | Add a public Client method for this operation. |
| Settings | Get webhooks | — | GET | `/api/v8/partner/settings/webhooks` | [Get webhooks](https://docs.veryfi.com/api/settings/get-webhooks/) | MISSING | MISSING | No | Add a public Client method for this operation. |
| Settings | Add a webhook | — | POST | `/api/v8/partner/settings/webhooks` | [Add a webhook](https://docs.veryfi.com/api/settings/add-a-webhook/) | MISSING | MISSING | No | Add a public Client method for this operation. |
| Settings | Confirm a webhook | — | POST | `/api/v8/partner/settings/webhooks/confirm` | [Confirm a webhook](https://docs.veryfi.com/api/settings/confirm-a-webhook/) | MISSING | MISSING | No | Add a public Client method for this operation. |
| W-2s | Get W-2s | `get_w2s()` | GET | `/api/v8/partner/w2s` | [Get W-2s](https://docs.veryfi.com/api/w2s/get-w-2-s/) | PARTIAL | PARTIAL | Yes | Send documented query parameters as a query string, not a JSON body. |
| W-2s | Process a W-2 | `process_w2()`, `process_w2_base64()`, `process_w2_from_url()` | POST | `/api/v8/partner/w2s` | [Process a W-2](https://docs.veryfi.com/api/w2s/process-a-w-2/) | PARTIAL | PARTIAL | Yes | Pass `has_files=true` for local-file multipart uploads (`process_w2`, `process_w9`). |
| W-2s | Get W-2 sets | — | GET | `/api/v8/partner/w2s-set` | [Get W-2 sets](https://docs.veryfi.com/api/get-w-2-sets/) | MISSING | MISSING | No | Add a public Client method for this operation. |
| W-2s | Split and process a PDF with multiple W-2s | — | POST | `/api/v8/partner/w2s-set` | [Split and process a PDF with multiple W-2s](https://docs.veryfi.com/api/split-and-process-a-pdf-with-multiple-w-2-s/) | MISSING | MISSING | No | Add a public Client method for this operation. |
| W-2s | Get a W-2 set | — | GET | `/api/v8/partner/w2s-set/:document_id` | [Get a W-2 set](https://docs.veryfi.com/api/get-a-w-2-set/) | MISSING | MISSING | No | Add a public Client method for this operation. |
| W-2s | Delete a W-2 | `delete_w2()` | DELETE | `/api/v8/partner/w2s/:document_id` | [Delete a W-2](https://docs.veryfi.com/api/w2s/delete-a-w-2/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| W-2s | Get a W-2 | `get_w2()` | GET | `/api/v8/partner/w2s/:document_id` | [Get a W-2](https://docs.veryfi.com/api/w2s/get-a-w-2/) | PARTIAL | PARTIAL | Yes | Send documented query parameters as a query string, not a JSON body. |
| W-2s | Update a W-2 | — | PUT | `/api/v8/partner/w2s/:document_id` | [Update a W-2](https://docs.veryfi.com/api/w2s/update-a-w-2/) | MISSING | MISSING | No | Add an update method for this resource. |
| W-2s | Unlink all tags from a W-2 | — | DELETE | `/api/v8/partner/w2s/:document_id/tags` | [Unlink all tags from a W-2](https://docs.veryfi.com/api/unlink-all-tags-from-a-w-2/) | MISSING | MISSING | No | Add tag methods for this resource, following the receipts-invoices tag pattern. |
| W-2s | Get W-2 tags | — | GET | `/api/v8/partner/w2s/:document_id/tags` | [Get W-2 tags](https://docs.veryfi.com/api/get-w-2-tags/) | MISSING | MISSING | No | Add a public Client method for this operation. |
| W-2s | Add tags to a W-2 | — | POST | `/api/v8/partner/w2s/:document_id/tags` | [Add tags to a W-2](https://docs.veryfi.com/api/add-tags-to-a-w-2/) | MISSING | MISSING | No | Add tag methods for this resource, following the receipts-invoices tag pattern. |
| W-2s | Add a tag to a W-2 | — | PUT | `/api/v8/partner/w2s/:document_id/tags` | [Add a tag to a W-2](https://docs.veryfi.com/api/add-a-tag-to-a-w-2/) | MISSING | MISSING | No | Add tag methods for this resource, following the receipts-invoices tag pattern. |
| W-2s | Unlink a tag from a W-2 | — | DELETE | `/api/v8/partner/w2s/:document_id/tags/:tag_id` | [Unlink a tag from a W-2](https://docs.veryfi.com/api/unlink-a-tag-from-a-w-2/) | MISSING | MISSING | No | Add tag methods for this resource, following the receipts-invoices tag pattern. |
| W-8BEN-E | Get W-8BEN-Es | `get_w8benes()` | GET | `/api/v8/partner/w-8ben-e` | [Get W-8BEN-Es](https://docs.veryfi.com/api/w-8ben-e/get-w-8-ben-es/) | PARTIAL | PARTIAL | Yes | Send documented query parameters as a query string, not a JSON body. |
| W-8BEN-E | Process a W-8BEN-E | `process_w8bene()`, `process_w8bene_base64()`, `process_w8bene_from_url()` | POST | `/api/v8/partner/w-8ben-e` | [Process a W-8BEN-E](https://docs.veryfi.com/api/w-8ben-e/process-a-w-8-ben-e/) | IMPLEMENTED | IMPLEMENTED | Yes | Add a test for `process_w8bene_base64()`. |
| W-8BEN-E | Delete a W-8BEN-E | `delete_w8bene()` | DELETE | `/api/v8/partner/w-8ben-e/:document_id` | [Delete a W-8BEN-E](https://docs.veryfi.com/api/w-8ben-e/delete-a-w-8-ben-e/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| W-8BEN-E | Get a W-8BEN-E | `get_w8bene()` | GET | `/api/v8/partner/w-8ben-e/:document_id` | [Get a W-8BEN-E](https://docs.veryfi.com/api/w-8ben-e/get-a-w-8-ben-e/) | PARTIAL | PARTIAL | No | Send documented query parameters as a query string, not a JSON body. Add tests. |
| W-8BEN-E | Update a W-8BEN-E | — | PUT | `/api/v8/partner/w-8ben-e/:document_id` | [Update a W-8BEN-E](https://docs.veryfi.com/api/w-8ben-e/update-a-w-8-ben-e/) | MISSING | MISSING | No | Add an update method for this resource. |
| W-8BEN-E | Unlink all tags from a W-8BEN-E | — | DELETE | `/api/v8/partner/w-8ben-e/:document_id/tags` | [Unlink all tags from a W-8BEN-E](https://docs.veryfi.com/api/unlink-all-tags-from-a-w-8-ben-e/) | MISSING | MISSING | No | Add tag methods for this resource, following the receipts-invoices tag pattern. |
| W-8BEN-E | Get W-8BEN-E tags | — | GET | `/api/v8/partner/w-8ben-e/:document_id/tags` | [Get W-8BEN-E tags](https://docs.veryfi.com/api/get-w-8-ben-e-tags/) | MISSING | MISSING | No | Add a public Client method for this operation. |
| W-8BEN-E | Add tags to a W-8BEN-E | — | POST | `/api/v8/partner/w-8ben-e/:document_id/tags` | [Add tags to a W-8BEN-E](https://docs.veryfi.com/api/add-tags-to-a-w-8-ben-e/) | MISSING | MISSING | No | Add tag methods for this resource, following the receipts-invoices tag pattern. |
| W-8BEN-E | Add a tag to a W-8BEN-E | — | PUT | `/api/v8/partner/w-8ben-e/:document_id/tags` | [Add a tag to a W-8BEN-E](https://docs.veryfi.com/api/add-a-tag-to-a-w-8-ben-e/) | MISSING | MISSING | No | Add tag methods for this resource, following the receipts-invoices tag pattern. |
| W-8BEN-E | Unlink a tag from a W-8BEN-E | — | DELETE | `/api/v8/partner/w-8ben-e/:document_id/tags/:tag_id` | [Unlink a tag from a W-8BEN-E](https://docs.veryfi.com/api/unlink-a-tag-from-a-w-8-ben-e/) | MISSING | MISSING | No | Add tag methods for this resource, following the receipts-invoices tag pattern. |
| W-9s | Get W-9s | `get_w9s()` | GET | `/api/v8/partner/w9s` | [Get W-9s](https://docs.veryfi.com/api/w9s/get-w-9-s/) | PARTIAL | PARTIAL | Yes | Send documented query parameters as a query string, not a JSON body. |
| W-9s | Process a W-9 | `process_w9()`, `process_w9_base64()`, `process_w9_from_url()` | POST | `/api/v8/partner/w9s` | [Process a W-9](https://docs.veryfi.com/api/w9s/process-a-w-9/) | PARTIAL | PARTIAL | Yes | Pass `has_files=true` for local-file multipart uploads (`process_w2`, `process_w9`). |
| W-9s | Delete a W-9 | `delete_w9()` | DELETE | `/api/v8/partner/w9s/:document_id` | [Delete a W-9](https://docs.veryfi.com/api/w9s/delete-a-w-9/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| W-9s | Get a W-9 | `get_w9()` | GET | `/api/v8/partner/w9s/:document_id` | [Get a W-9](https://docs.veryfi.com/api/w9s/get-a-w-9/) | PARTIAL | PARTIAL | No | Send documented query parameters as a query string, not a JSON body. Add tests. |
| W-9s | Update a W-9 | — | PUT | `/api/v8/partner/w9s/:document_id` | [Update a W-9](https://docs.veryfi.com/api/w9s/update-a-w-9/) | MISSING | MISSING | No | Add an update method for this resource. |
| W-9s | Unlink all tags from a W-9 | — | DELETE | `/api/v8/partner/w9s/:document_id/tags` | [Unlink all tags from a W-9](https://docs.veryfi.com/api/unlink-all-tags-from-a-w-9/) | MISSING | MISSING | No | Add tag methods for this resource, following the receipts-invoices tag pattern. |
| W-9s | Get W-9 tags | — | GET | `/api/v8/partner/w9s/:document_id/tags` | [Get W-9 tags](https://docs.veryfi.com/api/get-w-9-tags/) | MISSING | MISSING | No | Add a public Client method for this operation. |
| W-9s | Add tags to a W-9 | — | POST | `/api/v8/partner/w9s/:document_id/tags` | [Add tags to a W-9](https://docs.veryfi.com/api/add-tags-to-a-w-9/) | MISSING | MISSING | No | Add tag methods for this resource, following the receipts-invoices tag pattern. |
| W-9s | Add a tag to a W-9 | — | PUT | `/api/v8/partner/w9s/:document_id/tags` | [Add a tag to a W-9](https://docs.veryfi.com/api/add-a-tag-to-a-w-9/) | MISSING | MISSING | No | Add tag methods for this resource, following the receipts-invoices tag pattern. |
| W-9s | Unlink a tag from a W-9 | — | DELETE | `/api/v8/partner/w9s/:document_id/tags/:tag_id` | [Unlink a tag from a W-9](https://docs.veryfi.com/api/unlink-a-tag-from-a-w-9/) | MISSING | MISSING | No | Add tag methods for this resource, following the receipts-invoices tag pattern. |

## PARTIAL operations — parameter comparison

Location values: `path`, `query`, `body`, `header`, `multipart/file`, `other`.

### Get ∀Docs

- **SDK method(s):** `get_any_documents()`
- **HTTP / route:** `GET /api/v8/partner/any-documents`
- **Docs:** https://docs.veryfi.com/api/anydocs/get-A-docs/
- **Content types (docs):** application/json

| SDK Method | API Parameter | Location | Type | Required | SDK Support | SDK Parameter | Action | Docs URL |
|---|---|---|---|---|---|---|---|---|
| `get_any_documents()` | `meta.tags` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['meta.tags']` | Send as query string | https://docs.veryfi.com/api/anydocs/get-A-docs/ |
| `get_any_documents()` | `meta.external_id` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['meta.external_id']` | Send as query string | https://docs.veryfi.com/api/anydocs/get-A-docs/ |
| `get_any_documents()` | `created_date__gt` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['created_date__gt']` | Send as query string | https://docs.veryfi.com/api/anydocs/get-A-docs/ |
| `get_any_documents()` | `created_date__lt` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['created_date__lt']` | Send as query string | https://docs.veryfi.com/api/anydocs/get-A-docs/ |
| `get_any_documents()` | `created_date__gte` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['created_date__gte']` | Send as query string | https://docs.veryfi.com/api/anydocs/get-A-docs/ |
| `get_any_documents()` | `created_date__lte` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['created_date__lte']` | Send as query string | https://docs.veryfi.com/api/anydocs/get-A-docs/ |
| `get_any_documents()` | `updated_date__gt` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['updated_date__gt']` | Send as query string | https://docs.veryfi.com/api/anydocs/get-A-docs/ |
| `get_any_documents()` | `updated_date__lt` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['updated_date__lt']` | Send as query string | https://docs.veryfi.com/api/anydocs/get-A-docs/ |
| `get_any_documents()` | `updated_date__gte` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['updated_date__gte']` | Send as query string | https://docs.veryfi.com/api/anydocs/get-A-docs/ |
| `get_any_documents()` | `updated_date__lte` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['updated_date__lte']` | Send as query string | https://docs.veryfi.com/api/anydocs/get-A-docs/ |
| `get_any_documents()` | `page` | query | see docs | no | Named, but sent as JSON body | $page | Send as query string | https://docs.veryfi.com/api/anydocs/get-A-docs/ |
| `get_any_documents()` | `page_size` | query | see docs | no | Named, but sent as JSON body | $page_size | Send as query string | https://docs.veryfi.com/api/anydocs/get-A-docs/ |
| `get_any_documents()` | `bounding_boxes` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['bounding_boxes']` | Send as query string | https://docs.veryfi.com/api/anydocs/get-A-docs/ |
| `get_any_documents()` | `confidence_details` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['confidence_details']` | Send as query string | https://docs.veryfi.com/api/anydocs/get-A-docs/ |
| `get_any_documents()` | `q` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['q']` | Send as query string | https://docs.veryfi.com/api/anydocs/get-A-docs/ |
| `get_any_documents()` | `track_total_results` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['track_total_results']` | Send as query string | https://docs.veryfi.com/api/anydocs/get-A-docs/ |
| `get_any_documents()` | `blueprint_name` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['blueprint_name']` | Send as query string | https://docs.veryfi.com/api/anydocs/get-A-docs/ |
| `get_any_documents()` | `template_name` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['template_name']` | Send as query string | https://docs.veryfi.com/api/anydocs/get-A-docs/ |

### Process a ∀Doc

- **SDK method(s):** `process_any_document()`, `process_any_document_base64()`, `process_any_document_url()`
- **HTTP / route:** `POST /api/v8/partner/any-documents`
- **Docs:** https://docs.veryfi.com/api/anydocs/process-a-A-doc/
- **Content types (docs):** application/json, multipart/form-data, application/json, application/json, application/json, application/json, application/json, application/json, application/json, application/json, application/json, application/json

| SDK Method | API Parameter | Location | Type | Required | SDK Support | SDK Parameter | Action | Docs URL |
|---|---|---|---|---|---|---|---|---|
| `process_any_document()`, `process_any_document_base64()`, `process_any_document_url()` | `blueprint_name` | body | string | no | No (sends template_name) | $template_name | Rename or send both names | https://docs.veryfi.com/api/anydocs/process-a-A-doc/ |
| `process_any_document()`, `process_any_document_base64()`, `process_any_document_url()` | `template_name` | body | string (SDK) | yes in SDK | Yes, but not documented | $template_name | Verify alias with API | https://docs.veryfi.com/api/anydocs/process-a-A-doc/ |
| `process_any_document()`, `process_any_document_base64()`, `process_any_document_url()` | `file` | multipart/file | file | no | Yes | CURLFile | OK | https://docs.veryfi.com/api/anydocs/process-a-A-doc/ |
| `process_any_document()`, `process_any_document_base64()`, `process_any_document_url()` | `file_data` | body | string | no | Yes via helpers | file_data | OK | https://docs.veryfi.com/api/anydocs/process-a-A-doc/ |
| `process_any_document()`, `process_any_document_base64()`, `process_any_document_url()` | `file_url` | body | string | no | Yes via helpers | file_url | OK | https://docs.veryfi.com/api/anydocs/process-a-A-doc/ |
| `process_any_document()`, `process_any_document_base64()`, `process_any_document_url()` | `file_urls` | body | see docs | no | Via $kwargs | $kwargs | OK | https://docs.veryfi.com/api/anydocs/process-a-A-doc/ |
| `process_any_document()`, `process_any_document_base64()`, `process_any_document_url()` | `file_name` | body | see docs | no | Via $kwargs | $kwargs | OK | https://docs.veryfi.com/api/anydocs/process-a-A-doc/ |
| `process_any_document()`, `process_any_document_base64()`, `process_any_document_url()` | `package_path` | body | see docs | no | Via $kwargs | $kwargs | OK | https://docs.veryfi.com/api/anydocs/process-a-A-doc/ |
| `process_any_document()`, `process_any_document_base64()`, `process_any_document_url()` | `bucket` | body | see docs | no | Via $kwargs | $kwargs | OK | https://docs.veryfi.com/api/anydocs/process-a-A-doc/ |
| `process_any_document()`, `process_any_document_base64()`, `process_any_document_url()` | `external_id` | body | see docs | no | Via $kwargs | $kwargs | OK | https://docs.veryfi.com/api/anydocs/process-a-A-doc/ |
| `process_any_document()`, `process_any_document_base64()`, `process_any_document_url()` | `meta.tags` | body | see docs | no | Via $kwargs | $kwargs | OK | https://docs.veryfi.com/api/anydocs/process-a-A-doc/ |
| `process_any_document()`, `process_any_document_base64()`, `process_any_document_url()` | `max_pages_to_process` | body | integer | no | Yes | $max_pages_to_process | OK | https://docs.veryfi.com/api/anydocs/process-a-A-doc/ |
| `process_any_document()`, `process_any_document_base64()`, `process_any_document_url()` | `auto_delete` | body | see docs | no | Via $kwargs | $kwargs | OK | https://docs.veryfi.com/api/anydocs/process-a-A-doc/ |

### Get a ∀Doc

- **SDK method(s):** `get_any_document()`
- **HTTP / route:** `GET /api/v8/partner/any-documents/:document_id`
- **Docs:** https://docs.veryfi.com/api/anydocs/get-a-A-doc/
- **Content types (docs):** application/json

| SDK Method | API Parameter | Location | Type | Required | SDK Support | SDK Parameter | Action | Docs URL |
|---|---|---|---|---|---|---|---|---|
| `get_any_document()` | `document_id` | path | string/int | yes | Yes | $document_id | OK | https://docs.veryfi.com/api/anydocs/get-a-A-doc/ |
| `get_any_document()` | `bounding_boxes` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['bounding_boxes']` | Send as query string | https://docs.veryfi.com/api/anydocs/get-a-A-doc/ |
| `get_any_document()` | `confidence_details` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['confidence_details']` | Send as query string | https://docs.veryfi.com/api/anydocs/get-a-A-doc/ |

### Get Bank Statements

- **SDK method(s):** `get_bank_statements()`
- **HTTP / route:** `GET /api/v8/partner/bank-statements`
- **Docs:** https://docs.veryfi.com/api/bank-statements/get-bank-statements/
- **Content types (docs):** application/json

| SDK Method | API Parameter | Location | Type | Required | SDK Support | SDK Parameter | Action | Docs URL |
|---|---|---|---|---|---|---|---|---|
| `get_bank_statements()` | `meta.tags` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['meta.tags']` | Send as query string | https://docs.veryfi.com/api/bank-statements/get-bank-statements/ |
| `get_bank_statements()` | `meta.external_id` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['meta.external_id']` | Send as query string | https://docs.veryfi.com/api/bank-statements/get-bank-statements/ |
| `get_bank_statements()` | `created_date__gt` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['created_date__gt']` | Send as query string | https://docs.veryfi.com/api/bank-statements/get-bank-statements/ |
| `get_bank_statements()` | `created_date__lt` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['created_date__lt']` | Send as query string | https://docs.veryfi.com/api/bank-statements/get-bank-statements/ |
| `get_bank_statements()` | `created_date__gte` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['created_date__gte']` | Send as query string | https://docs.veryfi.com/api/bank-statements/get-bank-statements/ |
| `get_bank_statements()` | `created_date__lte` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['created_date__lte']` | Send as query string | https://docs.veryfi.com/api/bank-statements/get-bank-statements/ |
| `get_bank_statements()` | `updated_date__gt` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['updated_date__gt']` | Send as query string | https://docs.veryfi.com/api/bank-statements/get-bank-statements/ |
| `get_bank_statements()` | `updated_date__lt` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['updated_date__lt']` | Send as query string | https://docs.veryfi.com/api/bank-statements/get-bank-statements/ |
| `get_bank_statements()` | `updated_date__gte` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['updated_date__gte']` | Send as query string | https://docs.veryfi.com/api/bank-statements/get-bank-statements/ |
| `get_bank_statements()` | `updated_date__lte` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['updated_date__lte']` | Send as query string | https://docs.veryfi.com/api/bank-statements/get-bank-statements/ |
| `get_bank_statements()` | `page` | query | see docs | no | Named, but sent as JSON body | $page | Send as query string | https://docs.veryfi.com/api/bank-statements/get-bank-statements/ |
| `get_bank_statements()` | `page_size` | query | see docs | no | Named, but sent as JSON body | $page_size | Send as query string | https://docs.veryfi.com/api/bank-statements/get-bank-statements/ |
| `get_bank_statements()` | `bounding_boxes` | query | see docs | no | Named, but sent as JSON body | $bounding_boxes | Send as query string | https://docs.veryfi.com/api/bank-statements/get-bank-statements/ |
| `get_bank_statements()` | `confidence_details` | query | see docs | no | Named, but sent as JSON body | $confidence_details | Send as query string | https://docs.veryfi.com/api/bank-statements/get-bank-statements/ |
| `get_bank_statements()` | `q` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['q']` | Send as query string | https://docs.veryfi.com/api/bank-statements/get-bank-statements/ |
| `get_bank_statements()` | `track_total_results` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['track_total_results']` | Send as query string | https://docs.veryfi.com/api/bank-statements/get-bank-statements/ |

### Get a Bank Statement

- **SDK method(s):** `get_bank_statement()`
- **HTTP / route:** `GET /api/v8/partner/bank-statements/:document_id`
- **Docs:** https://docs.veryfi.com/api/bank-statements/get-a-bank-statement/
- **Content types (docs):** application/json

| SDK Method | API Parameter | Location | Type | Required | SDK Support | SDK Parameter | Action | Docs URL |
|---|---|---|---|---|---|---|---|---|
| `get_bank_statement()` | `document_id` | path | string/int | yes | Yes | $document_id | OK | https://docs.veryfi.com/api/bank-statements/get-a-bank-statement/ |
| `get_bank_statement()` | `bounding_boxes` | query | see docs | no | Named, but sent as JSON body | $bounding_boxes | Send as query string | https://docs.veryfi.com/api/bank-statements/get-a-bank-statement/ |
| `get_bank_statement()` | `confidence_details` | query | see docs | no | Named, but sent as JSON body | $confidence_details | Send as query string | https://docs.veryfi.com/api/bank-statements/get-a-bank-statement/ |

### Get Business Cards

- **SDK method(s):** `get_business_cards()`
- **HTTP / route:** `GET /api/v8/partner/business-cards`
- **Docs:** https://docs.veryfi.com/api/business-cards/get-business-cards/
- **Content types (docs):** application/json

| SDK Method | API Parameter | Location | Type | Required | SDK Support | SDK Parameter | Action | Docs URL |
|---|---|---|---|---|---|---|---|---|
| `get_business_cards()` | `meta.tags` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['meta.tags']` | Send as query string | https://docs.veryfi.com/api/business-cards/get-business-cards/ |
| `get_business_cards()` | `meta.external_id` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['meta.external_id']` | Send as query string | https://docs.veryfi.com/api/business-cards/get-business-cards/ |
| `get_business_cards()` | `created_date__gt` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['created_date__gt']` | Send as query string | https://docs.veryfi.com/api/business-cards/get-business-cards/ |
| `get_business_cards()` | `created_date__lt` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['created_date__lt']` | Send as query string | https://docs.veryfi.com/api/business-cards/get-business-cards/ |
| `get_business_cards()` | `created_date__gte` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['created_date__gte']` | Send as query string | https://docs.veryfi.com/api/business-cards/get-business-cards/ |
| `get_business_cards()` | `created_date__lte` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['created_date__lte']` | Send as query string | https://docs.veryfi.com/api/business-cards/get-business-cards/ |
| `get_business_cards()` | `updated_date__gt` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['updated_date__gt']` | Send as query string | https://docs.veryfi.com/api/business-cards/get-business-cards/ |
| `get_business_cards()` | `updated_date__lt` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['updated_date__lt']` | Send as query string | https://docs.veryfi.com/api/business-cards/get-business-cards/ |
| `get_business_cards()` | `updated_date__gte` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['updated_date__gte']` | Send as query string | https://docs.veryfi.com/api/business-cards/get-business-cards/ |
| `get_business_cards()` | `updated_date__lte` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['updated_date__lte']` | Send as query string | https://docs.veryfi.com/api/business-cards/get-business-cards/ |
| `get_business_cards()` | `page` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['page']` | Send as query string | https://docs.veryfi.com/api/business-cards/get-business-cards/ |
| `get_business_cards()` | `page_size` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['page_size']` | Send as query string | https://docs.veryfi.com/api/business-cards/get-business-cards/ |
| `get_business_cards()` | `bounding_boxes` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['bounding_boxes']` | Send as query string | https://docs.veryfi.com/api/business-cards/get-business-cards/ |
| `get_business_cards()` | `confidence_details` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['confidence_details']` | Send as query string | https://docs.veryfi.com/api/business-cards/get-business-cards/ |
| `get_business_cards()` | `q` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['q']` | Send as query string | https://docs.veryfi.com/api/business-cards/get-business-cards/ |
| `get_business_cards()` | `track_total_results` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['track_total_results']` | Send as query string | https://docs.veryfi.com/api/business-cards/get-business-cards/ |

### Get a Business Card

- **SDK method(s):** `get_business_card()`
- **HTTP / route:** `GET /api/v8/partner/business-cards/:document_id`
- **Docs:** https://docs.veryfi.com/api/business-cards/get-a-business-card/
- **Content types (docs):** application/json

| SDK Method | API Parameter | Location | Type | Required | SDK Support | SDK Parameter | Action | Docs URL |
|---|---|---|---|---|---|---|---|---|
| `get_business_card()` | `document_id` | path | string/int | yes | Yes | $document_id | OK | https://docs.veryfi.com/api/business-cards/get-a-business-card/ |
| `get_business_card()` | `bounding_boxes` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['bounding_boxes']` | Send as query string | https://docs.veryfi.com/api/business-cards/get-a-business-card/ |
| `get_business_card()` | `confidence_details` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['confidence_details']` | Send as query string | https://docs.veryfi.com/api/business-cards/get-a-business-card/ |

### Get Checks

- **SDK method(s):** `get_checks()`
- **HTTP / route:** `GET /api/v8/partner/checks`
- **Docs:** https://docs.veryfi.com/api/checks/get-checks/
- **Content types (docs):** application/json

| SDK Method | API Parameter | Location | Type | Required | SDK Support | SDK Parameter | Action | Docs URL |
|---|---|---|---|---|---|---|---|---|
| `get_checks()` | `meta.tags` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['meta.tags']` | Send as query string | https://docs.veryfi.com/api/checks/get-checks/ |
| `get_checks()` | `meta.external_id` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['meta.external_id']` | Send as query string | https://docs.veryfi.com/api/checks/get-checks/ |
| `get_checks()` | `created_date__gt` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['created_date__gt']` | Send as query string | https://docs.veryfi.com/api/checks/get-checks/ |
| `get_checks()` | `created_date__lt` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['created_date__lt']` | Send as query string | https://docs.veryfi.com/api/checks/get-checks/ |
| `get_checks()` | `created_date__gte` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['created_date__gte']` | Send as query string | https://docs.veryfi.com/api/checks/get-checks/ |
| `get_checks()` | `created_date__lte` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['created_date__lte']` | Send as query string | https://docs.veryfi.com/api/checks/get-checks/ |
| `get_checks()` | `updated_date__gt` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['updated_date__gt']` | Send as query string | https://docs.veryfi.com/api/checks/get-checks/ |
| `get_checks()` | `updated_date__lt` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['updated_date__lt']` | Send as query string | https://docs.veryfi.com/api/checks/get-checks/ |
| `get_checks()` | `updated_date__gte` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['updated_date__gte']` | Send as query string | https://docs.veryfi.com/api/checks/get-checks/ |
| `get_checks()` | `updated_date__lte` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['updated_date__lte']` | Send as query string | https://docs.veryfi.com/api/checks/get-checks/ |
| `get_checks()` | `page` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['page']` | Send as query string | https://docs.veryfi.com/api/checks/get-checks/ |
| `get_checks()` | `page_size` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['page_size']` | Send as query string | https://docs.veryfi.com/api/checks/get-checks/ |
| `get_checks()` | `bounding_boxes` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['bounding_boxes']` | Send as query string | https://docs.veryfi.com/api/checks/get-checks/ |
| `get_checks()` | `confidence_details` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['confidence_details']` | Send as query string | https://docs.veryfi.com/api/checks/get-checks/ |
| `get_checks()` | `q` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['q']` | Send as query string | https://docs.veryfi.com/api/checks/get-checks/ |
| `get_checks()` | `track_total_results` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['track_total_results']` | Send as query string | https://docs.veryfi.com/api/checks/get-checks/ |

### Get a Check

- **SDK method(s):** `get_check()`
- **HTTP / route:** `GET /api/v8/partner/checks/:document_id`
- **Docs:** https://docs.veryfi.com/api/checks/get-a-check/
- **Content types (docs):** application/json

| SDK Method | API Parameter | Location | Type | Required | SDK Support | SDK Parameter | Action | Docs URL |
|---|---|---|---|---|---|---|---|---|
| `get_check()` | `document_id` | path | string/int | yes | Yes | $document_id | OK | https://docs.veryfi.com/api/checks/get-a-check/ |
| `get_check()` | `bounding_boxes` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['bounding_boxes']` | Send as query string | https://docs.veryfi.com/api/checks/get-a-check/ |
| `get_check()` | `confidence_details` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['confidence_details']` | Send as query string | https://docs.veryfi.com/api/checks/get-a-check/ |

### Classify a document

- **SDK method(s):** `classify_document_from_base64()`, `classify_document_from_url()`
- **HTTP / route:** `POST /api/v8/partner/classify`
- **Docs:** https://docs.veryfi.com/api/classify/classify-a-document/
- **Content types (docs):** application/json, multipart/form-data, application/json, application/json

| SDK Method | API Parameter | Location | Type | Required | SDK Support | SDK Parameter | Action | Docs URL |
|---|---|---|---|---|---|---|---|---|
| `classify_document_from_base64()`, `classify_document_from_url()` | `file` | multipart/file | file | no | No | — | Add process-from-path multipart helper | https://docs.veryfi.com/api/classify/classify-a-document/ |
| `classify_document_from_base64()`, `classify_document_from_url()` | `file_data` | body | string | no | Yes as file_data via base64 helper | $base64_encoded_string → file_data | OK | https://docs.veryfi.com/api/classify/classify-a-document/ |
| `classify_document_from_base64()`, `classify_document_from_url()` | `file_url` | body | string/array | no | Yes | $file_url / $file_urls | OK | https://docs.veryfi.com/api/classify/classify-a-document/ |
| `classify_document_from_base64()`, `classify_document_from_url()` | `file_urls` | body | string/array | no | Yes | $file_url / $file_urls | OK | https://docs.veryfi.com/api/classify/classify-a-document/ |
| `classify_document_from_base64()`, `classify_document_from_url()` | `file_name` | body | see docs | no | Via $kwargs | $kwargs | OK | https://docs.veryfi.com/api/classify/classify-a-document/ |
| `classify_document_from_base64()`, `classify_document_from_url()` | `package_path` | body | see docs | no | Via $kwargs | $kwargs | OK | https://docs.veryfi.com/api/classify/classify-a-document/ |
| `classify_document_from_base64()`, `classify_document_from_url()` | `bucket` | body | see docs | no | Via $kwargs | $kwargs | OK | https://docs.veryfi.com/api/classify/classify-a-document/ |
| `classify_document_from_base64()`, `classify_document_from_url()` | `external_id` | body | see docs | no | Via $kwargs | $kwargs | OK | https://docs.veryfi.com/api/classify/classify-a-document/ |
| `classify_document_from_base64()`, `classify_document_from_url()` | `document_types` | body | see docs | no | Via $kwargs | $kwargs | OK | https://docs.veryfi.com/api/classify/classify-a-document/ |

### Search Documents

- **SDK method(s):** `get_documents()`
- **HTTP / route:** `GET /api/v8/partner/documents`
- **Docs:** https://docs.veryfi.com/api/receipts-invoices/search-documents/
- **Content types (docs):** application/json, application/json, application/json, application/json, application/json, application/json, application/json, application/json, application/json, application/json

| SDK Method | API Parameter | Location | Type | Required | SDK Support | SDK Parameter | Action | Docs URL |
|---|---|---|---|---|---|---|---|---|
| `get_documents()` | `page` | query | see docs | no | Incorrect location | `$kwargs['page']` | Send as query string | https://docs.veryfi.com/api/receipts-invoices/search-documents/ |
| `get_documents()` | `page_size` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['page_size']` | Send as query string | https://docs.veryfi.com/api/receipts-invoices/search-documents/ |
| `get_documents()` | `bounding_boxes` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['bounding_boxes']` | Send as query string | https://docs.veryfi.com/api/receipts-invoices/search-documents/ |
| `get_documents()` | `confidence_details` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['confidence_details']` | Send as query string | https://docs.veryfi.com/api/receipts-invoices/search-documents/ |
| `get_documents()` | `detailed` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['detailed']` | Send as query string | https://docs.veryfi.com/api/receipts-invoices/search-documents/ |
| `get_documents()` | `q` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['q']` | Send as query string | https://docs.veryfi.com/api/receipts-invoices/search-documents/ |
| `get_documents()` | `order_by` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['order_by']` | Send as query string | https://docs.veryfi.com/api/receipts-invoices/search-documents/ |
| `get_documents()` | `external_id` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['external_id']` | Send as query string | https://docs.veryfi.com/api/receipts-invoices/search-documents/ |
| `get_documents()` | `device_id` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['device_id']` | Send as query string | https://docs.veryfi.com/api/receipts-invoices/search-documents/ |
| `get_documents()` | `device_user_uuid` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['device_user_uuid']` | Send as query string | https://docs.veryfi.com/api/receipts-invoices/search-documents/ |
| `get_documents()` | `status` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['status']` | Send as query string | https://docs.veryfi.com/api/receipts-invoices/search-documents/ |
| `get_documents()` | `tag` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['tag']` | Send as query string | https://docs.veryfi.com/api/receipts-invoices/search-documents/ |
| `get_documents()` | `owner` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['owner']` | Send as query string | https://docs.veryfi.com/api/receipts-invoices/search-documents/ |
| `get_documents()` | `created__gt` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['created__gt']` | Send as query string | https://docs.veryfi.com/api/receipts-invoices/search-documents/ |
| `get_documents()` | `created__lt` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['created__lt']` | Send as query string | https://docs.veryfi.com/api/receipts-invoices/search-documents/ |
| `get_documents()` | `created__gte` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['created__gte']` | Send as query string | https://docs.veryfi.com/api/receipts-invoices/search-documents/ |
| `get_documents()` | `created__lte` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['created__lte']` | Send as query string | https://docs.veryfi.com/api/receipts-invoices/search-documents/ |
| `get_documents()` | `updated__gt` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['updated__gt']` | Send as query string | https://docs.veryfi.com/api/receipts-invoices/search-documents/ |
| `get_documents()` | `updated__lt` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['updated__lt']` | Send as query string | https://docs.veryfi.com/api/receipts-invoices/search-documents/ |
| `get_documents()` | `updated__gte` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['updated__gte']` | Send as query string | https://docs.veryfi.com/api/receipts-invoices/search-documents/ |
| `get_documents()` | `updated__lte` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['updated__lte']` | Send as query string | https://docs.veryfi.com/api/receipts-invoices/search-documents/ |
| `get_documents()` | `date__gt` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['date__gt']` | Send as query string | https://docs.veryfi.com/api/receipts-invoices/search-documents/ |
| `get_documents()` | `date__lt` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['date__lt']` | Send as query string | https://docs.veryfi.com/api/receipts-invoices/search-documents/ |
| `get_documents()` | `date__gte` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['date__gte']` | Send as query string | https://docs.veryfi.com/api/receipts-invoices/search-documents/ |
| `get_documents()` | `date__lte` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['date__lte']` | Send as query string | https://docs.veryfi.com/api/receipts-invoices/search-documents/ |
| `get_documents()` | `track_total_results` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['track_total_results']` | Send as query string | https://docs.veryfi.com/api/receipts-invoices/search-documents/ |

### Get Submitted PDF

- **SDK method(s):** `get_split_documents()`
- **HTTP / route:** `GET /api/v8/partner/documents-set`
- **Docs:** https://docs.veryfi.com/api/receipts-invoices/get-submitted-pdf/
- **Content types (docs):** application/json, application/json, application/json, application/json, application/json, application/json, application/json, application/json, application/json, application/json

| SDK Method | API Parameter | Location | Type | Required | SDK Support | SDK Parameter | Action | Docs URL |
|---|---|---|---|---|---|---|---|---|
| `get_split_documents()` | `page` | query | see docs | no | Named, but sent as JSON body | $page | Send as query string | https://docs.veryfi.com/api/receipts-invoices/get-submitted-pdf/ |
| `get_split_documents()` | `page_size` | query | see docs | no | Named, but sent as JSON body | $page_size | Send as query string | https://docs.veryfi.com/api/receipts-invoices/get-submitted-pdf/ |
| `get_split_documents()` | `file` | multipart/file | file | no | No | — | Add process-from-path multipart helper | https://docs.veryfi.com/api/receipts-invoices/get-submitted-pdf/ |
| `get_split_documents()` | `file_data` | body | string | no | Yes via base64 helper | file_data | OK | https://docs.veryfi.com/api/receipts-invoices/get-submitted-pdf/ |
| `get_split_documents()` | `file_url` | body | string/array | no | Yes | $file_url / $file_urls | OK | https://docs.veryfi.com/api/receipts-invoices/get-submitted-pdf/ |
| `get_split_documents()` | `file_urls` | body | string/array | no | Yes | $file_url / $file_urls | OK | https://docs.veryfi.com/api/receipts-invoices/get-submitted-pdf/ |
| `get_split_documents()` | `file_name` | body | see docs | no | Via $kwargs | $kwargs | OK | https://docs.veryfi.com/api/receipts-invoices/get-submitted-pdf/ |
| `get_split_documents()` | `package_path` | body | see docs | no | Via $kwargs | $kwargs | OK | https://docs.veryfi.com/api/receipts-invoices/get-submitted-pdf/ |
| `get_split_documents()` | `bucket` | body | see docs | no | Via $kwargs | $kwargs | OK | https://docs.veryfi.com/api/receipts-invoices/get-submitted-pdf/ |
| `get_split_documents()` | `external_id` | body | see docs | no | Via $kwargs | $kwargs | OK | https://docs.veryfi.com/api/receipts-invoices/get-submitted-pdf/ |
| `get_split_documents()` | `meta.tags` | body | see docs | no | Via $kwargs | $kwargs | OK | https://docs.veryfi.com/api/receipts-invoices/get-submitted-pdf/ |
| `get_split_documents()` | `categories` | body | see docs | no | Via $kwargs | $kwargs | OK | https://docs.veryfi.com/api/receipts-invoices/get-submitted-pdf/ |
| `get_split_documents()` | `tags` | body | see docs | no | Via $kwargs | $kwargs | OK | https://docs.veryfi.com/api/receipts-invoices/get-submitted-pdf/ |
| `get_split_documents()` | `max_pages_to_process` | body | see docs | no | Via $kwargs | $kwargs | OK | https://docs.veryfi.com/api/receipts-invoices/get-submitted-pdf/ |

### Split and process a PDF

- **SDK method(s):** `split_document_from_base64()`, `split_document_from_url()`
- **HTTP / route:** `POST /api/v8/partner/documents-set`
- **Docs:** https://docs.veryfi.com/api/receipts-invoices/split-and-process-a-pdf/
- **Content types (docs):** application/json, multipart/form-data, application/json, application/json, application/json, application/json, application/json, application/json, application/json, application/json, application/json, application/json

| SDK Method | API Parameter | Location | Type | Required | SDK Support | SDK Parameter | Action | Docs URL |
|---|---|---|---|---|---|---|---|---|
| `split_document_from_base64()`, `split_document_from_url()` | `file` | multipart/file | file | no | No | — | Add process-from-path multipart helper | https://docs.veryfi.com/api/receipts-invoices/split-and-process-a-pdf/ |
| `split_document_from_base64()`, `split_document_from_url()` | `file_data` | body | string | no | Yes via base64 helper | file_data | OK | https://docs.veryfi.com/api/receipts-invoices/split-and-process-a-pdf/ |
| `split_document_from_base64()`, `split_document_from_url()` | `file_url` | body | string/array | no | Yes | $file_url / $file_urls | OK | https://docs.veryfi.com/api/receipts-invoices/split-and-process-a-pdf/ |
| `split_document_from_base64()`, `split_document_from_url()` | `file_urls` | body | string/array | no | Yes | $file_url / $file_urls | OK | https://docs.veryfi.com/api/receipts-invoices/split-and-process-a-pdf/ |
| `split_document_from_base64()`, `split_document_from_url()` | `file_name` | body | see docs | no | Via $kwargs | $kwargs | OK | https://docs.veryfi.com/api/receipts-invoices/split-and-process-a-pdf/ |
| `split_document_from_base64()`, `split_document_from_url()` | `package_path` | body | see docs | no | Via $kwargs | $kwargs | OK | https://docs.veryfi.com/api/receipts-invoices/split-and-process-a-pdf/ |
| `split_document_from_base64()`, `split_document_from_url()` | `bucket` | body | see docs | no | Via $kwargs | $kwargs | OK | https://docs.veryfi.com/api/receipts-invoices/split-and-process-a-pdf/ |
| `split_document_from_base64()`, `split_document_from_url()` | `external_id` | body | see docs | no | Via $kwargs | $kwargs | OK | https://docs.veryfi.com/api/receipts-invoices/split-and-process-a-pdf/ |
| `split_document_from_base64()`, `split_document_from_url()` | `meta.tags` | body | see docs | no | Via $kwargs | $kwargs | OK | https://docs.veryfi.com/api/receipts-invoices/split-and-process-a-pdf/ |
| `split_document_from_base64()`, `split_document_from_url()` | `categories` | body | see docs | no | Via $kwargs | $kwargs | OK | https://docs.veryfi.com/api/receipts-invoices/split-and-process-a-pdf/ |
| `split_document_from_base64()`, `split_document_from_url()` | `tags` | body | see docs | no | Via $kwargs | $kwargs | OK | https://docs.veryfi.com/api/receipts-invoices/split-and-process-a-pdf/ |
| `split_document_from_base64()`, `split_document_from_url()` | `max_pages_to_process` | body | see docs | no | Via $kwargs | $kwargs | OK | https://docs.veryfi.com/api/receipts-invoices/split-and-process-a-pdf/ |

### Get a Document

- **SDK method(s):** `get_document()`
- **HTTP / route:** `GET /api/v8/partner/documents/:document_id`
- **Docs:** https://docs.veryfi.com/api/receipts-invoices/get-a-document/
- **Content types (docs):** application/json, application/json, application/json, application/json, application/json, application/json, application/json, application/json, application/json, application/json

| SDK Method | API Parameter | Location | Type | Required | SDK Support | SDK Parameter | Action | Docs URL |
|---|---|---|---|---|---|---|---|---|
| `get_document()` | `document_id` | path | string/int | yes | Yes | $document_id | OK | https://docs.veryfi.com/api/receipts-invoices/get-a-document/ |
| `get_document()` | `bounding_boxes` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['bounding_boxes']` | Send as query string | https://docs.veryfi.com/api/receipts-invoices/get-a-document/ |
| `get_document()` | `confidence_details` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['confidence_details']` | Send as query string | https://docs.veryfi.com/api/receipts-invoices/get-a-document/ |
| `get_document()` | `detailed` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['detailed']` | Send as query string | https://docs.veryfi.com/api/receipts-invoices/get-a-document/ |
| `get_document()` | `return_audit_trail` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['return_audit_trail']` | Send as query string | https://docs.veryfi.com/api/receipts-invoices/get-a-document/ |

### Create a Line Item

- **SDK method(s):** `add_line_item()`
- **HTTP / route:** `POST /api/v8/partner/documents/:document_id/line-items`
- **Docs:** https://docs.veryfi.com/api/receipts-invoices/create-a-line-item/
- **Content types (docs):** application/json, application/json, application/json, application/json, application/json, application/json, application/json, application/json, application/json, application/json, application/json

| SDK Method | API Parameter | Location | Type | Required | SDK Support | SDK Parameter | Action | Docs URL |
|---|---|---|---|---|---|---|---|---|
| `add_line_item()` | `document_id` | path | string/int | yes | Yes | $document_id | OK | https://docs.veryfi.com/api/receipts-invoices/create-a-line-item/ |

### Update a Line Item

- **SDK method(s):** `update_line_item()`
- **HTTP / route:** `PUT /api/v8/partner/documents/:document_id/line-items/:line_item_id`
- **Docs:** https://docs.veryfi.com/api/receipts-invoices/update-a-line-item/
- **Content types (docs):** application/json, application/json, application/json, application/json, application/json, application/json, application/json, application/json, application/json, application/json, application/json

| SDK Method | API Parameter | Location | Type | Required | SDK Support | SDK Parameter | Action | Docs URL |
|---|---|---|---|---|---|---|---|---|
| `update_line_item()` | `document_id` | path | string/int | yes | Yes | $document_id | OK | https://docs.veryfi.com/api/receipts-invoices/update-a-line-item/ |
| `update_line_item()` | `line_item_id` | path | string/int | yes | Yes | $line_item_id | OK | https://docs.veryfi.com/api/receipts-invoices/update-a-line-item/ |

### Get W-2s

- **SDK method(s):** `get_w2s()`
- **HTTP / route:** `GET /api/v8/partner/w2s`
- **Docs:** https://docs.veryfi.com/api/w2s/get-w-2-s/
- **Content types (docs):** application/json

| SDK Method | API Parameter | Location | Type | Required | SDK Support | SDK Parameter | Action | Docs URL |
|---|---|---|---|---|---|---|---|---|
| `get_w2s()` | `meta.tags` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['meta.tags']` | Send as query string | https://docs.veryfi.com/api/w2s/get-w-2-s/ |
| `get_w2s()` | `meta.external_id` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['meta.external_id']` | Send as query string | https://docs.veryfi.com/api/w2s/get-w-2-s/ |
| `get_w2s()` | `created_date__gt` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['created_date__gt']` | Send as query string | https://docs.veryfi.com/api/w2s/get-w-2-s/ |
| `get_w2s()` | `created_date__lt` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['created_date__lt']` | Send as query string | https://docs.veryfi.com/api/w2s/get-w-2-s/ |
| `get_w2s()` | `created_date__gte` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['created_date__gte']` | Send as query string | https://docs.veryfi.com/api/w2s/get-w-2-s/ |
| `get_w2s()` | `created_date__lte` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['created_date__lte']` | Send as query string | https://docs.veryfi.com/api/w2s/get-w-2-s/ |
| `get_w2s()` | `updated_date__gt` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['updated_date__gt']` | Send as query string | https://docs.veryfi.com/api/w2s/get-w-2-s/ |
| `get_w2s()` | `updated_date__lt` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['updated_date__lt']` | Send as query string | https://docs.veryfi.com/api/w2s/get-w-2-s/ |
| `get_w2s()` | `updated_date__gte` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['updated_date__gte']` | Send as query string | https://docs.veryfi.com/api/w2s/get-w-2-s/ |
| `get_w2s()` | `updated_date__lte` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['updated_date__lte']` | Send as query string | https://docs.veryfi.com/api/w2s/get-w-2-s/ |
| `get_w2s()` | `page` | query | see docs | no | Named, but sent as JSON body | $page | Send as query string | https://docs.veryfi.com/api/w2s/get-w-2-s/ |
| `get_w2s()` | `page_size` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['page_size']` | Send as query string | https://docs.veryfi.com/api/w2s/get-w-2-s/ |
| `get_w2s()` | `bounding_boxes` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['bounding_boxes']` | Send as query string | https://docs.veryfi.com/api/w2s/get-w-2-s/ |
| `get_w2s()` | `confidence_details` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['confidence_details']` | Send as query string | https://docs.veryfi.com/api/w2s/get-w-2-s/ |
| `get_w2s()` | `q` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['q']` | Send as query string | https://docs.veryfi.com/api/w2s/get-w-2-s/ |
| `get_w2s()` | `track_total_results` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['track_total_results']` | Send as query string | https://docs.veryfi.com/api/w2s/get-w-2-s/ |

### Process a W-2

- **SDK method(s):** `process_w2()`, `process_w2_base64()`, `process_w2_from_url()`
- **HTTP / route:** `POST /api/v8/partner/w2s`
- **Docs:** https://docs.veryfi.com/api/w2s/process-a-w-2/
- **Content types (docs):** application/json, multipart/form-data, application/json, application/json, application/json, application/json, application/json, application/json, application/json, application/json, application/json, application/json

| SDK Method | API Parameter | Location | Type | Required | SDK Support | SDK Parameter | Action | Docs URL |
|---|---|---|---|---|---|---|---|---|
| `process_w2()`, `process_w2_base64()`, `process_w2_from_url()` | `file` | multipart/file | file | no | Broken for local file helper | CURLFile in process_w2/process_w9 | Set has_files=true | https://docs.veryfi.com/api/w2s/process-a-w-2/ |
| `process_w2()`, `process_w2_base64()`, `process_w2_from_url()` | `file_name` | body | see docs | no | Yes named | derived | OK | https://docs.veryfi.com/api/w2s/process-a-w-2/ |
| `process_w2()`, `process_w2_base64()`, `process_w2_from_url()` | `file_data` | body | string/array | no | Yes via base64/url helpers + kwargs | file_data | OK | https://docs.veryfi.com/api/w2s/process-a-w-2/ |
| `process_w2()`, `process_w2_base64()`, `process_w2_from_url()` | `file_url` | body | string/array | no | Yes via base64/url helpers + kwargs | file_url | OK | https://docs.veryfi.com/api/w2s/process-a-w-2/ |
| `process_w2()`, `process_w2_base64()`, `process_w2_from_url()` | `file_urls` | body | string/array | no | Yes via base64/url helpers + kwargs | file_urls | OK | https://docs.veryfi.com/api/w2s/process-a-w-2/ |
| `process_w2()`, `process_w2_base64()`, `process_w2_from_url()` | `package_path` | body | see docs | no | Via $kwargs | $kwargs | OK | https://docs.veryfi.com/api/w2s/process-a-w-2/ |
| `process_w2()`, `process_w2_base64()`, `process_w2_from_url()` | `bucket` | body | see docs | no | Via $kwargs | $kwargs | OK | https://docs.veryfi.com/api/w2s/process-a-w-2/ |
| `process_w2()`, `process_w2_base64()`, `process_w2_from_url()` | `external_id` | body | see docs | no | Via $kwargs | $kwargs | OK | https://docs.veryfi.com/api/w2s/process-a-w-2/ |
| `process_w2()`, `process_w2_base64()`, `process_w2_from_url()` | `meta.tags` | body | see docs | no | Via $kwargs | $kwargs | OK | https://docs.veryfi.com/api/w2s/process-a-w-2/ |
| `process_w2()`, `process_w2_base64()`, `process_w2_from_url()` | `bounding_boxes` | body | see docs | no | Via $kwargs | $kwargs | OK | https://docs.veryfi.com/api/w2s/process-a-w-2/ |
| `process_w2()`, `process_w2_base64()`, `process_w2_from_url()` | `confidence_details` | body | see docs | no | Via $kwargs | $kwargs | OK | https://docs.veryfi.com/api/w2s/process-a-w-2/ |
| `process_w2()`, `process_w2_base64()`, `process_w2_from_url()` | `max_pages_to_process` | body | see docs | no | Yes named | $max_pages_to_process | OK | https://docs.veryfi.com/api/w2s/process-a-w-2/ |
| `process_w2()`, `process_w2_base64()`, `process_w2_from_url()` | `auto_delete` | body | see docs | no | Yes named | $auto_delete | OK | https://docs.veryfi.com/api/w2s/process-a-w-2/ |

### Get a W-2

- **SDK method(s):** `get_w2()`
- **HTTP / route:** `GET /api/v8/partner/w2s/:document_id`
- **Docs:** https://docs.veryfi.com/api/w2s/get-a-w-2/
- **Content types (docs):** application/json

| SDK Method | API Parameter | Location | Type | Required | SDK Support | SDK Parameter | Action | Docs URL |
|---|---|---|---|---|---|---|---|---|
| `get_w2()` | `document_id` | path | string/int | yes | Yes | $document_id | OK | https://docs.veryfi.com/api/w2s/get-a-w-2/ |
| `get_w2()` | `bounding_boxes` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['bounding_boxes']` | Send as query string | https://docs.veryfi.com/api/w2s/get-a-w-2/ |
| `get_w2()` | `confidence_details` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['confidence_details']` | Send as query string | https://docs.veryfi.com/api/w2s/get-a-w-2/ |

### Get W-8BEN-Es

- **SDK method(s):** `get_w8benes()`
- **HTTP / route:** `GET /api/v8/partner/w-8ben-e`
- **Docs:** https://docs.veryfi.com/api/w-8ben-e/get-w-8-ben-es/
- **Content types (docs):** application/json

| SDK Method | API Parameter | Location | Type | Required | SDK Support | SDK Parameter | Action | Docs URL |
|---|---|---|---|---|---|---|---|---|
| `get_w8benes()` | `meta.tags` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['meta.tags']` | Send as query string | https://docs.veryfi.com/api/w-8ben-e/get-w-8-ben-es/ |
| `get_w8benes()` | `meta.external_id` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['meta.external_id']` | Send as query string | https://docs.veryfi.com/api/w-8ben-e/get-w-8-ben-es/ |
| `get_w8benes()` | `created_date__gt` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['created_date__gt']` | Send as query string | https://docs.veryfi.com/api/w-8ben-e/get-w-8-ben-es/ |
| `get_w8benes()` | `created_date__lt` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['created_date__lt']` | Send as query string | https://docs.veryfi.com/api/w-8ben-e/get-w-8-ben-es/ |
| `get_w8benes()` | `created_date__gte` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['created_date__gte']` | Send as query string | https://docs.veryfi.com/api/w-8ben-e/get-w-8-ben-es/ |
| `get_w8benes()` | `created_date__lte` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['created_date__lte']` | Send as query string | https://docs.veryfi.com/api/w-8ben-e/get-w-8-ben-es/ |
| `get_w8benes()` | `updated_date__gt` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['updated_date__gt']` | Send as query string | https://docs.veryfi.com/api/w-8ben-e/get-w-8-ben-es/ |
| `get_w8benes()` | `updated_date__lt` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['updated_date__lt']` | Send as query string | https://docs.veryfi.com/api/w-8ben-e/get-w-8-ben-es/ |
| `get_w8benes()` | `updated_date__gte` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['updated_date__gte']` | Send as query string | https://docs.veryfi.com/api/w-8ben-e/get-w-8-ben-es/ |
| `get_w8benes()` | `updated_date__lte` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['updated_date__lte']` | Send as query string | https://docs.veryfi.com/api/w-8ben-e/get-w-8-ben-es/ |
| `get_w8benes()` | `page` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['page']` | Send as query string | https://docs.veryfi.com/api/w-8ben-e/get-w-8-ben-es/ |
| `get_w8benes()` | `page_size` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['page_size']` | Send as query string | https://docs.veryfi.com/api/w-8ben-e/get-w-8-ben-es/ |
| `get_w8benes()` | `bounding_boxes` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['bounding_boxes']` | Send as query string | https://docs.veryfi.com/api/w-8ben-e/get-w-8-ben-es/ |
| `get_w8benes()` | `confidence_details` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['confidence_details']` | Send as query string | https://docs.veryfi.com/api/w-8ben-e/get-w-8-ben-es/ |
| `get_w8benes()` | `q` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['q']` | Send as query string | https://docs.veryfi.com/api/w-8ben-e/get-w-8-ben-es/ |
| `get_w8benes()` | `track_total_results` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['track_total_results']` | Send as query string | https://docs.veryfi.com/api/w-8ben-e/get-w-8-ben-es/ |

### Get a W-8BEN-E

- **SDK method(s):** `get_w8bene()`
- **HTTP / route:** `GET /api/v8/partner/w-8ben-e/:document_id`
- **Docs:** https://docs.veryfi.com/api/w-8ben-e/get-a-w-8-ben-e/
- **Content types (docs):** application/json

| SDK Method | API Parameter | Location | Type | Required | SDK Support | SDK Parameter | Action | Docs URL |
|---|---|---|---|---|---|---|---|---|
| `get_w8bene()` | `document_id` | path | string/int | yes | Yes | $document_id | OK | https://docs.veryfi.com/api/w-8ben-e/get-a-w-8-ben-e/ |
| `get_w8bene()` | `bounding_boxes` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['bounding_boxes']` | Send as query string | https://docs.veryfi.com/api/w-8ben-e/get-a-w-8-ben-e/ |
| `get_w8bene()` | `confidence_details` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['confidence_details']` | Send as query string | https://docs.veryfi.com/api/w-8ben-e/get-a-w-8-ben-e/ |

### Get W-9s

- **SDK method(s):** `get_w9s()`
- **HTTP / route:** `GET /api/v8/partner/w9s`
- **Docs:** https://docs.veryfi.com/api/w9s/get-w-9-s/
- **Content types (docs):** application/json

| SDK Method | API Parameter | Location | Type | Required | SDK Support | SDK Parameter | Action | Docs URL |
|---|---|---|---|---|---|---|---|---|
| `get_w9s()` | `meta.tags` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['meta.tags']` | Send as query string | https://docs.veryfi.com/api/w9s/get-w-9-s/ |
| `get_w9s()` | `meta.external_id` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['meta.external_id']` | Send as query string | https://docs.veryfi.com/api/w9s/get-w-9-s/ |
| `get_w9s()` | `created_date__gt` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['created_date__gt']` | Send as query string | https://docs.veryfi.com/api/w9s/get-w-9-s/ |
| `get_w9s()` | `created_date__lt` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['created_date__lt']` | Send as query string | https://docs.veryfi.com/api/w9s/get-w-9-s/ |
| `get_w9s()` | `created_date__gte` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['created_date__gte']` | Send as query string | https://docs.veryfi.com/api/w9s/get-w-9-s/ |
| `get_w9s()` | `created_date__lte` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['created_date__lte']` | Send as query string | https://docs.veryfi.com/api/w9s/get-w-9-s/ |
| `get_w9s()` | `updated_date__gt` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['updated_date__gt']` | Send as query string | https://docs.veryfi.com/api/w9s/get-w-9-s/ |
| `get_w9s()` | `updated_date__lt` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['updated_date__lt']` | Send as query string | https://docs.veryfi.com/api/w9s/get-w-9-s/ |
| `get_w9s()` | `updated_date__gte` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['updated_date__gte']` | Send as query string | https://docs.veryfi.com/api/w9s/get-w-9-s/ |
| `get_w9s()` | `updated_date__lte` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['updated_date__lte']` | Send as query string | https://docs.veryfi.com/api/w9s/get-w-9-s/ |
| `get_w9s()` | `page` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['page']` | Send as query string | https://docs.veryfi.com/api/w9s/get-w-9-s/ |
| `get_w9s()` | `page_size` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['page_size']` | Send as query string | https://docs.veryfi.com/api/w9s/get-w-9-s/ |
| `get_w9s()` | `bounding_boxes` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['bounding_boxes']` | Send as query string | https://docs.veryfi.com/api/w9s/get-w-9-s/ |
| `get_w9s()` | `confidence_details` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['confidence_details']` | Send as query string | https://docs.veryfi.com/api/w9s/get-w-9-s/ |
| `get_w9s()` | `q` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['q']` | Send as query string | https://docs.veryfi.com/api/w9s/get-w-9-s/ |
| `get_w9s()` | `track_total_results` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['track_total_results']` | Send as query string | https://docs.veryfi.com/api/w9s/get-w-9-s/ |

### Process a W-9

- **SDK method(s):** `process_w9()`, `process_w9_base64()`, `process_w9_from_url()`
- **HTTP / route:** `POST /api/v8/partner/w9s`
- **Docs:** https://docs.veryfi.com/api/w9s/process-a-w-9/
- **Content types (docs):** application/json, multipart/form-data, application/json, application/json, application/json, application/json, application/json, application/json, application/json, application/json, application/json, application/json

| SDK Method | API Parameter | Location | Type | Required | SDK Support | SDK Parameter | Action | Docs URL |
|---|---|---|---|---|---|---|---|---|
| `process_w9()`, `process_w9_base64()`, `process_w9_from_url()` | `file` | multipart/file | file | no | Broken for local file helper | CURLFile in process_w2/process_w9 | Set has_files=true | https://docs.veryfi.com/api/w9s/process-a-w-9/ |
| `process_w9()`, `process_w9_base64()`, `process_w9_from_url()` | `file_name` | body | see docs | no | Yes named | derived | OK | https://docs.veryfi.com/api/w9s/process-a-w-9/ |
| `process_w9()`, `process_w9_base64()`, `process_w9_from_url()` | `file_data` | body | string/array | no | Yes via base64/url helpers + kwargs | file_data | OK | https://docs.veryfi.com/api/w9s/process-a-w-9/ |
| `process_w9()`, `process_w9_base64()`, `process_w9_from_url()` | `file_url` | body | string/array | no | Yes via base64/url helpers + kwargs | file_url | OK | https://docs.veryfi.com/api/w9s/process-a-w-9/ |
| `process_w9()`, `process_w9_base64()`, `process_w9_from_url()` | `file_urls` | body | string/array | no | Yes via base64/url helpers + kwargs | file_urls | OK | https://docs.veryfi.com/api/w9s/process-a-w-9/ |
| `process_w9()`, `process_w9_base64()`, `process_w9_from_url()` | `package_path` | body | see docs | no | Via $kwargs | $kwargs | OK | https://docs.veryfi.com/api/w9s/process-a-w-9/ |
| `process_w9()`, `process_w9_base64()`, `process_w9_from_url()` | `bucket` | body | see docs | no | Via $kwargs | $kwargs | OK | https://docs.veryfi.com/api/w9s/process-a-w-9/ |
| `process_w9()`, `process_w9_base64()`, `process_w9_from_url()` | `external_id` | body | see docs | no | Via $kwargs | $kwargs | OK | https://docs.veryfi.com/api/w9s/process-a-w-9/ |
| `process_w9()`, `process_w9_base64()`, `process_w9_from_url()` | `meta.tags` | body | see docs | no | Via $kwargs | $kwargs | OK | https://docs.veryfi.com/api/w9s/process-a-w-9/ |
| `process_w9()`, `process_w9_base64()`, `process_w9_from_url()` | `bounding_boxes` | body | see docs | no | Via $kwargs | $kwargs | OK | https://docs.veryfi.com/api/w9s/process-a-w-9/ |
| `process_w9()`, `process_w9_base64()`, `process_w9_from_url()` | `confidence_details` | body | see docs | no | Via $kwargs | $kwargs | OK | https://docs.veryfi.com/api/w9s/process-a-w-9/ |
| `process_w9()`, `process_w9_base64()`, `process_w9_from_url()` | `max_pages_to_process` | body | see docs | no | Yes named | $max_pages_to_process | OK | https://docs.veryfi.com/api/w9s/process-a-w-9/ |
| `process_w9()`, `process_w9_base64()`, `process_w9_from_url()` | `auto_delete` | body | see docs | no | Yes named | $auto_delete | OK | https://docs.veryfi.com/api/w9s/process-a-w-9/ |
| `process_w9()`, `process_w9_base64()`, `process_w9_from_url()` | `parse_address` | body | boolean | no | Via $kwargs | $kwargs | OK | https://docs.veryfi.com/api/w9s/process-a-w-9/ |

### Get a W-9

- **SDK method(s):** `get_w9()`
- **HTTP / route:** `GET /api/v8/partner/w9s/:document_id`
- **Docs:** https://docs.veryfi.com/api/w9s/get-a-w-9/
- **Content types (docs):** application/json

| SDK Method | API Parameter | Location | Type | Required | SDK Support | SDK Parameter | Action | Docs URL |
|---|---|---|---|---|---|---|---|---|
| `get_w9()` | `document_id` | path | string/int | yes | Yes | $document_id | OK | https://docs.veryfi.com/api/w9s/get-a-w-9/ |
| `get_w9()` | `bounding_boxes` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['bounding_boxes']` | Send as query string | https://docs.veryfi.com/api/w9s/get-a-w-9/ |
| `get_w9()` | `confidence_details` | query | see docs | no | Via $kwargs, but sent as JSON body | `$kwargs['confidence_details']` | Send as query string | https://docs.veryfi.com/api/w9s/get-a-w-9/ |

## SDK-only or undocumented functionality

| SDK Method | HTTP Method | Route | Source File | Issue | Recommended Action |
|---|---|---|---|---|---|
| `get_tags()` | GET | `/api/v8/partner/tags` | `src/veryfi/documents/tags/GetTags.php` | No current official docs page for GET /tags/. | Confirm whether this workspace-level tags list is still supported; document or remove. |
| `replace_tags()` | PUT | `/api/v8/partner/documents/:document_id` | `src/veryfi/documents/tags/ReplaceTags.php` | Convenience wrapper around Update a Document (sends `{tags: ...}`). Not a distinct documented operation. | Keep as helper; document that it maps to PUT /documents/:document_id. Avoid documenting it as a unique endpoint. |
| `verify_signature()` | — | `— (local HMAC)` | `src/veryfi/client/VeryfiSignature.php` | Local webhook signature verification, not an HTTP API operation. | Keep. Link to webhook docs in PHPDoc. |
| `process_document_base64()` / `process_document_url()` | POST | `/api/v8/partner/documents` | documents/ProcessDocument*.php | Additional upload-mode helpers for the same Process a Document operation. | Keep. This is an SDK design pattern, not extra API coverage. |
| `process_*_base64()` / `process_*_url()` / `process_*_from_url()` | POST | `resource process routes` | various | Upload-mode helpers sharing one documented POST route per resource. | Keep. Do not count as separate API operations. |

Additional public helpers that are **not** extra API operations (same method+route as the process endpoint): `process_document_base64`, `process_document_url`, `process_any_document_base64`, `process_any_document_url`, `process_bank_statement_base64`, `process_bank_statement_url`, `process_business_card_base64`, `process_business_card_from_url`, `process_check_base64`, `process_check_from_url`, `process_w2_base64`, `process_w2_from_url`, `process_w8bene_base64`, `process_w8bene_from_url`, `process_w9_base64`, `process_w9_from_url`, `classify_document_from_base64`, `classify_document_from_url`, `split_document_from_base64`, `split_document_from_url`.

## Inconsistencies (SDK vs current docs)

### Correctness

- **Query vs body:** All list/get methods that accept filters (`get_documents`, `get_any_documents`, `get_bank_statements`, `get_checks`, `get_business_cards`, `get_w2s`, `get_w9s`, `get_w8benes`, `get_split_documents`, and corresponding get-by-id methods with `bounding_boxes` / `confidence_details`) pass those fields through `CURLOPT_POSTFIELDS` as JSON. Docs place them on the **query string**.
- **Multipart encoding:** `process_w2()` and `process_w9()` build a `CURLFile` payload but JSON-encode it. `process_document()`, `process_any_document()`, `process_bank_statement()`, `process_business_card()`, `process_check()`, and `process_w8bene()` correctly pass `$has_files = true`.
- **Global Content-Type:** `Content-Type: application/json` is always applied, including multipart uploads.
- **AnyDocs process field name:** SDK requires `template_name`; current Process a ∀Doc schema lists `blueprint_name` (and list filters include both `blueprint_name` and `template_name`).
- **W-2/W-9 `max_pages_to_process` default:** SDK defaults to `1`; receipts default in docs is a 15-page limit (resource-specific defaults differ — confirm W-2/W-9 docs).

### Missing CRUD / sibling operations on supported resources

- Receipts: bulk process, tax-line CRUD, async is a **body flag** on POST `/documents` (supported via `$kwargs`), not a separate route.
- Bank statements / checks / AnyDocs: dedicated `POST .../async` routes are **not** implemented.
- Checks: `POST /check-with-document` (check with remittance) is missing.
- Tags exist in the SDK **only** for receipts/invoices documents. The API documents the same tag family for AnyDocs, bank statements, checks, business cards, contracts, W-2, W-8BEN-E, and W-9.
- Update methods are missing for AnyDocs, bank statements, business cards, checks, W-2, W-8BEN-E, W-9 (receipts `update_document` exists).
- PDF splitter extras: W-2 set and bank-statement set APIs are missing (`/w2s-set`, `/bank-statements-set`). Receipts `/documents-set` is implemented.

### Entire products missing from the SDK

- **Contracts** (`/contracts`)
- **Parse / Markdown** (`/parse`, `/parse-set`)
- **Extract** (`POST /extract` — classify and extract)
- **Fraud blocklist** (`/fraud/blocklist`)
- **Settings:** webhooks, client-keys, TLS certificates, API keys (v1), permissions, verify key
- **Blueprints**, **OCR counts**, **OpenAPI schema**, **release notifications**

### Line items

- CRUD routes match docs (create/list/get/update/delete/delete-all).
- Request models (`LineItem`, `LineItemUpdate`) only allow a fixed set of **flat** properties and throw `Bad Argument` on unknown keys, so documented fields such as `expanded_description`, `brand`, and `tags` cannot be sent through the public model.
- The OpenAPI body for create/update is largely nested DetailedField objects; the SDK historically sends flat scalars (`description`, `total`, `sku`, …). Treat this as a contract mismatch to verify against live API behavior.

### Tests

Present for the main process/list/delete paths of receipts, line items, tags, split, anydocs, bank statements, business cards, checks, classify, W-2, W-8BEN-E, W-9.

Missing or incomplete tests include: `get_document` query flags, `get_business_card`, `get_check`, `get_w9`, `get_w8bene`, `process_w9_base64`, `process_w8bene_base64`, and all missing resources.

### PHPDoc / README

- README only shows `process_document` and `update_document`.
- Several process methods do not list documented optional body fields in PHPDoc (they rely on `$kwargs`).
- `get_tags()` has no docs URL.

## Summary metrics

| Metric | Count |
|---|---|
| Total documented API operations | **151** |
| Fully implemented (IMPLEMENTED) | **24** |
| Partially implemented (PARTIAL) | **24** |
| Missing (MISSING) | **103** |
| Uncertain (UNCERTAIN) | **0** |
| SDK-only / undocumented operations | **3** |

| Coverage | Formula | Value |
|---|---|---|
| Endpoint coverage | (IMPLEMENTED + PARTIAL) / Total | **31.8%** (48/151) |
| Full API coverage | IMPLEMENTED / Total | **15.9%** (24/151) |

By product:

| Product | Total | Implemented | Partial | Missing |
|---|---:|---:|---:|---:|
| AnyDocs | 12 | 1 | 3 | 8 |
| Bank Statements | 14 | 2 | 2 | 10 |
| Business Cards | 10 | 2 | 2 | 6 |
| Checks | 12 | 2 | 2 | 8 |
| Classification | 2 | 0 | 1 | 1 |
| Contracts | 10 | 0 | 0 | 10 |
| Fraud Detection | 3 | 0 | 0 | 3 |
| Parse Documents | 9 | 0 | 0 | 9 |
| Receipts & Invoices | 25 | 13 | 6 | 6 |
| Settings | 21 | 0 | 0 | 21 |
| W-2s | 13 | 1 | 3 | 9 |
| W-8BEN-E | 10 | 2 | 2 | 6 |
| W-9s | 10 | 1 | 3 | 6 |

## Recommended Implementation Priority

### P0 — Correctness issues

Existing SDK functionality that can call the API incorrectly:

1. **Serialize GET (and any query) parameters onto the query string** in `Request::request()`. Today list/search/get filters are JSON bodies.
2. **Fix multipart uploads** for `process_w2()` and `process_w9()` (`$has_files = true`).
3. **Do not force `Content-Type: application/json` on multipart requests**; let cURL set the boundary or set `multipart/form-data`.
4. **AnyDocs process:** send documented `blueprint_name` (keep `template_name` as a backward-compatible alias if the API still accepts it).

### P1 — Partial existing functionality

1. Expand `LineItem` / `LineItemUpdate` (or allow an open `array` payload) so documented create/update fields are not rejected.
2. Add multipart `file` helpers for classify (`POST /classify`) and PDF split (`POST /documents-set`).
3. First-class PHPDoc (and README examples) for documented process flags: `async`, `tags`, `boost_mode`, `parse_address`, `device_data`, `allowed_async_enrichments`, `thinking`, S3 `package_path`/`bucket`.
4. Align `boost_mode` type with docs (`boolean` vs SDK `int` on `process_document_url`).

### P2 — Missing operations for already-supported resources

1. **Update** methods: AnyDocs, bank statements, business cards, checks, W-2, W-8BEN-E, W-9.
2. **Tag family** for every supported resource that documents tags (same PUT/POST/GET/DELETE pattern as receipts).
3. Dedicated **async process** routes: `POST /any-documents/async`, `/bank-statements/async`, `/checks/async`.
4. Receipts: `POST /documents/bulk`; tax-line CRUD under `/documents/:id/tax-lines`.
5. Checks: `POST /check-with-document`.
6. Splitter sets: `/w2s-set`, `/bank-statements-set` (list/get/process).

### P3 — Entirely missing API resources

1. Contracts (`/contracts` + tags).
2. Parse / markdown (`/parse`, `/parse-set`, async).
3. Classify-and-extract (`POST /extract`).
4. Fraud device blocklist (`/fraud/blocklist`).
5. Settings: webhooks, client keys, TLS certificates, API keys (note **v1** path), permissions.
6. Blueprints, OCR counts, OpenAPI schema, release notifications.

### P4 — SDK quality improvements

1. Tests for `get_business_card`, `get_check`, `get_w9`, `get_w8bene`, `process_w9_base64`, `process_w8bene_base64`, and query-parameter encoding once fixed.
2. README examples for each supported resource (not only receipts process/update).
3. PHPDoc links on every public method (several already include docs URLs; `get_tags` / `replace_tags` do not).
4. Optional response DTOs are **not** required by current architecture (raw JSON strings). Add them only if the project chooses to introduce models beyond line-item request objects.
5. Document `replace_tags` as an update-document helper, not a unique endpoint.
6. Confirm `GET /tags/` with Veryfi; it is not in the current public docs sitemap.

## Authentication, pagination, async, files, webhooks (docs vs SDK)

| Topic | Docs | SDK |
|---|---|---|
| Auth | Client ID + API key and/or request signature; optional bearer keys (v1 settings) | Standard keys + HMAC signature on every request. No bearer-key helpers. |
| Pagination | `page`, `page_size` (typical max 50 per page) on list endpoints | Named on some list methods; others only `$kwargs`; **not sent as query string**. No response pagination helpers. |
| Async | Receipts: `async` boolean on POST `/documents`. AnyDocs/bank/checks/parse: separate `/async` routes + webhooks | Receipts can pass `async` via `$kwargs` to the sync route. Dedicated `/async` routes missing. No webhook CRUD. Local `verify_signature()` only. |
| File upload | JSON `file_data` / `file_url` / `file_urls` / S3 `package_path`+`bucket`, and `multipart/form-data` `file` | Filesystem + base64 + URL helpers on most resources; S3 via `$kwargs`; classify/split lack filesystem multipart; W-2/W-9 filesystem helpers mis-encode. |
| Webhooks | Settings `POST/GET /settings/webhooks`, confirm | Not implemented (signature verify only). |

## Appendix — documented operations inventory

Operation pages were enumerated from the official sitemap. Product landing pages that timed out were not required because each operation has its own URL and OpenAPI method/path heading.

Parser artifacts used while producing this report (not part of the SDK): parameter names for GET query strings and POST/PUT top-level body fields. Nested DetailedField children (`value`, `bounding_box`, …) were excluded from “top-level body” lists except where they are the documented query/path names.

